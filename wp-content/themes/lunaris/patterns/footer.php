<?php
/**
 * Title: JONILUX footer
 * Slug: lunaris/footer
 * Categories: featured
 * Inserter: no
 */
if (!defined("ABSPATH")) { exit; }
$shop = function_exists("wc_get_page_permalink") ? wc_get_page_permalink("shop") : home_url("/shop/");
$terminos = home_url("/terminos-y-condiciones/");
$envios = home_url("/politica-de-envios-y-devoluciones/");
$privacidad = home_url("/politica-de-privacidad/");
$libro = home_url("/libro-de-reclamaciones/");
?>
<!-- wp:html -->
<footer class="lunaris-footer-extended" role="contentinfo">
    <div class="lunaris-footer-container">
        <!-- Grid principal de 4 columnas -->
        <div class="lunaris-footer-grid">
            <!-- Columna 1: Marca y Empresa -->
            <div class="lunaris-footer-col brand-col">
                <a class="lunaris-footer-logo-ext" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
                <p class="lunaris-footer-tagline">Esenciales urbanos. Moda moderna, precios claros y compra sin complicaciones.</p>
                <div class="lunaris-company-info">
                    <p><strong>JONILUX S.A.C.</strong></p>
                    <p>RUC: 20612345678</p>
                    <p>Av. Universitaria 1250, San Miguel, Lima</p>
                </div>
                <div class="lunaris-social-links">
                    <a href="https://instagram.com/jonilux.pe" target="_blank" rel="noopener noreferrer" aria-label="Instagram de JONILUX">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        <span>@jonilux.pe</span>
                    </a>
                    <a href="https://tiktok.com/@jonilux.pe" target="_blank" rel="noopener noreferrer" aria-label="TikTok de JONILUX">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
                        <span>TikTok</span>
                    </a>
                    <a href="https://facebook.com/jonilux.pe" target="_blank" rel="noopener noreferrer" aria-label="Facebook de JONILUX">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        <span>Facebook</span>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Navegación del Catálogo -->
            <div class="lunaris-footer-col">
                <h4 class="lunaris-footer-heading">Colecciones</h4>
                <ul class="lunaris-footer-list">
                    <li><a href="<?php echo esc_url($shop); ?>">Ver Catálogo Completo</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#colecciones')); ?>">Polos Urbanos & Oversize</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#colecciones')); ?>">Hoodies & Polerones</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#colecciones')); ?>">Pantalones Joggers & Cargo</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#accesorios')); ?>">Gorras y Accesorios</a></li>
                </ul>
            </div>

            <!-- Columna 3: Atención y Logística -->
            <div class="lunaris-footer-col">
                <h4 class="lunaris-footer-heading">Atención y Logística</h4>
                <ul class="lunaris-footer-list">
                    <li><span><strong>Lima Metropolitana:</strong> Envíos 24 - 48 hrs</span></li>
                    <li><span><strong>Provincias:</strong> Olva / Shalom (2 a 4 días)</span></li>
                    <li><span><strong>WhatsApp:</strong> <a href="https://wa.me/51987654321?text=Hola%20JONILUX,%20tengo%20una%20consulta" target="_blank" rel="noopener noreferrer">+51 987 654 321</a></span></li>
                    <li><span><strong>Email:</strong> soporte@jonilux.pe</span></li>
                    <li><span><strong>Horario:</strong> Lun a Sáb: 9:00 - 20:00</span></li>
                </ul>
            </div>

            <!-- Columna 4: Aspectos Legales e INDECOPI -->
            <div class="lunaris-footer-col legal-col">
                <h4 class="lunaris-footer-heading">Garantía y Legal</h4>
                <ul class="lunaris-footer-list">
                    <li><a href="<?php echo esc_url($terminos); ?>">Términos y Condiciones</a></li>
                    <li><a href="<?php echo esc_url($envios); ?>">Envíos y Logística Inversa</a></li>
                    <li><a href="<?php echo esc_url($privacidad); ?>">Política de Privacidad</a></li>
                </ul>

                <!-- Libro de Reclamaciones INDECOPI Badge -->
                <div class="lunaris-reclamaciones-box">
                    <a href="<?php echo esc_url($libro); ?>" class="lunaris-reclamaciones-link" title="Ir al Libro de Reclamaciones Virtual">
                        <div class="lunaris-reclamaciones-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f1c40f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div class="lunaris-reclamaciones-text">
                            <strong>LIBRO DE RECLAMACIONES</strong>
                            <small>Conforme a la Ley N° 29571 - INDECOPI</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Barra intermedia de Pasarelas y Medios de Pago -->
        <div class="lunaris-footer-payments">
            <div class="lunaris-payments-label">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Formas de pago</span>
            </div>
            <div class="lunaris-payment-badges">
                <span class="pay-badge yape">YAPE</span>
                <span class="pay-badge plin">PLIN</span>
                <span class="pay-badge bcp">BCP</span>
                <span class="pay-badge bbva">BBVA</span>
                <span class="pay-badge visa">VISA</span>
                <span class="pay-badge mastercard">MASTERCARD</span>
                <span class="pay-badge cod">PAGO CONTRA ENTREGA</span>
            </div>
        </div>

        <!-- Barra inferior: Derechos y Créditos -->
        <div class="lunaris-footer-bottom-bar">
            <span>© <?php echo esc_html(wp_date('Y')); ?> JONILUX S.A.C. — Todos los derechos reservados. RUC 20612345678.</span>
            <span>PROYECTO DE E-BUSINESS • UPN 2026</span>
        </div>
    </div>
</footer>
<!-- /wp:html -->
