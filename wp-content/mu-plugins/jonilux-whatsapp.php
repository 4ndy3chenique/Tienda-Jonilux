<?php
/**
 * Plugin Name: JONILUX – Asistente Virtual y Chatbot (LUNA)
 * Description: Chatbot interactivo de atención al cliente e integración con WhatsApp para JONILUX.
 */

defined('ABSPATH') || exit;

add_action('wp_footer', function () {
    $numero_wa = '51987654321';
    $wa_url = 'https://wa.me/' . $numero_wa . '?text=' . rawurlencode('Hola JONILUX, necesito ayuda con una consulta sobre mi compra.');
    ?>
    <!-- CHATBOT INTERACTIVO JONILUX -->
    <div id="jonilux-chatbot-widget" class="jonilux-chatbot-container">
        <!-- Ventana del Chat -->
        <div id="jonilux-chat-window" class="jonilux-chat-window" style="display:none;" role="dialog" aria-label="Asistente Virtual JONILUX">
            <!-- Header -->
            <div class="jonilux-chat-header">
                <div class="jonilux-chat-header-info">
                    <div class="jonilux-avatar">
                        <span class="jonilux-avatar-letter">L</span>
                        <span class="jonilux-online-status"></span>
                    </div>
                    <div>
                        <strong class="jonilux-bot-title">LUNA • Asistente Virtual</strong>
                        <small class="jonilux-bot-subtitle">Atención al Cliente JONILUX</small>
                    </div>
                </div>
                <button type="button" id="jonilux-close-chat" class="jonilux-chat-close-btn" aria-label="Cerrar chat">&times;</button>
            </div>

            <!-- Cuerpo de Mensajes -->
            <div id="jonilux-chat-body" class="jonilux-chat-body">
                <!-- Mensaje de bienvenida -->
                <div class="jonilux-msg bot-msg">
                    <div class="jonilux-msg-bubble">
                        ¡Hola! Soy <strong>LUNA</strong>, tu asistente virtual de <strong>JONILUX</strong>.<br><br>
                        ¿En qué te puedo ayudar hoy? Elige una opción rápida o escribe tu consulta:
                    </div>
                </div>

                <!-- Opciones Rápidas Iniciales -->
                <div id="jonilux-quick-options" class="jonilux-quick-options">
                    <button type="button" class="jonilux-opt-btn" data-action="tallas">¿Cómo saber mi talla?</button>
                    <button type="button" class="jonilux-opt-btn" data-action="envios">Costos y tiempos de envío</button>
                    <button type="button" class="jonilux-opt-btn" data-action="pagos">Medios de pago (Yape / Plin)</button>
                    <button type="button" class="jonilux-opt-btn" data-action="cambios">Garantía de cambios y devoluciones</button>
                    <button type="button" class="jonilux-opt-btn" data-action="asesor">Hablar con asesor por WhatsApp</button>
                </div>
            </div>

            <!-- Input para escribir consultas -->
            <div class="jonilux-chat-footer">
                <input type="text" id="jonilux-chat-input" placeholder="Escribe tu pregunta..." autocomplete="off">
                <button type="button" id="jonilux-chat-send" aria-label="Enviar mensaje">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Botón Flotante Disparador -->
        <button type="button" id="jonilux-toggle-chat" class="jonilux-chatbot-trigger" aria-label="Abrir asistente virtual LUNA">
            <span class="jonilux-badge-pulse"></span>
            <svg class="jonilux-icon-chat" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <span class="jonilux-trigger-tooltip">¿Dudas? Chatea con LUNA</span>
        </button>
    </div>

    <!-- Estilos del Chatbot -->
    <style>
        .jonilux-chatbot-container{position:fixed;right:25px;bottom:25px;z-index:99999;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif}
        .jonilux-chatbot-trigger{width:60px;height:60px;border-radius:50%;background:#111a16;border:2px solid #c8d4af;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 24px rgba(0,0,0,.25);position:relative;transition:transform .2s,background .2s}
        .jonilux-chatbot-trigger:hover{transform:scale(1.08);background:#18241f}
        .jonilux-badge-pulse{position:absolute;top:2px;right:2px;width:14px;height:14px;background:#25d366;border:2px solid #fff;border-radius:50%;animation:pulseGlow 2s infinite}
        .jonilux-trigger-tooltip{position:absolute;right:72px;background:#111a16;color:#fff;padding:8px 14px;border-radius:8px;font-size:12px;white-space:nowrap;pointer-events:none;opacity:0;transform:translateX(10px);transition:all .2s;border:1px solid #c8d4af;box-shadow:0 4px 15px rgba(0,0,0,.15)}
        .jonilux-chatbot-trigger:hover .jonilux-trigger-tooltip{opacity:1;transform:translateX(0)}
        
        /* Ventana del Chat */
        .jonilux-chat-window{width:360px;max-width:calc(100vw - 40px);height:500px;max-height:calc(100vh - 120px);background:#fff;border-radius:14px;box-shadow:0 12px 35px rgba(0,0,0,.25);border:1px solid #e1e5de;display:flex;flex-direction:column;position:absolute;right:0;bottom:75px;overflow:hidden;animation:chatSlideUp .3s ease}
        .jonilux-chat-header{background:#111a16;color:#fff;padding:16px;display:flex;justify-content:space-between;align-items:center}
        .jonilux-chat-header-info{display:flex;align-items:center;gap:12px}
        .jonilux-avatar{width:38px;height:38px;background:#c8d4af;color:#111a16;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;position:relative}
        .jonilux-online-status{position:absolute;bottom:0;right:0;width:10px;height:10px;background:#2ecc71;border:2px solid #111a16;border-radius:50%}
        .jonilux-bot-title{display:block;font-size:14px;color:#fff}
        .jonilux-bot-subtitle{font-size:11px;color:#a8b4a2}
        .jonilux-chat-close-btn{background:none;border:none;color:#aaa;font-size:24px;cursor:pointer;line-height:1;transition:color .2s}
        .jonilux-chat-close-btn:hover{color:#fff}
        
        .jonilux-chat-body{flex:1;padding:16px;overflow-y:auto;background:#f9f9f7;display:flex;flex-direction:column;gap:12px}
        .jonilux-msg{display:flex;margin-bottom:4px}
        .jonilux-msg.bot-msg{justify-content:flex-start}
        .jonilux-msg.user-msg{justify-content:flex-end}
        .jonilux-msg-bubble{max-width:85%;padding:12px 14px;border-radius:12px;font-size:13px;line-height:1.5}
        .bot-msg .jonilux-msg-bubble{background:#fff;color:#222;border:1px solid #e5e8e2;border-bottom-left-radius:3px;box-shadow:0 2px 6px rgba(0,0,0,.04)}
        .user-msg .jonilux-msg-bubble{background:#111a16;color:#fff;border-bottom-right-radius:3px}
        
        .jonilux-quick-options{display:flex;flex-direction:column;gap:8px;margin-top:6px}
        .jonilux-opt-btn{background:#fff;border:1px solid #d5dcce;color:#18241f;padding:10px 14px;border-radius:8px;font-size:12px;font-weight:600;text-align:left;cursor:pointer;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.03)}
        .jonilux-opt-btn:hover{background:#111a16;color:#fff;border-color:#111a16}
        
        .jonilux-chat-footer{padding:10px 14px;background:#fff;border-top:1px solid #eee;display:flex;gap:8px;align-items:center}
        .jonilux-chat-footer input{flex:1;border:1px solid #ddd;border-radius:6px;padding:9px 12px;font-size:13px;outline:none;font-family:inherit}
        .jonilux-chat-footer input:focus{border-color:#111a16}
        .jonilux-chat-footer button{background:#111a16;border:none;color:#fff;width:36px;height:36px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s}
        .jonilux-chat-footer button:hover{background:#23382c}
        
        .jonilux-chat-action-btn{display:inline-block;margin-top:10px;background:#25d366;color:#fff!important;text-decoration:none!important;padding:8px 14px;border-radius:6px;font-size:12px;font-weight:bold;transition:background .2s}
        .jonilux-chat-action-btn:hover{background:#1eb856}
        .jonilux-chat-back-btn{display:inline-block;margin-top:8px;background:none;border:none;color:#555;font-size:11px;text-decoration:underline;cursor:pointer;padding:0}
        
        @keyframes chatSlideUp{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}
        @keyframes pulseGlow{0%,100%{transform:scale(1);box-shadow:0 0 0 0 rgba(37,211,102,.7)}50%{transform:scale(1.15);box-shadow:0 0 0 5px rgba(37,211,102,0)}}
    </style>

    <!-- Lógica Interactiva del Chatbot -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.getElementById('jonilux-toggle-chat');
        var chatWin = document.getElementById('jonilux-chat-window');
        var closeBtn = document.getElementById('jonilux-close-chat');
        var chatBody = document.getElementById('jonilux-chat-body');
        var chatInput = document.getElementById('jonilux-chat-input');
        var chatSend = document.getElementById('jonilux-chat-send');
        var waLink = '<?php echo esc_js($wa_url); ?>';

        if (!toggleBtn || !chatWin) return;

        toggleBtn.addEventListener('click', function () {
            var isHidden = (chatWin.style.display === 'none' || chatWin.style.display === '');
            chatWin.style.display = isHidden ? 'flex' : 'none';
            if (isHidden && chatInput) chatInput.focus();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                chatWin.style.display = 'none';
            });
        }

        function scrollToBottom() {
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function appendUserMessage(text) {
            var msgDiv = document.createElement('div');
            msgDiv.className = 'jonilux-msg user-msg';
            msgDiv.innerHTML = '<div class="jonilux-msg-bubble">' + text + '</div>';
            chatBody.appendChild(msgDiv);
            scrollToBottom();
        }

        function appendBotMessage(htmlContent) {
            var msgDiv = document.createElement('div');
            msgDiv.className = 'jonilux-msg bot-msg';
            msgDiv.innerHTML = '<div class="jonilux-msg-bubble">' + htmlContent + '</div>';
            chatBody.appendChild(msgDiv);
            scrollToBottom();
        }

        function renderMainMenu() {
            var menuDiv = document.createElement('div');
            menuDiv.className = 'jonilux-quick-options';
            menuDiv.innerHTML = '<button type="button" class="jonilux-opt-btn" data-action="tallas">¿Cómo saber mi talla?</button>' +
                                '<button type="button" class="jonilux-opt-btn" data-action="envios">Costos y tiempos de envío</button>' +
                                '<button type="button" class="jonilux-opt-btn" data-action="pagos">Medios de pago (Yape / Plin)</button>' +
                                '<button type="button" class="jonilux-opt-btn" data-action="cambios">Garantía de cambios y devoluciones</button>' +
                                '<button type="button" class="jonilux-opt-btn" data-action="asesor">Hablar con asesor por WhatsApp</button>';
            chatBody.appendChild(menuDiv);
            scrollToBottom();
        }

        function handleAction(action) {
            if (action === 'tallas') {
                appendUserMessage('¿Cómo saber mi talla?');
                setTimeout(function () {
                    appendBotMessage('Nuestras prendas tienen corte urbano y oversize estándar peruano:<br><br>' +
                        '• <strong>Talla S:</strong> Pecho 96-102 cm (altura aprox. 1.60 - 1.68 m)<br>' +
                        '• <strong>Talla M:</strong> Pecho 104-110 cm (altura aprox. 1.69 - 1.76 m)<br>' +
                        '• <strong>Talla L:</strong> Pecho 112-118 cm (altura aprox. 1.77 - 1.84 m)<br>' +
                        '• <strong>Talla XL:</strong> Pecho 120-128 cm (+1.85 m o corte holgado)<br><br>' +
                        '<em>En las prendas de vestir encontrarás el botón oficial "Guía de Tallas y Medidas".</em>' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Volver al menú de ayuda</button>');
                }, 300);
            } else if (action === 'envios') {
                appendUserMessage('Costos y tiempos de envío');
                setTimeout(function () {
                    appendBotMessage('Realizamos envíos a todo el Perú:<br><br>' +
                        '<strong>Lima Metropolitana y Callao:</strong><br>' +
                        '• Estándar (24 a 48 hrs): <strong>S/ 10.00</strong><br>' +
                        '• <strong>¡Envío GRATIS!</strong> en compras desde <strong>S/ 150.00</strong>.<br>' +
                        '• Express Same-Day: S/ 18.00 (pedidos antes de la 1:00 PM).<br><br>' +
                        '<strong>Provincias:</strong><br>' +
                        '• Vía Olva Courier o Shalom (2 a 4 días): <strong>S/ 18.00</strong> con código de seguimiento.' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Volver al menú de ayuda</button>');
                }, 300);
            } else if (action === 'pagos') {
                appendUserMessage('Medios de pago');
                setTimeout(function () {
                    appendBotMessage('Aceptamos los métodos más seguros y rápidos:<br><br>' +
                        '• <strong>Yape y Plin:</strong> 987 654 321 (JONILUX S.A.C.)<br>' +
                        '• <strong>Transferencia bancaria:</strong> BCP y BBVA.<br>' +
                        '• <strong>Pago Contra Entrega:</strong> Paga en efectivo o con POS físico en la puerta de tu casa (Lima).' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Volver al menú de ayuda</button>');
                }, 300);
            } else if (action === 'cambios') {
                appendUserMessage('Cambios y devoluciones');
                setTimeout(function () {
                    appendBotMessage('¡Tu compra tiene garantía de satisfacción!<br><br>' +
                        '• Tienes hasta <strong>7 días calendario</strong> para solicitar cambio de talla o modelo.<br>' +
                        '• La prenda debe estar sin uso, con etiquetas y empaque intacto.<br>' +
                        '• Si hubo falla de confección o error en el envío, el recojo es <strong>100% gratis</strong>.' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Volver al menú de ayuda</button>');
                }, 300);
            } else if (action === 'asesor') {
                appendUserMessage('Hablar con un asesor');
                setTimeout(function () {
                    appendBotMessage('¡Te derivo con nuestro equipo en WhatsApp!<br><br>' +
                        'Te atenderemos en breve para responder tus consultas personalizadas.' +
                        '<br><a href="' + waLink + '" target="_blank" class="jonilux-chat-action-btn">Abrir WhatsApp (+51 987 654 321)</a>' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Volver al menú de ayuda</button>');
                }, 300);
            }
        }

        window.joniluxShowMenu = function () {
            appendBotMessage('¿En qué más te puedo ayudar?');
            renderMainMenu();
        };

        // Delegación de eventos para botones de opciones
        chatBody.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('jonilux-opt-btn')) {
                var act = e.target.getAttribute('data-action');
                handleAction(act);
            }
        });

        // Enviar consulta por texto
        function handleTextInput() {
            var val = chatInput.value.trim();
            if (!val) return;
            appendUserMessage(val);
            chatInput.value = '';

            var lower = val.toLowerCase();
            setTimeout(function () {
                if (lower.indexOf('talla') !== -1 || lower.indexOf('medida') !== -1) {
                    handleAction('tallas');
                } else if (lower.indexOf('envio') !== -1 || lower.indexOf('envío') !== -1 || lower.indexOf('costo') !== -1 || lower.indexOf('lima') !== -1 || lower.indexOf('provincia') !== -1) {
                    handleAction('envios');
                } else if (lower.indexOf('pago') !== -1 || lower.indexOf('yape') !== -1 || lower.indexOf('plin') !== -1 || lower.indexOf('tarjeta') !== -1) {
                    handleAction('pagos');
                } else if (lower.indexOf('cambio') !== -1 || lower.indexOf('devolu') !== -1 || lower.indexOf('garantia') !== -1) {
                    handleAction('cambios');
                } else if (lower.indexOf('asesor') !== -1 || lower.indexOf('humano') !== -1 || lower.indexOf('whatsapp') !== -1 || lower.indexOf('contacto') !== -1) {
                    handleAction('asesor');
                } else {
                    appendBotMessage('Gracias por tu mensaje. Para darte la mejor atención, te comparto nuestras opciones rápidas o puedes comunicarte con un asesor directo:' +
                        '<br><a href="' + waLink + '" target="_blank" class="jonilux-chat-action-btn">Hablar con Asesor en WhatsApp</a>' +
                        '<br><button type="button" class="jonilux-chat-back-btn" onclick="joniluxShowMenu()">← Ver Menú de Ayuda</button>');
                }
            }, 400);
        }

        if (chatSend && chatInput) {
            chatSend.addEventListener('click', handleTextInput);
            chatInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') handleTextInput();
            });
        }
    });
    </script>
    <?php
});
