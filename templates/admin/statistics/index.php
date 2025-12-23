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

<?php if (!empty($isPrint)): ?>
    <style>
        @media print {
            .admin-sidebar, .admin-header, .page-actions { display: none !important; }
            .admin-main { margin-left: 0 !important; }
            .admin-content { padding: 0 !important; }
            .data-table { box-shadow: none !important; }
            a { text-decoration: none !important; color: inherit !important; }
        }
    </style>
    <script>
        // Print view: allow browser "Save as PDF" without extra dependencies
        window.addEventListener('load', () => window.print());
    </script>
<?php endif; ?>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value" data-stat="total_news"><?= number_format($stats['total_news'] ?? 0) ?></h3>
            <p class="stats-label">Toplam Haber</p>
            <div class="mt-2">
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    Bu ay: +<span data-stat="news_this_month"><?= number_format($stats['news_this_month'] ?? 0) ?></span>
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-eye"></i>
            </div>
            <h3 class="stats-value" data-stat="total_views"><?= number_format($stats['total_views'] ?? 0) ?></h3>
            <p class="stats-label">Toplam Görüntülenme</p>
            <div class="mt-2">
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Bugün: <span data-stat="today_views"><?= number_format($stats['today_views'] ?? 0) ?></span>
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-folder"></i>
            </div>
            <h3 class="stats-value" data-stat="active_categories"><?= number_format($stats['active_categories'] ?? 0) ?></h3>
            <p class="stats-label">Aktif Kategori</p>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-newspaper me-1"></i>
                    Ortalama: <?= ($stats['active_categories'] ?? 0) > 0 ? round(($stats['total_news'] ?? 0) / ($stats['active_categories'] ?? 1), 1) : 0 ?> haber/kategori
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="stats-value" data-stat="total_users"><?= number_format($stats['total_users'] ?? 0) ?></h3>
            <p class="stats-label">Sistem Kullanıcısı</p>
            <div class="mt-2">
                <small class="text-warning">
                    <i class="fas fa-user-check me-1"></i>
                    Aktif: <span data-stat="active_users"><?= number_format($stats['active_users'] ?? 0) ?></span>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Combined Advanced Chart -->
    <div class="col-12">
        <div class="data-table">
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Gelişmiş Analiz (Haber + Görüntülenme + Kategori)
                    </h5>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" data-period="7">7 Gün</button>
                        <button type="button" class="btn btn-outline-secondary active" data-period="30">30 Gün</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="90">90 Gün</button>
                        <button type="button" class="btn btn-outline-secondary" data-period="365">1 Yıl</button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="date" class="form-control form-control-sm" id="combinedStartDate" style="max-width: 165px;" aria-label="Start date">
                        <span class="text-muted small">-</span>
                        <input type="date" class="form-control form-control-sm" id="combinedEndDate" style="max-width: 165px;" aria-label="End date">
                        <button type="button" class="btn btn-sm btn-primary" id="combinedApplyRange">
                            Uygula
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="combinedClearRange" title="Tarih aralığını temizle">
                            Temizle
                        </button>
                    </div>
                    </div>
                </div>
            </div>
            <div class="p-3" style="height: 460px;">
                <canvas id="combinedChart" height="240"></canvas>
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
    
    <!-- Referrer Analysis (beside Most Viewed) -->
    <div class="col-lg-6">
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2"></i>
                    Son 7 Gün Trafik Kaynakları (Referer)
                </h5>
            </div>
            <div class="p-3">
            <?php if (!empty($stats['top_referrers'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kaynak</th>
                                <th>Görüntülenme</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['top_referrers'] as $row): ?>
                            <tr>
                                <td>
                                    <div class="small text-muted"><?= escape(truncateText((string)($row['referer'] ?? ''), 120)) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= number_format((int)($row['views'] ?? 0)) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center text-muted">
                    <i class="fas fa-link fa-2x mb-2 opacity-25"></i>
                    <div>Referer verisi bulunamadı.</div>
                </div>
            <?php endif; ?>
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
            updateCombinedChart(period);

            // Clear date range inputs when period is selected
            const s = document.getElementById('combinedStartDate');
            const e = document.getElementById('combinedEndDate');
            if (s) s.value = '';
            if (e) e.value = '';
        });
    });

    // Date range apply
    const applyBtn = document.getElementById('combinedApplyRange');
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            const s = document.getElementById('combinedStartDate');
            const e = document.getElementById('combinedEndDate');
            const startDate = s ? s.value : '';
            const endDate = e ? e.value : '';

            if (!startDate || !endDate) {
                showNotification('Lütfen başlangıç ve bitiş tarihini seçin', 'error');
                return;
            }

            // Remove active period highlight (custom range mode)
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));

            updateCombinedChartByRange(startDate, endDate);
        });
    }

    // Date range clear (reset to 30 days)
    const clearBtn = document.getElementById('combinedClearRange');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            const s = document.getElementById('combinedStartDate');
            const e = document.getElementById('combinedEndDate');
            if (s) s.value = '';
            if (e) e.value = '';

            const btn30 = document.querySelector('[data-period="30"]');
            document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
            if (btn30) btn30.classList.add('active');
            updateCombinedChart(30);
        });
    }
});

