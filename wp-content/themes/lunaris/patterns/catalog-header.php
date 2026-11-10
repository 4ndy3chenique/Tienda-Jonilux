<?php
/**
 * Title: JONILUX catalog header
 * Slug: lunaris/catalog-header
 * Categories: featured
 * Inserter: no
 */
if (!defined('ABSPATH')) { exit; }
if (!function_exists('wc_get_page_permalink')) { return; }
$shop = wc_get_page_permalink('shop');
$is_catalog = is_shop() && !is_search();
$title = $is_catalog ? 'La colección' : woocommerce_page_title(false);
?>
<!-- wp:html -->
<section class="lunaris-catalog-header" aria-labelledby="catalog-title">
    <div class="lunaris-catalog-header-inner">
        <?php woocommerce_breadcrumb(); ?>
        <p class="lunaris-eyebrow">Esenciales con identidad</p>
        <h1 id="catalog-title"><?php echo esc_html($title); ?></h1>
        <?php if ($is_catalog) : ?><p>Prendas y accesorios para moverte a tu manera.<br>Encuentra eso que va contigo.</p><?php endif; ?>
        <nav class="lunaris-catalog-links" aria-label="Categorías del catálogo">
            <a href="<?php echo esc_url($shop); ?>" <?php if ($is_catalog) echo 'aria-current="page"'; ?>>Todo el catálogo</a>
            <?php foreach (['hombre' => 'Hombre', 'mujer' => 'Mujer', 'accesorios' => 'Accesorios'] as $slug => $label) :
                $term = get_term_by('slug', $slug, 'product_cat');
                $url = $term ? get_term_link($term) : $shop;
                if (is_wp_error($url)) { $url = $shop; }
                ?>
                <a href="<?php echo esc_url($url); ?>" <?php if (is_product_category($slug)) echo 'aria-current="page"'; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</section>
<!-- /wp:html -->
