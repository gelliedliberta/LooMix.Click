<?php
/**
 * Admin Controller - Admin paneli yönetimi
 * LooMix.Click
 */

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Admin rotalarından login hariç hepsi authentication gerektirir
        $currentRoute = $this->getCurrentRoute();
        if (!in_array($currentRoute, ['/admin/login', '/admin/auth'])) {
            $this->requireAdmin();
        }

        // Merkezi CSRF kontrolü: Admin API altında yazma işlemleri için zorunlu
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $isStateChanging = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        if ($isStateChanging && strpos($currentRoute, '/admin/api/') === 0) {
            if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
                $this->json(['error' => 'CSRF token hatalı'], 400);
            }
        }
    }
    
    /**
     * Admin Dashboard - Ana sayfa
     */
    public function index() {
        // Dashboard istatistikleri
        $stats = $this->getDashboardStats();
        
        // Son haberler
        $recentNews = $this->db->fetchAll("
            SELECT n.id, n.title, n.slug, n.status, n.created_at, 
                   c.name as category_name, c.color as category_color 
            FROM news n 
            INNER JOIN categories c ON n.category_id = c.id 
            ORDER BY n.created_at DESC 
            LIMIT 10
        ");
        
        // Son yorumlar (gelecekte eklenebilir)
        $recentComments = [];
        
        $view = new View();
        $view->render('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard - ' . SITE_NAME,
            'stats' => $stats,
            'recentNews' => $recentNews,
            'recentComments' => $recentComments,
            'currentUser' => $this->getCurrentUser()
        ], 'admin');
    }
    
    /**
     * Login sayfası
     */
    public function login() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Zaten giriş yapmışsa dashboard'a yönlendir
        if (isset($_SESSION[ADMIN_SESSION_NAME]) && $_SESSION[ADMIN_SESSION_NAME] === true) {
            $this->redirect('/admin');
            return;
        }
        
        $view = new View();
        
        // Hata mesajını al ve temizle
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        
        $view->render('admin/login', [
            'pageTitle' => 'Admin Giriş - ' . SITE_NAME,
            'error' => $error
        ], false); // Layout kullanma
    }
    
    /**
     * Authentication işlemi
     */
    public function authenticate() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Kullanıcı adı ve şifre gereklidir.';
            $this->redirect('/admin/login');
            return;
        }
        
        // Kullanıcıyı doğrula
        $user = $this->db->fetch(
            "SELECT * FROM admin_users WHERE username = :username AND is_active = 1",
            ['username' => $username]
        );
        
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
            $this->redirect('/admin/login');
            return;
        }
        
        // Başarılı giriş
        $_SESSION[ADMIN_SESSION_NAME] = true;
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ];
        
        // Login count ve last_login güncelle
        $this->db->update('admin_users', [
            'last_login' => date('Y-m-d H:i:s'),
            'login_count' => $user['login_count'] + 1
        ], 'id = :id', ['id' => $user['id']]);
        
        $this->redirect('/admin');
    }
    
    /**
     * Çıkış işlemi
     */
    public function logout() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Session'ı temizle ve yok et
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        $this->redirect('/admin/login');
    }
    
    /**
     * Haberler listesi
     */
    public function news() {
        $page = (int)$this->get('page', 1);
        $search = $this->get('search', '');
        $category = $this->get('category', '');
        $status = $this->get('status', '');
        
        $newsModel = new News();
        
        // Filtreleme ve sayfalama için SQL oluştur
        $where = [];
        $params = [];
        
        if (!empty(trim((string)$search))) {
            // PDO'da aynı isimli placeholder tekrar kullanılamaz; benzersiz placeholder kullan
            $where[] = "(n.title LIKE :search1 OR n.summary LIKE :search2 OR n.content LIKE :search3 OR n.slug LIKE :search4)";
            $like = "%" . trim((string)$search) . "%";
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
            $params['search4'] = $like;
        }
        
        if (!empty($category)) {
            $where[] = "n.category_id = :category";
            $params['category'] = $category;
        }
        
        if (!empty($status)) {
            $where[] = "n.status = :status";
            $params['status'] = $status;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Haberler
        $perPage = ADMIN_NEWS_PER_PAGE;
        $offset = ($page - 1) * $perPage;
        
        $news = $this->db->fetchAll("
            SELECT n.*, c.name as category_name, c.color as category_color
            FROM news n
            INNER JOIN categories c ON n.category_id = c.id
            {$whereClause}
            ORDER BY n.created_at DESC
            LIMIT {$offset}, {$perPage}
        ", $params);
        
        // Toplam sayı
        $totalCount = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news n
            INNER JOIN categories c ON n.category_id = c.id
            {$whereClause}
        ", $params);
        
        // Kategoriler (filtre için)
        $categoryModel = new Category();
        $categories = $categoryModel->getActiveCategories();
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_count' => $totalCount,
            'total_pages' => ceil($totalCount / $perPage)
        ];
        
        $view = new View();
        $view->render('admin/news/index', [
            'pageTitle' => 'Haber Yönetimi - Admin',
            'news' => $news,
            'pagination' => $pagination,
            'categories' => $categories,
            'currentFilters' => [
                'search' => $search,
                'category' => $category,
                'status' => $status,
                'page' => $page
            ]
        ], 'admin');
    }
    
    /**
     * Yeni haber ekleme formu
     */
    public function addNews() {
        $categoryModel = new Category();
        $tagModel = new Tag();
        
        $categories = $categoryModel->getActiveCategories();
        $popularTags = $tagModel->getPopularTags(20);
        
        $view = new View();
        $view->render('admin/news/add', [
            'pageTitle' => 'Yeni Haber Ekle - Admin',
            'categories' => $categories,
            'popularTags' => $popularTags,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Haber düzenleme formu
     */
    public function editNews($id) {
        $newsModel = new News();
        $categoryModel = new Category();
        $tagModel = new Tag();
        
        $news = $newsModel->find($id);
        if (!$news) {
            $_SESSION['error'] = 'Haber bulunamadı.';
            $this->redirect('/admin/haberler');
            return;
        }
        
        // Haber etiketleri
        $newsTags = $newsModel->getNewsTags($id);
        
        $categories = $categoryModel->getActiveCategories();
        $popularTags = $tagModel->getPopularTags(20);
        
        $view = new View();
        $view->render('admin/news/edit', [
            'pageTitle' => 'Haber Düzenle - Admin',
            'news' => $news,
            'newsTags' => $newsTags,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Haber kaydetme
     */
    public function saveNews() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $data = [
            'category_id' => (int)$this->post('category_id'),
            'title' => $this->post('title'),
            'summary' => $this->post('summary'),
            'content' => $this->post('content'),
            'featured_image' => $this->post('featured_image'),
            'image_alt' => $this->post('image_alt'),
            'author_name' => $this->post('author_name') ?: $_SESSION['admin_user']['full_name'],
            'status' => $this->post('status', 'draft'),
            'is_featured' => (bool)$this->post('is_featured'),
            'is_breaking' => (bool)$this->post('is_breaking'),
            'meta_title' => $this->post('meta_title'),
            'meta_description' => $this->post('meta_description'),
            'meta_keywords' => $this->post('meta_keywords'),
            'publish_date' => $this->post('publish_date') ?: date('Y-m-d H:i:s')
        ];
        
        // Validasyon
        $errors = $this->validate($data, [
            'category_id' => 'required',
            'title' => 'required|min:5|max:255',
            'summary' => 'required|min:20',
            'content' => 'required|min:50'
        ]);
        
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }
        
        // Slug oluştur
        $data['slug'] = $this->createUniqueSlug($data['title']);
        
        $newsModel = new News();
        
        $newsId = $this->post('id');
        if ($newsId) {
            // Güncelleme
            $newsModel->update($newsId, $data);
        } else {
            // Yeni kayıt
            $newsId = $newsModel->create($data);
        }
        
        // Etiketleri kaydet
        $tags = $this->post('tags', []);
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }
        
        $tagModel = new Tag();
        $tagModel->syncNewsTagsByNames($newsId, $tags);
        
        // Okuma süresini hesapla
        $newsModel->updateReadingTime($newsId, $data['content']);
        
        $this->json(['success' => true, 'message' => 'Haber başarıyla kaydedildi.', 'news_id' => $newsId]);
    }
    
    /**
     * Haber silme
     */
    public function deleteNews($id) {
        $newsModel = new News();
        
        if ($newsModel->delete($id)) {
            $_SESSION['success'] = 'Haber başarıyla silindi.';
        } else {
            $_SESSION['error'] = 'Haber silinirken hata oluştu.';
        }
        
        $this->redirect('/admin/haberler');
    }
    
    /**
     * Kategoriler
     */
    public function categories() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllForAdmin();
        
        $view = new View();
        $view->render('admin/categories/index', [
            'pageTitle' => 'Kategori Yönetimi - Admin',
            'categories' => $categories,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Kategori ekleme/düzenleme formu (API)
     */
    public function getCategoryById($id) {
        $category = $this->db->fetch("
            SELECT * FROM categories WHERE id = :id
        ", ['id' => $id]);
        
        if ($category) {
            $this->json(['success' => true, 'category' => $category]);
        } else {
            $this->json(['error' => 'Kategori bulunamadı'], 404);
        }
    }
    
    /**
     * Kategori kaydetme (ekleme/düzenleme)
     */
    public function saveCategory() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $data = [
            'name' => trim($this->post('name')),
            'slug' => trim($this->post('slug')),
            'description' => trim($this->post('description')),
            'color' => $this->post('color', '#007bff'),
            'icon' => trim($this->post('icon')),
            'parent_id' => $this->post('parent_id') ?: null,
            'sort_order' => (int)$this->post('sort_order', 0),
            'is_active' => (bool)$this->post('is_active'),
            'show_in_menu' => (bool)$this->post('show_in_menu'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Validasyon
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Kategori adı gereklidir';
        }
        
        if (strlen($data['name']) > 100) {
            $errors['name'] = 'Kategori adı 100 karakterden fazla olamaz';
        }
        
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }
        
        // Slug oluştur
        if (empty($data['slug'])) {
            $data['slug'] = createSlug($data['name']);
        }
        
        $categoryId = $this->post('id');
        
        // Slug benzersizliği kontrol et
        $existingSlug = $this->db->fetch("
            SELECT id FROM categories WHERE slug = :slug" . ($categoryId ? " AND id != :exclude_id" : ""),
            $categoryId ? ['slug' => $data['slug'], 'exclude_id' => $categoryId] : ['slug' => $data['slug']]
        );
        
        if ($existingSlug) {
            $this->json(['error' => 'Bu slug zaten kullanılıyor'], 400);
            return;
        }
        
        if ($categoryId) {
            // Güncelleme
            $result = $this->db->update('categories', $data, 'id = :id', ['id' => $categoryId]);
            $message = 'Kategori başarıyla güncellendi';
        } else {
            // Yeni ekleme
            $data['created_at'] = date('Y-m-d H:i:s');
            $categoryId = $this->db->insert('categories', $data);
            $result = $categoryId !== false;
            $message = 'Kategori başarıyla eklendi';
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => $message, 'category_id' => $categoryId]);
        } else {
            $this->json(['error' => 'Kategori kaydedilirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kategori durumunu değiştir
     */
    public function toggleCategoryStatus($id) {
        $status = (bool)$this->post('is_active');
        
        $result = $this->db->update('categories', [
            'is_active' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Kategori durumu güncellendi']);
        } else {
            $this->json(['error' => 'Durum güncellenirken hata oluştu'], 500);
        }
    }

    /**
     * Kategori menü görünürlüğünü değiştir
     */
    public function toggleCategoryMenu($id) {
        $show = (bool)$this->post('show_in_menu');
        
        $result = $this->db->update('categories', [
            'show_in_menu' => $show ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Menü görünürlüğü güncellendi']);
        } else {
            $this->json(['error' => 'Menü görünürlüğü güncellenirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kategori sıralamasını güncelle
     */
    public function updateCategoryOrder($id) {
        $sortOrder = (int)$this->post('sort_order');
        
        $result = $this->db->update('categories', [
            'sort_order' => $sortOrder,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Sıralama güncellendi']);
        } else {
            $this->json(['error' => 'Sıralama güncellenirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kategori silme
     */
    public function deleteCategory($id) {
        // CSRF kontrolü (DELETE istekleri için)
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        // Kategoriye ait haber var mı kontrol et
        $newsCount = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news WHERE category_id = :id
        ", ['id' => $id]);
        
        if ($newsCount > 0) {
            $this->json(['error' => 'Bu kategoriye ait haberler bulunuyor. Önce haberleri başka kategoriye taşıyın'], 400);
            return;
        }
        
        // Alt kategoriler var mı kontrol et
        $childCount = $this->db->fetchColumn("
            SELECT COUNT(*) FROM categories WHERE parent_id = :id
        ", ['id' => $id]);
        
        if ($childCount > 0) {
            $this->json(['error' => 'Bu kategorinin alt kategorileri var. Önce onları silin veya başka kategoriye taşıyın'], 400);
            return;
        }
        
        $result = $this->db->delete('categories', 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Kategori başarıyla silindi']);
        } else {
            $this->json(['error' => 'Kategori silinirken hata oluştu'], 500);
        }
    }

    /**
     * Kullanıcı yönetimi
     */
    public function users() {
        $users = $this->db->fetchAll("
            SELECT * FROM admin_users 
            ORDER BY created_at DESC
        ");
        
        $view = new View();
        $view->render('admin/users/index', [
            'pageTitle' => 'Kullanıcı Yönetimi - Admin',
            'users' => $users,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Kullanıcı bilgilerini getir (API)
     */
    public function getUserById($id) {
        $user = $this->db->fetch("
            SELECT * FROM admin_users WHERE id = :id
        ", ['id' => $id]);
        
        if ($user) {
            // Şifreyi gizle
            unset($user['password']);
            $this->json(['success' => true, 'user' => $user]);
        } else {
            $this->json(['error' => 'Kullanıcı bulunamadı'], 404);
        }
    }
    
    /**
     * Kullanıcı kaydetme (ekleme/düzenleme)
     */
    public function saveUser() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $data = [
            'full_name' => trim($this->post('full_name')),
            'username' => trim($this->post('username')),
            'email' => trim($this->post('email')),
            'role' => $this->post('role'),
            'avatar' => trim($this->post('avatar')),
            'is_active' => (bool)$this->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = $this->post('id');
        $password = $this->post('password');
        
        // Validasyon
        $errors = [];
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Ad Soyad gereklidir';
        }
        
        if (empty($data['username'])) {
            $errors['username'] = 'Kullanıcı adı gereklidir';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Kullanıcı adında sadece harf, rakam ve alt çizgi olabilir';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'E-posta gereklidir';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geçerli bir e-posta adresi girin';
        }
        
        if (empty($data['role'])) {
            $errors['role'] = 'Rol seçimi gereklidir';
        } elseif (!in_array($data['role'], ['admin', 'editor', 'author'])) {
            $errors['role'] = 'Geçersiz rol';
        }
        
        // Yeni kullanıcı için şifre zorunlu
        if (!$userId && empty($password)) {
            $errors['password'] = 'Şifre gereklidir';
        } elseif (!empty($password) && strlen($password) < 6) {
            $errors['password'] = 'Şifre en az 6 karakter olmalıdır';
        }
        
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }
        
        // Username benzersizliği kontrol et
        $existingUser = $this->db->fetch("
            SELECT id FROM admin_users WHERE username = :username" . ($userId ? " AND id != :exclude_id" : ""),
            $userId ? ['username' => $data['username'], 'exclude_id' => $userId] : ['username' => $data['username']]
        );
        
        if ($existingUser) {
            $this->json(['error' => 'Bu kullanıcı adı zaten kullanılıyor'], 400);
            return;
        }
        
        // Email benzersizliği kontrol et
        $existingEmail = $this->db->fetch("
            SELECT id FROM admin_users WHERE email = :email" . ($userId ? " AND id != :exclude_id" : ""),
            $userId ? ['email' => $data['email'], 'exclude_id' => $userId] : ['email' => $data['email']]
        );
        
        if ($existingEmail) {
            $this->json(['error' => 'Bu e-posta adresi zaten kullanılıyor'], 400);
            return;
        }
        
        // Şifre varsa hashle
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if ($userId) {
            // Güncelleme - şifre yoksa password alanını çıkar
            if (empty($password)) {
                unset($data['password']);
            }
            
            // Kendi kendini deaktif etmesini engelle
            if ($userId == ($_SESSION['admin_user']['id'] ?? 0) && !$data['is_active']) {
                $this->json(['error' => 'Kendi hesabınızı deaktif edemezsiniz'], 400);
                return;
            }
            
            $result = $this->db->update('admin_users', $data, 'id = :id', ['id' => $userId]);
            $message = 'Kullanıcı başarıyla güncellendi';
        } else {
            // Yeni ekleme
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['login_count'] = 0;
            $userId = $this->db->insert('admin_users', $data);
            $result = $userId !== false;
            $message = 'Kullanıcı başarıyla eklendi';
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => $message, 'user_id' => $userId]);
        } else {
            $this->json(['error' => 'Kullanıcı kaydedilirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kullanıcı durumunu değiştir
     */
    public function toggleUserStatus($id) {
        // Kendi kendini deaktif etmesini engelle
        if ($id == ($_SESSION['admin_user']['id'] ?? 0)) {
            $this->json(['error' => 'Kendi hesabınızı deaktif edemezsiniz'], 400);
            return;
        }
        
        $status = (bool)$this->post('is_active');
        
        $result = $this->db->update('admin_users', [
            'is_active' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Kullanıcı durumu güncellendi']);
        } else {
            $this->json(['error' => 'Durum güncellenirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kullanıcı silme
     */
    public function deleteUserById($id) {
        // CSRF kontrolü (DELETE istekleri için)
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        // Kendi kendini silmesini engelle
        if ($id == ($_SESSION['admin_user']['id'] ?? 0)) {
            $this->json(['error' => 'Kendi hesabınızı silemezsiniz'], 400);
            return;
        }
        
        $result = $this->db->delete('admin_users', 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Kullanıcı başarıyla silindi']);
        } else {
            $this->json(['error' => 'Kullanıcı silinirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kullanıcı istatistikleri
     */
    public function getUserStats($id) {
        $stats = [];
        
        // Kullanıcının haber istatistikleri
        $stats['total_news'] = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news WHERE author_name = (
                SELECT full_name FROM admin_users WHERE id = :id
            )
        ", ['id' => $id]);
        
        $stats['published_news'] = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news WHERE author_name = (
                SELECT full_name FROM admin_users WHERE id = :id
            ) AND status = 'published'
        ", ['id' => $id]);
        
        $stats['draft_news'] = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news WHERE author_name = (
                SELECT full_name FROM admin_users WHERE id = :id
            ) AND status = 'draft'
        ", ['id' => $id]);
        
        $this->json(['success' => true, 'stats' => $stats]);
    }
    
    /**
     * Reklam alanları
     */
    public function adZones() {
        $adZones = $this->db->fetchAll("
            SELECT * FROM ad_zones 
            ORDER BY zone_name ASC
        ");
        
        $view = new View();
        $view->render('admin/ads/index', [
            'pageTitle' => 'Reklam Alanları - Admin',
            'adZones' => $adZones,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Reklam alanı bilgilerini getir (API)
     */
    public function getAdZoneById($id) {
        $zone = $this->db->fetch("
            SELECT * FROM ad_zones WHERE id = :id
        ", ['id' => $id]);
        
        if ($zone) {
            $this->json(['success' => true, 'zone' => $zone]);
        } else {
            $this->json(['error' => 'Reklam alanı bulunamadı'], 404);
        }
    }
    
    /**
     * Reklam alanı kaydetme (ekleme/düzenleme)
     */
    public function saveAdZone() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $data = [
            'zone_name' => trim($this->post('zone_name')),
            'zone_description' => trim($this->post('zone_description')),
            'ad_type' => $this->post('ad_type'),
            'ad_code' => trim($this->post('ad_code')),
            'position' => $this->post('position'),
            'width' => $this->post('width') ? (int)$this->post('width') : null,
            'height' => $this->post('height') ? (int)$this->post('height') : null,
            'is_responsive' => (bool)$this->post('is_responsive'),
            'is_active' => (bool)$this->post('is_active'),
            'display_rules' => $this->post('display_rules'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $zoneId = $this->post('id');
        
        // Validasyon
        $errors = [];
        if (empty($data['zone_name'])) {
            $errors['zone_name'] = 'Alan adı gereklidir';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $data['zone_name'])) {
            $errors['zone_name'] = 'Alan adında sadece harf, rakam, alt çizgi ve tire olabilir';
        }
        
        if (empty($data['ad_type'])) {
            $errors['ad_type'] = 'Reklam türü seçimi gereklidir';
        } elseif (!in_array($data['ad_type'], ['adsense', 'custom', 'banner'])) {
            $errors['ad_type'] = 'Geçersiz reklam türü';
        }
        
        if (empty($data['ad_code'])) {
            $errors['ad_code'] = 'Reklam kodu gereklidir';
        }
        
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }
        
        // Zone name benzersizliği kontrol et
        $existingZone = $this->db->fetch("
            SELECT id FROM ad_zones WHERE zone_name = :zone_name" . ($zoneId ? " AND id != :exclude_id" : ""),
            $zoneId ? ['zone_name' => $data['zone_name'], 'exclude_id' => $zoneId] : ['zone_name' => $data['zone_name']]
        );
        
        if ($existingZone) {
            $this->json(['error' => 'Bu alan adı zaten kullanılıyor'], 400);
            return;
        }
        
        if ($zoneId) {
            // Güncelleme
            $result = $this->db->update('ad_zones', $data, 'id = :id', ['id' => $zoneId]);
            $message = 'Reklam alanı başarıyla güncellendi';
        } else {
            // Yeni ekleme
            $data['created_at'] = date('Y-m-d H:i:s');
            $zoneId = $this->db->insert('ad_zones', $data);
            $result = $zoneId !== false;
            $message = 'Reklam alanı başarıyla eklendi';
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => $message, 'zone_id' => $zoneId]);
        } else {
            $this->json(['error' => 'Reklam alanı kaydedilirken hata oluştu'], 500);
        }
    }
    
    /**
     * Reklam alanı durumunu değiştir
     */
    public function toggleAdZoneStatus($id) {
        $status = (bool)$this->post('is_active');
        
        $result = $this->db->update('ad_zones', [
            'is_active' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Reklam alanı durumu güncellendi']);
        } else {
            $this->json(['error' => 'Durum güncellenirken hata oluştu'], 500);
        }
    }
    
    /**
     * Reklam alanı silme
     */
    public function deleteAdZone($id) {
        // CSRF kontrolü (DELETE istekleri için)
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $result = $this->db->delete('ad_zones', 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Reklam alanı başarıyla silindi']);
        } else {
            $this->json(['error' => 'Reklam alanı silinirken hata oluştu'], 500);
        }
    }
    
    /**
     * Reklam alanlarını test et
     */
    public function testAdZones() {
        $zones = $this->db->fetchAll("SELECT * FROM ad_zones WHERE is_active = 1");
        $results = [];
        
        foreach ($zones as $zone) {
            $results[] = [
                'zone_name' => $zone['zone_name'],
                'ad_type' => $zone['ad_type'],
                'status' => 'working', // Basic test - can be enhanced
                'last_checked' => date('Y-m-d H:i:s')
            ];
        }
        
        $this->json(['success' => true, 'results' => $results]);
    }
    
    /**
     * Etiket yönetimi
     */
    public function tags() {
        $page = (int)$this->get('page', 1);
        $search = $this->get('search', '');
        $sort = $this->get('sort', 'name');
        $limit = (int)$this->get('limit', 50);
        
        $tagModel = new Tag();
        
        // Filtreleme için SQL oluştur
        $where = [];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(t.name LIKE :search OR t.description LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Sıralama
        $orderBy = match($sort) {
            'usage' => 'usage_count DESC, t.name ASC',
            'recent' => 't.created_at DESC',
            default => 't.name ASC'
        };
        
        // Etiketler
        $offset = ($page - 1) * $limit;
        
        $tags = $this->db->fetchAll("
            SELECT t.*, 
                   COUNT(DISTINCT nt.news_id) as usage_count,
                   COUNT(DISTINCT n.id) as news_count
            FROM tags t
            LEFT JOIN news_tags nt ON t.id = nt.tag_id
            LEFT JOIN news n ON nt.news_id = n.id AND n.status = 'published'
            {$whereClause}
            GROUP BY t.id
            ORDER BY {$orderBy}
            LIMIT {$offset}, {$limit}
        ", $params);
        
        // Toplam sayı
        $totalCount = $this->db->fetchColumn("
            SELECT COUNT(DISTINCT t.id) FROM tags t
            LEFT JOIN news_tags nt ON t.id = nt.tag_id
            {$whereClause}
        ", $params);
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $limit,
            'total_count' => $totalCount,
            'total_pages' => ceil($totalCount / $limit)
        ];
        
        $view = new View();
        $view->render('admin/tags/index', [
            'pageTitle' => 'Etiket Yönetimi - Admin',
            'tags' => $tags,
            'pagination' => $pagination,
            'currentFilters' => [
                'search' => $search,
                'sort' => $sort,
                'limit' => $limit,
                'page' => $page
            ],
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Etiket bilgilerini getir (API)
     */
    public function getTagById($id) {
        $tag = $this->db->fetch("
            SELECT * FROM tags WHERE id = :id
        ", ['id' => $id]);
        
        if ($tag) {
            $this->json(['success' => true, 'tag' => $tag]);
        } else {
            $this->json(['error' => 'Etiket bulunamadı'], 404);
        }
    }
    
    /**
     * Etiket kaydetme (ekleme/düzenleme)
     */
    public function saveTag() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        $data = [
            'name' => trim($this->post('name')),
            'slug' => trim($this->post('slug')),
            'description' => trim($this->post('description')),
            'color' => $this->post('color', '#6c757d'),
            'is_active' => (bool)$this->post('is_active'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $tagId = $this->post('id');
        
        // Validasyon
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Etiket adı gereklidir';
        }
        
        if (strlen($data['name']) > 50) {
            $errors['name'] = 'Etiket adı 50 karakterden fazla olamaz';
        }
        
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }
        
        // Slug oluştur
        if (empty($data['slug'])) {
            $data['slug'] = createSlug($data['name']);
        }
        
        // Slug benzersizliği kontrol et
        $existingSlug = $this->db->fetch("
            SELECT id FROM tags WHERE slug = :slug" . ($tagId ? " AND id != :exclude_id" : ""),
            $tagId ? ['slug' => $data['slug'], 'exclude_id' => $tagId] : ['slug' => $data['slug']]
        );
        
        if ($existingSlug) {
            $this->json(['error' => 'Bu slug zaten kullanılıyor'], 400);
            return;
        }
        
        // Name benzersizliği kontrol et
        $existingName = $this->db->fetch("
            SELECT id FROM tags WHERE name = :name" . ($tagId ? " AND id != :exclude_id" : ""),
            $tagId ? ['name' => $data['name'], 'exclude_id' => $tagId] : ['name' => $data['name']]
        );
        
        if ($existingName) {
            $this->json(['error' => 'Bu etiket adı zaten kullanılıyor'], 400);
            return;
        }
        
        if ($tagId) {
            // Güncelleme
            $result = $this->db->update('tags', $data, 'id = :id', ['id' => $tagId]);
            $message = 'Etiket başarıyla güncellendi';
        } else {
            // Yeni ekleme
            $data['created_at'] = date('Y-m-d H:i:s');
            $tagId = $this->db->insert('tags', $data);
            $result = $tagId !== false;
            $message = 'Etiket başarıyla eklendi';
        }
        
        if ($result) {
            $this->json(['success' => true, 'message' => $message, 'tag_id' => $tagId]);
        } else {
            $this->json(['error' => 'Etiket kaydedilirken hata oluştu'], 500);
        }
    }
    
    /**
     * Etiket silme
     */
    public function deleteTag($id) {
        // CSRF kontrolü (DELETE istekleri için)
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        // Önce etiket-haber ilişkilerini sil
        $this->db->delete('news_tags', 'tag_id = :tag_id', ['tag_id' => $id]);
        
        // Sonra etiketi sil
        $result = $this->db->delete('tags', 'id = :id', ['id' => $id]);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Etiket başarıyla silindi']);
        } else {
            $this->json(['error' => 'Etiket silinirken hata oluştu'], 500);
        }
    }
    
    /**
     * Kullanılmayan etiketleri temizle
     */
    public function cleanUnusedTags() {
        // CSRF kontrolü (DELETE istekleri için)
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        // Kullanılmayan etiketleri bul
        $unusedTags = $this->db->fetchAll("
            SELECT t.id, t.name 
            FROM tags t
            LEFT JOIN news_tags nt ON t.id = nt.tag_id
            WHERE nt.tag_id IS NULL
        ");
        
        $deletedCount = 0;
        foreach ($unusedTags as $tag) {
            if ($this->db->delete('tags', 'id = :id', ['id' => $tag['id']])) {
                $deletedCount++;
            }
        }
        
        $this->json([
            'success' => true, 
            'message' => "{$deletedCount} kullanılmayan etiket temizlendi",
            'deleted_count' => $deletedCount
        ]);
    }
    
    /**
     * Site ayarları
     */
    public function settings() {
        // Mevcut ayarları getir
        $settings = $this->getSettings();
        
        $view = new View();
        $view->render('admin/settings/index', [
            'pageTitle' => 'Site Ayarları - Admin',
            'settings' => $settings,
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * Ayarları kaydet
     */
    public function saveSettings() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }

        // İstekten gelen verileri al (JSON/form desteği)
        $requestData = $this->post();
        if (!is_array($requestData)) {
            $requestData = [];
        }
        unset($requestData['csrf_token']);

        // İzin verilen anahtarlar (beyaz liste)
        $allowedKeys = [
            'site_name', 'site_description', 'site_url', 'admin_email',
            'timezone', 'language', 'theme_color', 'news_per_page',
            'enable_dark_mode', 'show_author',
            'meta_title', 'meta_description', 'meta_keywords',
            'google_analytics_id', 'google_search_console', 'auto_sitemap',
            'facebook_url', 'twitter_url', 'instagram_url', 'youtube_url',
            'enable_social_sharing',
            'google_adsense_id', 'auto_ads', 'ad_blocker_detection', 'enable_ads', 'lazy_load_ads',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption',
            'enable_cache', 'cache_duration', 'debug_mode',
            'max_login_attempts', 'enable_csrf', 'enable_rate_limit',
            // İletişim bilgileri
            'contact_email', 'contact_email_editor', 'contact_phone', 'contact_address',
            'contact_twitter_handle', 'contact_facebook_page', 'contact_instagram_handle', 'contact_linkedin_page'
        ];

        // Sadece gönderilen (var olan) alanları kaydet
        $settingsData = [];
        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $requestData)) {
                $settingsData[$key] = $requestData[$key];
            }
        }
        
        $savedCount = 0;
        $criticalChanges = false;
        
        foreach ($settingsData as $key => $value) {
            if ($this->saveSetting($key, $value)) {
                $savedCount++;
                
                // Critical settings that require restart
                if (in_array($key, ['debug_mode', 'enable_cache', 'timezone'])) {
                    $criticalChanges = true;
                }
            }
        }
        
        $this->json([
            'success' => true, 
            'message' => "{$savedCount} ayar kaydedildi",
            'restart_recommended' => $criticalChanges
        ]);
    }
    
    /**
     * Ayarları varsayılanlara sıfırla
     */
    public function resetSettings() {
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }
        
        // Tüm ayarları sil (varsayılan değerler kullanılacak)
        $result = $this->db->exec("DELETE FROM site_settings");
        
        if ($result !== false) {
            $this->json(['success' => true, 'message' => 'Ayarlar varsayılanlara sıfırlandı']);
        } else {
            $this->json(['error' => 'Ayarlar sıfırlanırken hata oluştu'], 500);
        }
    }
    
    /**
     * Tüm ayarları getir
     */
    private function getSettings() {
        // Tüm ayarları veritabanından getir ve key=>value haritası olarak döndür
        $rows = $this->db->fetchAll("SELECT setting_key, setting_value FROM site_settings", []);
        $settings = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (isset($row['setting_key'])) {
                    $settings[$row['setting_key']] = $row['setting_value'] ?? null;
                }
            }
        }
        return $settings;
    }
    
    /**
     * Tek bir ayarı kaydet
     */
    private function saveSetting($key, $value) {
        // Boolean değerleri 1/0 çevir
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }
        
        // Mevcut ayar var mı kontrol et
        $existing = $this->db->fetch("
            SELECT setting_value FROM site_settings WHERE setting_key = :key
        ", ['key' => $key]);
        
        if ($existing) {
            // Güncelle
            return $this->db->update('site_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'setting_key = :key', ['key' => $key]);
        } else {
            // Yeni kayıt
            return $this->db->insert('site_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Gelir raporları
     */
    public function revenueReports() {
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-d'));
        $reportType = $this->get('report_type', 'daily');
        
        $revenue = $this->getRevenueData($startDate, $endDate, $reportType);
        
        $view = new View();
        $view->render('admin/revenue/index', [
            'pageTitle' => 'Gelir Raporları - Admin',
            'revenue' => $revenue,
            'currentFilters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'report_type' => $reportType
            ]
        ], 'admin');
    }
    
    /**
     * Gelir verilerini yenile
     */
    public function refreshRevenueData() {
        // Bu metod AdSense API'si entegrasyonu ile gerçek veri çekebilir
        // Şu an için mock veri döndürüyoruz
        
        $this->json([
            'success' => true,
            'message' => 'Gelir verileri yenilendi',
            'last_updated' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Gelir raporu dışa aktarma
     */
    public function exportRevenueReport() {
        $startDate = $this->get('start_date', date('Y-m-01'));
        $endDate = $this->get('end_date', date('Y-m-d'));
        $reportType = $this->get('report_type', 'daily');
        
        $revenue = $this->getRevenueData($startDate, $endDate, $reportType);
        
        // CSV header
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="gelir_raporu_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV başlıkları
        fputcsv($output, [
            'Tarih',
            'Gelir ($)',
            'Tıklama',
            'Gösterim',
            'CTR (%)',
            'RPM ($)'
        ]);
        
        // Veri satırları
        foreach ($revenue['chart_data'] as $data) {
            fputcsv($output, [
                $data['date'],
                number_format($data['revenue'], 2),
                $data['clicks'],
                $data['impressions'],
                number_format($data['ctr'], 2),
                number_format($data['rpm'], 2)
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Gelir verilerini getir
     */
    private function getRevenueData($startDate, $endDate, $reportType) {
        // Mock data - gerçek AdSense API entegrasyonu ile değiştirilebilir
        $revenue = [
            'total_revenue' => 145.67,
            'revenue_change' => 12.5,
            'total_clicks' => 1234,
            'total_impressions' => 45678,
            'ctr' => 2.70,
            'rpm' => 3.19,
            'top_zone' => 'Header Banner',
            'active_zones' => []
        ];
        
        // Chart data oluştur
        $chartData = [];
        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime($startDate . ' +' . $i . ' days'));
            $chartData[] = [
                'date' => $date,
                'revenue' => rand(50, 200) / 10,
                'clicks' => rand(20, 80),
                'impressions' => rand(800, 2000),
                'ctr' => rand(150, 350) / 100,
                'rpm' => rand(200, 400) / 100
            ];
        }
        
        $revenue['chart_data'] = $chartData;
        
        // Reklam alanı performansı
        $adZones = $this->db->fetchAll("
            SELECT zone_name, ad_type, position 
            FROM ad_zones 
            WHERE is_active = 1
        ");
        
        $zonePerformance = [];
        foreach ($adZones as $zone) {
            $zonePerformance[] = [
                'zone_name' => $zone['zone_name'],
                'ad_type' => $zone['ad_type'],
                'position' => $zone['position'],
                'revenue' => rand(1000, 5000) / 100,
                'clicks' => rand(10, 50),
                'impressions' => rand(500, 1500),
                'ctr' => rand(150, 400) / 100,
                'rpm' => rand(200, 500) / 100,
                'color' => '#' . substr(md5($zone['zone_name']), 0, 6)
            ];
        }
        
        $revenue['zone_performance'] = $zonePerformance;
        
        // En çok gelir getiren sayfalar
        $topPages = [
            [
                'title' => 'Ana Sayfa',
                'url' => SITE_URL,
                'page_views' => 12543,
                'revenue' => 45.67,
                'rpm' => 3.64
            ],
            [
                'title' => 'En Popüler Haber',
                'url' => SITE_URL . '/haber/en-popular-haber',
                'page_views' => 8932,
                'revenue' => 32.15,
                'rpm' => 3.60
            ]
        ];
        
        $revenue['top_pages'] = $topPages;
        
        return $revenue;
    }
    
    /**
     * İstatistikler
     */
    public function statistics() {
        // Genel istatistikler
        $stats = $this->getDetailedStats();
        
        $view = new View();
        $view->render('admin/statistics/index', [
            'pageTitle' => 'İstatistikler - Admin',
            'stats' => $stats
        ], 'admin');
    }

    /**
     * Profil sayfası - mevcut admin kullanıcının bilgileri
     */
    public function profile() {
        // Mevcut kullanıcı
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->redirect('/admin/login');
            return;
        }

        // Veritabanından güncel bilgileri çek
        $user = $this->db->fetch("SELECT id, full_name, username, email, avatar, role, last_login, login_count, created_at, updated_at FROM admin_users WHERE id = :id", [
            'id' => $currentUser['id']
        ]);

        $view = new View();
        $view->render('admin/profile/index', [
            'pageTitle' => 'Profilim - Admin',
            'user' => $user,
            'csrfToken' => $this->generateCsrfToken(),
            'breadcrumb' => [
                [ 'title' => 'Profil', 'url' => '' ]
            ],
            'currentUser' => $currentUser
        ], 'admin');
    }

    /**
     * Profil bilgilerini kaydet (full_name, email, avatar)
     */
    public function saveProfile() {
        // CSRF doğrulama
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }

        // Giriş yapmış kullanıcıyı al
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->json(['error' => 'Oturum bulunamadı'], 401);
            return;
        }

        $data = [
            'full_name' => trim($this->post('full_name')),
            'email' => trim($this->post('email')),
            'avatar' => trim($this->post('avatar')),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Validasyon
        $errors = [];
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Ad Soyad gereklidir';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'E-posta gereklidir';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geçerli bir e-posta adresi girin';
        }

        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }

        // Email benzersizliği (başkası kullanıyor mu?)
        $existingEmail = $this->db->fetch(
            "SELECT id FROM admin_users WHERE email = :email AND id != :id",
            ['email' => $data['email'], 'id' => $currentUser['id']]
        );
        if ($existingEmail) {
            $this->json(['error' => 'Bu e-posta adresi zaten kullanılıyor'], 400);
            return;
        }

        $result = $this->db->update('admin_users', $data, 'id = :id', ['id' => $currentUser['id']]);

        if ($result) {
            // Session bilgisini güncelle
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_user']['full_name'] = $data['full_name'];
            // Profil resmi değiştiyse opsiyonel olarak saklayabiliriz
            $this->json(['success' => true, 'message' => 'Profil bilgileriniz güncellendi']);
        } else {
            $this->json(['error' => 'Profil güncellenirken hata oluştu'], 500);
        }
    }

    /**
     * Şifre değiştir
     */
    public function changePassword() {
        // CSRF doğrulama
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }

        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->json(['error' => 'Oturum bulunamadı'], 401);
            return;
        }

        $currentPassword = (string)$this->post('current_password');
        $newPassword = (string)$this->post('new_password');
        $confirmPassword = (string)$this->post('confirm_password');

        // Validasyon
        $errors = [];
        if (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Yeni şifre en az 6 karakter olmalıdır';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Şifreler eşleşmiyor';
        }
        if (!empty($errors)) {
            $this->json(['error' => 'Validasyon hatası', 'errors' => $errors], 400);
            return;
        }

        // Kullanıcının mevcut hash'ini al
        $user = $this->db->fetch("SELECT id, password FROM admin_users WHERE id = :id", ['id' => $currentUser['id']]);
        if (!$user || empty($user['password'])) {
            $this->json(['error' => 'Kullanıcı bulunamadı'], 404);
            return;
        }

        // Mevcut şifre doğrulaması
        if (!password_verify($currentPassword, $user['password'])) {
            $this->json(['error' => 'Mevcut şifre hatalı'], 400);
            return;
        }

        // Yeni şifre hashle ve kaydet
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $saved = $this->db->update('admin_users', [
            'password' => $hashed,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $currentUser['id']]);

        if ($saved) {
            $this->json(['success' => true, 'message' => 'Şifreniz güncellendi']);
        } else {
            $this->json(['error' => 'Şifre güncellenemedi'], 500);
        }
    }
    
    /**
     * Dashboard istatistikleri
     */
    private function getDashboardStats() {
        $stats = [];
        
        // Toplam haber sayısı
        $stats['total_news'] = $this->db->fetchColumn("SELECT COUNT(*) FROM news");
        $stats['published_news'] = $this->db->fetchColumn("SELECT COUNT(*) FROM news WHERE status = 'published'");
        $stats['draft_news'] = $this->db->fetchColumn("SELECT COUNT(*) FROM news WHERE status = 'draft'");
        
        // Kategoriler
        $stats['total_categories'] = $this->db->fetchColumn("SELECT COUNT(*) FROM categories");
        
        // Kullanıcılar
        $stats['total_users'] = $this->db->fetchColumn("SELECT COUNT(*) FROM admin_users");
        
        // Bu ay eklenen haberler
        $stats['news_this_month'] = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news 
            WHERE MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE())
        ");
        
        // Toplam görüntülenme
        $stats['total_views'] = $this->db->fetchColumn("SELECT SUM(view_count) FROM news") ?: 0;
        
        // Bugünkü görüntülenmeler
        $stats['today_views'] = $this->db->fetchColumn("
            SELECT COUNT(*) FROM news_views 
            WHERE view_date = CURDATE()
        ") ?: 0;
        
        return $stats;
    }
    
    /**
     * Detaylı istatistikler
     */
    private function getDetailedStats() {
        $stats = $this->getDashboardStats();
        
        // Son 30 günün haber sayıları
        $stats['daily_news'] = $this->db->fetchAll("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM news
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        // Kategori bazında haber sayıları
        $stats['news_by_category'] = $this->db->fetchAll("
            SELECT c.name, c.color, COUNT(n.id) as count
            FROM categories c
            LEFT JOIN news n ON c.id = n.category_id
            GROUP BY c.id
            ORDER BY count DESC
        ");
        
        // En çok okunan haberler
        $stats['most_viewed'] = $this->db->fetchAll("
            SELECT title, slug, view_count
            FROM news
            WHERE status = 'published'
            ORDER BY view_count DESC
            LIMIT 10
        ");
        
        // Son 7 günün görüntülenme istatistikleri
        $stats['daily_views'] = $this->db->fetchAll("
            SELECT view_date as date, COUNT(*) as views
            FROM news_views
            WHERE view_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY view_date
            ORDER BY view_date ASC
        ");
        
        return $stats;
    }
    
    /**
     * Benzersiz slug oluştur
     */
    private function createUniqueSlug($title, $excludeId = null) {
        $baseSlug = createSlug($title);
        $slug = $baseSlug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM news WHERE slug = :slug";
            $params = ['slug' => $slug];
            
            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }
            
            $existing = $this->db->fetch($sql, $params);
            
            if (!$existing) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Mevcut kullanıcı bilgilerini al
     */
    private function getCurrentUser() {
        // Session'ı başlat (zaten başlamışsa devam eder)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['admin_user'] ?? null;
    }
    
    /**
     * File upload işlemi
     */
    public function uploadFile() {
        if (!isset($_FILES['file'])) {
            $this->json(['error' => 'Dosya seçilmedi'], 400);
            return;
        }
        
        $file = $_FILES['file'];
        
        // Dosya validasyonu
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['error' => 'Dosya yükleme hatası'], 400);
            return;
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            $this->json(['error' => 'Dosya çok büyük'], 400);
            return;
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            $this->json(['error' => 'Geçersiz dosya türü'], 400);
            return;
        }
        
        // Dosya adı oluştur
        $fileName = date('Y/m/') . uniqid() . '.' . $extension;
        $uploadDir = UPLOAD_PATH . dirname($fileName);
        
        // Dizin oluştur
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = UPLOAD_PATH . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // URL için assets/uploads/ prefix kullan
            $urlPath = 'assets/uploads/' . $fileName;
            $this->json([
                'success' => true,
                'url' => url($urlPath),
                'filename' => $fileName
            ]);
        } else {
            $this->json(['error' => 'Dosya yüklenemedi'], 500);
        }
    }
    
    /**
     * Create full year/month/day folder structure under uploads
     * Example: assets/uploads/2025/10/19
     */
    public function createDateFolders() {
        // CSRF check
        if (!$this->verifyCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token hatalı'], 400);
            return;
        }

        // Read input
        $yearInput = $this->post('year');
        $baseSubdirInput = (string)$this->post('base_subdir', '');

        // Validate year
        if (!is_numeric($yearInput)) {
            $this->json(['error' => 'Yıl geçersiz'], 422);
            return;
        }
        $year = (int)$yearInput;
        if ($year < 1970 || $year > 2100) {
            $this->json(['error' => 'Yıl aralığı 1970-2100 olmalıdır'], 422);
            return;
        }

        // Sanitize base subdir (optional). Keep it inside UPLOAD_PATH.
        $baseSubdir = str_replace('\\', '/', trim($baseSubdirInput));
        // Collapse multiple slashes
        $baseSubdir = preg_replace('#/{2,}#', '/', $baseSubdir);
        // Disallow traversal
        if (strpos($baseSubdir, '..') !== false) {
            $this->json(['error' => 'Geçersiz alt klasör yolu'], 422);
            return;
        }
        // Trim leading/trailing slashes
        $baseSubdir = trim($baseSubdir, '/');

        // Build base directory under uploads
        $uploadsRoot = rtrim(UPLOAD_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $baseDir = $uploadsRoot;
        if ($baseSubdir !== '') {
            $baseDir .= str_replace('/', DIRECTORY_SEPARATOR, $baseSubdir) . DIRECTORY_SEPARATOR;
        }

        // Ensure base directory exists (create if missing)
        if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true)) {
            $this->json(['error' => 'Temel klasör oluşturulamadı'], 500);
            return;
        }

        // Create structure: {year}/{month}/{day}
        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        for ($month = 1; $month <= 12; $month++) {
            $monthStr = str_pad((string)$month, 2, '0', STR_PAD_LEFT);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayStr = str_pad((string)$day, 2, '0', STR_PAD_LEFT);
                $targetDir = $baseDir . $year . DIRECTORY_SEPARATOR . $monthStr . DIRECTORY_SEPARATOR . $dayStr;

                if (is_dir($targetDir)) {
                    $skippedCount++;
                    continue;
                }

                if (@mkdir($targetDir, 0755, true)) {
                    $createdCount++;
                } else {
                    // If another process created it in between
                    if (is_dir($targetDir)) {
                        $skippedCount++;
                    } else {
                        $errorCount++;
                    }
                }
            }
        }

        $relativeBase = str_replace(rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $baseDir);

        $this->json([
            'success' => true,
            'message' => "{$year} yılı için klasör oluşturma tamamlandı",
            'year' => $year,
            'base' => $relativeBase,
            'created_count' => $createdCount,
            'skipped_count' => $skippedCount,
            'error_count' => $errorCount
        ]);
    }
    
    /**
     * Mevcut route'u al (Router ile tutarlı şekilde)
     */
    private function getCurrentRoute() {
        // CLI ortamında REQUEST_URI olmayabilir
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '/admin/login'; // CLI için varsayılan route
        }
        
        $url = $_SERVER['REQUEST_URI'];
        
        // Query string'i kaldır
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }
        
        // Base path'i çıkar (XAMPP için)
        $scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptPath !== '/' && !empty($scriptPath) && strpos($url, $scriptPath) === 0) {
            $url = substr($url, strlen($scriptPath));
        }
        
        // Başlangıçta / yoksa ekle
        if (empty($url) || $url[0] !== '/') {
            $url = '/' . $url;
        }
        
        return $url;
    }
    
    // ================================================
    // SOSYAL MEDYA YÖNETİMİ
    // ================================================
    
    /**
     * Sosyal medya linkleri yönetim sayfası
     */
    public function socialMedia() {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        $links = $socialModel->getAll();
        
        $view = new View();
        $view->render('admin/social-media/index', [
            'pageTitle' => 'Sosyal Medya Yönetimi - ' . SITE_NAME,
            'links' => $links,
            'currentUser' => $this->getCurrentUser(),
            'csrfToken' => $this->generateCsrfToken()
        ], 'admin');
    }
    
    /**
     * API: Sosyal medya linki getir
     */
    public function getSocialMediaById($id) {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        $link = $socialModel->getById($id);
        
        if (!$link) {
            $this->json(['error' => 'Link bulunamadı'], 404);
        }
        
        $this->json(['success' => true, 'link' => $link]);
    }
    
    /**
     * API: Sosyal medya linkini kaydet (yeni veya güncelle)
     */
    public function saveSocialMedia() {
        $this->requireAdmin();
        
        try {
            $data = [
                'id' => $this->post('id'),
                'platform' => $this->post('platform'),
                'name' => $this->post('name'),
                'icon' => $this->post('icon'),
                'url' => $this->post('url'),
                'is_active' => $this->post('is_active', 0),
                'display_order' => $this->post('display_order', 0),
                'show_in_header' => $this->post('show_in_header', 0),
                'show_in_footer' => $this->post('show_in_footer', 0),
                'color' => $this->post('color')
            ];
            
            // Validasyon
            if (empty($data['platform']) || empty($data['name']) || empty($data['icon'])) {
                $this->json(['error' => 'Platform, isim ve ikon zorunludur'], 400);
            }
            
            $socialModel = new SocialMedia();
            $id = $socialModel->save($data);
            
            if ($id) {
                $this->json([
                    'success' => true,
                    'message' => $data['id'] ? 'Link güncellendi' : 'Link oluşturuldu',
                    'id' => $id
                ]);
            } else {
                $this->json(['error' => 'Link kaydedilemedi'], 500);
            }
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * API: Sosyal medya linkini aktif/pasif yap
     */
    public function toggleSocialMediaStatus($id) {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        $result = $socialModel->toggleStatus($id);
        
        if ($result) {
            $link = $socialModel->getById($id);
            $this->json([
                'success' => true,
                'message' => 'Durum güncellendi',
                'is_active' => $link['is_active']
            ]);
        } else {
            $this->json(['error' => 'Durum güncellenemedi'], 500);
        }
    }
    
    /**
     * API: Header'da gösterim durumunu değiştir
     */
    public function toggleSocialMediaHeader($id) {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        $result = $socialModel->toggleHeader($id);
        
        if ($result) {
            $link = $socialModel->getById($id);
            $this->json([
                'success' => true,
                'message' => 'Header durumu güncellendi',
                'show_in_header' => $link['show_in_header']
            ]);
        } else {
            $this->json(['error' => 'Durum güncellenemedi'], 500);
        }
    }
    
    /**
     * API: Footer'da gösterim durumunu değiştir
     */
    public function toggleSocialMediaFooter($id) {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        $result = $socialModel->toggleFooter($id);
        
        if ($result) {
            $link = $socialModel->getById($id);
            $this->json([
                'success' => true,
                'message' => 'Footer durumu güncellendi',
                'show_in_footer' => $link['show_in_footer']
            ]);
        } else {
            $this->json(['error' => 'Durum güncellenemedi'], 500);
        }
    }
    
    /**
     * API: Sosyal medya linkini sil
     */
    public function deleteSocialMedia($id) {
        $this->requireAdmin();
        
        $socialModel = new SocialMedia();
        
        // RSS gibi sistem linklerini silme kontrolü
        $link = $socialModel->getById($id);
        if ($link && $link['platform'] === 'rss') {
            $this->json(['error' => 'Sistem linkleri silinemez'], 400);
        }
        
        $result = $socialModel->deleteLink($id);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Link silindi']);
        } else {
            $this->json(['error' => 'Link silinemedi'], 500);
        }
    }
    
    /**
     * API: Sıralamayı güncelle
     */
    public function updateSocialMediaOrder($id) {
        $this->requireAdmin();
        
        $order = $this->post('order', 0);
        
        $socialModel = new SocialMedia();
        $result = $socialModel->updateOrder($id, $order);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Sıra güncellendi']);
        } else {
            $this->json(['error' => 'Sıra güncellenemedi'], 500);
        }
    }
}
?>
