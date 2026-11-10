# JONILUX: WordPress y WooCommerce

Este proyecto esta en `C:\xampp\htdocs\wordpress`. Usa el PHP de XAMPP
(`C:\xampp\php\php.exe`) y MariaDB. No es Laravel: no ejecutes Artisan ni instales Laravel Boost.
Modifica los mu-plugins de JONILUX y el tema `lunaris`; conserva intactos el core y los plugins de terceros.
No muestres credenciales de `wp-config.php`, datos de clientes ni dumps al auditor.
No realices cobros, no envies correos ni sobrescribas la base activa para probar.

## Flujo solicitado por el usuario

1. Si el usuario pide una revision inicial, realiza primero la inspeccion y las pruebas de diagnostico.
2. Guarda su solicitud original sin reformular en `.codex/requests/` y ejecuta
   `scripts/Invoke-ClaudePrompt.ps1 -RequestFile <ruta> -Context <contexto y hallazgos>`.
   Espera el prompt mejorado antes de implementar. Las instrucciones originales siempre
   prevalecen; descarta ampliaciones de alcance, decisiones comerciales inventadas y aprobaciones adicionales.
3. Codex implementa y prueba. Claude solo reformula y audita, sin escribir archivos.
4. Ejecuta `scripts/Invoke-ClaudeAudit.ps1 -Files @('rutas modificadas') -Context <requisito y pruebas>`.
   Verifica sus hallazgos, corrige los defectos confirmados y revalida. Maximo dos rondas por entrega.
5. Si PowerShell bloquea scripts, usa `powershell -NoProfile -ExecutionPolicy Bypass`
   solo para ese proceso. Para varias rutas usa `-Command` con un array real, no `-File`.

Ambos scripts usan la sesion Pro de Claude con herramientas de lectura. No uses claves de API.
Versiones del prompt: `.codex/requests/`; informes: `.codex/audits/`, todo ignorado por Git y bloqueado por HTTP.
Si Claude falla, informa el fallo y no presentes la reformulacion o auditoria como completada.
Saludos, estados y aclaraciones menores no necesitan una nueva reformulacion.
