<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-ad me-2"></i>
        Reklam Alanları
    </h1>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adZoneModal">
            <i class="fas fa-plus me-2"></i>Yeni Reklam Alanı
        </button>
    </div>
</div>

<!-- Ad Zones Table -->
<div class="data-table">
    <div class="p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Reklam Alanları 
                <span class="badge bg-secondary ms-2"><?= count($adZones) ?></span>
            </h5>
            
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-info" onclick="previewAds()">
                    <i class="fas fa-eye me-1"></i>Önizleme
                </button>
                <button type="button" class="btn btn-outline-success" onclick="testAds()">
                    <i class="fas fa-flask me-1"></i>Test Et
                </button>
            </div>
        </div>
    </div>
    
    <?php if (!empty($adZones)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reklam Alanı</th>
                        <th width="120">Tür</th>
                        <th width="100">Boyut</th>
                        <th width="120">Pozisyon</th>
                        <th width="100">Durum</th>
                        <th width="150">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adZones as $zone): ?>
                    <tr>
                        <td>
                            <div>
                                <h6 class="mb-1 fw-bold"><?= escape($zone['zone_name']) ?></h6>
                                <?php if ($zone['zone_description']): ?>
                                    <div class="text-muted small">
                                        <?= escape($zone['zone_description']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mt-2">
                                    <?php if ($zone['is_responsive']): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-mobile-alt me-1"></i>Responsive
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($zone['display_rules'])): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-filter me-1"></i>Kurallar
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $typeColors = [
                                'adsense' => 'bg-danger text-white',
                                'custom' => 'bg-info text-white',
                                'banner' => 'bg-success text-white'
                            ];
                            $typeTexts = [
                                'adsense' => 'AdSense',
                                'custom' => 'Özel Kod',
                                'banner' => 'Banner'
                            ];
                            ?>
                            <span class="badge <?= $typeColors[$zone['ad_type']] ?? 'bg-secondary' ?>">
                                <?= $typeTexts[$zone['ad_type']] ?? ucfirst($zone['ad_type']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($zone['width'] && $zone['height']): ?>
                                <div class="small">
                                    <strong><?= $zone['width'] ?>×<?= $zone['height'] ?></strong>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Otomatik</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <?= ucfirst($zone['position']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       <?= $zone['is_active'] ? 'checked' : '' ?>
                                       onchange="toggleAdZoneStatus(<?= $zone['id'] ?>, this)">
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" 
                                        onclick="editAdZone(<?= $zone['id'] ?>)" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" 
                                        onclick="previewAdZone(<?= $zone['id'] ?>)" title="Önizle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" 
                                        onclick="viewAdStats(<?= $zone['id'] ?>)" title="İstatistikler">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="deleteAdZone(<?= $zone['id'] ?>, '<?= escape($zone['zone_name']) ?>')" 
                                        title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-ad fa-4x mb-3 opacity-25"></i>
            <h4>Henüz reklam alanı eklenmemiş</h4>
            <p>Gelir elde etmek için reklam alanları oluşturun.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adZoneModal">
                <i class="fas fa-plus me-2"></i>İlk Reklam Alanını Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- AdSense Integration Status -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-danger">
                <i class="fab fa-google"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($adZones, fn($z) => $z['ad_type'] === 'adsense')) ?></h3>
            <p class="stats-label">AdSense Alanı</p>
            <div class="mt-2">
                <small class="<?= !empty(GOOGLE_ADSENSE_ID) ? 'text-success' : 'text-warning' ?>">
                    <i class="fas fa-<?= !empty(GOOGLE_ADSENSE_ID) ? 'check' : 'exclamation-triangle' ?> me-1"></i>
                    <?= !empty(GOOGLE_ADSENSE_ID) ? 'Entegre' : 'Kurulum Gerekli' ?>
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-code"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($adZones, fn($z) => $z['ad_type'] === 'custom')) ?></h3>
            <p class="stats-label">Özel Kod</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-image"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($adZones, fn($z) => $z['ad_type'] === 'banner')) ?></h3>
            <p class="stats-label">Banner</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-toggle-on"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($adZones, fn($z) => $z['is_active'])) ?></h3>
            <p class="stats-label">Aktif Alan</p>
        </div>
    </div>
</div>

<!-- Ad Zone Modal -->
<div class="modal fade" id="adZoneModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adZoneModalTitle">Yeni Reklam Alanı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adZoneForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="adZoneId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="zoneName" class="form-label">
                                    Alan Adı <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="zoneName" name="zone_name" 
                                       placeholder="header_banner" required maxlength="100">
                                <div class="form-text">Programatik erişim için kullanılır</div>
                                <div class="invalid-feedback">
                                    Alan adı gereklidir.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="adType" class="form-label">
                                    Reklam Türü <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="adType" name="ad_type" required onchange="handleAdTypeChange(this.value)">
                                    <option value="">Tür seçin...</option>
                                    <option value="adsense">Google AdSense</option>
                                    <option value="custom">Özel HTML/JS Kodu</option>
                                    <option value="banner">Banner Reklam</option>
                                </select>
                                <div class="invalid-feedback">
                                    Reklam türü seçimi zorunludur.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="zoneDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="zoneDescription" name="zone_description" 
                                  rows="2" placeholder="Reklam alanı açıklaması..." maxlength="500"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="position" class="form-label">Pozisyon</label>
                                <select class="form-select" id="position" name="position">
                                    <option value="">Pozisyon seçin...</option>
                                    <option value="header">Header</option>
                                    <option value="sidebar">Sidebar</option>
                                    <option value="content">İçerik Arası</option>
                                    <option value="footer">Footer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="width" class="form-label">Genişlik (px)</label>
                                <input type="number" class="form-control" id="width" name="width" 
                                       placeholder="728" min="1" max="2000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="height" class="form-label">Yükseklik (px)</label>
                                <input type="number" class="form-control" id="height" name="height" 
                                       placeholder="90" min="1" max="1000">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adCode" class="form-label">
                            Reklam Kodu <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="adCode" name="ad_code" rows="6" 
                                  placeholder="AdSense kodu veya HTML/JS kodunu buraya yapıştırın..." required></textarea>
                        <div class="form-text" id="adCodeHelp">
                            Google AdSense kodunu buraya yapıştırın
                        </div>
                        <div class="invalid-feedback">
                            Reklam kodu gereklidir.
                        </div>
                    </div>
                    
                    <!-- Banner specific fields -->
                    <div id="bannerFields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bannerImage" class="form-label">Banner Resmi</label>
                                    <input type="url" class="form-control" id="bannerImage" 
                                           placeholder="https://example.com/banner.jpg">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bannerLink" class="form-label">Bağlantı URL</label>
                                    <input type="url" class="form-control" id="bannerLink" 
                                           placeholder="https://example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="isResponsive" 
                                       name="is_responsive" value="1" checked>
                                <label class="form-check-label" for="isResponsive">
                                    <i class="fas fa-mobile-alt me-1"></i>
                                    Responsive reklam
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="isActive" 
                                       name="is_active" value="1" checked>
                                <label class="form-check-label" for="isActive">
                                    Aktif reklam alanı
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Display Rules -->
                    <div class="mb-3">
                        <label class="form-label">Görüntüleme Kuralları</label>
                        <div class="border rounded p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label small">Sayfalar</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnHome" checked>
                                        <label class="form-check-label" for="showOnHome">Ana Sayfa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnNews" checked>
                                        <label class="form-check-label" for="showOnNews">Haber Sayfaları</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnCategory" checked>
                                        <label class="form-check-label" for="showOnCategory">Kategori Sayfaları</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Cihazlar</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnDesktop" checked>
                                        <label class="form-check-label" for="showOnDesktop">Masaüstü</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showOnMobile" checked>
                                        <label class="form-check-label" for="showOnMobile">Mobil</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- AdSense Setup Guide Modal -->
<div class="modal fade" id="adsenseGuideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Google AdSense Kurulum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>AdSense Kurulum Adımları</h6>
                    <ol class="mb-0">
                        <li>Google AdSense hesabınızdan reklam kodu alın</li>
                        <li>config.php dosyasında GOOGLE_ADSENSE_ID ayarlayın</li>
                        <li>Reklam kodunu aşağıdaki alana yapıştırın</li>
                        <li>Test edin ve yayınlayın</li>
                    </ol>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Mevcut AdSense ID</label>
                    <input type="text" class="form-control" value="<?= GOOGLE_ADSENSE_ID ?>" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission
    document.getElementById('adZoneForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAdZone();
    });
    
    // Show AdSense guide if no AdSense ID
    <?php if (empty(GOOGLE_ADSENSE_ID)): ?>
    setTimeout(() => {
        if (<?= count($adZones) ?> === 0) {
            new bootstrap.Modal(document.getElementById('adsenseGuideModal')).show();
        }
    }, 2000);
    <?php endif; ?>
});

