<?php
/**
 * Home Controller - Ana sayfa ve genel sayfalar
 * LooMix.Click
 */

class HomeController extends Controller {
    
    /**
     * Ana sayfa
     */
    public function index() {
        try {
            $newsModel = new News();
            $categoryModel = new Category();
            
            // Son dakika haberleri - ÖNCELİKLİ (header'da gösteriliyor)
            $breakingNews = $newsModel->getBreakingNews(5);
            
            // Öne çıkan haberler (slider için en az 7 öğe: 5 slider + 2 yan)
            $featuredNews = $newsModel->getFeaturedNews(7);
            
            // Son haberler
            $latestNews = $newsModel->getPublishedNews(12);
            
            // Popüler haberler
            $popularNews = $newsModel->getPopularNews(8);
            
            // Kategoriler ve haber sayıları
            $categories = $categoryModel->getCategoriesWithNewsCount();
            
        } catch (Exception $e) {
            // Veritabanı hatası durumunda boş verilerle devam et
            $featuredNews = [];
            $latestNews = [];
            $popularNews = [];
            $breakingNews = [];
            $categories = [];
            
            // Debug modda hatayı göster
            if (DEBUG_MODE) {
                error_log("HomeController Hatası: " . $e->getMessage());
            }
        }
        
        // Eğer hiç veri yoksa örnek veriler
        if (empty($featuredNews) && empty($latestNews)) {
            $featuredNews = $this->getSampleFeaturedNews();
            $latestNews = $this->getSampleLatestNews();
            $popularNews = $this->getSamplePopularNews();
            $categories = $this->getSampleCategories();
        }
        
        $view = new View();
        $view->render('home/index', [
            'pageTitle' => SITE_NAME . ' - ' . SITE_DESCRIPTION,
            'metaDescription' => SITE_DESCRIPTION,
            'metaKeywords' => SITE_KEYWORDS,
            'canonicalUrl' => SITE_URL,
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
            'popularNews' => $popularNews,
            'breakingNews' => $breakingNews,
            'categories' => $categories,
            'isHomePage' => true
        ], 'main');
    }
    
