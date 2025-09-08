<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users me-2"></i>
        Kullanıcı Yönetimi
    </h1>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="fas fa-user-plus me-2"></i>Yeni Kullanıcı
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="data-table">
    <div class="p-3 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Kullanıcılar 
                <span class="badge bg-secondary ms-2"><?= count($users) ?></span>
            </h5>
            
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>Dışa Aktar
                </button>
                <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th width="120">Rol</th>
                        <th width="120">Durum</th>
                        <th width="150">Son Giriş</th>
                        <th width="100">Giriş Sayısı</th>
                        <th width="120">Üyelik Tarihi</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?= getImageUrl($user['avatar']) ?>" 
                                             alt="<?= escape($user['full_name']) ?>" 
                                             class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; font-weight: 600;">
                                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= escape($user['full_name']) ?></div>
                                    <div class="text-muted small">
                                        <i class="fas fa-at me-1"></i><?= escape($user['username']) ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-envelope me-1"></i><?= escape($user['email']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $roleColors = [
                                'admin' => 'bg-danger',
                                'editor' => 'bg-warning text-dark',
                                'author' => 'bg-info'
                            ];
                            $roleTexts = [
                                'admin' => 'Yönetici',
                                'editor' => 'Editör',
                                'author' => 'Yazar'
                            ];
                            ?>
                            <span class="badge <?= $roleColors[$user['role']] ?? 'bg-secondary' ?>">
                                <?= $roleTexts[$user['role']] ?? ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       <?= $user['is_active'] ? 'checked' : '' ?>
                                       onchange="toggleUserStatus(<?= $user['id'] ?>, this)">
                                <label class="form-check-label small">
                                    <?= $user['is_active'] ? 'Aktif' : 'Pasif' ?>
                                </label>
                            </div>
                        </td>
                        <td>
                            <?php if ($user['last_login']): ?>
                                <div class="small">
                                    <div><?= formatDate($user['last_login'], 'd.m.Y') ?></div>
                                    <div class="text-muted"><?= formatDate($user['last_login'], 'H:i') ?></div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Hiç giriş yapmamış</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark"><?= number_format($user['login_count']) ?></span>
                        </td>
                        <td>
                            <div class="small">
                                <?= formatDate($user['created_at'], 'd.m.Y') ?>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" 
                                        onclick="editUser(<?= $user['id'] ?>)" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" 
                                        onclick="viewUserStats(<?= $user['id'] ?>)" title="İstatistikler">
                                    <i class="fas fa-chart-bar"></i>
                                </button>
                                <?php if ($user['id'] != ($_SESSION['admin_user']['id'] ?? 0)): ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= escape($user['full_name']) ?>')" 
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
            <i class="fas fa-users fa-4x mb-3 opacity-25"></i>
            <h4>Henüz kullanıcı eklenmemiş</h4>
            <p>Yönetim için yeni kullanıcılar ekleyin.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                <i class="fas fa-user-plus me-2"></i>İlk Kullanıcıyı Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- User Stats -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-danger">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></h3>
            <p class="stats-label">Yönetici</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-user-edit"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($users, fn($u) => $u['role'] === 'editor')) ?></h3>
            <p class="stats-label">Editör</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-user-edit"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($users, fn($u) => $u['role'] === 'author')) ?></h3>
            <p class="stats-label">Yazar</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-user-check"></i>
            </div>
            <h3 class="stats-value"><?= count(array_filter($users, fn($u) => $u['is_active'])) ?></h3>
            <p class="stats-label">Aktif Kullanıcı</p>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Yeni Kullanıcı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-3">
                        <label for="userFullName" class="form-label">
                            Ad Soyad <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="userFullName" name="full_name" 
                               placeholder="Ad Soyad" required maxlength="100">
                        <div class="invalid-feedback">
                            Ad Soyad gereklidir.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userUsername" class="form-label">
                                    Kullanıcı Adı <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="userUsername" name="username" 
                                       placeholder="kullaniciadi" required maxlength="50" pattern="[a-zA-Z0-9_]+">
                                <div class="form-text">Sadece harf, rakam ve alt çizgi kullanın</div>
                                <div class="invalid-feedback">
                                    Geçerli bir kullanıcı adı giriniz.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">
                                    E-posta <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="userEmail" name="email" 
                                       placeholder="email@example.com" required maxlength="100">
                                <div class="invalid-feedback">
                                    Geçerli bir e-posta adresi giriniz.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">
                                    Şifre <span class="text-danger" id="passwordRequired">*</span>
                                </label>
                                <input type="password" class="form-control" id="userPassword" name="password" 
                                       placeholder="Güvenli şifre" minlength="6">
                                <div class="form-text">En az 6 karakter olmalıdır</div>
                                <div class="invalid-feedback">
                                    Şifre en az 6 karakter olmalıdır.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userRole" class="form-label">
                                    Rol <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="">Rol seçin...</option>
                                    <option value="admin">Yönetici</option>
                                    <option value="editor">Editör</option>
                                    <option value="author">Yazar</option>
                                </select>
                                <div class="invalid-feedback">
                                    Rol seçimi zorunludur.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="userAvatar" class="form-label">Profil Resmi</label>
                        <input type="file" class="form-control" id="userAvatar" 
                               accept="image/*" onchange="handleAvatarUpload(this)">
                        <div class="form-text">JPG, PNG formatları desteklenir. Max: 2MB</div>
                    </div>
                    
                    <input type="hidden" name="avatar" id="userAvatarUrl">
                    
                    <div id="avatarPreview" class="text-center mb-3" style="display: none;">
                        <img id="previewAvatar" src="" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAvatar()">
                                <i class="fas fa-trash me-1"></i>Resmi Kaldır
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="userActive" 
                               name="is_active" value="1" checked>
                        <label class="form-check-label" for="userActive">
                            Aktif kullanıcı
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