function handleAdTypeChange(type) {
    const adCode = document.getElementById('adCode');
    const adCodeHelp = document.getElementById('adCodeHelp');
    const bannerFields = document.getElementById('bannerFields');
    
    bannerFields.style.display = 'none';
    
    switch(type) {
        case 'adsense':
            adCode.placeholder = 'Google AdSense kodunu buraya yapıştırın...';
            adCodeHelp.textContent = 'Google AdSense kodunu buraya yapıştırın';
            break;
        case 'custom':
            adCode.placeholder = 'HTML/JavaScript kodunu buraya yazın...';
            adCodeHelp.textContent = 'Özel HTML veya JavaScript kodunu buraya yazın';
            break;
        case 'banner':
            adCode.placeholder = 'JSON formatında banner bilgilerini girin...';
            adCodeHelp.textContent = 'Banner reklam için JSON verisi';
            bannerFields.style.display = 'block';
            break;
    }
}

function editAdZone(id) {
    fetch(`<?= url('/admin/api/ad-zones/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const zone = data.zone;
                
                // Fill form
                document.getElementById('adZoneId').value = zone.id;
                document.getElementById('zoneName').value = zone.zone_name;
                document.getElementById('zoneDescription').value = zone.zone_description || '';
                document.getElementById('adType').value = zone.ad_type;
                document.getElementById('position').value = zone.position || '';
                document.getElementById('width').value = zone.width || '';
                document.getElementById('height').value = zone.height || '';
                document.getElementById('adCode').value = zone.ad_code || '';
                document.getElementById('isResponsive').checked = zone.is_responsive == 1;
                document.getElementById('isActive').checked = zone.is_active == 1;
                
                // Handle ad type specific fields
                handleAdTypeChange(zone.ad_type);
                
                // Update modal title
                document.getElementById('adZoneModalTitle').textContent = 'Reklam Alanı Düzenle';
                
                // Show modal
                new bootstrap.Modal(document.getElementById('adZoneModal')).show();
            }
        })
        .catch(error => {
            showNotification('Reklam alanı bilgileri alınırken hata oluştu: ' + error.message, 'error');
        });
}

function saveAdZone() {
    const form = document.getElementById('adZoneForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Show loading
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...';
    submitBtn.disabled = true;
    
    // Get form data
    const formData = new FormData(form);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    // Add display rules
    data.display_rules = JSON.stringify({
        pages: [
            ...(document.getElementById('showOnHome').checked ? ['home'] : []),
            ...(document.getElementById('showOnNews').checked ? ['news'] : []),
            ...(document.getElementById('showOnCategory').checked ? ['category'] : [])
        ],
        devices: [
            ...(document.getElementById('showOnDesktop').checked ? ['desktop'] : []),
            ...(document.getElementById('showOnMobile').checked ? ['mobile'] : [])
        ]
    });
    
    // For banner type, create JSON ad_code
    if (data.ad_type === 'banner') {
        const bannerData = {
            image_url: document.getElementById('bannerImage').value,
            link_url: document.getElementById('bannerLink').value,
            alt_text: data.zone_name
        };
        data.ad_code = JSON.stringify(bannerData);
    }
    
    // Save ad zone
    fetch('<?= url('/admin/api/ad-zones/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reklam alanı başarıyla kaydedildi!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('adZoneModal')).hide();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Reklam alanı kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function toggleAdZoneStatus(id, checkbox) {
    const status = checkbox.checked ? 1 : 0;
    
    fetch(`<?= url('/admin/api/ad-zones/') ?>${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            is_active: status,
            csrf_token: document.querySelector('[name="csrf_token"]').value 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Reklam alanı durumu güncellendi', 'success');
        } else {
            // Revert checkbox
            checkbox.checked = !checkbox.checked;
            throw new Error(data.message || 'Güncelleme hatası');
        }
    })
    .catch(error => {
        showNotification('Durum güncellenirken hata oluştu: ' + error.message, 'error');
    });
}

