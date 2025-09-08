<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-bar me-2"></i>
        İstatistikler & Raporlar
    </h1>
    <div class="page-actions">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="exportReport('pdf')">
                <i class="fas fa-file-pdf me-2"></i>PDF Rapor
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-2"></i>Excel Rapor
            </button>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['total_news'] ?? 0) ?></h3>
            <p class="stats-label">Toplam Haber</p>
            <div class="mt-2">
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Bu ay: +<?= number_format($stats['news_this_month'] ?? 0) ?>
                </small>
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
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Bugün: <?= number_format($stats['today_views'] ?? 0) ?>
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-folder"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['total_categories'] ?? 0) ?></h3>
            <p class="stats-label">Aktif Kategori</p>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-newspaper me-1"></i>
                    Ortalama: <?= $stats['total_categories'] > 0 ? round(($stats['total_news'] ?? 0) / $stats['total_categories'], 1) : 0 ?> haber/kategori
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stats-value"><?= number_format($stats['total_users'] ?? 0) ?></h3>
            <p class="stats-label">Sistem Kullanıcısı</p>
            <div class="mt-2">
                <small class="text-warning">
                    <i class="fas fa-user-check me-1"></i>
                    Aktif: <?= number_format($stats['active_users'] ?? 0) ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Daily News Chart -->
    <div class="col-lg-8">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Son 30 Gün Haber Sayıları
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-period="30">30 Gün</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="90">90 Gün</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="365">1 Yıl</button>
                    </div>
                </div>
            </div>
            <div class="p-3">
                <canvas id="dailyNewsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- News by Category -->
    <div class="col-lg-4">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Kategoriye Göre Dağılım
                </h5>
            </div>
            <div class="p-3">
                <canvas id="categoryChart" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <!-- Most Viewed News -->
    <div class="col-lg-6">
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-fire me-2"></i>
                    En Çok Okunan Haberler
                </h5>
            </div>
            <?php if (!empty($stats['most_viewed'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Haber</th>
                                <th>Görüntülenme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['most_viewed'] as $index => $news): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?= $index < 3 ? 'warning' : 'light' ?> text-dark">
                                        #<?= $index + 1 ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('/haber/' . $news['slug']) ?>" 
                                       class="text-decoration-none" target="_blank">
                                        <?= truncateText($news['title'], 60) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= number_format($news['view_count']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <i class="fas fa-chart-bar fa-2x mb-2 opacity-25"></i>
                    <p>Henüz görüntülenme verisi yok</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Category Performance -->
    <div class="col-lg-6">
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-folder-open me-2"></i>
                    Kategori Performansı
                </h5>
            </div>
            <?php if (!empty($stats['news_by_category'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Haber Sayısı</th>
                                <th>Oran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCategoryNews = array_sum(array_column($stats['news_by_category'], 'count'));
                            foreach ($stats['news_by_category'] as $category): 
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-box me-2" 
                                             style="width: 12px; height: 12px; background-color: <?= $category['color'] ?: '#007bff' ?>; border-radius: 2px;"></div>
                                        <?= escape($category['name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark"><?= number_format($category['count']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar" 
                                                 style="width: <?= $totalCategoryNews > 0 ? round(($category['count'] / $totalCategoryNews) * 100, 1) : 0 ?>%; background-color: <?= $category['color'] ?: '#007bff' ?>"></div>
                                        </div>
                                        <small><?= $totalCategoryNews > 0 ? round(($category['count'] / $totalCategoryNews) * 100, 1) : 0 ?>%</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <i class="fas fa-folder fa-2x mb-2 opacity-25"></i>
                    <p>Kategori verisi yok</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Daily Views Chart -->
<div class="row">
    <div class="col-12">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Son 7 Gün Görüntülenme Analizi
                </h5>
            </div>
            <div class="p-3">
                <canvas id="dailyViewsChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="row mt-4">
    <div class="col-12">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    Sistem Bilgileri
                </h5>
            </div>
            <div class="p-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">PHP Versiyonu</h6>
                            <div class="fw-bold"><?= phpversion() ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">MySQL Versiyonu</h6>
                            <div class="fw-bold">
                                <?php
                                try {
                                    $version = Database::getInstance()->fetchColumn("SELECT VERSION()");
                                    echo explode('-', $version)[0];
                                } catch (Exception $e) {
                                    echo 'Bilinmiyor';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Disk Kullanımı</h6>
                            <div class="fw-bold">
                                <?php 
                                $bytes = disk_free_space('.');
                                $gb = round($bytes / (1024*1024*1024), 2);
                                echo $gb . ' GB boş';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <h6 class="text-muted mb-1">Bellek Kullanımı</h6>
                            <div class="fw-bold">
                                <?= round(memory_get_usage() / (1024*1024), 2) ?> MB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    // Period change handlers
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const period = this.dataset.period;
            updateDailyNewsChart(period);
        });
    });
});

function initializeCharts() {
    // Daily News Chart
    const dailyNewsCtx = document.getElementById('dailyNewsChart').getContext('2d');
    const dailyNewsChart = new Chart(dailyNewsCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($stats['daily_news'] ?? [], 'date')) ?>,
            datasets: [{
                label: 'Haber Sayısı',
                data: <?= json_encode(array_column($stats['daily_news'] ?? [], 'count')) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($stats['news_by_category'] ?? [], 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($stats['news_by_category'] ?? [], 'count')) ?>,
                backgroundColor: <?= json_encode(array_column($stats['news_by_category'] ?? [], 'color')) ?>
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            }
        }
    });
    
    // Daily Views Chart
    const dailyViewsCtx = document.getElementById('dailyViewsChart').getContext('2d');
    const dailyViewsChart = new Chart(dailyViewsCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($stats['daily_views'] ?? [], 'date')) ?>,
            datasets: [{
                label: 'Görüntülenme',
                data: <?= json_encode(array_column($stats['daily_views'] ?? [], 'views')) ?>,
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: '#17a2b8',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function updateDailyNewsChart(period) {
    fetch(`/admin/api/statistics/daily-news?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update chart data
                const chart = Chart.getChart('dailyNewsChart');
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.data;
                chart.update();
            }
        })
        .catch(error => {
            console.error('Chart update error:', error);
        });
}

function exportReport(type) {
    const url = `/admin/api/statistics/export?type=${type}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = `loomix-rapor-${new Date().toISOString().split('T')[0]}.${type}`;
    link.click();
}

// Real-time stats update every 5 minutes
setInterval(function() {
    fetch('<?= url('/admin/api/statistics/live-stats') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update live stats
                document.querySelector('[data-stat="today_views"]').textContent = data.stats.today_views.toLocaleString();
                document.querySelector('[data-stat="total_views"]').textContent = data.stats.total_views.toLocaleString();
            }
        })
        .catch(error => {
            console.log('Live stats update failed:', error);
        });
}, 5 * 60 * 1000);
</script>
