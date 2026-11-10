<?php
/**
 * Plugin Name: JONILUX – Libro de Reclamaciones Virtual (INDECOPI)
 * Description: Sistema 100% funcional de Libro de Reclamaciones conforme a la Ley N° 29571 y D.S. 011-2011-PCM de INDECOPI con numeración correlativa, almacenamiento de registros, panel administrativo e impresión formal.
 * Version: 1.0.0
 * Author: JONILUX Team
 */

defined('ABSPATH') || exit;

// 1. Filtrar el contenido de la página del Libro de Reclamaciones
add_filter('the_content', 'jonilux_libro_reclamaciones_content_filter', 20);
function jonilux_libro_reclamaciones_content_filter($content) {
    if (is_page('libro-de-reclamaciones') || is_page(183) || (is_singular('page') && get_post_field('post_name') === 'libro-de-reclamaciones')) {
        return jonilux_render_libro_reclamaciones_html();
    }
    return $content;
}

// Shortcode alternativo
add_shortcode('jonilux_libro_reclamaciones', 'jonilux_render_libro_reclamaciones_html');

// 2. Renderizado del formulario y de la interfaz
function jonilux_render_libro_reclamaciones_html() {
    $counter = (int) get_option('jonilux_reclamos_counter', 42);
    $next_code = sprintf('REC-%s-%04d', date('Y'), $counter + 1);
    $ajax_url = admin_url('admin-ajax.php');

    ob_start();
    ?>
    <div class="jonilux-reclamaciones-wrap" id="jonilux-rec-wrapper">
        <style>
            .jonilux-reclamaciones-wrap {
                max-width: 900px;
                margin: 30px auto;
                padding: 35px 40px;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 25px rgba(0,0,0,0.06);
                font-family: Arial, Helvetica, sans-serif;
                color: #17211a;
            }
            .rec-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                border-bottom: 2px solid #111614;
                padding-bottom: 20px;
                margin-bottom: 25px;
                flex-wrap: wrap;
                gap: 15px;
            }
            .rec-header h1 {
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 6px 0;
                letter-spacing: -0.02em;
                color: #111614;
                text-transform: uppercase;
            }
            .rec-header p {
                margin: 0;
                font-size: 13px;
                color: #626b63;
            }
            .rec-badge-code {
                background: #111614;
                color: #ffffff;
                padding: 10px 18px;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: 0.05em;
                text-align: right;
            }
            .rec-badge-code span {
                display: block;
                color: #c8d4af;
                font-size: 16px;
                font-weight: 700;
                margin-top: 3px;
            }
            .rec-company-box {
                background: #f4f3ef;
                border-left: 4px solid #111614;
                padding: 16px 20px;
                border-radius: 6px;
                font-size: 13px;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .rec-section-title {
                font-size: 16px;
                font-weight: 700;
                color: #111614;
                border-bottom: 1px solid #dcded7;
                padding-bottom: 8px;
                margin: 25px 0 18px 0;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .rec-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 18px;
                margin-bottom: 15px;
            }
            .rec-grid-3 {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 18px;
                margin-bottom: 15px;
            }
            .rec-field {
                display: flex;
                flex-direction: column;
                margin-bottom: 15px;
            }
            .rec-field label {
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 6px;
                color: #2e3831;
            }
            .rec-field label .req {
                color: #c0392b;
            }
            .rec-input, .rec-select, .rec-textarea {
                padding: 12px 14px;
                border: 1px solid #c7cbc4;
                border-radius: 6px;
                font-size: 14px;
                font-family: inherit;
                background: #ffffff;
                color: #111614;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .rec-input:focus, .rec-select:focus, .rec-textarea:focus {
                border-color: #111614;
                outline: none;
                box-shadow: 0 0 0 2px rgba(17,22,20,0.15);
            }
            .rec-radio-group {
                display: flex;
                gap: 25px;
                background: #fbfbf9;
                padding: 15px 20px;
                border: 1px solid #e2e4de;
                border-radius: 8px;
                margin-bottom: 15px;
            }
            .rec-radio-label {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                cursor: pointer;
                font-size: 13px;
                line-height: 1.4;
            }
            .rec-radio-label input {
                margin-top: 3px;
            }
            .rec-legal-notice {
                background: #fafaf8;
                border: 1px dashed #c7cbc4;
                padding: 15px 20px;
                border-radius: 6px;
                font-size: 12px;
                color: #555d57;
                line-height: 1.5;
                margin: 25px 0 20px 0;
            }
            .rec-submit-btn {
                background: #111614;
                color: #ffffff;
                padding: 16px 36px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                cursor: pointer;
                transition: background 0.2s, transform 0.1s;
                display: inline-flex;
                align-items: center;
                gap: 12px;
            }
            .rec-submit-btn:hover {
                background: #2b3b33;
            }
            .rec-submit-btn:disabled {
                background: #999;
                cursor: not-allowed;
            }
            /* Success confirmation box */
            #jonilux-rec-success {
                display: none;
                padding: 30px;
                background: #f6fbf7;
                border: 2px solid #2ecc71;
                border-radius: 10px;
                margin-top: 20px;
            }
            .rec-success-head {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
                border-bottom: 1px solid #d4efdf;
                padding-bottom: 15px;
            }
            .rec-success-head svg {
                color: #27ae60;
                flex-shrink: 0;
            }
            .rec-success-head h3 {
                font-size: 20px;
                color: #1e8449;
                margin: 0 0 5px 0;
            }
            .rec-summary-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 13px;
            }
            .rec-summary-table td {
                padding: 8px 12px;
                border: 1px solid #d4efdf;
            }
            .rec-summary-table td.label-col {
                background: #eafaf1;
                font-weight: 600;
                width: 30%;
                color: #145a32;
            }
            .rec-actions {
                display: flex;
                gap: 15px;
                margin-top: 25px;
                flex-wrap: wrap;
            }
            .rec-print-btn {
                background: #111614;
                color: #ffffff;
                padding: 12px 24px;
                border: none;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            .rec-new-btn {
                background: transparent;
                color: #111614;
                padding: 12px 20px;
                border: 1px solid #111614;
                border-radius: 6px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
            }
            @media (max-width: 768px) {
                .jonilux-reclamaciones-wrap { padding: 25px 20px; }
                .rec-grid-2, .rec-grid-3 { grid-template-columns: 1fr; }
                .rec-radio-group { flex-direction: column; gap: 15px; }
            }
            /* Print mode styles */
            @media print {
                body * { visibility: hidden; }
                #jonilux-rec-success, #jonilux-rec-success * { visibility: visible; }
                #jonilux-rec-success {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    border: 1px solid #000;
                    box-shadow: none;
                    background: #fff !important;
                }
                .rec-actions { display: none !important; }
            }
        </style>

        <!-- Cabecera Oficial -->
        <div class="rec-header">
            <div>
                <h1>Libro de Reclamaciones Virtual</h1>
                <p>Conforme a lo establecido en el Código de Protección y Defensa del Consumidor (Ley N° 29571) - INDECOPI</p>
            </div>
            <div class="rec-badge-code">
                Hoja de Reclamación
                <span id="display-next-code"><?php echo esc_html($next_code); ?></span>
            </div>
        </div>

        <!-- Datos del Proveedor -->
        <div class="rec-company-box">
            <strong>Razón Social:</strong> JONILUX S.A.C. &nbsp;|&nbsp; <strong>RUC:</strong> 20612345678<br>
            <strong>Nombre Comercial:</strong> JONILUX (Moda y Accesorios Urbanos)<br>
            <strong>Establecimiento:</strong> Tienda Virtual E-Commerce (www.jonilux.pe)<br>
            <strong>Domicilio Fiscal:</strong> Av. Universitaria 1250, San Miguel, Lima - Perú
        </div>

        <!-- Formulario -->
        <form id="jonilux-reclamo-form" method="post" action="<?php echo esc_url($ajax_url); ?>">
            <input type="hidden" name="action" value="jonilux_guardar_reclamo">
            <?php wp_nonce_field('jonilux_reclamo_nonce', 'rec_nonce'); ?>

            <!-- 1. Identificación del Consumidor -->
            <div class="rec-section-title">1. Identificación del Consumidor Reclamante</div>
            
            <div class="rec-grid-2">
                <div class="rec-field">
                    <label for="rec_nombre">Nombres y Apellidos <span class="req">*</span></label>
                    <input type="text" id="rec_nombre" name="nombre" class="rec-input" required placeholder="Ej. Juan Pérez Quispe">
                </div>
                <div class="rec-field">
                    <label for="rec_tipo_doc">Tipo de Documento <span class="req">*</span></label>
                    <select id="rec_tipo_doc" name="tipo_doc" class="rec-select" required>
                        <option value="DNI">DNI (Documento Nacional de Identidad)</option>
                        <option value="CE">Carné de Extranjería</option>
                        <option value="PASAPORTE">Pasaporte</option>
                    </select>
                </div>
            </div>

            <div class="rec-grid-3">
                <div class="rec-field">
                    <label for="rec_num_doc">N° de Documento <span class="req">*</span></label>
                    <input type="text" id="rec_num_doc" name="num_doc" class="rec-input" required placeholder="Ej. 72345678">
                </div>
                <div class="rec-field">
                    <label for="rec_telefono">Teléfono / Celular <span class="req">*</span></label>
                    <input type="tel" id="rec_telefono" name="telefono" class="rec-input" required placeholder="Ej. 987654321">
                </div>
                <div class="rec-field">
                    <label for="rec_email">Correo Electrónico <span class="req">*</span></label>
                    <input type="email" id="rec_email" name="email" class="rec-input" required placeholder="nombre@correo.com">
                </div>
            </div>

            <div class="rec-field">
                <label for="rec_direccion">Domicilio / Dirección del Reclamante <span class="req">*</span></label>
                <input type="text" id="rec_direccion" name="direccion" class="rec-input" required placeholder="Av. / Jr. / Calle, N°, Distrito, Ciudad">
            </div>

            <div class="rec-field" style="margin-top: 5px;">
                <label style="display:flex;align-items:center;gap:8px;font-weight:normal;text-transform:none;cursor:pointer;">
                    <input type="checkbox" id="rec_menor_edad" name="es_menor" value="1">
                    <span>Marcar si el reclamante es <strong>menor de edad</strong> (se requerirán datos del padre/madre o apoderado).</span>
                </label>
            </div>
            <div id="rec_apoderado_wrap" class="rec-field" style="display:none;margin-top:10px;">
                <label for="rec_apoderado">Nombre completo del Padre, Madre o Tutor Legal</label>
                <input type="text" id="rec_apoderado" name="apoderado" class="rec-input" placeholder="Nombres y DNI del apoderado">
            </div>

            <!-- 2. Identificación del Bien Contratado -->
            <div class="rec-section-title">2. Identificación del Bien Contratado</div>

            <div class="rec-grid-3">
                <div class="rec-field">
                    <label for="rec_tipo_bien">Tipo de Bien <span class="req">*</span></label>
                    <select id="rec_tipo_bien" name="tipo_bien" class="rec-select" required>
                        <option value="Producto">Producto (Prenda / Ropa / Accesorio)</option>
                        <option value="Servicio">Servicio (Envío / Entrega / Atención)</option>
                    </select>
                </div>
                <div class="rec-field">
                    <label for="rec_pedido">N° de Pedido / Boleta (Opcional)</label>
                    <input type="text" id="rec_pedido" name="num_pedido" class="rec-input" placeholder="Ej. #1042">
                </div>
                <div class="rec-field">
                    <label for="rec_monto">Monto Reclamado en S/. (Opcional)</label>
                    <input type="number" step="0.01" id="rec_monto" name="monto" class="rec-input" placeholder="0.00">
                </div>
            </div>

            <div class="rec-field">
                <label for="rec_desc_bien">Descripción del Producto o Servicio Contratado</label>
                <input type="text" id="rec_desc_bien" name="desc_bien" class="rec-input" placeholder="Ej. Hoodie Negro Talla L / Polo Oversize">
            </div>

            <!-- 3. Detalle de la Reclamación -->
            <div class="rec-section-title">3. Detalle de la Reclamación y Pedido del Consumidor</div>

            <div class="rec-radio-group">
                <label class="rec-radio-label">
                    <input type="radio" name="tipo_accion" value="Reclamo" checked>
                    <span><strong>RECLAMO:</strong> Disconformidad relacionada con los productos o servicios adquiridos.</span>
                </label>
                <label class="rec-radio-label">
                    <input type="radio" name="tipo_accion" value="Queja">
                    <span><strong>QUEJA:</strong> Malestar o descontento respecto a la atención al cliente o trato recibido.</span>
                </label>
            </div>

            <div class="rec-field">
                <label for="rec_detalle">Detalle del Reclamo o Queja <span class="req">*</span></label>
                <textarea id="rec_detalle" name="detalle" rows="4" class="rec-textarea" required placeholder="Explica detalladamente los hechos ocurridos..."></textarea>
            </div>

            <div class="rec-field">
                <label for="rec_pedido_cliente">Pedido Concreto del Consumidor <span class="req">*</span></label>
                <textarea id="rec_pedido_cliente" name="pedido_cliente" rows="3" class="rec-textarea" required placeholder="Indica concretamente qué solución solicitas (ej. cambio de prenda, devolución de dinero, disculpas formales)..."></textarea>
            </div>

            <div class="rec-legal-notice">
                • <strong>Plazo legal de respuesta:</strong> Conforme al Código de Protección y Defensa del Consumidor (Ley N° 29571), JONILUX S.A.C. dará respuesta a su reclamación en un plazo máximo de <strong>quince (15) días hábiles</strong> improrrogables.<br>
                • La formulación del presente reclamo no excluye acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante <strong>INDECOPI</strong>.
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
                    <input type="checkbox" required name="terminos_legales" value="1">
                    <span>Declaro ser el titular del reclamo y doy fe de la veracidad de los datos consignados bajo responsabilidad.</span>
                </label>
            </div>

            <button type="submit" class="rec-submit-btn" id="btn-submit-rec">
                <span>Enviar Hoja de Reclamación</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </form>

        <!-- Contenedor de Éxito / Constancia Formal -->
        <div id="jonilux-rec-success">
            <div class="rec-success-head">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div>
                    <h3>¡Hoja de Reclamación Registrada con Éxito!</h3>
                    <p style="margin:0;font-size:13px;color:#2c3e50;">Su reclamo ha sido ingresado en nuestro Libro de Reclamaciones Virtual conforme a ley.</p>
                </div>
            </div>

            <table class="rec-summary-table">
                <tr>
                    <td class="label-col">Código Correlativo:</td>
                    <td><strong id="res-codigo" style="font-size:15px;color:#111614;"></strong></td>
                </tr>
                <tr>
                    <td class="label-col">Fecha y Hora de Registro:</td>
                    <td id="res-fecha"></td>
                </tr>
                <tr>
                    <td class="label-col">Proveedor:</td>
                    <td>JONILUX S.A.C. (RUC 20612345678)</td>
                </tr>
                <tr>
                    <td class="label-col">Consumidor Reclamante:</td>
                    <td id="res-cliente"></td>
                </tr>
                <tr>
                    <td class="label-col">Documento:</td>
                    <td id="res-doc"></td>
                </tr>
                <tr>
                    <td class="label-col">Contacto:</td>
                    <td id="res-contacto"></td>
                </tr>
                <tr>
                    <td class="label-col">Tipo de Solicitud:</td>
                    <td><strong id="res-tipo"></strong> (<span id="res-bien"></span>)</td>
                </tr>
                <tr>
                    <td class="label-col">Detalle del Reclamo/Queja:</td>
                    <td id="res-detalle" style="white-space: pre-wrap;"></td>
                </tr>
                <tr>
                    <td class="label-col">Pedido Concreto:</td>
                    <td id="res-pedido" style="white-space: pre-wrap;"></td>
                </tr>
                <tr>
                    <td class="label-col">Plazo de Respuesta:</td>
                    <td><strong>15 días hábiles</strong> (notificación formal a su correo electrónico).</td>
                </tr>
            </table>

            <div class="rec-actions">
                <button type="button" class="rec-print-btn" onclick="window.print()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    <span>Imprimir / Descargar Hoja (PDF)</span>
                </button>
                <button type="button" class="rec-new-btn" onclick="location.reload()">
                    Registrar otra reclamación
                </button>
            </div>
        </div>
    </div>

    <!-- Script de interacción y envío AJAX -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var chkMenor = document.getElementById('rec_menor_edad');
        var wrapApoderado = document.getElementById('rec_apoderado_wrap');
        var form = document.getElementById('jonilux-reclamo-form');
        var btnSubmit = document.getElementById('btn-submit-rec');
        var successBox = document.getElementById('jonilux-rec-success');

        if (chkMenor) {
            chkMenor.addEventListener('change', function() {
                wrapApoderado.style.display = this.checked ? 'block' : 'none';
            });
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                btnSubmit.disabled = true;
                btnSubmit.querySelector('span').textContent = 'Procesando registro...';

                var formData = new FormData(form);

                fetch('<?php echo esc_url($ajax_url); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        form.style.display = 'none';
                        successBox.style.display = 'block';

                        document.getElementById('res-codigo').textContent = data.data.codigo;
                        document.getElementById('res-fecha').textContent = data.data.fecha;
                        document.getElementById('res-cliente').textContent = data.data.nombre;
                        document.getElementById('res-doc').textContent = data.data.tipo_doc + ': ' + data.data.num_doc;
                        document.getElementById('res-contacto').textContent = data.data.telefono + ' / ' + data.data.email;
                        document.getElementById('res-tipo').textContent = data.data.tipo_accion.toUpperCase();
                        document.getElementById('res-bien').textContent = data.data.tipo_bien;
                        document.getElementById('res-detalle').textContent = data.data.detalle;
                        document.getElementById('res-pedido').textContent = data.data.pedido_cliente;

                        successBox.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        alert('Error al registrar: ' + (data.data || 'Ocurrió un error inesperado.'));
                        btnSubmit.disabled = false;
                        btnSubmit.querySelector('span').textContent = 'Enviar Hoja de Reclamación';
                    }
                })
                .catch(function(err) {
                    alert('Error de conexión al enviar el reclamo. Por favor intenta de nuevo.');
                    btnSubmit.disabled = false;
                    btnSubmit.querySelector('span').textContent = 'Enviar Hoja de Reclamación';
                });
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

