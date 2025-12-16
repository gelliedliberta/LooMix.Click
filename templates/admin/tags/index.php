<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tags me-2"></i>
        Etiket Yönetimi
    </h1>
    <div class="page-actions d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tagModal">
            <i class="fas fa-plus me-2"></i>Yeni Etiket
        </button>
        <button type="button" class="btn btn-outline-warning" onclick="cleanUnusedTags()">
            <i class="fas fa-trash-alt me-2"></i>Kullanılmayanları Temizle
        </button>
    </div>
</div>

<!-- Filter & Search -->
<div class="data-table mb-4">
    <div class="p-3 bg-light border-bottom">
        <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Filtreleme & Arama</h6>
    </div>
    <div class="p-3">
        <form class="row g-3" method="GET" action="<?= url('/admin/etiketler') ?>">
            <div class="col-12 col-md-5">
                <label for="tagSearch" class="form-label small text-muted">Arama</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="tagSearch" name="search" 
                           value="<?= escape($currentFilters['search'] ?? '') ?>"
                           placeholder="Etiket adı veya açıklama ara...">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label for="tagSort" class="form-label small text-muted">Sıralama</label>
                <select class="form-select" id="tagSort" name="sort">
                    <option value="name" <?= ($currentFilters['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Ad (A-Z)</option>
                    <option value="usage" <?= ($currentFilters['sort'] ?? '') === 'usage' ? 'selected' : '' ?>>Kullanım Sayısı</option>
                    <option value="recent" <?= ($currentFilters['sort'] ?? '') === 'recent' ? 'selected' : '' ?>>Son Eklenen</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="tagLimit" class="form-label small text-muted">Göster</label>
                <select class="form-select" id="tagLimit" name="limit">
                    <option value="50" <?= ($currentFilters['limit'] ?? 50) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($currentFilters['limit'] ?? 50) == 100 ? 'selected' : '' ?>>100</option>
                    <option value="200" <?= ($currentFilters['limit'] ?? 50) == 200 ? 'selected' : '' ?>>200</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Ara
                    </button>
                    <a href="<?= url('/admin/etiketler') ?>" class="btn btn-outline-secondary" title="Filtreleri Temizle">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tags Grid -->
<div class="data-table">
    <div class="p-3 border-bottom bg-light">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Etiketler 
                <span class="badge bg-primary ms-2"><?= count($tags) ?></span>
                <?php if (!empty($currentFilters['search'])): ?>
                    <small class="text-muted ms-2">"<?= escape($currentFilters['search']) ?>" için sonuçlar</small>
                <?php endif; ?>
            </h5>
            <?php if ($pagination['total_count'] > 0): ?>
                <small class="text-muted">
                    Toplam <?= $pagination['total_count'] ?> etiket
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($tags)): ?>
        <div class="p-3">
            <div class="row g-3">
                <?php foreach ($tags as $tag): ?>
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="tag-card" data-tag-id="<?= $tag['id'] ?>">
                        <div class="tag-card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="tag-name mb-0">
                                    <i class="fas fa-tag me-1"></i>
                                    <?= escape($tag['name']) ?>
                                </h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" onclick="editTag(<?= $tag['id'] ?>)">
                                                <i class="fas fa-edit me-2"></i>Düzenle
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= url('/etiket/' . $tag['slug']) ?>" target="_blank">
                                                <i class="fas fa-eye me-2"></i>Görüntüle
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" 
                                                    onclick="deleteTag(<?= $tag['id'] ?>, '<?= escape($tag['name']) ?>')">
                                                <i class="fas fa-trash me-2"></i>Sil
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="tag-stats">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-value"><?= $tag['usage_count'] ?></div>
                                        <div class="stat-label">Kullanım</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-value"><?= $tag['news_count'] ?></div>
                                        <div class="stat-label">Haber</div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($tag['description']): ?>
                                <div class="tag-description mt-2">
                                    <small class="text-muted">
                                        <?= truncateText($tag['description'], 60) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="tag-meta mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= formatDate($tag['created_at'], 'd.m.Y') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Sayfa navigasyonu">
                        <ul class="pagination">
                            <!-- Previous Button -->
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('/admin/etiketler?page=' . ($pagination['current_page'] - 1) . (!empty($currentFilters['search']) ? '&search=' . urlencode($currentFilters['search']) : '') . (!empty($currentFilters['sort']) ? '&sort=' . $currentFilters['sort'] : '') . (!empty($currentFilters['limit']) ? '&limit=' . $currentFilters['limit'] : '')) ?>" aria-label="Önceki">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php 
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): 
                            ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('/admin/etiketler?page=' . $i . (!empty($currentFilters['search']) ? '&search=' . urlencode($currentFilters['search']) : '') . (!empty($currentFilters['sort']) ? '&sort=' . $currentFilters['sort'] : '') . (!empty($currentFilters['limit']) ? '&limit=' . $currentFilters['limit'] : '')) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next Button -->
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= url('/admin/etiketler?page=' . ($pagination['current_page'] + 1) . (!empty($currentFilters['search']) ? '&search=' . urlencode($currentFilters['search']) : '') . (!empty($currentFilters['sort']) ? '&sort=' . $currentFilters['sort'] : '') . (!empty($currentFilters['limit']) ? '&limit=' . $currentFilters['limit'] : '')) ?>" aria-label="Sonraki">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <h4>Henüz etiket eklenmemiş</h4>
            <p>İçerikleri kategorize etmek için etiketler oluşturun.</p>
            <button type="button" class="btn btn-primary btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#tagModal">
                <i class="fas fa-plus me-2"></i>İlk Etiketi Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Stats -->
