<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Nyitás előtti ébresztő</p>
        <h1>Hajnali Kávézó</h1>
        <p class="lead">Aki 04:00 és 06:00 között érkezik kávézni, automatikusan 30% kedvezményt kap minden kávéitalra.</p>
        <div class="hero-actions">
            <a class="button primary" href="<?= h(route_url('kepek')) ?>">Galéria</a>
            <a class="button ghost" href="<?= h(route_url('kapcsolat')) ?>">Asztalt kérnék</a>
        </div>
    </div>
</section>

<section class="section promo-grid">
    <article>
        <span class="metric">04-06</span>
        <h2>Hajnali kedvezmény</h2>
        <p>Az első vonat, az első meeting vagy egy csendes tanulós reggel mellé jár a 30% kedvezmény.</p>
    </article>
    <article>
        <span class="metric">30%</span>
        <h2>Kávéra számolva</h2>
        <p>Espresso, cappuccino, latte és filter kávé is része az akciónak a megadott idősávban.</p>
    </article>
    <article>
        <span class="metric">PHP</span>
        <h2>Élő webalkalmazás</h2>
        <p>Regisztráció, üzenetküldés, képfeltöltés és CRUD felület kezeli a kávézó tartalmát.</p>
    </article>
</section>

<section class="section split">
    <div>
        <p class="eyebrow">Saját videó</p>
        <h2>Hajnali hangulat</h2>
        <video controls muted preload="metadata" poster="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80">
            <source src="assets/video/hajnali-kavezo.mp4" type="video/mp4">
        </video>
    </div>
    <div>
        <p class="eyebrow">Szolgáltatói videó</p>
        <h2>Kávékészítés inspiráció</h2>
        <div class="video-frame">
            <iframe src="https://www.youtube.com/embed/1oB1oDrDkHM" title="Coffee brewing video" allowfullscreen></iframe>
        </div>
    </div>
</section>

<section class="section split map-section">
    <div>
        <p class="eyebrow">Cím</p>
        <h2>Budapest, Váci utca 1.</h2>
        <p>A demonstrációs kávézó belvárosi címen szerepel, hogy a beadandó Google térképes követelménye is teljesüljön.</p>
    </div>
    <iframe class="map" title="Hajnali Kávézó térkép" src="https://maps.google.com/maps?q=Budapest%2C%20V%C3%A1ci%20utca%201&t=&z=15&ie=UTF8&iwloc=&output=embed"></iframe>
</section>

