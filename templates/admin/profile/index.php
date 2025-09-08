<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user me-2"></i>
        Profilim
    </h1>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" onclick="submitProfileForm()">
            <i class="fas fa-save me-2"></i>Profili Kaydet
        </button>
    </div>
    <input type="hidden" id="csrfToken" value="<?= $csrfToken ?>">
    <input type="hidden" id="uploadEndpoint" value="<?= url('/admin/upload-file') ?>">
    <input type="hidden" id="saveProfileEndpoint" value="<?= url('/admin/api/profile/save') ?>">
    <input type="hidden" id="changePasswordEndpoint" value="<?= url('/admin/api/profile/change-password') ?>">
</div>

<div class="row">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="stats-card text-center">
            <div class="position-relative d-inline-block">
                <?php $avatarUrl = getImageUrl($user['avatar'] ?? '', asset('images/logo.png')); ?>
                <img id="profileAvatarPreview" src="<?= $avatarUrl ?>" alt="Avatar" class="rounded-circle"
                     style="width: 120px; height: 120px; object-fit: cover;">
                <button class="btn btn-sm btn-outline-secondary position-absolute" style="right: -5px; bottom: -5px;"
                        onclick="document.getElementById('profileAvatarInput').click()">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h3 class="mt-3 mb-1"><?= escape($user['full_name'] ?? '') ?></h3>
            <div class="text-muted small mb-2">@<?= escape($user['username'] ?? '') ?> • <?= ucfirst($user['role'] ?? '') ?></div>
            <div class="small text-muted">
                <div>Üyelik: <?= formatDate($user['created_at'] ?? '') ?></div>
                <div>Son Giriş: <?= $user['last_login'] ? formatDate($user['last_login']) : '—' ?></div>
                <div>Giriş Sayısı: <?= number_format($user['login_count'] ?? 0) ?></div>
            </div>
            <input type="file" id="profileAvatarInput" accept="image/*" class="d-none" onchange="handleProfileAvatarUpload(this)">
            <input type="hidden" id="profileAvatarUrl" value="<?= escape($user['avatar'] ?? '') ?>">
        </div>
    </div>

    <!-- Profile Forms -->
    <div class="col-lg-8">
        <div class="data-table mb-4">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Kişisel Bilgiler</h5>
                <button type="button" class="btn btn-sm btn-primary" onclick="submitProfileForm()">
                    <i class="fas fa-save me-1"></i>Kaydet
                </button>
            </div>
            <div class="p-3">
                <form id="profileForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="full_name" id="profileFullName"
                                       value="<?= escape($user['full_name'] ?? '') ?>" required maxlength="100">
                                <div class="invalid-feedback">Ad Soyad gereklidir.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" value="<?= escape($user['username'] ?? '') ?>" disabled>
                                <div class="form-text">Kullanıcı adını değiştirmeniz gerekirse yönetici ile iletişime geçin.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">E-posta <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="profileEmail"
                                       value="<?= escape($user['email'] ?? '') ?>" required>
                                <div class="invalid-feedback">Geçerli bir e-posta giriniz.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['role'] ?? '') ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="avatar" id="profileAvatarField" value="<?= escape($user['avatar'] ?? '') ?>">
                </form>
            </div>
        </div>

        <div class="data-table">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Şifre Değiştir</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="submitPasswordForm()">
                    <i class="fas fa-key me-1"></i>Şifreyi Güncelle
                </button>
            </div>
            <div class="p-3">
                <form id="passwordForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Mevcut Şifre <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="current_password" id="currentPassword" required>
                                <div class="invalid-feedback">Mevcut şifrenizi giriniz.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Yeni Şifre <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="new_password" id="newPassword" minlength="6" required>
                                <div class="form-text">En az 6 karakter</div>
                                <div class="invalid-feedback">Geçerli bir şifre giriniz.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Yeni Şifre (Tekrar) <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="confirm_password" id="confirmPassword" minlength="6" required>
                                <div class="invalid-feedback">Şifreleri doğrulayın.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function submitProfileForm() {
    const form = document.getElementById('profileForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    const endpoint = document.getElementById('saveProfileEndpoint').value;
    const payload = {
        csrf_token: document.getElementById('csrfToken').value,
        full_name: document.getElementById('profileFullName').value,
        email: document.getElementById('profileEmail').value,
        avatar: document.getElementById('profileAvatarField').value
    };
    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            showNotification('Profil güncellendi', 'success');
        } else {
            const message = resp.error || 'Güncelleme hatası';
            showNotification(message, 'error');
        }
    })
    .catch(err => showNotification('Profil kaydedilirken hata: ' + err.message, 'error'));
}

function submitPasswordForm() {
    const form = document.getElementById('passwordForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    if (newPass !== confirmPass) {
        showNotification('Şifreler eşleşmiyor', 'error');
        return;
    }
    const endpoint = document.getElementById('changePasswordEndpoint').value;
    const payload = {
        csrf_token: document.getElementById('csrfToken').value,
        current_password: document.getElementById('currentPassword').value,
        new_password: newPass,
        confirm_password: confirmPass
    };
    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            showNotification('Şifre güncellendi', 'success');
            form.reset();
        } else {
            const message = resp.error || 'Güncelleme hatası';
            showNotification(message, 'error');
        }
    })
    .catch(err => showNotification('Şifre güncellenirken hata: ' + err.message, 'error'));
}

function handleProfileAvatarUpload(input) {
    const file = input.files && input.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) {
        alert('Lütfen geçerli bir resim seçin');
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
    const uploadUrl = document.getElementById('uploadEndpoint').value;
    fetch(uploadUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.url) {
                document.getElementById('profileAvatarPreview').src = data.url;
                document.getElementById('profileAvatarField').value = data.url;
                document.getElementById('profileAvatarUrl').value = data.url;
                showNotification('Profil resmi yüklendi', 'success');
            } else {
                throw new Error(data.error || 'Upload failed');
            }
        })
        .catch(err => {
            showNotification('Resim yüklenemedi: ' + err.message, 'error');
            input.value = '';
        });
}
</script>


