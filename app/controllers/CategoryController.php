<?php
/**
 * Category Controller - Kategori sayfaları
 * LooMix.Click
 */

class CategoryController extends Controller {
    
    /**
     * Kategori sayfası
     */
    public function show($slug) {
        $categoryModel = new Category();
        $newsModel = new News();
        
        // Kategoryi getir (SEO bilgileri ile)
        $category = $categoryModel->getCategoryWithSeo($slug);
        
        if (!$category) {
            // Legacy slug kontrolü ve 301 yönlendirme
            $legacy = $this->db->fetch(
                "SELECT new_slug FROM legacy_slugs WHERE entity_type = 'category' AND old_slug = :slug",
                ['slug' => $slug]
            );
            if ($legacy && !empty($legacy['new_slug'])) {
                Router::redirect('/kategori/' . $legacy['new_slug'], 301);
                return;
            }
            
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }
        
        // Sayfa numarası
        $page = (int)$this->get('page', 1);
        $perPage = NEWS_PER_PAGE;
        
        // Kategori haberlerini getir
        $categoryData = $newsModel->getCategoryNews($slug, $page, $perPage);
        
        if (!$categoryData) {
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }
        
        // Alt kategoriler
        $subCategories = $categoryModel->getSubCategories($category['id']);
        
        // Kategori breadcrumb
        $breadcrumb = $categoryModel->getBreadcrumb($category['id']);
        
        // Popüler haberler (sidebar için) - Son 7 gün, kategoriye özel (alt kategoriler dahil)
        $popularNews = $newsModel->getPopularNewsByCategory((int)$category['id'], 8, 7);
        
        // Son haberler
        $latestNews = $newsModel->getPublishedNews(8);
        
        // SEO meta bilgileri
        $metaTitle = !empty($category['seo_title']) ? $category['seo_title'] : $category['name'] . ' Haberleri' . META_TITLE_SUFFIX;
        
        // metaDescription için if-else yapısı (daha okunabilir)
        if (!empty($category['seo_description'])) {
            $metaDescription = $category['seo_description'];
        } elseif (!empty($category['description'])) {
            $metaDescription = $category['description'];
        } else {
            $metaDescription = $category['name'] . ' kategorisinden en güncel haberler';
        }
        $metaKeywords = !empty($category['seo_keywords']) ? $category['seo_keywords'] : $category['name'] . ', haberler, güncel';
        
        // Sayfalama için canonical URL
        $canonicalUrl = $page > 1 ? url('/kategori/' . $slug . '?page=' . $page) : url('/kategori/' . $slug);
        if (!empty($category['canonical_url'])) {
            $canonicalUrl = $category['canonical_url'];
        }
        
        // Open Graph resmi
        $ogImage = !empty($category['seo_og_image']) ? $category['seo_og_image'] : DEFAULT_META_IMAGE;
        
        // JSON-LD structured data
        $structuredData = $this->generateCategoryStructuredData($category, $categoryData['news']);
        
        $view = new View();
        $view->render('category/show', [
            'pageTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
            'metaImage' => $ogImage,
            'canonicalUrl' => $canonicalUrl,
            'structuredData' => $structuredData,
            'category' => $category,
            'news' => $categoryData['news'],
            'pagination' => $categoryData['pagination'],
            'subCategories' => $subCategories,
            'breadcrumb' => $breadcrumb,
            'popularNews' => $popularNews,
            'latestNews' => $latestNews,
            'currentPage' => $page,
            'isCategoryPage' => true
        ], 'main');
    }
    
