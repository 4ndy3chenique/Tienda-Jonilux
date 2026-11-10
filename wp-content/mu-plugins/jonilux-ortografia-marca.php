<?php
/**
 * Plugin Name: JONILUX – Correccion de Ortografia, Acentos y Marca
 * Description: Corrige automaticamente palabras mal escritas con caracteres corruptos (mojibake) en descripciones de productos, fichas y categorias, asegurando acentuacion limpia en espanol y forzando la marca JONILUX en todo el sitio.
 * Version: 1.0.0
 * Author: JONILUX Team
 */

defined('ABSPATH') || exit;

/**
 * Normaliza y repara caracteres corruptos y palabras en espanol
 */
function jonilux_reparar_texto($text) {
    if (empty($text) || !is_string($text)) {
        return $text;
    }

    // 1. Correccion estricta de la marca e idioma clave
    $text = str_replace('L U N A R I S', 'J O N I L U X', $text);
    $text = str_replace('LUNARIS', 'JONILUX', $text);
    $text = str_replace('Lunaris', 'Jonilux', $text);
    $text = str_replace(['My account', 'My Account'], 'Mi cuenta', $text);

    // 2. Mapeo de secuencias mojibake de codepages comunes (CP437, Latin1, UTF-8 mal interpretado)
    $mojibake = [
        '├┤' => 'ó',
        '├║' => 'ú',
        '├í' => 'á',
        '├®' => 'é',
        '├¡' => 'í',
        '├▒' => 'ñ',
        '├ó' => 'ó',
        '├│' => 'ó',
        '├ô' => 'Ó',
        '├Ü' => 'Ú',
        '├ü' => 'Á',
        '├ë' => 'É',
        '├CD' => 'Í',
        '├æ' => 'Ñ',
        'Ã¡' => 'á',
        'Ã©' => 'é',
        'Ã­' => 'í',
        'Ã³' => 'ó',
        'Ãº' => 'ú',
        'Ã±' => 'ñ',
        'Ã'  => 'í',
        'â€¢' => '•',
        'â€”' => '—',
        'â€“' => '–'
    ];
    $text = strtr($text, $mojibake);

    // 3. Diccionario ortografico exacto para terminos de ecommerce y prendas
    $diccionario = [
        '/\bAlgod[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Algodón',
        '/\balgod[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'algodón',
        '/\bVentilaci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Ventilación',
        '/\bventilaci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'ventilación',
        '/\bTalla\s+[^\s<>,.;:!?()\/]{1,4}nica\b/u' => 'Talla única',
        '/\btalla\s+[^\s<>,.;:!?()\/]{1,4}nica\b/u' => 'talla única',
        '/\bConfecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Confección',
        '/\bconfecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'confección',
        '/\bColecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Colección',
        '/\bcolecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'colección',
        '/\bDise[^\s<>,.;:!?()\/]{1,4}o\b/u' => 'Diseño',
        '/\bdise[^\s<>,.;:!?()\/]{1,4}o\b/u' => 'diseño',
        '/\bGarant[^\s<>,.;:!?()\/]{1,4}a\b/u' => 'Garantía',
        '/\bgarant[^\s<>,.;:!?()\/]{1,4}a\b/u' => 'garantía',
        '/\bPol[^\s<>,.;:!?()\/]{1,4}tica\b/u' => 'Política',
        '/\bpol[^\s<>,.;:!?()\/]{1,4}tica\b/u' => 'política',
        '/\bEdici[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Edición',
        '/\bedici[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'edición',
        '/\bAtenci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Atención',
        '/\batenci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'atención',
        '/\bDirecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Dirección',
        '/\bdirecci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'dirección',
        '/\bTel[^\s<>,.;:!?()\/]{1,4}fono\b/u' => 'Teléfono',
        '/\btel[^\s<>,.;:!?()\/]{1,4}fono\b/u' => 'teléfono',
        '/\bPer[^\s<>,.;:!?()\/]{1,3}\b/u' => 'Perú',
        '/\bT[^\s<>,.;:!?()\/]{1,4}rminos\b/u' => 'Términos',
        '/\bt[^\s<>,.;:!?()\/]{1,4}rminos\b/u' => 'términos',
        '/\bDescripci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Descripción',
        '/\bdescripci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'descripción',
        '/\bDevoluci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Devolución',
        '/\bdevoluci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'devolución',
        '/\bEnv[^\s<>,.;:!?()\/]{1,4}o\b/u' => 'Envío',
        '/\benv[^\s<>,.;:!?()\/]{1,4}o\b/u' => 'envío',
        '/\bEnv[^\s<>,.;:!?()\/]{1,4}os\b/u' => 'Envíos',
        '/\benv[^\s<>,.;:!?()\/]{1,4}os\b/u' => 'envíos',
        '/\bPromoci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Promoción',
        '/\bpromoci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'promoción',
        '/\bEst[^\s<>,.;:!?()\/]{1,4}ndar\b/u' => 'Estándar',
        '/\best[^\s<>,.;:!?()\/]{1,4}ndar\b/u' => 'estándar',
        '/\bDespu[^\s<>,.;:!?()\/]{1,4}s\b/u' => 'Después',
        '/\bdespu[^\s<>,.;:!?()\/]{1,4}s\b/u' => 'después',
        '/\bH[^\s<>,.;:!?()\/]{1,4}biles\b/u' => 'Hábiles',
        '/\bh[^\s<>,.;:!?()\/]{1,4}biles\b/u' => 'hábiles',
        '/\bP[^\s<>,.;:!?()\/]{1,4}gina\b/u' => 'Página',
        '/\bp[^\s<>,.;:!?()\/]{1,4}gina\b/u' => 'página',
        '/\bP[^\s<>,.;:!?()\/]{1,4}blica\b/u' => 'Pública',
        '/\bp[^\s<>,.;:!?()\/]{1,4}blica\b/u' => 'pública',
        '/\bP[^\s<>,.;:!?()\/]{1,4}blicas\b/u' => 'Públicas',
        '/\bp[^\s<>,.;:!?()\/]{1,4}blicas\b/u' => 'públicas',
        '/\bN[^\s<>,.;:!?()\/]{1,4}mero\b/u' => 'Número',
        '/\bn[^\s<>,.;:!?()\/]{1,4}mero\b/u' => 'número',
        '/\bC[^\s<>,.;:!?()\/]{1,4}digo\b/u' => 'Código',
        '/\bc[^\s<>,.;:!?()\/]{1,4}digo\b/u' => 'código',
        '/\bInal[^\s<>,.;:!?()\/]{1,4}mbrico/u' => 'Inalámbrico',
        '/\binal[^\s<>,.;:!?()\/]{1,4}mbrico/u' => 'inalámbrico',
        '/\bCat[^\s<>,.;:!?()\/]{1,4}logo\b/u' => 'Catálogo',
        '/\bcat[^\s<>,.;:!?()\/]{1,4}logo\b/u' => 'catálogo',
        '/\bB[^\s<>,.;:!?()\/]{1,4}isbol\b/u' => 'Béisbol',
        '/\bJoyer[^\s<>,.;:!?()\/]{1,4}a\b/u' => 'Joyería',
        '/\bjoyer[^\s<>,.;:!?()\/]{1,4}a\b/u' => 'joyería',
        '/\bReclamaci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'Reclamación',
        '/\breclamaci[^\s<>,.;:!?()\/]{1,4}n\b/u' => 'reclamación',
        '/\bAut[^\s<>,.;:!?()\/]{1,4}ntico\b/u' => 'Auténtico',
        '/\baut[^\s<>,.;:!?()\/]{1,4}ntico\b/u' => 'auténtico',
        '/\bCl[^\s<>,.;:!?()\/]{1,4}sico\b/u' => 'Clásico',
        '/\bcl[^\s<>,.;:!?()\/]{1,4}sico\b/u' => 'clásico',
        '/\bC[^\s<>,.;:!?()\/]{1,4}modo\b/u' => 'Cómodo',
        '/\bc[^\s<>,.;:!?()\/]{1,4}modo\b/u' => 'cómodo'
    ];

    return preg_replace(array_keys($diccionario), array_values($diccionario), $text);
}