<div class="row g-3 mt-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="stats-value"><?= count($tags) ?></h3>
            <p class="stats-label">Toplam Etiket</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="stats-value"><?= array_sum(array_column($tags, 'usage_count')) ?></h3>
            <p class="stats-label">Toplam Kullanım</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value"><?= array_sum(array_column($tags, 'news_count')) ?></h3>
            <p class="stats-label">Etiketli Haber</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning text-dark">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($tags, fn($t) => $t['usage_count'] === 0)) ?></h3>
            <p class="stats-label">Kullanılmayan</p>
        </div>
    </div>
</div>

<!-- Tag Modal -->
<div class="modal fade" id="tagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tagModalTitle">Yeni Etiket Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tagForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="tagId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="tagName" class="form-label">
                            Etiket Adı <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tagName" name="name" 
                               placeholder="Etiket adını girin..." required maxlength="50">
                        <div class="form-text">
                            <span id="nameCounter">0</span>/50 karakter
                        </div>
                        <div class="invalid-feedback">
                            Etiket adı gereklidir.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tagSlug" class="form-label">URL Slug</label>
                        <input type="text" class="form-control" id="tagSlug" name="slug" 
                               placeholder="otomatik-olusturulacak" maxlength="50">
                        <div class="form-text">
                            Boş bırakılırsa etiket adından otomatik oluşturulacak
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tagDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="tagDescription" name="description" 
                                  rows="3" placeholder="Etiket açıklaması..." maxlength="500"></textarea>
                        <div class="form-text">
                            <span id="descCounter">0</span>/500 karakter
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tagColor" class="form-label">Renk</label>
                        <input type="color" class="form-control form-control-color" 
                               id="tagColor" name="color" value="#6c757d">
                        <div class="form-text">
                            Etiket görünümü için renk seçin
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="tagActive" 
                               name="is_active" value="1" checked>
                        <label class="form-check-label" for="tagActive">
                            Aktif etiket
                        </label>
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

<style>
/* Tag Cards */
.tag-card {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    transition: all 0.3s ease;
    height: 100%;
    background: white;
    overflow: hidden;
}

.tag-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-3px);
    border-color: var(--primary-color);
}

.tag-card-body {
    padding: 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.tag-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark-color);
    word-break: break-word;
}

