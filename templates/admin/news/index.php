<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-newspaper me-2"></i>
        Haber Yönetimi
    </h1>
    <div class="page-actions">
        <a href="<?= url('/admin/haber-ekle') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Yeni Haber Ekle
        </a>
    </div>
</div>

<!-- Filters -->
<div class="data-table mb-4">
    <div class="p-3">
        <form method="GET" class="row g-3" id="newsFilters">
            <div class="col-md-4">
                <label class="form-label">Ara</label>
                <input type="text" class="form-control" name="search" 
                       value="<?= escape($currentFilters['search']) ?>" 
                       placeholder="Başlık veya içerik ara...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select class="form-select" name="category">
                    <option value="">Tüm Kategoriler</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" 
                                <?= $currentFilters['category'] == $category['id'] ? 'selected' : '' ?>>
                            <?= escape($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Durum</label>
                <select class="form-select" name="status">
                    <option value="">Tümü</option>
                    <option value="published" <?= $currentFilters['status'] === 'published' ? 'selected' : '' ?>>Yayında</option>
                    <option value="draft" <?= $currentFilters['status'] === 'draft' ? 'selected' : '' ?>>Taslak</option>
                    <option value="archived" <?= $currentFilters['status'] === 'archived' ? 'selected' : '' ?>>Arşiv</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrele
                </button>
                <a href="<?= url('/admin/haberler') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- News Table -->
<div class="data-table">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <h5 class="mb-0">
            Haberler 
            <span class="badge bg-secondary ms-2"><?= $pagination['total_count'] ?></span>
        </h5>
        
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-1"></i>Toplu İşlemler
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="bulkAction('publish')">
                    <i class="fas fa-eye me-2"></i>Yayınla
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="bulkAction('draft')">
                    <i class="fas fa-edit me-2"></i>Taslağa Al
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="bulkAction('archive')">
                    <i class="fas fa-archive me-2"></i>Arşivle
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                    <i class="fas fa-trash me-2"></i>Sil
                </a></li>
            </ul>
        </div>
    </div>
    
    <?php if (!empty($news)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th>Haber</th>
                        <th width="150">Kategori</th>
                        <th width="100">Durum</th>
                        <th width="120">Görüntülenme</th>
                        <th width="120">Tarih</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news as $newsItem): ?>
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input news-checkbox" type="checkbox" 
                                       value="<?= $newsItem['id'] ?>">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
                                <?php if ($newsItem['featured_image']): ?>
                                    <img src="<?= getImageUrl($newsItem['featured_image']) ?>" 
                                         alt="<?= escape($newsItem['title']) ?>" 
                                         class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="me-3 rounded bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="<?= url('/admin/haber-duzenle/' . $newsItem['id']) ?>" 
                                           class="text-decoration-none text-dark">
                                            <?= escape($newsItem['title']) ?>
                                        </a>
                                        <?php if ($newsItem['is_featured']): ?>
                                            <span class="badge bg-warning text-dark ms-1">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($newsItem['is_breaking']): ?>
                                            <span class="badge bg-danger ms-1">
                                                <i class="fas fa-bolt"></i>
                                            </span>
                                        <?php endif; ?>
                                    </h6>
                                    <div class="small text-muted">
                                        <?= truncateText(strip_tags($newsItem['summary'] ?? ''), 80) ?>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <span class="me-3">
                                            <i class="fas fa-user me-1"></i><?= escape($newsItem['author_name'] ?? 'Bilinmeyen') ?>
                                        </span>
                                        <?php if ($newsItem['reading_time']): ?>
                                            <span class="me-3">
                                                <i class="fas fa-clock me-1"></i><?= $newsItem['reading_time'] ?> dk
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark" 
                                  style="border-left: 3px solid <?= $newsItem['category_color'] ?>">
                                <?= escape($newsItem['category_name']) ?>
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
                            <span class="status-badge <?= $statusClass[$newsItem['status']] ?? '' ?>"
                                  data-news-id="<?= $newsItem['id'] ?>" 
                                  onclick="quickStatusChange(this, '<?= $newsItem['id'] ?>')">
                                <?= $statusText[$newsItem['status']] ?? ucfirst($newsItem['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-center">
                                <strong><?= number_format($newsItem['view_count']) ?></strong>
                                <div class="small text-muted">görüntülenme</div>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <div><strong><?= formatDate($newsItem['created_at'], 'd.m.Y') ?></strong></div>
                                <div class="text-muted"><?= formatDate($newsItem['created_at'], 'H:i') ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= url('/haber/' . $newsItem['slug']) ?>" 
                                   class="btn btn-outline-info" target="_blank" title="Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= url('/admin/haber-duzenle/' . $newsItem['id']) ?>" 
                                   class="btn btn-outline-primary" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="deleteNews(<?= $newsItem['id'] ?>, '<?= escape($newsItem['title']) ?>')" 
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
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="p-3 border-top">
                <nav aria-label="Haber sayfalama">
                    <ul class="pagination justify-content-center mb-0">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($currentFilters, ['page' => $pagination['current_page'] - 1])) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($currentFilters, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?= http_build_query(array_merge($currentFilters, ['page' => $pagination['current_page'] + 1])) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="text-center mt-2 text-muted small">
                    Toplam <?= number_format($pagination['total_count']) ?> haber, 
                    sayfa <?= $pagination['current_page'] ?> / <?= $pagination['total_pages'] ?>
                </div>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-newspaper fa-4x mb-3 opacity-25"></i>
            <h4>Haber bulunamadı</h4>
            <p>Aradığınız kriterlere uygun haber bulunmuyor.</p>
            <a href="<?= url('/admin/haber-ekle') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>İlk Haberi Ekle
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.news-checkbox');
    
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Update select all when individual checkboxes change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.news-checkbox:checked').length;
            selectAll.checked = checkedCount === checkboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        });
    });
    
    // Auto-submit filters on change
    document.querySelectorAll('#newsFilters select').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('newsFilters').submit();
        });
    });
});