function initializeCharts() {
    const initial = <?= json_encode($stats['combined_chart'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const ctx = document.getElementById('combinedChart').getContext('2d');
    const chart = new Chart(ctx, {
        data: {
            labels: initial.labels || [],
            datasets: buildCombinedDatasets(initial)
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, boxWidth: 10 }
                },
                tooltip: {
                    callbacks: {
                        footer: (items) => {
                            // Show stacked total for news axis
                            let totalNews = 0;
                            items.forEach(i => {
                                if (i.dataset && i.dataset.yAxisID === 'yNews' && i.dataset.type === 'bar') {
                                    totalNews += Number(i.parsed.y || 0);
                                }
                            });
                            return totalNews > 0 ? `Total News: ${totalNews}` : '';
                        }
                    }
                }
            },
            scales: {
                x: { stacked: true },
                yNews: {
                    type: 'linear',
                    position: 'left',
                    stacked: true,
                    beginAtZero: true,
                    title: { display: true, text: 'News Count' }
                },
                yViews: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Views' }
                }
            }
        }
    });

    // store for updates
    window.__combinedChart = chart;
    window.__combinedChartFilter = { type: 'period', period: 30 };
}

function buildCombinedDatasets(payload) {
    const labels = payload.labels || [];
    const cat = payload.category_datasets || [];
    const totalNews = payload.total_news || [];
    const views = payload.daily_views || [];

    const datasets = [];

    // Stacked bars per category
    cat.forEach((d) => {
        datasets.push({
            type: 'bar',
            label: d.label,
            data: d.data || new Array(labels.length).fill(0),
            backgroundColor: d.color || '#adb5bd',
            borderWidth: 0,
            stack: 'news',
            yAxisID: 'yNews'
        });
    });

    // Total news line
    datasets.push({
        type: 'line',
        label: 'Total News',
        data: totalNews,
        borderColor: '#0d6efd',
        backgroundColor: 'transparent',
        borderWidth: 2,
        pointRadius: 0,
        tension: 0.35,
        yAxisID: 'yNews'
    });

    // Views line (right axis)
    datasets.push({
        type: 'line',
        label: 'Views',
        data: views,
        borderColor: '#17a2b8',
        backgroundColor: 'rgba(23, 162, 184, 0.10)',
        borderWidth: 2,
        pointRadius: 0,
        tension: 0.35,
        yAxisID: 'yViews'
    });

    return datasets;
}

function updateCombinedChart(period) {
    window.__combinedChartFilter = { type: 'period', period: Number(period) || 30 };
    fetch(`<?= url('/admin/api/statistics/combined-chart') ?>?period=${period}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success || !res.data) return;
            const chart = window.__combinedChart;
            if (!chart) return;

            chart.data.labels = res.data.labels || [];
            chart.data.datasets = buildCombinedDatasets(res.data);
            chart.update();
        })
        .catch(err => console.error('Combined chart update error:', err));
}

function updateCombinedChartByRange(startDate, endDate) {
    window.__combinedChartFilter = { type: 'range', start_date: startDate, end_date: endDate };
    const qs = new URLSearchParams({ start_date: startDate, end_date: endDate });
    fetch(`<?= url('/admin/api/statistics/combined-chart') ?>?${qs.toString()}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success || !res.data) {
                if (res && res.error) {
                    showNotification(res.error, 'error');
                }
                return;
            }
            const chart = window.__combinedChart;
            if (!chart) return;

            chart.data.labels = res.data.labels || [];
            chart.data.datasets = buildCombinedDatasets(res.data);
            chart.update();
        })
        .catch(err => console.error('Combined chart update (range) error:', err));
}

function exportReport(type) {
    if (type === 'pdf') {
        // Dependency-free PDF: open print view, user can "Save as PDF"
        window.open('<?= url('/admin/istatistikler?print=1') ?>', '_blank');
        return;
    }
    const filter = window.__combinedChartFilter || { type: 'period', period: 30 };
    const qs = new URLSearchParams({ type: 'excel' });
    if (filter.type === 'range' && filter.start_date && filter.end_date) {
        qs.set('start_date', filter.start_date);
        qs.set('end_date', filter.end_date);
    } else {
        const period = Number(filter.period || 30);
        qs.set('period', String(period));
    }

    const url = `<?= url('/admin/api/statistics/export') ?>?${qs.toString()}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = `loomix-rapor-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

// Real-time stats update every 5 minutes
setInterval(function() {
    fetch('<?= url('/admin/api/statistics/live-stats') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update live stats
                const todayViews = document.querySelector('[data-stat="today_views"]');
                const totalViews = document.querySelector('[data-stat="total_views"]');
                const activeUsers = document.querySelector('[data-stat="active_users"]');

                if (todayViews) todayViews.textContent = Number(data.stats.today_views || 0).toLocaleString();
                if (totalViews) totalViews.textContent = Number(data.stats.total_views || 0).toLocaleString();
                if (activeUsers) activeUsers.textContent = Number(data.stats.active_users || 0).toLocaleString();
            }
        })
        .catch(error => {
            console.log('Live stats update failed:', error);
        });
}, 5 * 60 * 1000);
</script>
