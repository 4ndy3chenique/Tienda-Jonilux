<?php
// Migracion selectiva de la marca publica. CLI; por defecto solo muestra el plan.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('ABSPATH')) {
    define('DISABLE_WP_CRON', true);
    define('WP_HTTP_BLOCK_EXTERNAL', true);
    require dirname(__DIR__) . '/wp-load.php';
}
if (!function_exists('wc_get_coupon_id_by_code')) {
    throw new RuntimeException('WooCommerce debe estar activo.');
}

function jonilux_brand_text($text) {
    $text = str_replace('JONILUX / LUNARIS', 'JONILUX', $text);
    return preg_replace_callback('/lunaris/i', static function ($match) {
        return $match[0] === strtoupper($match[0]) ? 'JONILUX' : ($match[0] === strtolower($match[0]) ? 'jonilux' : 'Jonilux');
    }, $text);
}

function jonilux_brand_html($html) {
    $processor = new WP_HTML_Tag_Processor($html);
    while ($processor->next_token()) {
        if ($processor->get_token_type() !== '#text') { continue; }
        $text = $processor->get_modifiable_text();
        $updated = jonilux_brand_text($text);
        if ($updated !== $text) { $processor->set_modifiable_text($updated); }
    }
    return $processor->get_updated_html();
}

$apply = in_array('--apply', $argv, true);
$old_id = wc_get_coupon_id_by_code('lunarisvip');
$new_id = wc_get_coupon_id_by_code('joniluxvip');
if ($old_id && $new_id && $old_id !== $new_id) {
    throw new RuntimeException('Hay dos cupones distintos. Resolver la colision antes de cambiar datos.');
}
if (!$old_id && !$new_id) {
    throw new RuntimeException('No se encontro el cupon VIP existente.');
}

// Lista comprobada en el diagnostico: texto de productos, medios y terminos.
// No se modifican slugs, HTML de plantillas, archivos, pedidos ni clientes.
$posts = [];
foreach ([41, 42, 43, 44, 45, 46, 47, 48, 184] as $id) {
    $post = get_post($id);
    if (!$post || !in_array($post->post_type, ['attachment', 'product', 'page'], true)) {
        throw new RuntimeException('El contenido esperado no existe: ' . $id);
    }
    foreach (['post_title', 'post_content', 'post_excerpt'] as $field) {
        $replacement = $field === 'post_title' ? jonilux_brand_text($post->$field) : jonilux_brand_html($post->$field);
        if ($replacement !== $post->$field) {
            $posts[$id]['slug'] = $post->post_name;
            $posts[$id]['before'] = array_intersect_key((array) $post, array_flip(['post_title', 'post_content', 'post_excerpt', 'post_content_filtered', 'post_name']));
            $posts[$id]['after'][$field] = $replacement;
        }
    }
}
$options = [];
foreach (['blogname', 'blogdescription', 'woocommerce_email_from_name'] as $name) {
    $value = get_option($name);
    if (is_string($value) && jonilux_brand_text($value) !== $value) {
        $options[$name] = ['before' => $value, 'after' => jonilux_brand_text($value)];
    }
}
$coupon = new WC_Coupon($old_id ?: $new_id);
$description = jonilux_brand_text($coupon->get_description());
$coupon_change = $coupon->get_code() !== 'joniluxvip' || $description !== $coupon->get_description();
$before = $coupon->get_data();
unset($before['code'], $before['description'], $before['date_modified']);
$terms_hash = hash('sha256', wp_json_encode($before));
$plan = ['option_names' => array_keys($options), 'post_ids' => array_keys($posts), 'coupon_id' => $coupon->get_id(), 'rename_coupon' => $coupon_change];
echo wp_json_encode($plan, JSON_PRETTY_PRINT), "\n";
if (!$apply || (!$options && !$posts && !$coupon_change)) {
    echo $apply ? "SIN CAMBIOS PENDIENTES\n" : "SIMULACION: usar --apply para ejecutar.\n";
    exit;
}

$backup_dir = dirname(__DIR__) . '/.local-backups';
if (!is_dir($backup_dir) && !wp_mkdir_p($backup_dir)) {
    throw new RuntimeException('No se pudo crear el directorio local de respaldo.');
}
$backup = $backup_dir . '/jonilux-brand-before-' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.json';
$snapshot = ['options' => $options, 'posts' => $posts, 'coupon' => ['id' => $coupon->get_id(), 'code' => $coupon->get_code(), 'description' => $coupon->get_description(), 'conditions_sha256' => $terms_hash]];
if (file_put_contents($backup, wp_json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) === false) {
    throw new RuntimeException('No se pudo guardar el respaldo; no se cambiaron datos.');
}

foreach ($options as $name => $change) {
    update_option($name, $change['after']);
    if (get_option($name) !== $change['after']) { throw new RuntimeException('No se actualizo la opcion ' . $name); }
}
// WordPress recalcula unicidad incluso al editar solo el titulo de un medio.
// Algunos medios existentes comparten slug con su producto: conservarlo aqui.
$preserve_slug = static function ($override, $slug, $id) use ($posts) {
    return isset($posts[$id]) && $slug === $posts[$id]['slug'] ? $slug : $override;
};
add_filter('pre_wp_unique_post_slug', $preserve_slug, PHP_INT_MAX, 3);
// Se preserva HTML existente, sin recortarlo por ejecutar en CLI sin usuario.
// Retirar solo los filtros KSES de estos campos y restaurarlos al terminar.
$kses_filters = [];
foreach (['title_save_pre' => 'wp_filter_kses', 'content_save_pre' => 'wp_filter_post_kses', 'excerpt_save_pre' => 'wp_filter_post_kses', 'content_filtered_save_pre' => 'wp_filter_post_kses'] as $hook => $callback) {
    $priority = has_filter($hook, $callback);
    if ($priority !== false) {
        $kses_filters[] = [$hook, $callback, $priority];
        remove_filter($hook, $callback, $priority);
    }
}
try {
    foreach ($posts as $id => $change) {
        $result = wp_update_post(wp_slash(['ID' => $id, 'post_name' => $change['slug']] + $change['after']), true);
        if (is_wp_error($result)) { throw new RuntimeException($result->get_error_message()); }
    }
} finally {
    remove_filter('pre_wp_unique_post_slug', $preserve_slug, PHP_INT_MAX);
    foreach ($kses_filters as [$hook, $callback, $priority]) { add_filter($hook, $callback, $priority); }
}
if ($coupon_change) {
    $coupon->set_code('joniluxvip');
    $coupon->set_description($description);
    $coupon->save();
    $saved = new WC_Coupon($coupon->get_id());
    $after = $saved->get_data();
    unset($after['code'], $after['description'], $after['date_modified']);
    if ($terms_hash !== hash('sha256', wp_json_encode($after))) {
        throw new RuntimeException('Las condiciones del cupon variaron; revisar antes de usarlo.');
    }
}
echo "MARCA ACTUALIZADA; condiciones e ID del cupon conservados.\n";
