<?php
/**
 * Plugin Name: JONILUX – Checkout Optimizado para Perú (DNI / RUC / Referencias)
 * Description: Añade el campo obligatorio de DNI/RUC para emisión de boleta electrónica, hace opcional el código postal y adapta las referencias de dirección para compras en Lima y Provincias.
 * Version: 1.0.0
 * Author: JONILUX Team
 */

defined('ABSPATH') || exit;

function jonilux_sanitizar_documento($value) {
    return is_scalar($value) ? trim(sanitize_text_field((string) $value)) : '';
}

function jonilux_documento_valido($value) {
    return is_string($value) && preg_match('/^(?:[0-9]{8}|[0-9]{11})$/D', $value) === 1;
}

// El checkout actual usa bloques: los hooks clasicos no registran campos en el Store API.
add_action('woocommerce_init', function () {
    if (!function_exists('woocommerce_register_additional_checkout_field')) {
        return;
    }
    woocommerce_register_additional_checkout_field(array(
        'id' => 'jonilux/documento',
        'label' => 'DNI / RUC (para boleta o factura)',
        'location' => 'order',
        'type' => 'text',
        'required' => true,
        'attributes' => array('maxLength' => 11, 'pattern' => '[0-9]{8}|[0-9]{11}'),
        'sanitize_callback' => 'jonilux_sanitizar_documento',
        'validate_callback' => function ($value) {
            if (!jonilux_documento_valido($value)) {
                return new WP_Error('jonilux_documento_invalido', 'Ingresa un DNI de 8 digitos o un RUC de 11 digitos, solo numeros.');
            }
        },
    ));
});

// Conservar la misma clave usada por los pedidos clasicos, compatible con HPOS.
add_action('woocommerce_set_additional_field_value', function ($key, $value, $group, $object) {
    if ($key === 'jonilux/documento' && $group === 'other' && $object instanceof WC_Order) {
        $object->update_meta_data('_billing_dni', jonilux_sanitizar_documento($value));
    }
}, 10, 4);

// 1. Modificar campos del formulario de Checkout
add_filter('woocommerce_checkout_fields', 'jonilux_custom_checkout_fields');
function jonilux_custom_checkout_fields($fields) {
    // Campo obligatorio DNI / RUC
    $fields['billing']['billing_dni'] = array(
        'type'        => 'text',
        'label'       => 'DNI / RUC (para boleta o factura electrónica)',
        'placeholder' => 'Ej. 72345678 (DNI) o 20612345678 (RUC)',
        'required'    => true,
        'class'       => array('form-row-wide'),
        'priority'    => 25,
        'clear'       => true
    );

    // Ajustar Dirección 2 como Referencia de entrega
    if (isset($fields['billing']['billing_address_2'])) {
        $fields['billing']['billing_address_2']['label'] = 'Referencia de entrega';
        $fields['billing']['billing_address_2']['placeholder'] = 'Ej. Frente al parque, piso 3, portón negro';
        $fields['billing']['billing_address_2']['required'] = false;
    }

    if (isset($fields['shipping']['shipping_address_2'])) {
        $fields['shipping']['shipping_address_2']['label'] = 'Referencia de entrega';
        $fields['shipping']['shipping_address_2']['placeholder'] = 'Ej. Frente al parque, piso 3, portón negro';
        $fields['shipping']['shipping_address_2']['required'] = false;
    }

    // Código postal no obligatorio en Perú
    if (isset($fields['billing']['billing_postcode'])) {
        $fields['billing']['billing_postcode']['required'] = false;
        $fields['billing']['billing_postcode']['placeholder'] = 'Opcional';
    }
    if (isset($fields['shipping']['shipping_postcode'])) {
        $fields['shipping']['shipping_postcode']['required'] = false;
        $fields['shipping']['shipping_postcode']['placeholder'] = 'Opcional';
    }

    return $fields;
}

// 2. Validación en el Checkout
add_action('woocommerce_checkout_process', 'jonilux_validate_checkout_dni');
function jonilux_validate_checkout_dni() {
    $dni = jonilux_sanitizar_documento(wp_unslash($_POST['billing_dni'] ?? ''));
    if (!jonilux_documento_valido($dni)) {
        wc_add_notice('Ingresa un DNI de 8 digitos o un RUC de 11 digitos, solo numeros.', 'error');
    }
}

// 3. Guardar DNI en los metadatos del pedido (compatible con HPOS y post_meta clásico)
add_action('woocommerce_checkout_create_order', 'jonilux_save_dni_to_order', 10, 2);
function jonilux_save_dni_to_order($order, $data) {
    if (!empty($_POST['billing_dni'])) {
        $order->update_meta_data('_billing_dni', jonilux_sanitizar_documento(wp_unslash($_POST['billing_dni'])));
    }
}

// 4. Mostrar DNI en la pantalla de gestión del pedido en WP Admin
add_action('woocommerce_admin_order_data_after_billing_address', 'jonilux_display_dni_in_admin_order', 10, 1);
function jonilux_display_dni_in_admin_order($order) {
    $dni = $order->get_meta('_billing_dni');
    if ($dni) {
        echo '<p style="margin-top:8px;"><strong>DNI / RUC:</strong> ' . esc_html($dni) . '</p>';
    }
}

// 5. Mostrar DNI en la página de orden recibida y correos de cliente
add_action('woocommerce_order_details_after_customer_details', 'jonilux_display_dni_in_order_details');
function jonilux_display_dni_in_order_details($order) {
    $dni = $order->get_meta('_billing_dni');
    if ($dni) {
        echo '<p style="margin-top:10px;"><strong>Documento (DNI/RUC):</strong> ' . esc_html($dni) . '</p>';
    }
}

