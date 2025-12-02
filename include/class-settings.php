<?php
/**
 * Ajustes del Buzón de Transparencia: menú, página y registro de opciones.
 * Autor: @walvarez - 2024
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WPCF7: Deshabilita la validación de configuración del formulario (permite configuración personalizada)
add_filter( 'wpcf7_validate_configuration', '__return_false' );

/**
 * Registrar página de ajustes bajo "Ajustes"
 */
function edv_bt_add_custom_email_settings_menu() {
	add_options_page(
		__( 'Configuración de Parámetros para Buzón de Transparencia', 'edv-buzon-transparencia' ),
		__( 'EDV Buzón Transparencia', 'edv-buzon-transparencia' ),
		'manage_options',
		'custom-email-settings',
		'edv_bt_render_custom_email_settings_page'
	);
}
add_action( 'admin_menu', 'edv_bt_add_custom_email_settings_menu' );

/**
 * Renderizar página de ajustes
 */
function edv_bt_render_custom_email_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	wp_enqueue_script( 'user-profile' );
	?>
	<div class="wrap">
		<h1><b><?php _e( 'EDV Buzón de Transparencia - Configuraciones', 'edv-buzon-transparencia' ); ?></b></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'custom_email_settings_group' );
			do_settings_sections( 'custom-email-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Registrar ajustes y campos
 */
function edv_bt_register_custom_email_settings() {
	register_setting(
		'custom_email_settings_group',
		'institutional_email_send',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_email',
			'default'           => '',
		)
	);
	register_setting(
		'custom_email_settings_group',
		'institutional_email_send_pass',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	register_setting(
		'custom_email_settings_group',
		'institutional_email_1',
		array(
			'type'              => 'string',
			'sanitize_callback' => function ( $value ) {
				$emails = array_map( 'trim', explode( ',', (string) $value ) );
				$valid  = array();
				foreach ( $emails as $email ) {
					$san = sanitize_email( $email );
					if ( ! empty( $san ) ) {
						$valid[] = $san;
					}
				}
				return implode( ',', array_unique( $valid ) );
			},
			'default'           => '',
		)
	);

	// Sección: Cuenta de correo BT
	add_settings_section(
		'custom_email_settings_section_send',
		'<b>' . esc_html__( 'Cuenta Correo Buzón Transparencia', 'edv-buzon-transparencia' ) . '</b>',
		null,
		'custom-email-settings'
	);

	// Campo: correo remitente
	add_settings_field(
		'institutional_email_send',
		esc_html__( 'Correo Usuario Buzón Transparencia', 'edv-buzon-transparencia' ),
		'edv_bt_custom_email_bt_callback',
		'custom-email-settings',
		'custom_email_settings_section_send'
	);

	// Campo: contraseña remitente
	add_settings_field(
		'institutional_email_send_pass',
		esc_html__( 'Contraseña Usuario Buzón Transparencia', 'edv-buzon-transparencia' ),
		'edv_bt_custom_email_bt_pass_callback',
		'custom-email-settings',
		'custom_email_settings_section_send'
	);

	// Sección: destinatarios
	add_settings_section(
		'custom_email_settings_section',
		'<b>' . esc_html__( 'Correo Electrónico para recepción del formulario', 'edv-buzon-transparencia' ) . '</b>',
		null,
		'custom-email-settings'
	);

	// Campo: lista de destinatarios
	add_settings_field(
		'institutional_email_1',
		esc_html__( 'Cuenta(s) de correo(s) para Recepción:', 'edv-buzon-transparencia' ),
		'edv_bt_custom_email_1_callback',
		'custom-email-settings',
		'custom_email_settings_section'
	);
}
add_action( 'admin_init', 'edv_bt_register_custom_email_settings' );

/**
 * Callbacks de campos
 */
function edv_bt_custom_email_bt_callback() {
	$value = get_option( 'institutional_email_send', '' );
	echo '<input type="email" name="institutional_email_send" value="' . esc_attr( $value ) . '" class="regular-text">';
}

function edv_bt_custom_email_bt_pass_callback() {
	$value   = get_option( 'institutional_email_send_pass', '' );
	$mensaje = '';

	if ( empty( $value ) ) {
		$mensaje = "<p style='color:red;'>" . esc_html__( 'Contraseña no definida.', 'edv-buzon-transparencia' ) . '</p>';
	}

	echo $mensaje . '<input type="password" name="institutional_email_send_pass" value="' . esc_attr( $value ) . '" class="regular-text">';
}

function edv_bt_custom_email_1_callback() {
	$value = get_option( 'institutional_email_1', '' );
	echo '<input type="text" name="institutional_email_1" value="' . esc_attr( $value ) . '" class="regular-text" style="width:70%">';
	echo '<p>' . esc_html__( 'Para agregar mas de 1 cuenta de correo, agregue separandolos con una coma (,)', 'edv-buzon-transparencia' ) . '</p>';
}