// Quick status change
function quickStatusChange(element, newsId) {
    const currentStatus = element.textContent.trim();
    const statuses = {
        'Yayında': { next: 'draft', text: 'Taslak', class: 'status-draft' },
        'Taslak': { next: 'published', text: 'Yayında', class: 'status-published' },
        'Arşiv': { next: 'published', text: 'Yayında', class: 'status-published' }
    };
    
    if (!statuses[currentStatus]) return;
    
    const nextStatus = statuses[currentStatus];
    
    if (confirm(`Haberin durumunu "${nextStatus.text}" olarak değiştirmek istiyor musunuz?`)) {
        // Show loading
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Make AJAX request
        fetch(`<?= url('/admin/api/news/') ?>${newsId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: nextStatus.next
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update element
                element.className = `status-badge ${nextStatus.class}`;
                element.textContent = nextStatus.text;
                showNotification('Durum başarıyla güncellendi', 'success');
            } else {
                throw new Error(data.message || 'Güncelleme hatası');
            }
        })
        .catch(error => {
            // Revert changes
            element.className = `status-badge status-${currentStatus.toLowerCase()}`;
            element.textContent = currentStatus;
            showNotification('Durum güncellenirken hata oluştu: ' + error.message, 'error');
        });
    }
}

// Delete news
function deleteNews(id, title) {
    if (confirm(`"${title}" başlıklı haberi silmek istediğinizden emin misiniz?`)) {
        window.location.href = `/admin/haber-sil/${id}`;
    }
}

// Bulk actions
function bulkAction(action) {
    const selected = document.querySelectorAll('.news-checkbox:checked');
    
    if (selected.length === 0) {
        alert('Lütfen işlem yapmak istediğiniz haberleri seçin.');
        return;
    }
    
    const ids = Array.from(selected).map(cb => cb.value);
    const actionText = {
        'publish': 'yayınlamak',
        'draft': 'taslağa almak',
        'archive': 'arşivlemek',
        'delete': 'silmek'
    };
    
    if (confirm(`${selected.length} haberi ${actionText[action]} istediğinizden emin misiniz?`)) {
        // Make AJAX request for bulk action
        fetch('<?= url('/admin/api/news/bulk-action') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                ids: ids
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${selected.length} haber başarıyla güncellendi`, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Güncelleme hatası');
            }
        })
        .catch(error => {
            showNotification('İşlem sırasında hata oluştu: ' + error.message, 'error');
        });
    }
}
</script>
