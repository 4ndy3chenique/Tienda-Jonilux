<?php
/**
 * Title: JONILUX home
 * Slug: lunaris/home
 * Categories: featured
 * Inserter: no
 */
if (!defined("ABSPATH")) { exit; }
$base = get_stylesheet_directory_uri() . "/assets/images/";
$shop = function_exists("wc_get_page_permalink") ? wc_get_page_permalink("shop") : home_url("/shop/");
$brand = get_bloginfo('name');
$collections = [];
foreach (['hombre', 'mujer', 'accesorios'] as $slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    $link = $term ? get_term_link($term) : $shop;
    $collections[$slug] = ['url' => is_wp_error($link) ? $shop : $link, 'count' => $term ? (int) $term->count : 0];
}

// Helper seguro para renderizar tarjetas de producto
if (!function_exists('lunaris_render_product_card')) {
    function lunaris_render_product_card($product, $base) {
        $pid = $product->get_id();
        $permalink = esc_url($product->get_permalink());
        $title = $product->get_name();
        $price_html = $product->get_price_html();
        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : esc_url($base . 'accessories.webp');
        
        $terms = get_the_terms($pid, 'product_cat');
        $cat_name = get_bloginfo('name');
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $t) {
                if ($t->slug !== 'uncategorized' && $t->slug !== 'hombre' && $t->slug !== 'mujer') {
                    $cat_name = $t->name;
                    break;
                }
            }
        }
        
        $rating = $product->get_average_rating();
        $rating_count = $product->get_rating_count();
        ?>
        <article class="lunaris-product-card">
            <a href="<?php echo $permalink; ?>" class="lunaris-card-link">
                <div class="lunaris-product-thumb">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" width="1200" height="1600" loading="lazy">
                    <span class="lunaris-card-badge"><?php echo esc_html($cat_name); ?></span>
                </div>
                <div class="lunaris-card-details">
                    <?php if ($rating_count > 0) : ?>
                    <div class="lunaris-card-stars">
                            <?php echo wc_get_rating_html((float) $rating, $rating_count); ?>
                    </div>
                    <?php endif; ?>
                    <h3 class="lunaris-card-title"><?php echo esc_html($title); ?></h3>
                    <div class="lunaris-product-price">
                        <?php echo $price_html; ?>
                    </div>
                    <span class="lunaris-card-cta">Ver producto <span aria-hidden="true">↗</span></span>
                </div>
            </a>
        </article>
        <?php
    }
}

// Consultas segmentadas por categoría
$hombre_products = function_exists('wc_get_products') ? wc_get_products([
    'status' => 'publish',
    'limit' => 4,
    'category' => ['hombre'],
    'orderby' => 'date',
    'order' => 'DESC'
]) : [];

$mujer_products = function_exists('wc_get_products') ? wc_get_products([
    'status' => 'publish',
    'limit' => 4,
    'category' => ['mujer'],
    'orderby' => 'date',
    'order' => 'DESC'
]) : [];

