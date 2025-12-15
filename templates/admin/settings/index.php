<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cog me-2"></i>
        Site Ayarları
    </h1>
    <div class="page-actions">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" onclick="saveCurrentTabSettings()">
                <i class="fas fa-save me-2"></i>Bu Sekmeyi Kaydet
            </button>
            <button type="button" class="btn btn-success" onclick="saveAllSettings()">
                <i class="fas fa-save me-2"></i>Tüm Ayarları Kaydet
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Settings Navigation -->
    <div class="col-lg-3">
        <div class="data-table mb-4">
            <div class="p-3 border-bottom">
                <h6 class="mb-0">Ayar Kategorileri</h6>
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="pill" href="#general-settings">
                    <i class="fas fa-globe me-2"></i>Genel Ayarlar
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="pill" href="#seo-settings">
                    <i class="fas fa-search me-2"></i>SEO Ayarları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="pill" href="#ads-settings">
                    <i class="fas fa-ad me-2"></i>Reklam Ayarları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="pill" href="#contact-settings">
                    <i class="fas fa-address-book me-2"></i>İletişim Bilgileri
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="pill" href="#email-settings">
                    <i class="fas fa-envelope me-2"></i>E-posta Ayarları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="pill" href="#advanced-settings">
                    <i class="fas fa-code me-2"></i>Gelişmiş
                </a>
            </div>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="col-lg-9">
        <form id="settingsForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general-settings">
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Site Bilgileri
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Adı</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="<?= escape($settings['site_name'] ?? SITE_NAME) ?>" maxlength="100">
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Site Açıklaması</label>
                                <textarea class="form-control" id="site_description" name="site_description" 
                                          rows="3" maxlength="500"><?= escape($settings['site_description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="site_url" class="form-label">Site URL</label>
                                        <input type="url" class="form-control" id="site_url" name="site_url" 
                                               value="<?= escape($settings['site_url'] ?? SITE_URL) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_email" class="form-label">Yönetici E-posta</label>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                               value="<?= escape($settings['admin_email'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label">Saat Dilimi</label>
                                        <select class="form-select" id="timezone" name="timezone">
                                            <option value="Europe/Istanbul" <?= ($settings['timezone'] ?? 'Europe/Istanbul') === 'Europe/Istanbul' ? 'selected' : '' ?>>Türkiye (UTC+3)</option>
                                            <option value="UTC" <?= ($settings['timezone'] ?? 'Europe/Istanbul') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                            <option value="Europe/London" <?= ($settings['timezone'] ?? 'Europe/Istanbul') === 'Europe/London' ? 'selected' : '' ?>>Londra (UTC+0)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="language" class="form-label">Dil</label>
                                        <select class="form-select" id="language" name="language">
                                            <option value="tr" <?= ($settings['language'] ?? 'tr') === 'tr' ? 'selected' : '' ?>>Türkçe</option>
                                            <option value="en" <?= ($settings['language'] ?? 'tr') === 'en' ? 'selected' : '' ?>>İngilizce</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-palette me-2"></i>
                                Görünüm Ayarları
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="theme_color" class="form-label">Ana Tema Rengi</label>
                                        <input type="color" class="form-control form-control-color" 
                                               id="theme_color" name="theme_color" value="<?= $settings['theme_color'] ?? '#007bff' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="news_per_page" class="form-label">Sayfa Başına Haber</label>
                                        <select class="form-select" id="news_per_page" name="news_per_page">
                                            <option value="12" <?= ($settings['news_per_page'] ?? 12) == 12 ? 'selected' : '' ?>>12</option>
                                            <option value="18" <?= ($settings['news_per_page'] ?? 12) == 18 ? 'selected' : '' ?>>18</option>
                                            <option value="24" <?= ($settings['news_per_page'] ?? 12) == 24 ? 'selected' : '' ?>>24</option>
                                            <option value="36" <?= ($settings['news_per_page'] ?? 12) == 36 ? 'selected' : '' ?>>36</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_dark_mode" name="enable_dark_mode" 
                                       value="1" <?= !empty($settings['enable_dark_mode']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_dark_mode">
                                    <i class="fas fa-moon me-1"></i>Karanlık Mod Desteği
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_author" name="show_author" 
                                       value="1" <?= !empty($settings['show_author']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="show_author">
                                    <i class="fas fa-user me-1"></i>Yazar Bilgilerini Göster
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="tab-pane fade" id="seo-settings">
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>
                                SEO Meta Bilgileri
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Site Meta Başlık</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                       value="<?= escape($settings['meta_title'] ?? '') ?>" maxlength="200">
                                <div class="form-text">Arama motorları için optimize edilmiş başlık</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Site Meta Açıklaması</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" 
                                          rows="3" maxlength="160"><?= escape($settings['meta_description'] ?? '') ?></textarea>
                                <div class="form-text">
                                    <span id="metaDescCounter"><?= strlen($settings['meta_description'] ?? '') ?></span>/160 karakter
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Site Anahtar Kelimeler</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                       value="<?= escape($settings['meta_keywords'] ?? '') ?>">
                                <div class="form-text">Virgülle ayırarak yazın</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-robot me-2"></i>
                                Arama Motoru Ayarları
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                       value="<?= escape($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                            </div>
                            
                            <div class="mb-3">
                                <label for="google_search_console" class="form-label">Google Search Console Kodu</label>
                                <input type="text" class="form-control" id="google_search_console" name="google_search_console" 
                                       value="<?= escape($settings['google_search_console'] ?? '') ?>" 
                                       placeholder="google-site-verification content değeri">
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_sitemap" name="auto_sitemap" 
                                       value="1" <?= !empty($settings['auto_sitemap']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="auto_sitemap">
                                    <i class="fas fa-sitemap me-1"></i>Otomatik Sitemap Güncellemesi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertisement Settings -->
                <div class="tab-pane fade" id="ads-settings">
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fab fa-google me-2"></i>
                                Google AdSense
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="google_adsense_id" class="form-label">AdSense Yayıncı ID</label>
                                <input type="text" class="form-control" id="google_adsense_id" name="google_adsense_id" 
                                       value="<?= escape($settings['google_adsense_id'] ?? '') ?>" 
                                       placeholder="pub-XXXXXXXXXXXXXXXX">
                                <div class="form-text">Google AdSense hesabınızdan aldığınız Publisher ID</div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="auto_ads" name="auto_ads" 
                                       value="1" <?= !empty($settings['auto_ads']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="auto_ads">
                                    <i class="fas fa-magic me-1"></i>Otomatik Reklamlar
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ad_blocker_detection" name="ad_blocker_detection" 
                                       value="1" <?= !empty($settings['ad_blocker_detection']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ad_blocker_detection">
                                    <i class="fas fa-shield-alt me-1"></i>Ad Blocker Tespiti
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-ad me-2"></i>
                                Reklam Genel Ayarları
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_ads" name="enable_ads" 
                                       value="1" <?= !empty($settings['enable_ads']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_ads">
                                    <i class="fas fa-toggle-on me-1"></i>Reklamları Etkinleştir
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lazy_load_ads" name="lazy_load_ads" 
                                       value="1" <?= !empty($settings['lazy_load_ads']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="lazy_load_ads">
                                    <i class="fas fa-hourglass-half me-1"></i>Lazy Loading (Gecikmeli Yükleme)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="tab-pane fade" id="contact-settings">
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                E-posta Adresleri
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Genel İletişim E-posta</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?= escape($settings['contact_email'] ?? 'info@loomix.click') ?>" 
                                               placeholder="info@loomix.click">
                                        <small class="text-muted">İletişim sayfasında gösterilecek</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_email_editor" class="form-label">Editör E-posta</label>
                                        <input type="email" class="form-control" id="contact_email_editor" name="contact_email_editor" 
                                               value="<?= escape($settings['contact_email_editor'] ?? 'editor@loomix.click') ?>" 
                                               placeholder="editor@loomix.click">
                                        <small class="text-muted">İçerik ve editör ekibi için</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-phone me-2"></i>
                                İletişim Bilgileri
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                               value="<?= escape($settings['contact_phone'] ?? '') ?>" 
                                               placeholder="+90 XXX XXX XX XX">
                                        <small class="text-muted">Opsiyonel</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_address" class="form-label">Adres</label>
                                        <textarea class="form-control" id="contact_address" name="contact_address" 
                                                  rows="2" placeholder="İstanbul, Türkiye"><?= escape($settings['contact_address'] ?? '') ?></textarea>
                                        <small class="text-muted">Opsiyonel</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-share-alt me-2"></i>
                                Sosyal Medya Kullanıcı Adları
                            </h5>
                            <p class="mb-0 small text-muted mt-1">
                                İletişim sayfasında gösterilecek kullanıcı adları. URL'ler Sosyal Medya Yönetimi'nden düzenlenir.
                            </p>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_twitter_handle" class="form-label">
                                            <i class="fab fa-twitter text-info me-2"></i>Twitter/X Kullanıcı Adı
                                        </label>
                                        <input type="text" class="form-control" id="contact_twitter_handle" name="contact_twitter_handle" 
                                               value="<?= escape($settings['contact_twitter_handle'] ?? '@LooMixClick') ?>" 
                                               placeholder="@LooMixClick">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_facebook_page" class="form-label">
                                            <i class="fab fa-facebook text-primary me-2"></i>Facebook Sayfa Adı
                                        </label>
                                        <input type="text" class="form-control" id="contact_facebook_page" name="contact_facebook_page" 
                                               value="<?= escape($settings['contact_facebook_page'] ?? 'LooMix.Click') ?>" 
                                               placeholder="LooMix.Click">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_instagram_handle" class="form-label">
                                            <i class="fab fa-instagram text-danger me-2"></i>Instagram Kullanıcı Adı
                                        </label>
                                        <input type="text" class="form-control" id="contact_instagram_handle" name="contact_instagram_handle" 
                                               value="<?= escape($settings['contact_instagram_handle'] ?? '@loomixclick') ?>" 
                                               placeholder="@loomixclick">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_linkedin_page" class="form-label">
                                            <i class="fab fa-linkedin text-primary me-2"></i>LinkedIn Sayfa Adı
                                        </label>
                                        <input type="text" class="form-control" id="contact_linkedin_page" name="contact_linkedin_page" 
                                               value="<?= escape($settings['contact_linkedin_page'] ?? 'LooMix Click') ?>" 
                                               placeholder="LooMix Click">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Not:</strong> Sosyal medya URL'lerini düzenlemek için 
                                <a href="<?= url('/admin/sosyal-medya') ?>" class="alert-link">Sosyal Medya Yönetimi</a> 
                                sayfasını kullanın.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Email Settings -->
                <div class="tab-pane fade" id="email-settings">
                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-server me-2"></i>
                                SMTP Ayarları
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host</label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                               value="<?= escape($settings['smtp_host'] ?? '') ?>" 
                                               placeholder="smtp.gmail.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_port" class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                               value="<?= escape($settings['smtp_port'] ?? '587') ?>" 
                                               placeholder="587">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_username" class="form-label">SMTP Kullanıcı Adı</label>
                                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                               value="<?= escape($settings['smtp_username'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_password" class="form-label">SMTP Şifre</label>
                                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                               value="<?= escape($settings['smtp_password'] ?? '') ?>">
                                        <div class="form-text">Güvenlik nedeniyle şifreler maskelenir</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="smtp_encryption" name="smtp_encryption" 
                                       value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="smtp_encryption">
                                    <i class="fas fa-lock me-1"></i>TLS Şifreleme
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="tab-pane fade" id="advanced-settings">
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-database me-2"></i>
                                Performans & Önbellek
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_cache" name="enable_cache" 
                                       value="1" <?= !empty($settings['enable_cache']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_cache">
                                    <i class="fas fa-tachometer-alt me-1"></i>Önbellek Sistemi
                                </label>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cache_duration" class="form-label">Önbellek Süresi (dakika)</label>
                                <input type="number" class="form-control" id="cache_duration" name="cache_duration" 
                                       value="<?= escape($settings['cache_duration'] ?? '60') ?>" min="5" max="1440">
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode" 
                                       value="1" <?= !empty($settings['debug_mode']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="debug_mode">
                                    <i class="fas fa-bug me-1"></i>Debug Modu
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="data-table mb-4">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-folder-tree me-2"></i>
                                Yıl/Ay/Gün Klasörleri Oluşturma
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="folder_year" class="form-label">Yıl</label>
                                    <input type="number" class="form-control" id="folder_year" min="1970" max="2100" placeholder="2025" value="<?= date('Y') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="folder_base" class="form-label">Temel Klasör (opsiyonel)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">assets/uploads/</span>
                                        <input type="text" class="form-control" id="folder_base" placeholder="ör. images veya boş bırakın">
                                    </div>
                                    <div class="form-text">Boş bırakılırsa doğrudan <code>assets/uploads</code> altında oluşturulur.</div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100" id="btnCreateDateFolders">
                                        <i class="fas fa-hammer me-2"></i>Oluştur
                                    </button>
                                </div>
                            </div>
                            <div id="createFoldersResult" class="mt-3" style="display:none;"></div>
                        </div>
                    </div>

                    <div class="data-table">
                        <div class="p-3 border-bottom">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                Güvenlik Ayarları
                            </h5>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="max_login_attempts" class="form-label">Maksimum Giriş Denemesi</label>
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                       value="<?= escape($settings['max_login_attempts'] ?? '5') ?>" min="3" max="20">
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enable_csrf" name="enable_csrf" 
                                       value="1" <?= !empty($settings['enable_csrf']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_csrf">
                                    <i class="fas fa-shield me-1"></i>CSRF Koruması
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_rate_limit" name="enable_rate_limit" 
                                       value="1" <?= !empty($settings['enable_rate_limit']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enable_rate_limit">
                                    <i class="fas fa-stopwatch me-1"></i>Rate Limiting
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-2"></i>
                    Ayarları Kaydet
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="resetToDefaults()">
                    <i class="fas fa-undo me-2"></i>
                    Varsayılanlara Sıfırla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for meta description
    setupCharacterCounter('meta_description', 'metaDescCounter', 160);
    
    // Form submission
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAllSettings();
    });

    // Create date folders action
    const btn = document.getElementById('btnCreateDateFolders');
    if (btn) {
        btn.addEventListener('click', function() {
            const year = document.getElementById('folder_year').value;
            const base = document.getElementById('folder_base').value.trim();
            createDateFolders(year, base);
        });
    }
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

// Save all settings
function saveAllSettings() {
    const form = document.getElementById('settingsForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
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
    // Unchecked checkbox'ları da 0 olarak gönder
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        if (cb.name && !(cb.name in data)) {
            data[cb.name] = '0';
        }
    });
    
    // Save settings
    fetch('<?= url('/admin/api/settings/save') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ayarlar başarıyla kaydedildi!', 'success');
            
            // If critical settings changed, suggest restart
            if (data.restart_recommended) {
                showNotification('Bazı değişikliklerin geçerli olması için sayfayı yenileyin', 'info');
            }
        } else {
            throw new Error(data.message || 'Kaydetme hatası');
        }
    })
    .catch(error => {
        showNotification('Ayarlar kaydedilirken hata oluştu: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Save only current tab settings
function saveCurrentTabSettings() {
    const form = document.getElementById('settingsForm');
    const activeTab = document.querySelector('.tab-pane.active');
    if (!activeTab) {
        showNotification('Aktif sekme bulunamadı.', 'error');
        return;
    }

    // Topla: sadece aktif sekmedeki input/select/textarea
    const inputs = activeTab.querySelectorAll('input, select, textarea');
    const data = { csrf_token: form.querySelector('[name="csrf_token"]').value };
    inputs.forEach(el => {
        if (!el.name) return;
        if (el.type === 'checkbox') {
            // Aktif sekmede bulunan checkbox'ları açık/kapalı gönder
            data[el.name] = el.checked ? (el.value || '1') : '0';
        } else if (el.type === 'radio') {
            if (el.checked) data[el.name] = el.value;
        } else {
            data[el.name] = el.value;
        }
    });

    // Gönder
    fetch('<?= url('/admin/api/settings/save') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.success) {
            showNotification('Bu sekmedeki ayarlar kaydedildi.', 'success');
            if (resp.restart_recommended) {
                showNotification('Bazı değişiklikler için sayfayı yenileyin.', 'info');
            }
        } else {
            throw new Error(resp.message || 'Kaydetme hatası');
        }
    })
    .catch(err => showNotification('Ayarlar kaydedilirken hata: ' + err.message, 'error'));
}

// Reset to defaults
function resetToDefaults() {
    if (confirm('Tüm ayarları varsayılan değerlere sıfırlamak istediğinizden emin misiniz?')) {
        fetch('<?= url('/admin/api/settings/reset') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ csrf_token: document.querySelector('[name="csrf_token"]').value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Ayarlar varsayılanlara sıfırlandı!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Sıfırlama hatası');
            }
        })
        .catch(error => {
            showNotification('Ayarlar sıfırlanırken hata oluştu: ' + error.message, 'error');
        });
    }
}

// Create full date folders
function createDateFolders(year, baseSubdir) {
    const resultEl = document.getElementById('createFoldersResult');
    const btn = document.getElementById('btnCreateDateFolders');
    if (!year) {
        showNotification('Lütfen geçerli bir yıl girin.', 'error');
        return;
    }

    const payload = {
        csrf_token: document.querySelector('[name="csrf_token"]').value,
        year: year,
        base_subdir: baseSubdir || ''
    };

    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Oluşturuluyor...';
    btn.disabled = true;

    fetch('<?= url('/admin/api/uploads/create-date-folders') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(resp => {
        if (!resp.success) throw new Error(resp.error || resp.message || 'İşlem başarısız');
        showNotification(resp.message, 'success');
        resultEl.style.display = '';
        resultEl.className = 'alert alert-info';
        resultEl.innerHTML = `
            <div><strong>Yıl:</strong> ${resp.year}</div>
            <div><strong>Temel Klasör:</strong> ${resp.base || 'assets/uploads/'}</div>
            <div class="mt-2">Oluşturulan: <strong>${resp.created_count}</strong> · Zaten vardı: <strong>${resp.skipped_count}</strong> · Hata: <strong>${resp.error_count}</strong></div>
        `;
    })
    .catch(err => {
        showNotification('Klasörler oluşturulurken hata: ' + err.message, 'error');
    })
    .finally(() => {
        btn.innerHTML = original;
        btn.disabled = false;
    });
}
</script>
