=== edv-buzon-transparencia ===
Contributors: EDV
Tags: contact form 7, transparencia, buzon, phpmailer, smtp
Requires at least: 5.6
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Modulo Buzon Transparencia para recepcion datos de usuarios externos via formulario web.

== Descripción ==
Plugin para personalizar el formulario (Contact Form 7) Buzón de Transparencia de la EDV, con configuraciones específicas:
- Shortcodes: [_bt-sender_email] y [_bt-recipient_email]
- Configuración de cuenta remitente y destinatarios múltiples (separados por coma)
- Integración SMTP (Office365) via phpmailer_init
- Assets JS/CSS de validación y estilos del formulario

== Instalación ==
1. Subir la carpeta edv-buzon-transparencia al directorio /wp-content/plugins/
2. Activar el plugin desde el panel de administración de WordPress.
3. Ir a Ajustes > Buzón Transparencia y configurar:
   - Correo Usuario Buzón Transparencia (remitente)
   - Contraseña Usuario Buzón Transparencia
   - Cuenta(s) de correo(s) para Recepción (separados por coma)
4. En Contact Form 7, usar:
   - Para: [_bt-recipient_email]
   - De: "EDV Buzón de Transparencia" <[_bt-sender_email]>

== Seguridad ==
- Bloqueo de acceso directo.
- Sanitización y escape de datos en opciones y shortcodes.

== Hooks ==
- register_activation_hook, register_deactivation_hook
- add_action( 'phpmailer_init' )
- add_filter( 'wpcf7_mail_components' )

== Shortcodes ==
- [_bt-sender_email] Retorna el email remitente configurado.
- [_bt-recipient_email] Retorna la lista de destinatarios.

== Changelog ==
= 1.0.0 =
- Versión inicial.