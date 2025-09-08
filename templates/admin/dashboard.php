<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </h1>
    <div class="page-actions">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-plus me-2"></i>Hızlı İşlemler
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="<?= url('/admin/haber-ekle') ?>"><i class="fas fa-newspaper me-2"></i>Yeni Haber</a></li>
                <li><a class="dropdown-item" href="<?= url('/admin/kategori-ekle') ?>"><i class="fas fa-folder me-2"></i>Yeni Kategori</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= url('/admin/ayarlar') ?>"><i class="fas fa-cog me-2"></i>Site Ayarları</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['published_news'] ?? 0) ?></h3>
            <p class="stats-label">Yayınlanan Haberler</p>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: <?= $stats['total_news'] > 0 ? round(($stats['published_news'] / $stats['total_news']) * 100) : 0 ?>%"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-edit"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['draft_news'] ?? 0) ?></h3>
            <p class="stats-label">Taslak Haberler</p>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-warning" role="progressbar" 
                     style="width: <?= $stats['total_news'] > 0 ? round(($stats['draft_news'] / $stats['total_news']) * 100) : 0 ?>%"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-eye"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['total_views'] ?? 0) ?></h3>
            <p class="stats-label">Toplam Görüntülenme</p>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    Bugün: <?= number_format($stats['today_views'] ?? 0) ?>
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['news_this_month'] ?? 0) ?></h3>
            <p class="stats-label">Bu Ay Eklenenler</p>
            <div class="mt-2">
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    <?= date('F Y') ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent News -->
    <div class="col-xl-8 mb-4">
        <div class="data-table">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Son Eklenen Haberler
                </h5>
                <a href="<?= url('/admin/haberler') ?>" class="btn btn-sm btn-outline-primary">
                    Tümünü Gör <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <?php if (!empty($recentNews)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Haber</th>
                                <th>Kategori</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentNews as $news): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?= escape($news['title']) ?></h6>
                                            <small class="text-muted"><?= escape($news['slug']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark" style="border-left: 3px solid <?= $news['category_color'] ?>">
                                        <?= escape($news['category_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'published' => 'status-published',
                                        'draft' => 'status-draft',
                                        'archived' => 'status-archived'
                                    ];
                                    $statusText = [
                                        'published' => 'Yayında',
                                        'draft' => 'Taslak',
                                        'archived' => 'Arşiv'
                                    ];
                                    ?>
                                    <span class="status-badge <?= $statusClass[$news['status']] ?? '' ?>">
                                        <?= $statusText[$news['status']] ?? ucfirst($news['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= formatDate($news['created_at'], 'd.m.Y H:i') ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= url('/haber/' . $news['slug']) ?>" class="btn btn-outline-info" target="_blank" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= url('/admin/haber-duzenle/' . $news['id']) ?>" class="btn btn-outline-primary" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= url('/admin/haber-sil/' . $news['id']) ?>" class="btn btn-outline-danger" 
                                           data-action="delete" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-newspaper fa-3x mb-3 opacity-25"></i>
                    <p>Henüz haber eklenmemiş.</p>
                    <a href="<?= url('/admin/haber-ekle') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>İlk Haberi Ekle
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Stats & Actions -->
    <div class="col-xl-4">
        <!-- Quick Actions -->
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Hızlı İşlemler
                </h5>
            </div>
            <div class="p-3">
                <div class="d-grid gap-2">
                    <a href="<?= url('/admin/haber-ekle') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Yeni Haber Ekle
                    </a>
                    <a href="<?= url('/admin/kategoriler') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-folder me-2"></i>Kategorileri Yönet
                    </a>
                    <a href="<?= url('/admin/reklam-alanlari') ?>" class="btn btn-outline-success">
                        <i class="fas fa-ad me-2"></i>Reklamları Yönet
                    </a>
                    <a href="<?= url('/admin/istatistikler') ?>" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>Detaylı İstatistikler
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    Sistem Bilgileri
                </h5>
            </div>
            <div class="p-3">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <h6 class="mb-0"><?= $stats['total_categories'] ?? 0 ?></h6>
                            <small class="text-muted">Kategori</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <h6 class="mb-0"><?= $stats['total_users'] ?? 0 ?></h6>
                            <small class="text-muted">Kullanıcı</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="small text-muted">
                    <div class="d-flex justify-content-between mb-1">
                        <span>PHP Versiyonu:</span>
                        <strong><?= phpversion() ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Disk Kullanımı:</span>
                        <strong><?= round(disk_free_space('.') / (1024*1024*1024), 2) ?> GB</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Son Güncelleme:</span>
                        <strong><?= date('d.m.Y H:i') ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div class="data-table">
            <div class="p-3 bg-primary text-white rounded">
                <h5 class="text-white mb-2">
                    <i class="fas fa-hand-wave me-2"></i>
                    Hoş geldiniz!
                </h5>
                <p class="mb-2 opacity-75">
                    Merhaba <?= escape($currentUser['full_name'] ?? 'Admin') ?>, 
                    <?= escape(SITE_NAME) ?> admin paneline hoş geldiniz.
                </p>
                <small class="opacity-75">
                    <i class="fas fa-clock me-1"></i>
                    Son giriş: <?= date('d.m.Y H:i') ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section (Optional - for future enhancement) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Performans Özeti
                </h5>
            </div>
            <div class="p-3">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h4 class="mb-1"><?= number_format($stats['today_views'] ?? 0) ?></h4>
                            <small class="text-muted">Bugünkü Ziyaretçi</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-mouse-pointer fa-2x text-success mb-2"></i>
                            <h4 class="mb-1">%2.4</h4>
                            <small class="text-muted">Tıklama Oranı</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h4 class="mb-1">2:34</h4>
                            <small class="text-muted">Ortalama Kalış Süresi</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="fas fa-share-alt fa-2x text-info mb-2"></i>
                            <h4 class="mb-1"><?= number_format(rand(50, 200)) ?></h4>
                            <small class="text-muted">Sosyal Paylaşım</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh stats every 5 minutes
    setInterval(function() {
        // Refresh dashboard stats
        fetch('<?= url('/admin/api/dashboard-stats') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update stats values
                    Object.keys(data.stats).forEach(key => {
                        const element = document.querySelector(`[data-stat="${key}"]`);
                        if (element) {
                            element.textContent = data.stats[key].toLocaleString();
                        }
                    });
                }
            })
            .catch(error => console.log('Stats refresh error:', error));
    }, 5 * 60 * 1000);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
