<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-folder me-2"></i>
        Kategori Yönetimi
    </h1>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="fas fa-plus me-2"></i>Yeni Kategori
        </button>
    </div>
</div>

<!-- Categories Tree -->
<div class="data-table">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">
            Kategoriler 
            <span class="badge bg-secondary ms-2"><?= count($categories) ?></span>
        </h5>
    </div>
    
    <?php if (!empty($categories)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Kategori</th>
                        <th width="120">Renk</th>
                        <th width="100">Haber Sayısı</th>
                        <th width="100">Durum</th>
                        <th width="120">Sıralama</th>
                        <th width="150">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="categoriesTable">
                    <?php foreach ($categories as $category): ?>
                    <tr data-category-id="<?= $category['id'] ?>">
                        <td>
                            <button class="btn btn-sm btn-outline-secondary drag-handle" title="Sürükle">
                                <i class="fas fa-grip-vertical"></i>
                            </button>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($category['parent_name']): ?>
                                    <span class="text-muted me-2">└──</span>
                                <?php endif; ?>
                                
                                <div>
                                    <div class="d-flex align-items-center">
                                        <?php if ($category['icon']): ?>
                                            <i class="<?= $category['icon'] ?> me-2" style="color: <?= $category['color'] ?: '#007bff' ?>"></i>
                                        <?php endif; ?>
                                        <strong><?= escape($category['name']) ?></strong>
                                    </div>
                                    <div class="small text-muted">
                                        <span class="me-3"><?= escape($category['slug']) ?></span>
                                        <?php if ($category['parent_name']): ?>
                                            <span class="badge bg-light text-dark">Alt kategori: <?= escape($category['parent_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($category['description']): ?>
                                        <div class="small text-muted mt-1">
                                            <?= truncateText($category['description'], 80) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="color-box me-2" 
                                     style="width: 20px; height: 20px; background-color: <?= $category['color'] ?: '#007bff' ?>; border-radius: 4px;"></div>
                                <small><?= $category['color'] ?: '#007bff' ?></small>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= number_format($category['news_count']) ?></span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       <?= $category['is_active'] ? 'checked' : '' ?>
                                       onchange="toggleCategoryStatus(<?= $category['id'] ?>, this)">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" 
                                       value="<?= $category['sort_order'] ?>" 
                                       onchange="updateCategoryOrder(<?= $category['id'] ?>, this.value)"
                                       min="0" max="999">
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" 
                                        onclick="editCategory(<?= $category['id'] ?>)" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= url('/kategori/' . $category['slug']) ?>" 
                                   class="btn btn-outline-info" target="_blank" title="Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($category['news_count'] == 0): ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteCategory(<?= $category['id'] ?>, '<?= escape($category['name']) ?>')" 
                                            title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
            <h4>Henüz kategori eklenmemiş</h4>
            <p>İçerikleri organize etmek için kategoriler oluşturun.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fas fa-plus me-2"></i>İlk Kategoriyi Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Stats -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-folder"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($categories, fn($c) => !$c['parent_name'])) ?></h3>
            <p class="stats-label">Ana Kategori</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-sitemap"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($categories, fn($c) => $c['parent_name'])) ?></h3>
            <p class="stats-label">Alt Kategori</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-eye"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($categories, fn($c) => $c['is_active'])) ?></h3>
            <p class="stats-label">Aktif Kategori</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="stats-value"><?= array_sum(array_column($categories, 'news_count')) ?></h3>
            <p class="stats-label">Toplam Haber</p>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Yeni Kategori Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">
                            Kategori Adı <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="categoryName" name="name" 
                               placeholder="Kategori adını girin..." required maxlength="100">
                        <div class="invalid-feedback">
                            Kategori adı gereklidir.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categorySlug" class="form-label">URL Slug</label>
                        <input type="text" class="form-control" id="categorySlug" name="slug" 
                               placeholder="otomatik-olusturulacak" maxlength="100">
                        <div class="form-text">
                            Boş bırakılırsa kategori adından otomatik oluşturulacak
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="categoryDescription" name="description" 
                                  rows="3" placeholder="Kategori açıklaması..." maxlength="500"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryColor" class="form-label">Renk</label>
                                <input type="color" class="form-control form-control-color" 
                                       id="categoryColor" name="color" value="#007bff">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryIcon" class="form-label">
                                    İkon <small class="text-muted">(Font Awesome)</small>
                                </label>
                                <input type="text" class="form-control" id="categoryIcon" name="icon" 
                                       placeholder="fas fa-folder" maxlength="50">
                                <div class="form-text">
                                    Örnek: fas fa-laptop, fas fa-futbol
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoryParent" class="form-label">Ana Kategori</label>
                                <select class="form-select" id="categoryParent" name="parent_id">
                                    <option value="">Ana kategori olarak ekle</option>
                                    <?php foreach ($categories as $category): ?>
                                        <?php if (!$category['parent_name']): ?>
                                            <option value="<?= $category['id'] ?>"><?= escape($category['name']) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorySortOrder" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" id="categorySortOrder" 
                                       name="sort_order" value="0" min="0" max="999">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="categoryActive" 
                               name="is_active" value="1" checked>
                        <label class="form-check-label" for="categoryActive">
                            Aktif kategori
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    document.getElementById('categoryName').addEventListener('input', function() {
        const slug = createSlug(this.value);
        document.getElementById('categorySlug').value = slug;
    });
    
    // Form submission
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveCategory();
    });
    
    // Icon preview
    document.getElementById('categoryIcon').addEventListener('input', function() {
        const iconPreview = document.getElementById('iconPreview');
        if (iconPreview) {
            iconPreview.className = this.value || 'fas fa-folder';
        }
    });
    
    // Initialize sortable
    // new Sortable(document.getElementById('categoriesTable'), {
    //     handle: '.drag-handle',
    //     animation: 150,
    //     onEnd: function(evt) {
    //         updateCategoriesOrder();
    //     }
    // });
});

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