// 3. Procesamiento y Almacenamiento AJAX
add_action('wp_ajax_jonilux_guardar_reclamo', 'jonilux_handle_guardar_reclamo');
add_action('wp_ajax_nopriv_jonilux_guardar_reclamo', 'jonilux_handle_guardar_reclamo');

function jonilux_handle_guardar_reclamo() {
    check_ajax_referer('jonilux_reclamo_nonce', 'rec_nonce');

    $nombre = sanitize_text_field($_POST['nombre'] ?? '');
    $tipo_doc = sanitize_text_field($_POST['tipo_doc'] ?? 'DNI');
    $num_doc = sanitize_text_field($_POST['num_doc'] ?? '');
    $telefono = sanitize_text_field($_POST['telefono'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $direccion = sanitize_text_field($_POST['direccion'] ?? '');
    $apoderado = sanitize_text_field($_POST['apoderado'] ?? '');
    $tipo_bien = sanitize_text_field($_POST['tipo_bien'] ?? 'Producto');
    $num_pedido = sanitize_text_field($_POST['num_pedido'] ?? '');
    $monto = floatval($_POST['monto'] ?? 0);
    $desc_bien = sanitize_text_field($_POST['desc_bien'] ?? '');
    $tipo_accion = sanitize_text_field($_POST['tipo_accion'] ?? 'Reclamo');
    $detalle = sanitize_textarea_field($_POST['detalle'] ?? '');
    $pedido_cliente = sanitize_textarea_field($_POST['pedido_cliente'] ?? '');

    if (empty($nombre) || empty($num_doc) || empty($email) || empty($detalle) || empty($pedido_cliente)) {
        wp_send_json_error('Por favor completa todos los campos obligatorios (*).');
    }

    // Generar correlativo oficial
    $counter = (int) get_option('jonilux_reclamos_counter', 42);
    $counter++;
    update_option('jonilux_reclamos_counter', $counter);
    $codigo = sprintf('REC-%s-%04d', date('Y'), $counter);
    $fecha = current_time('d/m/Y H:i:s');

    $nuevo_registro = [
        'codigo' => $codigo,
        'fecha' => $fecha,
        'nombre' => $nombre,
        'tipo_doc' => $tipo_doc,
        'num_doc' => $num_doc,
        'telefono' => $telefono,
        'email' => $email,
        'direccion' => $direccion,
        'apoderado' => $apoderado,
        'tipo_bien' => $tipo_bien,
        'num_pedido' => $num_pedido,
        'monto' => $monto,
        'desc_bien' => $desc_bien,
        'tipo_accion' => $tipo_accion,
        'detalle' => $detalle,
        'pedido_cliente' => $pedido_cliente,
        'estado' => 'Pendiente'
    ];

    // Almacenar en lista persistente en base de datos
    $historial = get_option('jonilux_reclamaciones_list', []);
    if (!is_array($historial)) {
        $historial = [];
    }
    array_unshift($historial, $nuevo_registro);
    update_option('jonilux_reclamaciones_list', $historial);

    // Enviar correo de confirmación al cliente si wp_mail funciona
    $asunto = sprintf('Constancia de Reclamación Virtual - %s (JONILUX)', $codigo);
    $mensaje = "Estimado(a) {$nombre},\n\n";
    $mensaje .= "Confirmamos la recepción de su {$tipo_accion} ingresado en nuestro Libro de Reclamaciones Virtual.\n";
    $mensaje .= "Código de Seguimiento: {$codigo}\n";
    $mensaje .= "Fecha de Registro: {$fecha}\n\n";
    $mensaje .= "Detalle: {$detalle}\n";
    $mensaje .= "Pedido del Consumidor: {$pedido_cliente}\n\n";
    $mensaje .= "De acuerdo al Código de Protección y Defensa del Consumidor (Ley N° 29571), daremos respuesta formal en un plazo máximo de quince (15) días hábiles.\n\n";
    $mensaje .= "Atentamente,\nJONILUX S.A.C.\nAtención al Cliente";
    @wp_mail($email, $asunto, $mensaje);

    wp_send_json_success($nuevo_registro);
}

// 4. Panel de Administración en WordPress
add_action('admin_menu', 'jonilux_reclamaciones_admin_menu');
function jonilux_reclamaciones_admin_menu() {
    add_menu_page(
        'Libro de Reclamaciones',
        'Reclamaciones',
        'manage_options',
        'jonilux-reclamaciones',
        'jonilux_reclamaciones_admin_page',
        'dashicons-book-alt',
        56
    );
}

function jonilux_reclamaciones_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $historial = get_option('jonilux_reclamaciones_list', []);
    if (!is_array($historial)) {
        $historial = [];
    }

    // Acción para cambiar estado a Atendido
    if (isset($_GET['action']) && $_GET['action'] === 'atender' && isset($_GET['code'])) {
        $code = sanitize_text_field($_GET['code']);
        foreach ($historial as &$item) {
            if ($item['codigo'] === $code) {
                $item['estado'] = ($item['estado'] === 'Atendido') ? 'Pendiente' : 'Atendido';
                break;
            }
        }
        update_option('jonilux_reclamaciones_list', $historial);
        echo '<div class="updated"><p>Estado del reclamo actualizado.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span class="dashicons dashicons-book-alt" style="font-size:30px;width:30px;height:30px;"></span>
            Libro de Reclamaciones Virtual – JONILUX S.A.C.
        </h1>
        <p>Registro histórico y gestión de hojas de reclamación según la normativa de INDECOPI (Ley N° 29571).</p>

        <div style="background:#fff;padding:15px 20px;border-radius:8px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.1);display:flex;gap:30px;">
            <div><strong>Total Registros:</strong> <?php echo count($historial); ?></div>
            <div><strong>Pendientes:</strong> <?php echo count(array_filter($historial, fn($r) => ($r['estado'] ?? '') === 'Pendiente')); ?></div>
            <div><strong>Atendidos:</strong> <?php echo count(array_filter($historial, fn($r) => ($r['estado'] ?? '') === 'Atendido')); ?></div>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:130px;">Código</th>
                    <th style="width:140px;">Fecha</th>
                    <th>Consumidor</th>
                    <th style="width:110px;">Documento</th>
                    <th style="width:90px;">Tipo</th>
                    <th>Detalle / Pedido Concreto</th>
                    <th style="width:100px;">Estado</th>
                    <th style="width:110px;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)) : ?>
                    <tr><td colspan="8" style="text-align:center;padding:25px;">No hay reclamaciones registradas aún.</td></tr>
                <?php else : ?>
                    <?php foreach ($historial as $r) : ?>
                        <tr>
                            <td><strong><?php echo esc_html($r['codigo']); ?></strong></td>
                            <td><?php echo esc_html($r['fecha']); ?></td>
                            <td>
                                <strong><?php echo esc_html($r['nombre']); ?></strong><br>
                                <small><?php echo esc_html($r['email']); ?> | Tel: <?php echo esc_html($r['telefono']); ?></small>
                            </td>
                            <td><?php echo esc_html($r['tipo_doc'] . ': ' . $r['num_doc']); ?></td>
                            <td>
                                <span class="badge" style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:bold;color:#fff;background:<?php echo ($r['tipo_accion'] === 'Queja') ? '#e67e22' : '#c0392b'; ?>;">
                                    <?php echo esc_html(strtoupper($r['tipo_accion'])); ?>
                                </span>
                            </td>
                            <td>
                                <strong>Bien:</strong> <?php echo esc_html($r['tipo_bien'] . ($r['num_pedido'] ? ' (Pedido ' . $r['num_pedido'] . ')' : '')); ?><br>
                                <p style="margin:4px 0;"><?php echo esc_html(wp_trim_words($r['detalle'], 18)); ?></p>
                                <small style="color:#555;"><strong>Pedido:</strong> <?php echo esc_html(wp_trim_words($r['pedido_cliente'], 15)); ?></small>
                            </td>
                            <td>
                                <span style="font-weight:600;color:<?php echo (($r['estado'] ?? '') === 'Atendido') ? '#27ae60' : '#d35400'; ?>">
                                    <?php echo esc_html($r['estado'] ?? 'Pendiente'); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=jonilux-reclamaciones&action=atender&code=' . urlencode($r['codigo']))); ?>" class="button button-small">
                                    <?php echo (($r['estado'] ?? '') === 'Atendido') ? 'Marcar Pendiente' : 'Marcar Atendido'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

