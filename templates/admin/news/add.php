<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus me-2"></i>
        Yeni Haber Ekle
    </h1>
    <div class="page-actions">
        <a href="<?= url('/admin/haberler') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Geri Dön
        </a>
    </div>
</div>

<form id="newsForm" class="needs-validation" novalidate>
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Temel Bilgiler
                    </h5>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Başlık <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="Haber başlığını girin..." required maxlength="255">
                        <div class="form-text">
                            <span id="titleCounter">0</span>/255 karakter
                        </div>
                        <div class="invalid-feedback">
                            Haber başlığı gereklidir.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">
                            URL Slug
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><?= escape(SITE_URL) ?>/haber/</span>
                            <input type="text" class="form-control" id="slug" name="slug" 
                                   placeholder="otomatik-olusturulacak">
                        </div>
                        <div class="form-text">
                            Boş bırakılırsa başlıktan otomatik oluşturulacak
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="summary" class="form-label">
                            Özet <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="summary" name="summary" rows="3" 
                                  placeholder="Haber özeti..." required minlength="20" maxlength="500"></textarea>
                        <div class="form-text">
                            <span id="summaryCounter">0</span>/500 karakter (min: 20)
                        </div>
                        <div class="invalid-feedback">
                            Haber özeti en az 20 karakter olmalıdır.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        İçerik
                    </h5>
                </div>
                <div class="p-3">
                    <label for="content" class="form-label">
                        Haber İçeriği <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="content" name="content" 
                              style="min-height: 400px;" required></textarea>
                    <div class="form-text">
                        HTML etiketleri kullanabilirsiniz.
                    </div>
                    <div class="invalid-feedback">
                        Haber içeriği gereklidir.
                    </div>
                </div>
            </div>
            
            <!-- SEO Settings -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        SEO Ayarları
                    </h5>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">
                            Meta Başlık
                        </label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                               maxlength="200" placeholder="SEO için optimize edilmiş başlık">
                        <div class="form-text">
                            Boş bırakılırsa haber başlığı kullanılacak. Max: 200 karakter
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">
                            Meta Açıklama
                        </label>
                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                  rows="2" maxlength="160" placeholder="Arama motorları için açıklama"></textarea>
                        <div class="form-text">
                            <span id="metaDescCounter">0</span>/160 karakter (ideal: 150-160)
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">
                            Meta Anahtar Kelimeler
                        </label>
                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                               placeholder="kelime1, kelime2, kelime3">
                        <div class="form-text">
                            Virgülle ayırarak yazın
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Publish Settings -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>
                        Yayın Ayarları
                    </h5>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Durum <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft">Taslak</option>
                            <option value="published">Yayınla</option>
                            <option value="archived">Arşiv</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="publish_date" class="form-label">
                            Yayın Tarihi
                        </label>
                        <input type="datetime-local" class="form-control" id="publish_date" name="publish_date" 
                               value="<?= date('Y-m-d\TH:i') ?>">
                        <div class="form-text">
                            Gelecekteki bir tarih seçebilirsiniz
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="author_name" class="form-label">
                            Yazar
                        </label>
                        <input type="text" class="form-control" id="author_name" name="author_name" 
                               value="<?= escape($currentUser['full_name'] ?? '') ?>" placeholder="Yazar adı">
                    </div>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                        <label class="form-check-label" for="is_featured">
                            <i class="fas fa-star text-warning me-1"></i>
                            Öne Çıkan Haber
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_breaking" name="is_breaking" value="1">
                        <label class="form-check-label" for="is_breaking">
                            <i class="fas fa-bolt text-danger me-1"></i>
                            Son Dakika Haberi
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Category & Tags -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-folder me-2"></i>
                        Kategori & Etiketler
                    </h5>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Kategori seçin...</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        data-color="<?= $category['color'] ?>">
                                    <?= escape($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Kategori seçimi zorunludur.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">
                            Etiketler
                        </label>
                        <input type="text" class="form-control" id="tags" name="tags" 
                               placeholder="Etiket yazın ve Enter'a basın...">
                        <div class="form-text">
                            Enter tuşu ile etiket ekleyin
                        </div>
                        <div id="selectedTags" class="mt-2"></div>
                    </div>
                    
                    <!-- Popular Tags -->
                    <div class="mb-3">
                        <label class="form-label">Popüler Etiketler</label>
                        <div id="popularTags">
                            <?php foreach ($popularTags as $tag): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" 
                                        onclick="addTag('<?= escape($tag['name']) ?>')">
                                    <?= escape($tag['name']) ?>
                                    <span class="badge bg-light text-dark ms-1"><?= $tag['usage_count'] ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Featured Image -->
            <div class="data-table mb-4">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>
                        Öne Çıkan Resim
                    </h5>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="featured_image_file" 
                               accept="image/*" onchange="handleImageUpload(this)">
                        <div class="form-text">
                            JPG, PNG, WEBP formatları desteklenir. Max: 5MB
                        </div>
                    </div>
                    
                    <input type="hidden" name="featured_image" id="featured_image">
                    
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 200px;">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                                <i class="fas fa-trash me-1"></i>Resmi Kaldır
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_alt" class="form-label">
                            Resim Alt Metni
                        </label>
                        <input type="text" class="form-control" id="image_alt" name="image_alt" 
                               placeholder="SEO için resim açıklaması">
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="data-table">
                <div class="p-3">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Haberi Kaydet
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="previewNews()">
                            <i class="fas fa-eye me-2"></i>
                            Önizleme
                        </button>
                        <a href="<?= url('/admin/haberler') ?>" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Include TinyMCE (Anahtar gerektirmeyen self-hosted/Cloud dev anahtarı yerine CDN public build) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script>
let selectedTags = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family: Inter, sans-serif; font-size: 14px }',
        language: 'tr',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
    
    // Character counters
    setupCharacterCounter('title', 'titleCounter', 255);
    setupCharacterCounter('summary', 'summaryCounter', 500);
    setupCharacterCounter('meta_description', 'metaDescCounter', 160);
    
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = createSlug(this.value);
        document.getElementById('slug').value = slug;
    });
    
    // Tags input
    const tagsInput = document.getElementById('tags');
    tagsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addTag(this.value.trim());
            this.value = '';
        }
    });
    
    // Form submission
    document.getElementById('newsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveNews();
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

// Tag management
function addTag(tagName) {
    if (!tagName || selectedTags.includes(tagName)) return;
    
    selectedTags.push(tagName);
    updateTagsDisplay();
}

function removeTag(tagName) {
    selectedTags = selectedTags.filter(tag => tag !== tagName);
    updateTagsDisplay();
}

function updateTagsDisplay() {
    const container = document.getElementById('selectedTags');
    container.innerHTML = selectedTags.map(tag => `
        <span class="badge bg-primary me-1 mb-1">
            ${tag}
            <button type="button" class="btn-close btn-close-white ms-1" 
                    onclick="removeTag('${tag}')" style="font-size: 0.6rem;"></button>
        </span>
    `).join('');
}

// Image upload
function handleImageUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Lütfen geçerli bir resim dosyası seçin.');
        input.value = '';
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Resim boyutu 5MB\'dan küçük olmalıdır.');
        input.value = '';
        return;
    }
    
    // Show loading
    const preview = document.getElementById('imagePreview');
    preview.style.display = 'block';
    preview.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><div>Yükleniyor...</div></div>';
    
    // Upload file
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('<?= url('/admin/upload-file') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('featured_image').value = data.url;
            document.getElementById('previewImg').src = data.url;
            preview.innerHTML = `
                <img id="previewImg" src="${data.url}" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 200px;">
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                        <i class="fas fa-trash me-1"></i>Resmi Kaldır
                    </button>
                </div>
            `;
            
            // Auto-fill alt text if empty
            if (!document.getElementById('image_alt').value) {
                document.getElementById('image_alt').value = document.getElementById('title').value || 'Haber görseli';
            }
        } else {
            throw new Error(data.error || 'Upload failed');
        }
    })
    .catch(error => {
        alert('Resim yüklenirken hata oluştu: ' + error.message);
        preview.style.display = 'none';
        input.value = '';
    });
}

