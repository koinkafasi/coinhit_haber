@php($settings = $siteSettings ?? [])
<div class="cookie-bar" data-type="cookiePolicy" data-cookie-bar hidden>
    <div class="cookie-inner container">
        <p class="cookie-text">
            Sitemizde deneyiminizi iyileştirmek ve kişiselleştirilmiş içerik sunmak için çerezler kullanılmaktadır.
            Detaylar için
            <a href="/cerez-politikasi">Çerez Politikası</a> ve
            <a href="/aydinlatma-metni">Aydınlatma Metni</a>'ni inceleyebilirsiniz.
        </p>
        <div class="cookie-actions">
            <button type="button" class="cookie-accept" data-cookie-accept>Kabul Et</button>
            <button type="button" class="cookie-close" data-cookie-close aria-label="Kapat">&times;</button>
        </div>
    </div>
</div>