function deleteAdZone(id, name) {
    if (confirm(`"${name}" reklam alanını silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.`)) {
        fetch(`<?= url('/admin/api/ad-zones/') ?>${id}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                csrf_token: document.querySelector('[name="csrf_token"]').value 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Reklam alanı başarıyla silindi', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Silme hatası');
            }
        })
        .catch(error => {
            showNotification('Reklam alanı silinirken hata oluştu: ' + error.message, 'error');
        });
    }
}

function previewAdZone(id) {
    window.open(`<?= url('/admin/preview-ad-zone/') ?>${id}`, 'adPreview', 'width=800,height=600');
}

function viewAdStats(id) {
    // Implement ad statistics view
    showNotification('Reklam istatistikleri özelliği yakında eklenecek', 'info');
}

function previewAds() {
    window.open('<?= url('/') ?>', 'sitePreview', 'width=1200,height=800');
}

function testAds() {
    fetch('<?= url('/admin/api/ad-zones/test') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Tüm reklam alanları test edildi. Sonuçlar konsola yazıldı.', 'success');
            }
        });
}

// Reset modal on close
document.getElementById('adZoneModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('adZoneForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('adZoneId').value = '';
    document.getElementById('adZoneModalTitle').textContent = 'Yeni Reklam Alanı';
    document.getElementById('bannerFields').style.display = 'none';
});
</script>