<!-- User Stats Modal -->
<div class="modal fade" id="userStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı İstatistikleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userStatsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Yükleniyor...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission
    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    // Auto-generate username from full name
    document.getElementById('userFullName').addEventListener('input', function() {
        if (!document.getElementById('userId').value) { // Only for new users
            const username = this.value
                .toLowerCase()
                .replace(/[çğıöşü]/g, match => ({ 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u' }[match]))
                .replace(/[^a-z0-9]/g, '')
                .substring(0, 20);
            document.getElementById('userUsername').value = username;
        }
    });
});

// Edit user
function editUser(id) {
    fetch(`<?= url('/admin/api/users/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                
                // Fill form
                document.getElementById('userId').value = user.id;
                document.getElementById('userFullName').value = user.full_name;
                document.getElementById('userUsername').value = user.username;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role;
                document.getElementById('userActive').checked = user.is_active == 1;
                
                // Password not required for edit
                document.getElementById('userPassword').removeAttribute('required');
                document.getElementById('passwordRequired').style.display = 'none';
                
                if (user.avatar) {
                    document.getElementById('userAvatarUrl').value = user.avatar;
                    document.getElementById('previewAvatar').src = user.avatar;
                    document.getElementById('avatarPreview').style.display = 'block';
                }
                
                // Update modal title
                document.getElementById('userModalTitle').textContent = 'Kullanıcı Düzenle';
                
                // Show modal
                new bootstrap.Modal(document.getElementById('userModal')).show();
            }
        })
        .catch(error => {
            showNotification('Kullanıcı bilgileri alınırken hata oluştu: ' + error.message, 'error');
        });
}

// Save user
function saveUser() {
    const form = document.getElementById('userForm');
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
    
    // Save user
    fetch('<?= url('/admin/api/users/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Kullanıcı başarıyla kaydedildi!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Kullanıcı kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Toggle user status
function toggleUserStatus(id, checkbox) {
    const status = checkbox.checked ? 1 : 0;
    
    fetch(`<?= url('/admin/api/users/') ?>${id}/toggle-status`, {
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
            showNotification('Kullanıcı durumu güncellendi', 'success');
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

// Delete user
function deleteUser(id, name) {
    if (confirm(`"${name}" kullanıcısını silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.`)) {
        fetch(`<?= url('/admin/api/users/') ?>${id}/delete`, {
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
                showNotification('Kullanıcı başarıyla silindi', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Silme hatası');
            }
        })
        .catch(error => {
            showNotification('Kullanıcı silinirken hata oluştu: ' + error.message, 'error');
        });
    }
}

// View user stats
function viewUserStats(id) {
    const modal = new bootstrap.Modal(document.getElementById('userStatsModal'));
    modal.show();
    
    fetch(`<?= url('/admin/api/users/') ?>${id}/stats`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('userStatsContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stats-card">
                                <h4>${data.stats.total_news || 0}</h4>
                                <p>Toplam Haber</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card">
                                <h4>${data.stats.published_news || 0}</h4>
                                <p>Yayınlanan Haber</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('userStatsContent').innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>İstatistikler yüklenirken hata oluştu.</p>
                </div>
            `;
        });
}

// Avatar upload
function handleAvatarUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        alert('Lütfen geçerli bir resim dosyası seçin.');
        input.value = '';
        return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
        alert('Resim boyutu 2MB\'dan küçük olmalıdır.');
        input.value = '';
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('<?= url('/admin/upload-file') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('userAvatarUrl').value = data.url;
            document.getElementById('previewAvatar').src = data.url;
            document.getElementById('avatarPreview').style.display = 'block';
        } else {
            throw new Error(data.error || 'Upload failed');
        }
    })
    .catch(error => {
        alert('Resim yüklenirken hata oluştu: ' + error.message);
        input.value = '';
    });
}

function removeAvatar() {
    document.getElementById('userAvatarUrl').value = '';
    document.getElementById('userAvatar').value = '';
    document.getElementById('avatarPreview').style.display = 'none';
}

// Reset modal on close
document.getElementById('userModal').addEventListener('hidden.bs.modal', function() {
    const form = document.getElementById('userForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('userId').value = '';
    document.getElementById('userModalTitle').textContent = 'Yeni Kullanıcı Ekle';
    document.getElementById('userPassword').setAttribute('required', '');
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('avatarPreview').style.display = 'none';
});
</script>
