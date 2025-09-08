<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tags me-2"></i>
        Etiket Yönetimi
    </h1>
    <div class="page-actions">
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
    <div class="p-3">
        <form class="row g-3" method="GET">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       value="<?= escape($currentFilters['search'] ?? '') ?>"
                       placeholder="Etiket ara...">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="sort">
                    <option value="name" <?= ($currentFilters['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Ad (A-Z)</option>
                    <option value="usage" <?= ($currentFilters['sort'] ?? '') === 'usage' ? 'selected' : '' ?>>Kullanım Sayısı</option>
                    <option value="recent" <?= ($currentFilters['sort'] ?? '') === 'recent' ? 'selected' : '' ?>>Son Eklenen</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="limit">
                    <option value="50" <?= ($currentFilters['limit'] ?? 50) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($currentFilters['limit'] ?? 50) == 100 ? 'selected' : '' ?>>100</option>
                    <option value="200" <?= ($currentFilters['limit'] ?? 50) == 200 ? 'selected' : '' ?>>200</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Filtrele
                    </button>
                    <a href="<?= url('/admin/etiketler') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tags Grid -->
<div class="data-table">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            Etiketler 
            <span class="badge bg-secondary ms-2"><?= count($tags) ?></span>
            <?php if (!empty($currentFilters['search'])): ?>
                <small class="text-muted ms-2">"<?= escape($currentFilters['search']) ?>" için sonuçlar</small>
            <?php endif; ?>
        </h5>
    </div>
    
    <?php if (!empty($tags)): ?>
        <div class="p-3">
            <div class="row">
                <?php foreach ($tags as $tag): ?>
                <div class="col-md-4 col-lg-3 mb-3">
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
                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= url('/admin/etiketler?page=' . $i . '&' . http_build_query(array_filter($currentFilters, fn($v) => $v !== 'page'))) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-tags fa-4x mb-3 opacity-25"></i>
            <h4>Henüz etiket eklenmemiş</h4>
            <p>İçerikleri kategorize etmek için etiketler oluşturun.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tagModal">
                <i class="fas fa-plus me-2"></i>İlk Etiketi Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="stats-value"><?= count($tags) ?></h3>
            <p class="stats-label">Toplam Etiket</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="stats-value"><?= array_sum(array_column($tags, 'usage_count')) ?></h3>
            <p class="stats-label">Toplam Kullanım</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value"><?= array_sum(array_column($tags, 'news_count')) ?></h3>
            <p class="stats-label">Etiketli Haber</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
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
.tag-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    transition: all 0.2s;
    height: 100%;
}

.tag-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.tag-card-body {
    padding: 1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.tag-stats {
    margin: 1rem 0;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.stat-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #495057;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0;
}

.tag-description {
    flex: 1;
}

.tag-meta {
    border-top: 1px solid #e9ecef;
    padding-top: 0.5rem;
    margin-top: auto;
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
    fetch(`/admin/api/tags/${id}`)
        .then(response => response.json())
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
            }
        })
        .catch(error => {
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
        fetch(`/admin/api/tags/${id}/delete`, {
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
                showNotification('Etiket başarıyla silindi', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Silme hatası');
            }
        })
        .catch(error => {
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