function removeImage() {
    document.getElementById('featured_image').value = '';
    document.getElementById('featured_image_file').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}

// Preview news
function previewNews() {
    // Get form data
    const formData = new FormData(document.getElementById('newsForm'));
    
    // Add TinyMCE content
    tinymce.triggerSave();
    
    // Add selected tags
    formData.set('tags', selectedTags.join(','));
    
    // Open preview in new window
    const previewWindow = window.open('', 'preview', 'width=800,height=600,scrollbars=yes');
    previewWindow.document.write('<html><body><h1>Yükleniyor...</h1></body></html>');
    
    fetch('<?= url('/admin/preview-news') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        previewWindow.document.open();
        previewWindow.document.write(html);
        previewWindow.document.close();
    })
    .catch(error => {
        previewWindow.close();
        alert('Önizleme oluşturulurken hata oluştu: ' + error.message);
    });
}

// Save news
function saveNews() {
    const form = document.getElementById('newsForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // Get form data
    const formData = new FormData(form);
    
    // Add TinyMCE content
    tinymce.triggerSave();
    formData.set('content', tinymce.get('content').getContent());
    
    // Add selected tags
    formData.set('tags', selectedTags.join(','));
    
    // Show loading
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Kaydediliyor...';
    submitBtn.disabled = true;
    
    // Convert FormData to JSON
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    // Save news
    fetch('<?= url('/admin/haber-kaydet') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Haber başarıyla kaydedildi!', 'success');
            setTimeout(() => {
                window.location.href = '/admin/haberler';
            }, 1500);
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Haber kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>
