<?php
/**
 * Tag Controller - Etiket sayfaları
 * LooMix.Click
 */

class TagController extends Controller {

    /**
     * Etiketler listesi
     */
    public function index() {
        $tagModel = new Tag();
        $page = (int)$this->get('page', 1);
        $perPage = defined('TAGS_PER_PAGE') ? TAGS_PER_PAGE : 48;

        // 0 kullanımda gizle
        $result = $tagModel->getActiveTagsPaginated($page, $perPage, 1);

        $view = new View();
        $view->render('tag/index', [
            'pageTitle' => 'Etiketler' . META_TITLE_SUFFIX,
            'metaDescription' => 'Sitede kullanılan tüm etiketler ve içerikleri',
            'canonicalUrl' => $page > 1 ? url('/etiketler?page=' . $page) : url('/etiketler'),
            'tags' => $result['tags'],
            'pagination' => $result['pagination']
        ], 'main');
    }

    /**
     * Etiket sayfası
     */
    public function show($slug) {
        $tagModel = new Tag();
        $newsModel = new News();

        // Etiketi getir
        $tag = $tagModel->getBySlug($slug);
        if (!$tag) {
            $errorController = new ErrorController();
            $errorController->notFound();
            return;
        }

        // Sayfa ve paginasyon
        $page = (int)$this->get('page', 1);
        $perPage = NEWS_PER_PAGE;
        $result = $tagModel->getTagNews($tag['id'], $page, $perPage);

        $news = $result['news'];
        $pagination = $result['pagination'];

        // İlgili etiketler
        $relatedTags = $tagModel->getRelatedTags($tag['id'], 12);

        // SEO meta
        $pageTitle = $tag['name'] . ' Etiketi Haberleri' . META_TITLE_SUFFIX;
        $metaDescription = ($tag['description'] ?: ($tag['name'] . ' etiketi ile ilgili en güncel haberler'));
        $canonicalUrl = $page > 1 ? url('/etiket/' . $tag['slug'] . '?page=' . $page) : url('/etiket/' . $tag['slug']);

        $view = new View();
        $view->render('tag/show', [
            'pageTitle' => $pageTitle,
            'metaDescription' => $metaDescription,
            'canonicalUrl' => $canonicalUrl,
            'tag' => $tag,
            'news' => $news,
            'pagination' => $pagination,
            'relatedTags' => $relatedTags
        ], 'main');
    }
}
?>