/**
 * Limpia el HTML preservando etiquetas, atributos y enlaces
 */
function jonilux_limpiar_html_seguro($html) {
    if (empty($html) || !is_string($html)) {
        return $html;
    }

    // Dividir en fragmentos: etiquetas HTML vs texto visible
    $partes = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($partes)) {
        return $html;
    }

    for ($i = 0; $i < count($partes); $i += 2) {
        $partes[$i] = jonilux_reparar_texto($partes[$i]);
    }

    return implode('', $partes);
}

// 1. Forzar nombre de la tienda en todo WordPress
add_filter('option_blogname', fn() => 'JONILUX');
add_filter('bloginfo', function($val, $show) {
    return ($show === 'name') ? 'JONILUX' : $val;
}, 99, 2);

// 2. Filtros de WooCommerce y WordPress para contenido
add_filter('the_content', 'jonilux_limpiar_html_seguro', 999);
add_filter('the_title', 'jonilux_reparar_texto', 999);
add_filter('the_excerpt', 'jonilux_limpiar_html_seguro', 999);
add_filter('woocommerce_product_get_description', 'jonilux_limpiar_html_seguro', 999);
add_filter('woocommerce_product_get_short_description', 'jonilux_limpiar_html_seguro', 999);
add_filter('woocommerce_product_get_name', 'jonilux_reparar_texto', 999);
add_filter('woocommerce_short_description', 'jonilux_limpiar_html_seguro', 999);
add_filter('term_name', 'jonilux_reparar_texto', 999);
add_filter('term_description', 'jonilux_limpiar_html_seguro', 999);
add_filter('render_block', 'jonilux_limpiar_html_seguro', 999);

// 3. Buffer de salida global en frontend para garantizar 100% de cobertura
add_action('template_redirect', function () {
    if (!is_admin()) {
        ob_start('jonilux_limpiar_html_seguro');
    }
}, 1);
