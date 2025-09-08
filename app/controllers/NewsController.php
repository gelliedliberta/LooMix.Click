<?php
/**
 * News Controller - Haber detay sayfaları
 * LooMix.Click
 */

class NewsController extends Controller {
    
    /**
     * Haber detay sayfası
     */
    public function show($slug) {
        $newsModel = new News();
        $tagModel = new Tag();
        
        // Haberi getir
        $news = $newsModel->getBySlug($slug);
        
        if (!$news) {
            // Legacy slug kontrolü ve 301 yönlendirme
            $legacy = $this->db->fetch(
                "SELECT new_slug FROM legacy_slugs WHERE entity_type = 'news' AND old_slug = :slug",
                ['slug' => $slug]
            );
            if ($legacy && !empty($legacy['new_slug'])) {
                Router::redirect('/haber/' . $legacy['new_slug'], 301);
                return;
            }
            
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }
        
        // Görüntülenme kaydı ekle
        $newsModel->addView($news['id']);
        
        // Haber etiketlerini getir
        $tags = $newsModel->getNewsTags($news['id']);
        
        // İlgili haberler
        $relatedNews = $newsModel->getRelatedNews(
            $news['id'], 
            $news['category_id'], 
            6
        );
        
        // Son haberler (sidebar için)
        $latestNews = $newsModel->getPublishedNews(8);
        
        // Popüler haberler
        $popularNews = $newsModel->getPopularNews(6);
        
        // Breadcrumb için kategori bilgisi
        $categoryModel = new Category();
        $categoryBreadcrumb = $categoryModel->getBreadcrumb($news['category_id']);
        
        // SEO için meta bilgileri hazırla
        $metaTitle = !empty($news['meta_title']) ? $news['meta_title'] : $news['title'] . META_TITLE_SUFFIX;
        $metaDescription = !empty($news['meta_description']) ? $news['meta_description'] : truncateText(strip_tags($news['summary']), 160);
        $metaKeywords = !empty($news['meta_keywords']) ? $news['meta_keywords'] : implode(', ', array_column($tags, 'name'));
        
        // Open Graph resmi
        $ogImage = !empty($news['featured_image']) ? getImageUrl($news['featured_image']) : DEFAULT_META_IMAGE;
        
        // Canonical URL
        $canonicalUrl = !empty($news['canonical_url']) ? $news['canonical_url'] : url('/haber/' . $news['slug']);
        
        // JSON-LD struktural veri
        $structuredData = $this->generateNewsStructuredData($news, $tags, $canonicalUrl, $ogImage);
        
        $view = new View();
        $view->render('news/detail', [
            'pageTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
            'metaImage' => $ogImage,
            'canonicalUrl' => $canonicalUrl,
            'structuredData' => $structuredData,
            'news' => $news,
            'tags' => $tags,
            'relatedNews' => $relatedNews,
            'latestNews' => $latestNews,
            'popularNews' => $popularNews,
            'categoryBreadcrumb' => $categoryBreadcrumb,
            'isNewsDetail' => true
        ], 'main');
    }
    
    /**
     * Haber için JSON-LD structured data oluştur
     */
    private function generateNewsStructuredData($news, $tags, $canonicalUrl, $image) {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news['title'],
            'description' => strip_tags($news['summary']),
            'image' => $image,
            'datePublished' => date('c', strtotime($news['publish_date'])),
            'dateModified' => date('c', strtotime($news['updated_at'])),
            'author' => [
                '@type' => 'Person',
                'name' => $news['author_name']
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => SITE_NAME,
                'url' => SITE_URL,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url(SITE_LOGO)
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl
            ],
            'url' => $canonicalUrl
        ];
        
        // Etiketleri keywords olarak ekle
        if (!empty($tags)) {
            $structuredData['keywords'] = implode(', ', array_column($tags, 'name'));
        }
        
        // Okuma süresini ekle
        if ($news['reading_time'] > 0) {
            $structuredData['timeRequired'] = 'PT' . $news['reading_time'] . 'M';
        }
        
        // Kategori bilgisi
        $structuredData['articleSection'] = $news['category_name'];
        
        return json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Haber listesi (pagination ile)
     */
    public function index() {
        $page = (int)$this->get('page', 1);
        $perPage = NEWS_PER_PAGE;
        
        $newsModel = new News();
        $news = $newsModel->getPublishedNews($perPage, ($page - 1) * $perPage);
        
        // Toplam sayı
        $totalCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM news WHERE status = 'published' AND publish_date <= NOW()"
        );
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_count' => $totalCount,
            'total_pages' => ceil($totalCount / $perPage)
        ];
        
        // SEO için sayfa bilgisi
        $pageTitle = $page > 1 ? 'Haberler - Sayfa ' . $page . META_TITLE_SUFFIX : 'Tüm Haberler' . META_TITLE_SUFFIX;
        
        $view = new View();
        $view->render('news/index', [
            'pageTitle' => $pageTitle,
            'metaDescription' => 'LooMix.Click tüm haberler - Güncel haberler ve gelişmeler',
            'canonicalUrl' => $page > 1 ? url('/haberler?page=' . $page) : url('/haberler'),
            'news' => $news,
            'pagination' => $pagination,
            'currentPage' => $page
        ], 'main');
    }
    
    /**
     * Mobil AMP versiyonu (opsiyonel)
     */
    public function amp($slug) {
        $newsModel = new News();
        $news = $newsModel->getBySlug($slug);
        
        if (!$news) {
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }
        
        // AMP için özel view
        $view = new View();
        $view->render('news/amp', [
            'news' => $news,
            'canonicalUrl' => url('/haber/' . $news['slug']),
            'ampUrl' => url('/amp/haber/' . $news['slug'])
        ], 'amp'); // AMP layout kullan
    }
    
    /**
     * Haber paylaşım sayıları (AJAX)
     */
    public function getShareCounts($newsId) {
        // Bu endpoint sosyal medya paylaşım sayılarını getirebilir
        // Facebook, Twitter vb. API'larından veri çekebilir
        
        $this->json([
            'facebook' => 0,
            'twitter' => 0,
            'linkedin' => 0,
            'whatsapp' => 0
        ]);
    }
    
    /**
     * İlgili haberleri getir (AJAX)
     */
    public function getRelated($newsId) {
        $newsModel = new News();
        $news = $newsModel->find($newsId);
        
        if (!$news) {
            $this->json(['error' => 'Haber bulunamadı'], 404);
            return;
        }
        
        $relatedNews = $newsModel->getRelatedNews($newsId, $news['category_id'], 6);
        
        $this->json([
            'success' => true,
            'news' => $relatedNews
        ]);
    }
    
    /**
     * Haber görüntüleme istatistikleri
     */
    public function getStats($newsId) {
        $this->requireAdmin(); // Admin kontrolü
        
        // Son 30 günün görüntülenme istatistikleri
        $stats = $this->db->fetchAll("
            SELECT DATE(view_date) as date, COUNT(*) as views 
            FROM news_views 
            WHERE news_id = :news_id 
            AND view_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(view_date) 
            ORDER BY date ASC
        ", ['news_id' => $newsId]);
        
        $this->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
?>
