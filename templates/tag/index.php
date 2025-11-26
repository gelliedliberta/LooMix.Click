<div class="container my-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold mb-0">
            <i class="fas fa-tags me-2 text-primary"></i>
            Tüm Etiketler
        </h1>
        <span class="badge bg-light text-dark">
            Toplam: <?= number_format($pagination['total_count']) ?> etiket
        </span>
    </div>

    <?php if (!empty($tags)): ?>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
        <?php foreach ($tags as $tag): ?>
        <div class="col">
            <a href="<?= url('/etiket/' . $tag['slug']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag me-2" style="color: <?= escape($tag['color'] ?: '#6c757d') ?>"></i>
                            <span class="fw-semibold text-dark"><?= escape($tag['name']) ?></span>
                        </div>
                        <?php if (isset($tag['usage_count'])): ?>
                        <span class="badge bg-light text-dark" title="Kullanım">
                            <?= (int)$tag['usage_count'] ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <nav class="mt-4" aria-label="Sayfalama">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?= $i === (int)$pagination['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= url('/etiketler?page=' . $i) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
    <?php else: ?>
    <div class="alert alert-info">
        Henüz aktif etiket bulunamadı.
    </div>
    <?php endif; ?>
</div>