.tag-stats {
    margin: 1rem 0;
    padding: 0.75rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.tag-description {
    flex: 1;
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.5;
}

.tag-meta {
    border-top: 1px solid #e9ecef;
    padding-top: 0.75rem;
    margin-top: auto;
    font-size: 0.8rem;
}

/* Page Actions */
.page-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Filter Section */
.input-group-text {
    background-color: #f8f9fa;
    border-color: #ddd;
}

.form-label.small {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .page-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .page-actions .btn {
        width: 100%;
    }
    
    .tag-card {
        margin-bottom: 1rem;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #adb5bd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    document.getElementById('tagName').addEventListener('input', function() {
        const slug = createSlug(this.value);
        document.getElementById('tagSlug').value = slug;
    });
    
    // Character counters
    setupCharacterCounter('tagName', 'nameCounter', 50);
    setupCharacterCounter('tagDescription', 'descCounter', 500);
    
    // Form submission
    document.getElementById('tagForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveTag();
    });
});

// Character counter
function setupCharacterCounter(inputId, counterId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    
    input.addEventListener('input', function() {
        counter.textContent = this.value.length;
        
        if (this.value.length > maxLength * 0.9) {
            counter.className = 'text-warning';
        } else if (this.value.length > maxLength * 0.95) {
            counter.className = 'text-danger';
        } else {
            counter.className = '';
        }
    });
}

// Slug generation
function createSlug(text) {
    return text
        .toLowerCase()
        .replace(/[çğıöşü]/g, function(match) {
            const map = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u' };
            return map[match];
        })
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// Edit tag
function editTag(id) {
    fetch(`<?= url('/admin/api/tags/') ?>${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('API yanıt vermedi');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const tag = data.tag;
                
                // Fill form
                document.getElementById('tagId').value = tag.id;
                document.getElementById('tagName').value = tag.name;
                document.getElementById('tagSlug').value = tag.slug;
                document.getElementById('tagDescription').value = tag.description || '';
                document.getElementById('tagColor').value = tag.color || '#6c757d';
                document.getElementById('tagActive').checked = tag.is_active == 1;
                
                // Update counters
                document.getElementById('nameCounter').textContent = tag.name.length;
                document.getElementById('descCounter').textContent = (tag.description || '').length;
                
                // Update modal title
                document.getElementById('tagModalTitle').textContent = 'Etiket Düzenle';
                
                // Show modal
                new bootstrap.Modal(document.getElementById('tagModal')).show();
            } else {
                throw new Error(data.message || 'Etiket bilgileri alınamadı');
            }
        })
        .catch(error => {
            console.error('API Error:', error);
            showNotification('Etiket bilgileri alınırken hata oluştu: ' + error.message, 'error');
        });
}

// Save tag
function saveTag() {
    const form = document.getElementById('tagForm');
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
    
    // Save tag
    fetch('<?= url('/admin/api/tags/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Etiket başarıyla kaydedildi!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('tagModal')).hide();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Etiket kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Delete tag
function deleteTag(id, name) {
    if (confirm(`"${name}" etiketini silmek istediğinizden emin misiniz?\n\nBu etiket tüm haberlerden kaldırılacak.`)) {
        fetch(`<?= url('/admin/api/tags/') ?>${id}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                csrf_token: document.querySelector('[name="csrf_token"]').value 
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('API yanıt vermedi');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('Etiket başarıyla silindi', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Silme hatası');
            }
        })
        .catch(error => {
            console.error('Delete Error:', error);
            showNotification('Etiket silinirken hata oluştu: ' + error.message, 'error');
        });
    }
}

// Clean unused tags
function cleanUnusedTags() {
    if (confirm('Kullanılmayan etiketleri silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.')) {
        fetch('<?= url('/admin/api/tags/clean-unused') ?>', {
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
                showNotification(`${data.deleted_count} kullanılmayan etiket silindi`, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Temizleme hatası');
            }
        })
        .catch(error => {
            showNotification('Etiketler temizlenirken hata oluştu: ' + error.message, 'error');
        });
    }
}

// Reset modal on close
document.getElementById('tagModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('tagForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('tagId').value = '';
    document.getElementById('tagModalTitle').textContent = 'Yeni Etiket Ekle';
    document.getElementById('nameCounter').textContent = '0';
    document.getElementById('descCounter').textContent = '0';
});
</script>