    /**
     * Örnek öne çıkan haberler (veri yoksa)
     */
    private function getSampleFeaturedNews() {
        return [
            [
                'id' => 1,
                'title' => 'LooMix.Click Haber Sitesi Yayında!',
                'slug' => 'loomix-click-haber-sitesi-yayinda',
                'summary' => 'Modern ve kullanıcı dostu tasarımı ile LooMix.Click haber sitesi artık yayında. En güncel haberleri takip edebilirsiniz.',
                'content' => '<p>LooMix.Click haber sitesi yayında!</p>',
                'featured_image' => 'https://via.placeholder.com/800x400/007bff/ffffff?text=LooMix.Click+Ana+Haber',
                'image_alt' => 'LooMix.Click Ana Sayfa',
                'category_name' => 'Genel',
                'category_slug' => 'genel',
                'category_color' => '#007bff',
                'author_name' => 'LooMix Editör',
                'view_count' => 150,
                'is_featured' => true,
                'is_breaking' => false,
                'publish_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'title' => 'Teknoloji Dünyasından Son Gelişmeler',
                'slug' => 'teknoloji-dunyasindan-son-gelismeler',
                'summary' => 'Teknoloji sektöründeki en son gelişmeleri ve yenilikleri sizler için derledik.',
                'content' => '<p>Teknoloji haberleri...</p>',
                'featured_image' => 'https://via.placeholder.com/800x400/28a745/ffffff?text=Teknoloji+Haberleri',
                'image_alt' => 'Teknoloji Haberleri',
                'category_name' => 'Teknoloji',
                'category_slug' => 'teknoloji',
                'category_color' => '#28a745',
                'author_name' => 'LooMix Editör',
                'view_count' => 89,
                'is_featured' => true,
                'is_breaking' => false,
                'publish_date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ]
        ];
    }
    
    /**
     * Örnek son haberler (veri yoksa)
     */
    private function getSampleLatestNews() {
        return [
            [
                'id' => 3,
                'title' => 'Spor Dünyasından Öne Çıkan Haberler',
                'slug' => 'spor-dunyasindan-one-cikan-haberler',
                'summary' => 'Bu hafta spor dünyasında yaşanan önemli gelişmeleri sizler için özetledik.',
                'content' => '<p>Spor haberleri...</p>',
                'featured_image' => 'https://via.placeholder.com/800x400/ffc107/000000?text=Spor+Haberleri',
                'image_alt' => 'Spor Haberleri',
                'category_name' => 'Spor',
                'category_slug' => 'spor',
                'category_color' => '#ffc107',
                'author_name' => 'LooMix Editör',
                'view_count' => 45,
                'is_featured' => false,
                'is_breaking' => false,
                'publish_date' => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours'))
            ],
            [
                'id' => 4,
                'title' => 'Ekonomi ve Finans Piyasalarından Gelişmeler',
                'slug' => 'ekonomi-ve-finans-piyasalarindan-gelismeler',
                'summary' => 'Ekonomi alanındaki son gelişmeler ve piyasa analizleri.',
                'content' => '<p>Ekonomi haberleri...</p>',
                'featured_image' => 'https://via.placeholder.com/800x400/dc3545/ffffff?text=Ekonomi+Haberleri',
                'image_alt' => 'Ekonomi Haberleri',
                'category_name' => 'Ekonomi',
                'category_slug' => 'ekonomi',
                'category_color' => '#dc3545',
                'author_name' => 'LooMix Editör',
                'view_count' => 67,
                'is_featured' => false,
                'is_breaking' => false,
                'publish_date' => date('Y-m-d H:i:s', strtotime('-6 hours')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ]
        ];
    }
    
    /**
     * Örnek popüler haberler (veri yoksa)
     */
    private function getSamplePopularNews() {
        return [
            [
                'id' => 1,
                'title' => 'LooMix.Click Haber Sitesi Yayında!',
                'slug' => 'loomix-click-haber-sitesi-yayinda',
                'view_count' => 150
            ],
            [
                'id' => 4,
                'title' => 'Ekonomi ve Finans Piyasalarından Gelişmeler',
                'slug' => 'ekonomi-ve-finans-piyasalarindan-gelismeler',
                'view_count' => 67
            ]
        ];
    }
    
    /**
     * Örnek kategoriler (veri yoksa)
     */
    private function getSampleCategories() {
        return [
            [
                'id' => 1,
                'name' => 'Genel',
                'slug' => 'genel',
                'color' => '#007bff',
                'icon' => 'fas fa-newspaper',
                'news_count' => 1
            ],
            [
                'id' => 2,
                'name' => 'Teknoloji',
                'slug' => 'teknoloji',
                'color' => '#28a745',
                'icon' => 'fas fa-laptop',
                'news_count' => 1
            ],
            [
                'id' => 3,
                'name' => 'Spor',
                'slug' => 'spor',
                'color' => '#ffc107',
                'icon' => 'fas fa-futbol',
                'news_count' => 1
            ],
            [
                'id' => 4,
                'name' => 'Ekonomi',
                'slug' => 'ekonomi',
                'color' => '#dc3545',
                'icon' => 'fas fa-chart-line',
                'news_count' => 1
            ]
        ];
    }
    
    
    /**
     * Hakkımızda sayfası
     */
    public function about() {
        $view = new View();
        $view->render('home/about', [
            'pageTitle' => 'Hakkımızda' . META_TITLE_SUFFIX,
            'metaDescription' => 'LooMix.Click hakkında bilgi, misyonumuz ve vizyonumuz'
        ], 'main');
    }
    
    /**
     * İletişim sayfası
     */
    public function contact() {
        $view = new View();
        $view->render('home/contact', [
            'pageTitle' => 'İletişim' . META_TITLE_SUFFIX,
            'metaDescription' => 'Bizimle iletişime geçin - İletişim bilgileri ve adres'
        ], 'main');
    }
    
    /**
     * Gizlilik Politikası
     */
    public function privacy() {
        $view = new View();
        $view->render('home/privacy', [
            'pageTitle' => 'Gizlilik Politikası' . META_TITLE_SUFFIX,
            'metaDescription' => 'LooMix.Click gizlilik politikası ve kişisel veriler',
            'robotsIndex' => false
        ], 'main');
    }
    
    /**
     * Site Haritası
     */
    public function sitemap() {
        $view = new View();
        $view->render('home/sitemap', [
            'pageTitle' => 'Site Haritası' . META_TITLE_SUFFIX,
            'metaDescription' => 'LooMix.Click site haritası - Tüm sayfalar',
            'robotsIndex' => false
        ], 'main');
    }
    
    /**
     * RSS Feed
     */
    public function rss() {
        try {
            $newsModel = new News();
            $news = $newsModel->getPublishedNews(20); // Son 20 haber
            
            header('Content-Type: application/rss+xml; charset=utf-8');
            
            $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
            $rss .= '<channel>' . "\n";
            $rss .= '<title>' . htmlspecialchars(SITE_NAME) . '</title>' . "\n";
            $rss .= '<description>' . htmlspecialchars(SITE_DESCRIPTION) . '</description>' . "\n";
            $rss .= '<link>' . SITE_URL . '</link>' . "\n";
            $rss .= '<atom:link href="' . url('/rss') . '" rel="self" type="application/rss+xml" />' . "\n";
            $rss .= '<language>tr</language>' . "\n";
            $rss .= '<lastBuildDate>' . date('r') . '</lastBuildDate>' . "\n";
            
            foreach ($news as $item) {
                $rss .= '<item>' . "\n";
                $rss .= '<title>' . htmlspecialchars($item['title']) . '</title>' . "\n";
                $rss .= '<description>' . htmlspecialchars(strip_tags($item['summary'])) . '</description>' . "\n";
                $rss .= '<link>' . url('/haber/' . $item['slug']) . '</link>' . "\n";
                $rss .= '<category>' . htmlspecialchars($item['category_name'] ?? 'Genel') . '</category>' . "\n";
                $rss .= '<pubDate>' . date('r', strtotime($item['publish_date'])) . '</pubDate>' . "\n";
                $rss .= '<guid>' . url('/haber/' . $item['slug']) . '</guid>' . "\n";
                $rss .= '</item>' . "\n";
            }
            
            $rss .= '</channel>' . "\n";
            $rss .= '</rss>';
            
            echo $rss;
            exit();
        } catch (Exception $e) {
            // Hata durumunda boş RSS
            header('Content-Type: application/rss+xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel><title>' . SITE_NAME . '</title><description>RSS Feed</description><link>' . SITE_URL . '</link></channel></rss>';
            exit();
        }
    }

    /**
     * Arama sayfası
     */
    public function search() {
        $query = trim((string)$this->get('q', ''));
        $page = (int)$this->get('page', 1);
        if ($page < 1) { $page = 1; }

        $newsModel = new News();

        $perPage = NEWS_PER_PAGE;
        $results = [];
        $totalResults = 0;
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_count' => 0,
            'total_pages' => 0
        ];

        // Sidebar için son haberler
        $latestNews = $newsModel->getPublishedNews(8);

        if ($query !== '' && mb_strlen($query) >= 2) {
            try {
                $results = $newsModel->searchNews($query, $page, $perPage);
                $totalResults = $newsModel->countSearchResults($query);
                $pagination['total_count'] = $totalResults;
                $pagination['total_pages'] = (int)ceil($totalResults / $perPage);
                
            } catch (Exception $e) {
                if (DEBUG_MODE) {
                    error_log('Search Error: ' . $e->getMessage());
                    error_log('Search Error Trace: ' . $e->getTraceAsString());
                }
            }
        }

        $view = new View();
        $view->render('home/search', [
            'pageTitle' => ($query ? ('"' . $query . '" için arama sonuçları') : 'Arama') . META_TITLE_SUFFIX,
            'metaDescription' => $query ? ($query . ' araması sonuçları') : 'Haber arama sayfası',
            'canonicalUrl' => url('/ara' . ($query ? ('?q=' . urlencode($query) . ($page > 1 ? ('&page=' . $page) : '')) : '')),
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'pagination' => $pagination,
            'latestNews' => $latestNews
        ], 'main');
    }
}
?>