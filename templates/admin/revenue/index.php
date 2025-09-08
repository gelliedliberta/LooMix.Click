<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-line me-2"></i>
        Gelir Raporları
    </h1>
    <div class="page-actions">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-2"></i>Verileri Yenile
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Rapor İndir
            </button>
        </div>
    </div>
</div>

<!-- Date Range Filter -->
<div class="data-table mb-4">
    <div class="p-3">
        <form class="row g-3" id="dateFilter">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?= $currentFilters['start_date'] ?? date('Y-m-01') ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?= $currentFilters['end_date'] ?? date('Y-m-d') ?>">
            </div>
            <div class="col-md-3">
                <label for="report_type" class="form-label">Rapor Türü</label>
                <select class="form-select" id="report_type" name="report_type">
                    <option value="daily" <?= ($currentFilters['report_type'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Günlük</option>
                    <option value="weekly" <?= ($currentFilters['report_type'] ?? 'daily') === 'weekly' ? 'selected' : '' ?>>Haftalık</option>
                    <option value="monthly" <?= ($currentFilters['report_type'] ?? 'daily') === 'monthly' ? 'selected' : '' ?>>Aylık</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filtrele
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Revenue Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3 class="stats-value">$<?= number_format($revenue['total_revenue'], 2) ?></h3>
            <p class="stats-label">Toplam Gelir</p>
            <div class="stats-change text-success">
                <i class="fas fa-arrow-up"></i>
                %<?= $revenue['revenue_change'] ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-mouse-pointer"></i>
            </div>
            <h3 class="stats-value"><?= number_format($revenue['total_clicks']) ?></h3>
            <p class="stats-label">Toplam Tıklama</p>
            <div class="stats-change text-info">
                <i class="fas fa-percentage"></i>
                CTR: %<?= number_format($revenue['ctr'], 2) ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-eye"></i>
            </div>
            <h3 class="stats-value"><?= number_format($revenue['total_impressions']) ?></h3>
            <p class="stats-label">Toplam Gösterim</p>
            <div class="stats-change text-warning">
                <i class="fas fa-chart-bar"></i>
                RPM: $<?= number_format($revenue['rpm'], 2) ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-danger">
                <i class="fas fa-ad"></i>
            </div>
            <h3 class="stats-value"><?= count($revenue['active_zones']) ?></h3>
            <p class="stats-label">Aktif Reklam</p>
            <div class="stats-change text-muted">
                <i class="fas fa-chart-pie"></i>
                <?= $revenue['top_zone'] ?>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="data-table mb-4">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-chart-area me-2"></i>
            Gelir Trendi
        </h5>
    </div>
    <div class="p-3">
        <canvas id="revenueChart" width="400" height="100"></canvas>
    </div>
</div>

<!-- Ad Zones Performance -->
<div class="data-table mb-4">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-ad me-2"></i>
            Reklam Alanı Performansı
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reklam Alanı</th>
                    <th width="120">Tür</th>
                    <th width="100">Gelir</th>
                    <th width="100">Tıklama</th>
                    <th width="100">Gösterim</th>
                    <th width="80">CTR</th>
                    <th width="80">RPM</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($revenue['zone_performance'])): ?>
                    <?php foreach ($revenue['zone_performance'] as $zone): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="zone-indicator me-2" 
                                     style="width: 12px; height: 12px; background: <?= $zone['color'] ?>; border-radius: 2px;"></div>
                                <div>
                                    <strong><?= escape($zone['zone_name']) ?></strong>
                                    <div class="small text-muted"><?= escape($zone['position']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= $zone['ad_type'] ?></span>
                        </td>
                        <td>
                            <strong class="text-success">$<?= number_format($zone['revenue'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= number_format($zone['clicks']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark"><?= number_format($zone['impressions']) ?></span>
                        </td>
                        <td>
                            <span class="<?= $zone['ctr'] > 2 ? 'text-success' : ($zone['ctr'] > 1 ? 'text-warning' : 'text-danger') ?>">
                                %<?= number_format($zone['ctr'], 2) ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-primary">$<?= number_format($zone['rpm'], 2) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted p-4">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-25"></i>
                            <div>Henüz gelir verisi bulunmuyor</div>
                            <small>Reklam alanları aktif olduktan sonra veriler görünecek</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Top Pages by Revenue -->
<div class="data-table">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-star me-2"></i>
            En Çok Gelir Getiren Sayfalar
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Sayfa</th>
                    <th width="100">Görüntülenme</th>
                    <th width="100">Gelir</th>
                    <th width="80">RPM</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($revenue['top_pages'])): ?>
                    <?php foreach ($revenue['top_pages'] as $page): ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= escape($page['title']) ?></strong>
                                <div class="small text-muted">
                                    <a href="<?= $page['url'] ?>" target="_blank" class="text-decoration-none">
                                        <?= truncateText($page['url'], 60) ?>
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= number_format($page['page_views']) ?></span>
                        </td>
                        <td>
                            <strong class="text-success">$<?= number_format($page['revenue'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="text-primary">$<?= number_format($page['rpm'], 2) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted p-4">
                            <i class="fas fa-file-alt fa-3x mb-3 opacity-25"></i>
                            <div>Sayfa bazlı gelir verisi bulunmuyor</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize revenue chart
    initRevenueChart();
    
    // Date filter form
    document.getElementById('dateFilter').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.search = params.toString();
    });
});

// Initialize revenue chart
function initRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const chartData = <?= json_encode($revenue['chart_data'] ?? []) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: 'Gelir ($)',
                data: chartData.map(item => item.revenue),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Tıklama',
                data: chartData.map(item => item.clicks),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Gelir ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Tıklama Sayısı'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Gelir: $' + context.raw.toFixed(2);
                            } else {
                                return 'Tıklama: ' + context.raw;
                            }
                        }
                    }
                }
            }
        }
    });
}

// Refresh data
function refreshData() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Yenileniyor...';
    btn.disabled = true;
    
    fetch('<?= url('/admin/api/revenue/refresh') ?>', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Gelir verileri yenilendi!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Yenileme hatası');
        }
    })
    .catch(error => {
        showNotification('Veriler yenilenirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Export report
function exportReport() {
    const params = new URLSearchParams(new FormData(document.getElementById('dateFilter')));
    window.open('/admin/api/revenue/export?' + params.toString(), '_blank');
}
</script>

<style>
.zone-indicator {
    display: inline-block;
    border-radius: 2px;
}

.stats-change {
    font-size: 0.875rem;
    font-weight: 500;
}

.stats-change i {
    font-size: 0.75rem;
}
</style>
