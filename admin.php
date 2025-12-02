<?php
/**
 * Configuración de Parámetros para la funcionalidad del formulario de Buzón de Transparencia
 * Autor: @walvarez - 2024
*/ 
 
// WPCF7: Deshabilita la validacion de configuracion del formulario 
add_filter( 'wpcf7_validate_configuration', '__return_false' );

// Configuracion de parametros para el formulario Buzon de Transparencia 
// para adicionar un item submenu "Buzon de Transparencia" en el menu "Ajustes"
function add_custom_email_settings() {
    add_options_page(
        'Configuración de Parámetros para Buzón de Transparencia',
        'Buzón Transparencia',
        'manage_options',
        'custom-email-settings',
        'render_custom_email_settings_page'
    );
}
add_action('admin_menu', 'add_custom_email_settings');

// Creacion de un Formulario para la carga de parametros para Buzon de Transparencia
// con campos de usuario y contraseña para cuenta Buzon de transparencia
// y un campo de emails para la recepcion de datos del formulario Buzon de Transparencia
function render_custom_email_settings_page() {
	wp_enqueue_script( 'user-profile' );
    ?>
    <div class="wrap">
        <h1><b>Configuración de Parámetros para Buzón de Transparencia</b></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_email_settings_group');
            do_settings_sections('custom-email-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registro de campos del formulario de configuracion de Buzon de Transparencia en WordPress
function register_custom_email_settings() {
	register_setting('custom_email_settings_group', 'institutional_email_send');
	register_setting('custom_email_settings_group', 'institutional_email_send_pass');
    register_setting('custom_email_settings_group', 'institutional_email_1');

	// Definición del grupo de campos del usuario BT 
    add_settings_section(
        'custom_email_settings_section_send',
        '<b>Cuenta Correo Buzon Transparencia</b>',
        null,
        'custom-email-settings'
    );

	// Definicion del campo usuario BT
	add_settings_field(
        'institutional_email_send',
        'Correo Usuario Buzon Transparencia',
        'custom_email_bt_callback',
        'custom-email-settings',
        'custom_email_settings_section_send'
    );
	
	// Definicion del campo contraseña de usuario BT
	add_settings_field(
        'institutional_email_send_pass',
        'Contraseña Usuario Buzon Transparencia',
        'custom_email_bt_pass_callback',
        'custom-email-settings',
        'custom_email_settings_section_send'
    );
	
	// Definición del grupo de campos de recepcion de BT 
	add_settings_section(
        'custom_email_settings_section',
        '<b>Correo Electrónico para recepción del formulario</b>',
        null,
        'custom-email-settings'
    );
	
	// Definicion del campo cuentas de correo para ecepcion de correos desde el formulario BT
    add_settings_field(
        'institutional_email_1',
        'Cuenta(s) de correo(s) para Recepción:',
        'custom_email_1_callback',
        'custom-email-settings',
        'custom_email_settings_section'
    );
}
add_action('admin_init', 'register_custom_email_settings');

// Callback del campo "institutional_email_send"
function custom_email_bt_callback() {
    $value = get_option('institutional_email_send', '');
    echo '<input type="email" name="institutional_email_send" value="' . esc_attr($value) . '" class="regular-text">';
}

// Callback del campo "institutional_email_send_pass"
function custom_email_bt_pass_callback() {
    $value = get_option('institutional_email_send_pass', '');
   	$mensaje = "";
	
	if(empty($value) || $value == ""){
		$mensaje = "<p style='color:red;'>Contraseña no definida.</p>";
	}
	
	$input = '<input type="text" name="institutional_email_send_pass" id="pass1" class="regular-text" autocomplete="current-password" value="" data-reveal="0" data-pw="'.$value.'" aria-describedby="pass-strength-result" />';
	$button = '<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" data-start-masked="1" aria-label="'.esc_attr( 'Hide password' ).'"><span class="dashicons dashicons-hidden" aria-hidden="true"></span></button>';
	$passStrength = '<div style="display:none" id="pass-strength-result" aria-live="polite"></div>';
	
	echo $mensaje.'<input type="password" name="institutional_email_send_pass" value="' . esc_attr($value) . '" class="regular-text">';
}

// Callback del campo "institutional_email_1"
function custom_email_1_callback() {
    $value = get_option('institutional_email_1', '');
    echo '<input type="text" name="institutional_email_1" value="' . esc_attr($value) . '" class="regular-text" style="width:70%">
			<p>Para agregar mas de 1 cuenta de correo, agregue separandolos con una coma (,)</p>';
}

// Definición de un Shortcode para obtener la cuenta de correo usuario BT 
// Este shortcode estara insertado en el campo "De" del Formulario "Form buzon transparencia" de "Contact Form 7"
function get_admin_email_send() {
    $email_send = get_option('institutional_email_send','');
	return $email_send;
}
add_shortcode('_bt-sender_email', 'get_admin_email_send');

// Definición de un Shortcode para obtener Correos de Recepcion del Buzon de Transparencia 
// Este shortcode estara insertado en el campo "Para" del Formulario "Form buzon transparencia" de "Contact Form 7"
// [_site_admin_email] por defecto
function get_bt_correos_recepcion() {
    $email_1 = get_option('institutional_email_1','usuario1.recepcion@edv.com.bo');
    return $email_1;
}
add_shortcode('_bt-recipient_email', 'get_bt_correos_recepcion');

// Obtencion de cuentas de correos de Envio y recepcion para luego reemplazar en 
// la configuracion del componente del formulario "Form buzon transparencia" de "Contact Form 7"
function cf7_replace_shortcodes_in_mail( $form_tag ) {
	if ( $form_tag['recipient'] === '[_bt-recipient_email]' ) {
        $form_tag['recipient'] = do_shortcode( '[_bt-recipient_email]' );
    }
	if ( $form_tag['sender'] === '[_bt-sender_email]' ) {
        $doShortcodeBT = do_shortcode( '[_bt-sender_email]' );
		$titleBT = "EDV Buzon de Transparencia"; 
		$form_tag['sender']  = $titleBT." <".$doShortcodeBT.">";
    }
    return $form_tag;
}
add_filter( 'wpcf7_mail_components', 'cf7_replace_shortcodes_in_mail', 9999,1 );

// Shortcode auxiliar 
function cf7_get_admin_emails_shortcode() {
    return get_bt_correos_recepcion();
}
add_shortcode('bt-recipient-emails-pro', 'cf7_get_admin_emails_shortcode');

// Shortcode auxiliar 
function cf7_get_admin_email_send_shortcode() {
    return get_admin_email_send();
}
add_shortcode('bt-sender-email-pro', 'cf7_get_admin_email_send_shortcode');