// Edit category
function editCategory(id) {
    // Get category data via AJAX
    fetch(`<?= url('/admin/api/categories/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const category = data.category;
                
                // Fill form
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryName').value = category.name;
                document.getElementById('categorySlug').value = category.slug;
                document.getElementById('categoryDescription').value = category.description || '';
                document.getElementById('categoryColor').value = category.color || '#007bff';
                document.getElementById('categoryIcon').value = category.icon || '';
                document.getElementById('categoryParent').value = category.parent_id || '';
                document.getElementById('categorySortOrder').value = category.sort_order;
                document.getElementById('categoryActive').checked = category.is_active == 1;
                
                // Update modal title
                document.getElementById('categoryModalTitle').textContent = 'Kategori Düzenle';
                
                // Show modal
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            }
        })
        .catch(error => {
            showNotification('Kategori bilgileri alınırken hata oluştu: ' + error.message, 'error');
        });
}

// Save category
function saveCategory() {
    const form = document.getElementById('categoryForm');
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
    
    // Save category
    fetch('<?= url('/admin/api/categories/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Kategori başarıyla kaydedildi!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Kategori kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Toggle category status
function toggleCategoryStatus(id, checkbox) {
    const status = checkbox.checked ? 1 : 0;
    
    fetch(`<?= url('/admin/api/categories/') ?>${id}/toggle-status`, {
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
            showNotification('Kategori durumu güncellendi', 'success');
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

// Update category order
function updateCategoryOrder(id, order) {
    fetch(`<?= url('/admin/api/categories/') ?>${id}/update-order`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ sort_order: parseInt(order) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Sıralama güncellendi', 'success');
        } else {
            throw new Error(data.message || 'Güncelleme hatası');
        }
    })
    .catch(error => {
        showNotification('Sıralama güncellenirken hata oluştu: ' + error.message, 'error');
    });
}

// Delete category
function deleteCategory(id, name) {
    if (confirm(`"${name}" kategorisini silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.`)) {
        fetch(`<?= url('/admin/api/categories/') ?>${id}/delete`, {
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
                showNotification('Kategori başarıyla silindi', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Silme hatası');
            }
        })
        .catch(error => {
            showNotification('Kategori silinirken hata oluştu: ' + error.message, 'error');
        });
    }
}

// Reset modal on close
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('categoryForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModalTitle').textContent = 'Yeni Kategori Ekle';
});
</script>
