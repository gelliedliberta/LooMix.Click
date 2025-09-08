<?php
/**
 * Gizlilik Politikası Sayfası
 */
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="h2">Gizlilik Politikası</h1>
                <p class="lead text-muted">Kişisel verilerinizi nasıl koruduğumuz</p>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h3 class="h4 mb-4">
                        <i class="fas fa-shield-alt text-primary me-3"></i>
                        Kişisel Veriler
                    </h3>
                    
                    <p class="mb-4">
                        LooMix.Click olarak, ziyaretçilerimizin gizliliğine önem veriyoruz. 
                        Bu politika, kişisel bilgilerinizi nasıl topladığımızı, kullandığımızı 
                        ve koruduğumuzu açıklar.
                    </p>
                    
                    <h4 class="h5 mb-3">Topladığımız Bilgiler</h4>
                    <ul class="mb-4">
                        <li>İletişim bilgileri (e-posta adresi)</li>
                        <li>Teknik bilgiler (IP adresi, tarayıcı bilgileri)</li>
                        <li>Kullanım verileri (ziyaret edilen sayfalar, süre)</li>
                        <li>Çerez bilgileri</li>
                    </ul>
                    
                    <h4 class="h5 mb-3">Bilgilerin Kullanımı</h4>
                    <p class="mb-4">
                        Topladığımız bilgiler şu amaçlarla kullanılır:
                    </p>
                    <ul class="mb-4">
                        <li>Site deneyiminizi iyileştirmek</li>
                        <li>İçerik önerilerinde bulunmak</li>
                        <li>İstatistik ve analiz çalışmaları</li>
                        <li>Yasal yükümlülükleri yerine getirmek</li>
                    </ul>
                    
                    <h4 class="h5 mb-3">Çerezler (Cookies)</h4>
                    <p class="mb-4">
                        Sitemiz, kullanıcı deneyimini iyileştirmek için çerezler kullanır. 
                        Çerez ayarlarınızı tarayıcınızdan kontrol edebilirsiniz.
                    </p>
                    
                    <h4 class="h5 mb-3">Güvenlik</h4>
                    <p class="mb-4">
                        Kişisel bilgilerinizin güvenliği için endüstri standardı güvenlik 
                        önlemleri kullanıyoruz. Ancak, internet üzerinden veri iletiminin 
                        %100 güvenli olduğu garanti edilemez.
                    </p>
                    
                    <h4 class="h5 mb-3">Haklarınız</h4>
                    <p class="mb-4">
                        KVKK kapsamında sahip olduğunuz haklar:
                    </p>
                    <ul class="mb-4">
                        <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                        <li>İşlenen kişisel verilerinizi talep etme</li>
                        <li>Kişisel verilerinizin düzeltilmesini isteme</li>
                        <li>Kişisel verilerinizin silinmesini isteme</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>İletişim:</strong> Gizlilik politikamızla ilgili sorularınız için 
                        <a href="<?= url('/iletisim') ?>" class="text-decoration-none">iletişim sayfamızı</a> kullanabilirsiniz.
                    </div>
                    
                    <p class="text-muted small mt-4">
                        <strong>Son güncelleme:</strong> <?= date('d.m.Y') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
