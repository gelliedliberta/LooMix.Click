<?php
/**
 * İletişim Sayfası
 */

// İletişim bilgilerini ve sosyal medya linklerini getir
$contactInfo = getContactInfo();
$socialMedia = new SocialMedia();
$socialLinks = $socialMedia->getActive();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="h2">İletişim</h1>
                <p class="lead text-muted">Bizimle iletişime geçin</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="h5 mb-4">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                E-posta
                            </h4>
                            
                            <?php if (!empty($contactInfo['email'])): ?>
                            <p class="mb-2">Genel sorular ve öneriler için:</p>
                            <a href="mailto:<?= escape($contactInfo['email']) ?>" class="text-decoration-none">
                                <?= escape($contactInfo['email']) ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($contactInfo['email_editor'])): ?>
                            <p class="mt-3 mb-2">Editör ekibi için:</p>
                            <a href="mailto:<?= escape($contactInfo['email_editor']) ?>" class="text-decoration-none">
                                <?= escape($contactInfo['email_editor']) ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($contactInfo['phone'])): ?>
                            <p class="mt-3 mb-2">Telefon:</p>
                            <a href="tel:<?= escape(preg_replace('/[^0-9+]/', '', $contactInfo['phone'])) ?>" class="text-decoration-none">
                                <?= escape($contactInfo['phone']) ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($contactInfo['address'])): ?>
                            <p class="mt-3 mb-2">Adres:</p>
                            <p class="text-muted small"><?= escape($contactInfo['address']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="h5 mb-4">
                                <i class="fas fa-share-alt text-success me-3"></i>
                                Sosyal Medya
                            </h4>
                            <div class="d-flex flex-column gap-2">
                                <?php if (!empty($socialLinks)): ?>
                                    <?php foreach ($socialLinks as $link): ?>
                                        <?php if (!empty($link['url']) && $link['url'] !== '#'): ?>
                                            <?php 
                                            $isInternal = (strpos($link['url'], 'http') !== 0 && strpos($link['url'], '//') !== 0);
                                            $finalUrl = $isInternal ? url($link['url']) : $link['url'];
                                            $target = $isInternal ? '' : ' target="_blank" rel="noopener noreferrer"';
                                            
                                            // Platform için özel kullanıcı adı göster (varsa)
                                            $displayText = $link['name'];
                                            if ($link['platform'] === 'twitter' && !empty($contactInfo['twitter_handle'])) {
                                                $displayText = $contactInfo['twitter_handle'];
                                            } elseif ($link['platform'] === 'facebook' && !empty($contactInfo['facebook_page'])) {
                                                $displayText = $contactInfo['facebook_page'];
                                            } elseif ($link['platform'] === 'instagram' && !empty($contactInfo['instagram_handle'])) {
                                                $displayText = $contactInfo['instagram_handle'];
                                            } elseif ($link['platform'] === 'linkedin' && !empty($contactInfo['linkedin_page'])) {
                                                $displayText = $contactInfo['linkedin_page'];
                                            }
                                            ?>
                                            <a href="<?= escape($finalUrl) ?>" class="text-decoration-none"<?= $target ?>>
                                                <i class="<?= escape($link['icon']) ?> me-2" style="color: <?= escape($link['color'] ?? '#6c757d') ?>"></i>
                                                <?= escape($displayText) ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted small">Sosyal medya linkleri henüz eklenmedi.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-5">
                    <h4 class="h5 mb-4">
                        <i class="fas fa-paper-plane text-warning me-3"></i>
                        İletişim Formu
                    </h4>
                    
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Konu</label>
                            <select class="form-select" id="subject" required>
                                <option value="">Konu seçiniz...</option>
                                <option value="general">Genel Sorular</option>
                                <option value="technical">Teknik Destek</option>
                                <option value="content">İçerik Önerisi</option>
                                <option value="partnership">İş Birliği</option>
                                <option value="other">Diğer</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Mesaj</label>
                            <textarea class="form-control" id="message" rows="5" 
                                      placeholder="Mesajınızı buraya yazın..." required></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-send me-2"></i>
                                Mesajı Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Not:</strong> İletişim formumuz şu anda aktif değil. Lütfen doğrudan e-posta adreslerimizi kullanın.
                Mesajlarınıza en kısa sürede yanıt vermeye çalışıyoruz.
            </div>
        </div>
    </div>
</div>
