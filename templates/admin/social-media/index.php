<?php
/**
 * Admin - Sosyal Medya Yönetimi
 * LooMix.Click
 */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Sosyal Medya Yönetimi</h1>
                    <p class="text-muted">Header ve footer'da görünecek sosyal medya linklerini yönetin</p>
                </div>
                <button class="btn btn-primary" onclick="showAddModal()">
                    <i class="fas fa-plus me-2"></i>Yeni Link Ekle
                </button>
            </div>

            <!-- Info Alert -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Bilgi:</strong> URL alanı boş bırakılan linkler gösterilmez. Header ve Footer için ayrı ayrı aktif/pasif yapabilirsiniz.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Social Media Links Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="socialLinksTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">Sıra</th>
                                    <th width="80">İkon</th>
                                    <th>Platform</th>
                                    <th>İsim</th>
                                    <th>URL</th>
                                    <th width="100" class="text-center">Aktif</th>
                                    <th width="100" class="text-center">Header</th>
                                    <th width="100" class="text-center">Footer</th>
                                    <th width="150" class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($links)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-share-alt fa-3x mb-3 d-block"></i>
                                            Henüz sosyal medya linki eklenmemiş.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($links as $link): ?>
                                        <tr data-link-id="<?= $link['id'] ?>">
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm order-input" 
                                                       value="<?= $link['display_order'] ?>" 
                                                       data-id="<?= $link['id'] ?>"
                                                       style="width: 60px;">
                                            </td>
                                            <td>
                                                <i class="<?= escape($link['icon']) ?> fa-2x" 
                                                   style="color: <?= escape($link['color'] ?? '#6c757d') ?>"></i>
                                            </td>
                                            <td>
                                                <code><?= escape($link['platform']) ?></code>
                                            </td>
                                            <td>
                                                <strong><?= escape($link['name']) ?></strong>
                                            </td>
                                            <td>
                                                <?php if (!empty($link['url']) && $link['url'] !== '#'): ?>
                                                    <a href="<?= escape($link['url']) ?>" target="_blank" class="text-decoration-none">
                                                        <?= escape(strlen($link['url']) > 40 ? substr($link['url'], 0, 40) . '...' : $link['url']) ?>
                                                        <i class="fas fa-external-link-alt fa-xs ms-1"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted"><em>URL yok</em></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           <?= $link['is_active'] ? 'checked' : '' ?>
                                                           onchange="toggleStatus(<?= $link['id'] ?>)"
                                                           id="active-<?= $link['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           <?= $link['show_in_header'] ? 'checked' : '' ?>
                                                           onchange="toggleHeader(<?= $link['id'] ?>)"
                                                           id="header-<?= $link['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           <?= $link['show_in_footer'] ? 'checked' : '' ?>
                                                           onchange="toggleFooter(<?= $link['id'] ?>)"
                                                           id="footer-<?= $link['id'] ?>">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editLink(<?= $link['id'] ?>)"
                                                        title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($link['platform'] !== 'rss'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteLink(<?= $link['id'] ?>)"
                                                            title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Sistem linki silinemez">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Header Önizleme</h6>
                        </div>
                        <div class="card-body">
                            <div class="social-links text-center">
                                <?php foreach ($links as $link): ?>
                                    <?php if ($link['is_active'] && $link['show_in_header'] && !empty($link['url']) && $link['url'] !== '#'): ?>
                                        <a href="#" class="text-muted me-3" title="<?= escape($link['name']) ?>">
                                            <i class="<?= escape($link['icon']) ?> fa-2x" style="color: <?= escape($link['color'] ?? '#6c757d') ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Footer Önizleme</h6>
                        </div>
                        <div class="card-body bg-dark">
                            <div class="social-links text-center">
                                <?php foreach ($links as $link): ?>
                                    <?php if ($link['is_active'] && $link['show_in_footer'] && !empty($link['url']) && $link['url'] !== '#'): ?>
                                        <a href="#" class="text-light me-3" title="<?= escape($link['name']) ?>">
                                            <i class="<?= escape($link['icon']) ?> fa-3x" style="color: <?= escape($link['color'] ?? '#ffffff') ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="socialLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Yeni Sosyal Medya Linki</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="socialLinkForm">
                    <input type="hidden" id="linkId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Platform Kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="platform" name="platform" 
                               placeholder="facebook, twitter, instagram..." required>
                        <small class="text-muted">Küçük harf, tire ve alt çizgi kullanabilirsiniz</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Görünen İsim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Facebook" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Font Awesome İkon <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="icon" name="icon" 
                                   placeholder="fab fa-facebook" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="showIconPreview()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            Örnek: fab fa-facebook, fab fa-x-twitter, fab fa-instagram
                            <a href="https://fontawesome.com/icons" target="_blank" class="ms-2">İkonları Gör</a>
                        </small>
                        <div id="iconPreview" class="mt-2" style="display:none;">
                            <i id="previewIcon" class="fa-3x"></i>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" id="url" name="url" 
                               placeholder="https://facebook.com/yourpage">
                        <small class="text-muted">Boş bırakırsanız link gösterilmez</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Renk (Hex)</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#1877F2">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gösterim Sırası</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="0">
                        <small class="text-muted">Küçük sayılar önce gösterilir</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_in_header" name="show_in_header" value="1" checked>
                                <label class="form-check-label" for="show_in_header">Header'da Göster</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" value="1" checked>
                                <label class="form-check-label" for="show_in_footer">Footer'da Göster</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="saveLink()">
                    <i class="fas fa-save me-2"></i>Kaydet
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= url('/admin/api/social-media') ?>';
const csrfToken = '<?= $csrfToken ?>';

// Modal'ı göster (yeni ekleme)
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Yeni Sosyal Medya Linki';
    document.getElementById('socialLinkForm').reset();
    document.getElementById('linkId').value = '';
    document.getElementById('iconPreview').style.display = 'none';
    new bootstrap.Modal(document.getElementById('socialLinkModal')).show();
}