$accesorios_products = function_exists('wc_get_products') ? wc_get_products([
    'status' => 'publish',
    'limit' => 4,
    'category' => ['accesorios', 'accesorios-mujer', 'calzado', 'zapatillas'],
    'orderby' => 'date',
    'order' => 'DESC'
]) : [];
?>
<!-- wp:html -->
<main id="contenido" class="lunaris-home">

    <!-- HERO SECTION -->
    <section class="lunaris-hero" aria-labelledby="lunaris-title">
        <div class="lunaris-hero-copy">
            <p class="lunaris-eyebrow"><span aria-hidden="true">—</span> <?php echo esc_html($brand); ?> / Esenciales urbanos</p>
            <h1 id="lunaris-title">Tu estilo.<br><span>Tu propia órbita.</span></h1>
            <p>Esenciales urbanos con cortes contemporáneos, precios transparentes y sin complicaciones.</p>
            <div class="lunaris-hero-buttons">
                <a class="lunaris-button" href="<?php echo esc_url($shop); ?>">Explorar la colección <span aria-hidden="true">↗</span></a>
                <a class="lunaris-button secondary" href="#colecciones">Ver categorías <span aria-hidden="true">↓</span></a>
            </div>
            <div class="lunaris-hero-meta"><span>Hombre · Mujer · Accesorios</span><span>Encuentra eso que va contigo.</span></div>
        </div>
        <figure class="lunaris-hero-visual">
            <img src="<?php echo esc_url($base . 'hero.webp'); ?>" alt="Conjunto urbano negro en una calle de la ciudad" width="1920" height="700" fetchpriority="high">
            <figcaption><span>01 / Esencia urbana</span><p>Menos ruido.<br>Más personalidad.</p></figcaption>
        </figure>
    </section>

    <!-- BANNER DE GARANTÍAS / BENEFICIOS -->
    <div class="lunaris-strip">
        <span>Envíos Lima 24-48h y Provincias</span>
        <span>Yape / Plin / Transferencia / Contra Entrega</span>
        <span>Garantía de cambio por 7 días</span>
        <span>Guía de tallas oficial <?php echo esc_html($brand); ?></span>
    </div>

    <!-- SECCIÓN 01: SEGMENTACIÓN DE CATEGORÍAS -->
    <section class="lunaris-section" id="colecciones" aria-labelledby="colecciones-title">
        <div class="lunaris-section-head">
            <div>
                <p class="lunaris-eyebrow">01 / Categorías Principales</p>
                <h2 id="colecciones-title">Elige tu categoría</h2>
            </div>
            <a class="lunaris-text-link" href="<?php echo esc_url($shop); ?>">Ver catálogo completo ↗</a>
        </div>
        <div class="lunaris-categories">
            <a class="lunaris-category" href="<?php echo esc_url($collections['hombre']['url']); ?>">
                <img src="<?php echo esc_url($base.'men.webp'); ?>" alt="Colección de ropa urbana para hombre: hoodies y polos" width="1920" height="500" loading="lazy">
                <div class="lunaris-category-label">
                    <div>
                        <strong>Hombre</strong>
                        <small><?php echo (int) $collections['hombre']['count']; ?> productos · Tu esencia, sin reglas</small>
                    </div>
                    <span class="lunaris-cat-btn">Ver Colección ↗</span>
                </div>
            </a>
            <a class="lunaris-category" href="<?php echo esc_url($collections['mujer']['url']); ?>">
                <img src="<?php echo esc_url($base.'women.webp'); ?>" alt="Colección de ropa urbana para mujer: tops, vestidos y faldas" width="1920" height="500" loading="lazy">
                <div class="lunaris-category-label">
                    <div>
                        <strong>Mujer</strong>
                        <small><?php echo (int) $collections['mujer']['count']; ?> productos · A tu manera</small>
                    </div>
                    <span class="lunaris-cat-btn">Ver Colección ↗</span>
                </div>
            </a>
            <a class="lunaris-category" href="<?php echo esc_url($collections['accesorios']['url']); ?>">
                <img src="<?php echo esc_url($base.'accessories.webp'); ?>" alt="Selección de accesorios para completar tu estilo" width="1200" height="800" loading="lazy">
                <div class="lunaris-category-label">
                    <div><strong>Accesorios</strong><small><?php echo (int) $collections['accesorios']['count']; ?> productos · El detalle hace la diferencia</small></div>
                    <span class="lunaris-cat-btn" aria-hidden="true">↗</span>
                </div>
            </a>
        </div>
    </section>

    <!-- BARRA DE ACCESO RÁPIDO ENTRE CATEGORÍAS -->
    <div class="lunaris-category-tabs-bar">
        <div class="lunaris-category-tabs-inner">
            <span class="tabs-label">Explorar por categoría:</span>
            <a href="#coleccion-hombre" class="cat-tab-btn">Hombre</a>
            <a href="#coleccion-mujer" class="cat-tab-btn">Mujer</a>
            <a href="#accesorios" class="cat-tab-btn">Accesorios & Calzado</a>
            <a href="<?php echo esc_url($shop); ?>" class="cat-tab-btn all">Todo el Catálogo</a>
        </div>
    </div>

    <!-- SECCIÓN 02: COLECCIÓN HOMBRE -->
    <section class="lunaris-section lunaris-collection-section" id="coleccion-hombre" aria-labelledby="hombre-title">
        <div class="lunaris-section-head">
            <div>
                <p class="lunaris-eyebrow">02 / Colección Masculina</p>
                <h2 id="hombre-title">Ropa de Hombre</h2>
                <p class="lunaris-section-sub">Polos de alto gramaje, hoodies urbanos, sobrecamisas vintage y pantalones cargo.</p>
            </div>
            <a class="lunaris-text-link" href="<?php echo esc_url($collections['hombre']['url']); ?>">Explorar hombre ↗</a>
        </div>

        <div class="lunaris-products-grid">
            <?php
            if (!empty($hombre_products)) {
                foreach ($hombre_products as $p) {
                    lunaris_render_product_card($p, $base);
                }
            }
            ?>
        </div>
    </section>

    <!-- SECCIÓN 03: COLECCIÓN MUJER -->
    <section class="lunaris-section lunaris-collection-section" id="coleccion-mujer" aria-labelledby="mujer-title" style="background:#fafaf8; border-top:1px solid #ebece6; border-bottom:1px solid #ebece6;">
        <div class="lunaris-section-head">
            <div>
                <p class="lunaris-eyebrow">03 / Colección Femenina</p>
                <h2 id="mujer-title">Ropa de Mujer</h2>
                <p class="lunaris-section-sub">Tops contemporáneos, vestidos ligeros, faldas casuales y cardigans con estilo urbano.</p>
            </div>
            <a class="lunaris-text-link" href="<?php echo esc_url($collections['mujer']['url']); ?>">Explorar mujer ↗</a>
        </div>

        <div class="lunaris-products-grid">
            <?php
            if (!empty($mujer_products)) {
                foreach ($mujer_products as $p) {
                    lunaris_render_product_card($p, $base);
                }
            }
            ?>
        </div>
    </section>

    <!-- SECCIÓN 04: ACCESORIOS & CALZADO -->
    <section class="lunaris-section" id="accesorios" aria-labelledby="accesorios-title">
        <div class="lunaris-section-head">
            <div>
                <p class="lunaris-eyebrow">04 / El Toque Final</p>
                <h2 id="accesorios-title">Accesorios & Calzado</h2>
                <p class="lunaris-section-sub">Complementa tu outfit con gorras de diseño minimalista, pulseras de hematita y zapatillas urbanas.</p>
            </div>
            <a class="lunaris-text-link" href="<?php echo esc_url($collections['accesorios']['url']); ?>">Explorar accesorios ↗</a>
        </div>

        <div class="lunaris-products-grid">
            <?php
            if (!empty($accesorios_products)) {
                foreach ($accesorios_products as $p) {
                    lunaris_render_product_card($p, $base);
                }
            }
            ?>
        </div>
    </section>

    <!-- MANIFIESTO DE MARCA -->
    <section class="lunaris-section lunaris-story" id="lunaris" aria-labelledby="story-title">
        <div>
            <p class="lunaris-eyebrow">El universo <?php echo esc_html($brand); ?></p>
            <h2 id="story-title">Vestir también es expresarte.</h2>
        </div>
        <div>
            <p style="margin-bottom:15px;">JONILUX nació en 2026 como una propuesta de estudiantes de UPN para transformar la compra de ropa urbana: frente al desorden y la falta de transparencia de precios y tallas en redes sociales, creamos un e-commerce formal con catálogo en tiempo real, guía de medidas exacta y despacho confiable.</p>
            <p style="color:#888;font-size:14px;">Propuesta de valor: moda moderna, precios claros y compra sin complicaciones para jóvenes de todo el Perú.</p>
        </div>
    </section>

</main>
<!-- /wp:html -->
