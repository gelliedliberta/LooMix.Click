/**
 * Ad Blocker Detection and Ad Management
 * LooMix.Click
 */

(function() {
    'use strict';
    
    let adBlockDetected = false;
    let adsInitialized = false;
    const cfg = (typeof window !== 'undefined' && window.AppConfig) ? window.AppConfig : {};

    function joinUrl(base, path) {
        if (!base) return path;
        if (!path) return base;
        const b = base.endsWith('/') ? base.slice(0, -1) : base;
        const p = path.startsWith('/') ? path : `/${path}`;
        return `${b}${p}`;
    }

    function elementHasUsableSize(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        const style = window.getComputedStyle(el);
        return rect.width > 0 && rect.height > 0 && style.display !== 'none' && style.visibility !== 'hidden';
    }

    function pushAdWhenReady(ad) {
        if (!ad || ad.getAttribute('data-ad-status') === 'done' || ad.__adsbygooglePushed) {
            return;
        }
        if (elementHasUsableSize(ad)) {
            try {
                (window.adsbygoogle = window.adsbygoogle || []).push({});
                ad.__adsbygooglePushed = true;
            } catch (e) {
                console.warn('AdSense push error:', e);
            }
            return;
        }
        // Observe size changes and push once width > 0
        if (typeof ResizeObserver !== 'undefined') {
            const ro = new ResizeObserver((entries) => {
                for (const entry of entries) {
                    if (elementHasUsableSize(entry.target) && !entry.target.__adsbygooglePushed) {
                        try {
                            (window.adsbygoogle = window.adsbygoogle || []).push({});
                            entry.target.__adsbygooglePushed = true;
                        } catch (e) {
                            console.warn('AdSense push error (observer):', e);
                        }
                        ro.unobserve(entry.target);
                    }
                }
            });
            ro.observe(ad);
        } else {
            // Fallback: retry a few times
            let attempts = 20;
            const iv = setInterval(() => {
                if (elementHasUsableSize(ad) && !ad.__adsbygooglePushed) {
                    try {
                        (window.adsbygoogle = window.adsbygoogle || []).push({});
                        ad.__adsbygooglePushed = true;
                    } catch (e) {
                        console.warn('AdSense push error (interval):', e);
                    }
                    clearInterval(iv);
                }
                if (--attempts <= 0) {
                    clearInterval(iv);
                }
            }, 250);
        }
    }
    
    // Ad blocker detection
    function detectAdBlocker() {
        const testAd = document.createElement('div');
        testAd.innerHTML = '&nbsp;';
        testAd.className = 'adsbox ads ad adsbygoogle';
        testAd.style.cssText = 'position:absolute!important;left:-10000px!important;width:1px!important;height:1px!important;';
        
        document.body.appendChild(testAd);
        
        setTimeout(() => {
            try {
                if (testAd.offsetHeight === 0 || 
                    testAd.offsetLeft === -10000 || 
                    testAd.offsetTop === 0 || 
                    window.getComputedStyle(testAd).display === 'none') {
                    
                    adBlockDetected = true;
                    handleAdBlockerDetected();
                }
                
                document.body.removeChild(testAd);
            } catch(e) {
                adBlockDetected = true;
                handleAdBlockerDetected();
            }
        }, 100);
    }
    
    // Ad blocker tespit edildiğinde
    function handleAdBlockerDetected() {
        
        // Analytics'e gönder (varsa)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'ad_blocker_detected', {
                'event_category': 'Ad Blocker',
                'non_interaction': true
            });
        }
        
        // Strict modda alternatif mesaj göster
        if (cfg.adblockStrictMode) {
            const adZones = document.querySelectorAll('.ad-zone');
            adZones.forEach(zone => {
                if (!zone.children.length || zone.children[0].classList.contains('ad-lazy-placeholder')) {
                    zone.innerHTML = `
                        <div class="ad-blocker-message bg-light border rounded p-3 text-center">
                            <div class="small text-muted">
                                <i class="fas fa-shield-alt me-2"></i>
                                Bu içeriği desteklemek için reklam engelleyicinizi kapatabilirsiniz.
                            </div>
                        </div>
                    `;
                }
            });
        }
    }
    
    // AdSense reklamlarını yükle
    function initializeAds() {
        if (adsInitialized) {
            return;
        }
        if (cfg.adblockStrictMode && adBlockDetected) {
            return;
        }
        
        const adsenseElements = document.querySelectorAll('.adsbygoogle');
        adsenseElements.forEach(ad => pushAdWhenReady(ad));
        
        adsInitialized = true;
    }
    
    // Lazy loading için IntersectionObserver
    function initializeLazyAds() {
        const lazyAds = document.querySelectorAll('.ad-lazy-placeholder');
        
        if (!lazyAds.length) {
            return;
        }
        
        const adObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const placeholder = entry.target;
                    const zoneName = placeholder.dataset.zone;
                    
                    // AJAX ile reklam içeriğini yükle
                    loadAdContent(zoneName, placeholder);
                    adObserver.unobserve(placeholder);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.1
        });
        
        lazyAds.forEach(ad => adObserver.observe(ad));
    }
    
    // Reklam içeriğini AJAX ile yükle
    async function loadAdContent(zoneName, placeholder) {
        try {
            const response = await fetch(joinUrl(cfg.apiBaseUrl || '/api', `ads/load-zone/${encodeURIComponent(zoneName)}`));
            const data = await response.json();
            
            if (data.success) {
                placeholder.outerHTML = data.html;
                
                // Yeni eklenen AdSense reklamlarını initialize et
                setTimeout(initializeAds, 100);
            } else {
                placeholder.style.display = 'none';
            }
        } catch(error) {
            console.error('Ad loading error:', error);
            placeholder.style.display = 'none';
        }
    }
    
    // Reklam performans tracking
    function trackAdPerformance() {
        // Reklam görünürlüğünü izle
        const adZones = document.querySelectorAll('.ad-zone[data-zone]');
        
        const performanceObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const zone = entry.target;
                    const zoneName = zone.dataset.zone;
                    
                    // Görüntülenme kaydı
                    trackAdImpression(zoneName);
                }
            });
        }, {
            threshold: 0.5 // %50 görünür olduğunda say
        });
        
        adZones.forEach(zone => performanceObserver.observe(zone));
        
        // Tıklama tracking
        adZones.forEach(zone => {
            zone.addEventListener('click', () => {
                const zoneName = zone.dataset.zone;
                trackAdClick(zoneName);
            });
        });
    }
    
    // Reklam gösterimi tracking
    function trackAdImpression(zoneName) {
        if (adBlockDetected && cfg.adblockStrictMode) return;
        fetch(joinUrl(cfg.apiBaseUrl || '/api', 'ads/track-impression'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                zone: zoneName,
                url: window.location.pathname,
                timestamp: Date.now()
            })
        }).catch(error => {
            console.warn('Ad impression tracking failed:', error);
        });
    }
    
    // Reklam tıklama tracking  
    function trackAdClick(zoneName) {
        if (adBlockDetected && cfg.adblockStrictMode) return;
        fetch(joinUrl(cfg.apiBaseUrl || '/api', 'ads/track-click'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                zone: zoneName,
                url: window.location.pathname,
                timestamp: Date.now()
            })
        }).catch(error => {
            console.warn('Ad click tracking failed:', error);
        });
    }
    
    // AdSense hata handling
    window.addEventListener('error', (e) => {
        if (e.target && e.target.tagName === 'SCRIPT' && 
            e.target.src && e.target.src.includes('googlesyndication.com')) {
            console.warn('AdSense script loading failed');
            handleAdBlockerDetected();
        }
    });
    
    // Sayfa yüklendiğinde çalışacak fonksiyonlar
    document.addEventListener('DOMContentLoaded', function() {
        // Ad blocker kontrolü
        setTimeout(detectAdBlocker, 1000);
        
        // Lazy loading ads
        setTimeout(initializeLazyAds, 500);
        
        // Performance tracking
        setTimeout(trackAdPerformance, 2000);
        
        // AdSense initialize
        setTimeout(initializeAds, 1500);
    });
    
    // Global fonksiyonlar
    window.LoomixAds = {
        reinitialize: initializeAds,
        detectAdBlocker: detectAdBlocker,
        trackImpression: trackAdImpression,
        trackClick: trackAdClick,
        isAdBlockDetected: () => adBlockDetected
    };
})();