    /**
     * Kategori için JSON-LD structured data
     */
    private function generateCategoryStructuredData($category, $news) {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category['name'] . ' Haberleri',
            'description' => $category['description'] ?: $category['name'] . ' kategorisinden haberler',
            'url' => url('/kategori/' . $category['slug']),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($news),
                'itemListElement' => []
            ]
        ];
        
        // Haber listesini ekle
        foreach ($news as $index => $newsItem) {
            $structuredData['mainEntity']['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'NewsArticle',
                    'name' => $newsItem['title'],
                    'url' => url('/haber/' . $newsItem['slug']),
                    'datePublished' => date('c', strtotime($newsItem['publish_date']))
                ]
            ];
        }
        
        return json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Kategori listesi
     */
    public function index() {
        $categoryModel = new Category();
        
        // Kategori ağacını getir
        $categoryTree = $categoryModel->getCategoryTree();
        
        // Popüler kategoriler
        $popularCategories = $categoryModel->getPopularCategories(20);
        
        $view = new View();
        $view->render('category/index', [
            'pageTitle' => 'Kategoriler' . META_TITLE_SUFFIX,
            'metaDescription' => 'LooMix.Click haber kategorileri - Teknoloji, spor, ekonomi ve daha fazlası',
            'categoryTree' => $categoryTree,
            'popularCategories' => $popularCategories
        ], 'main');
    }
    
    /**
     * Kategori RSS feed'i
     */
    public function rss($slug) {
        $categoryModel = new Category();
        $newsModel = new News();
        
        $category = $categoryModel->getBySlug($slug);
        
        if (!$category) {
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }
        
        // Kategori haberlerini getir
        $news = $newsModel->getPublishedNews(20, 0, $category['id']);
        
        header('Content-Type: application/rss+xml; charset=utf-8');
        
        $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $rss .= '<channel>' . "\n";
        $rss .= '<title>' . htmlspecialchars(SITE_NAME . ' - ' . $category['name']) . '</title>' . "\n";
        $rss .= '<description>' . htmlspecialchars($category['description'] ?: $category['name'] . ' kategorisinden haberler') . '</description>' . "\n";
        $rss .= '<link>' . url('/kategori/' . $category['slug']) . '</link>' . "\n";
        $rss .= '<atom:link href="' . url('/kategori/' . $category['slug'] . '/rss') . '" rel="self" type="application/rss+xml" />' . "\n";
        $rss .= '<language>tr</language>' . "\n";
        $rss .= '<lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
        
        foreach ($news as $item) {
            $rss .= '<item>' . "\n";
            $rss .= '<title>' . htmlspecialchars($item['title']) . '</title>' . "\n";
            $rss .= '<description>' . htmlspecialchars(strip_tags($item['summary'])) . '</description>' . "\n";
            $rss .= '<link>' . url('/haber/' . $item['slug']) . '</link>' . "\n";
            $rss .= '<category>' . htmlspecialchars($category['name']) . '</category>' . "\n";
            $rss .= '<pubDate>' . date('r', strtotime($item['publish_date'])) . '</pubDate>' . "\n";
            $rss .= '<guid>' . url('/haber/' . $item['slug']) . '</guid>' . "\n";
            $rss .= '</item>' . "\n";
        }
        
        $rss .= '</channel>' . "\n";
        $rss .= '</rss>';
        
        echo $rss;
        exit();
    }
    
    /**
     * Kategori haberlerini AJAX ile getir (infinite scroll için)
     */
    public function loadMore($slug) {
        $page = (int)$this->post('page', 1);
        $perPage = (int)$this->post('per_page', NEWS_PER_PAGE);
        
        $newsModel = new News();
        $categoryData = $newsModel->getCategoryNews($slug, $page, $perPage);
        
        if (!$categoryData) {
            $this->json(['error' => 'Kategori bulunamadı'], 404);
            return;
        }
        
        $this->json([
            'success' => true,
            'news' => $categoryData['news'],
            'pagination' => $categoryData['pagination'],
            'hasMore' => $categoryData['pagination']['current_page'] < $categoryData['pagination']['total_pages']
        ]);
    }
    
    /**
     * Kategori istatistikleri (Admin için)
     */
    public function getStats($categoryId) {
        $this->requireAdmin();
        
        // Son 30 günün haber sayıları
        $newsStats = $this->db->fetchAll("
            SELECT DATE(publish_date) as date, COUNT(*) as count 
            FROM news 
            WHERE category_id = :category_id 
            AND status = 'published' 
            AND publish_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(publish_date) 
            ORDER BY date ASC
        ", ['category_id' => $categoryId]);
        
        // Toplam görüntülenme sayısı
        $totalViews = $this->db->fetchColumn("
            SELECT SUM(n.view_count) 
            FROM news n 
            WHERE n.category_id = :category_id 
            AND n.status = 'published'
        ", ['category_id' => $categoryId]);
        
        // En popüler haberler
        $topNews = $this->db->fetchAll("
            SELECT title, slug, view_count 
            FROM news 
            WHERE category_id = :category_id 
            AND status = 'published' 
            ORDER BY view_count DESC 
            LIMIT 10
        ", ['category_id' => $categoryId]);
        
        $this->json([
            'success' => true,
            'newsStats' => $newsStats,
            'totalViews' => $totalViews ?: 0,
            'topNews' => $topNews
        ]);
    }
}
?>