// Link düzenle
async function editLink(id) {
    try {
        const response = await fetch(`${baseUrl}/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const link = data.link;
            document.getElementById('modalTitle').textContent = 'Sosyal Medya Linkini Düzenle';
            document.getElementById('linkId').value = link.id;
            document.getElementById('platform').value = link.platform;
            document.getElementById('platform').readOnly = true; // Platform değiştirilemez
            document.getElementById('name').value = link.name;
            document.getElementById('icon').value = link.icon;
            document.getElementById('url').value = link.url || '';
            document.getElementById('color').value = link.color || '#6c757d';
            document.getElementById('display_order').value = link.display_order;
            document.getElementById('is_active').checked = link.is_active == 1;
            document.getElementById('show_in_header').checked = link.show_in_header == 1;
            document.getElementById('show_in_footer').checked = link.show_in_footer == 1;
            
            new bootstrap.Modal(document.getElementById('socialLinkModal')).show();
        }
    } catch (error) {
        alert('Link yüklenirken hata oluştu: ' + error.message);
    }
}

// Link kaydet
async function saveLink() {
    const form = document.getElementById('socialLinkForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`${baseUrl}/save`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('socialLinkModal')).hide();
            location.reload();
        } else {
            alert('Hata: ' + (data.error || 'Bilinmeyen hata'));
        }
    } catch (error) {
        alert('Kaydetme hatası: ' + error.message);
    }
}

// Durum değiştir
async function toggleStatus(id) {
    try {
        const response = await fetch(`${baseUrl}/${id}/toggle-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=${csrfToken}`
        });
        
        const data = await response.json();
        if (data.success) {
            showToast('Durum güncellendi', 'success');
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        alert('Hata: ' + error.message);
    }
}

// Header durumu değiştir
async function toggleHeader(id) {
    try {
        const response = await fetch(`${baseUrl}/${id}/toggle-header`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=${csrfToken}`
        });
        
        const data = await response.json();
        if (data.success) {
            showToast('Header durumu güncellendi', 'success');
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        alert('Hata: ' + error.message);
    }
}

// Footer durumu değiştir
async function toggleFooter(id) {
    try {
        const response = await fetch(`${baseUrl}/${id}/toggle-footer`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=${csrfToken}`
        });
        
        const data = await response.json();
        if (data.success) {
            showToast('Footer durumu güncellendi', 'success');
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        alert('Hata: ' + error.message);
    }
}

// Link sil
async function deleteLink(id) {
    if (!confirm('Bu linki silmek istediğinizden emin misiniz?')) return;
    
    try {
        const response = await fetch(`${baseUrl}/${id}/delete`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `csrf_token=${csrfToken}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Hata: ' + (data.error || 'Bilinmeyen hata'));
        }
    } catch (error) {
        alert('Silme hatası: ' + error.message);
    }
}

// Sıra değişikliği
document.querySelectorAll('.order-input').forEach(input => {
    input.addEventListener('change', async function() {
        const id = this.dataset.id;
        const order = this.value;
        
        try {
            const response = await fetch(`${baseUrl}/${id}/update-order`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `csrf_token=${csrfToken}&order=${order}`
            });
            
            const data = await response.json();
            if (data.success) {
                showToast('Sıra güncellendi', 'success');
                setTimeout(() => location.reload(), 500);
            }
        } catch (error) {
            alert('Hata: ' + error.message);
        }
    });
});

// İkon önizleme
function showIconPreview() {
    const iconClass = document.getElementById('icon').value;
    const preview = document.getElementById('iconPreview');
    const previewIcon = document.getElementById('previewIcon');
    
    if (iconClass) {
        previewIcon.className = iconClass + ' fa-3x';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Toast bildirimi
function showToast(message, type = 'success') {
    // Basit toast bildirimi
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}
</script>

<style>
.form-control-color {
    width: 60px;
    height: 38px;
}

.order-input {
    text-align: center;
}

.form-check-input {
    cursor: pointer;
}

.social-links a {
    transition: transform 0.2s;
}

.social-links a:hover {
    transform: scale(1.2);
}
</style>

