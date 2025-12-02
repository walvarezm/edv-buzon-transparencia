<?php
/**
 * Configuracion de parametros de conexion del Servidor de Correos institucional para el envio y recepcion
 * de correos para el Formulario de Buzon de Transparencia (BT)
 * Autor: @walvarez - 2024
 */

// Seguridad: bloquear acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determina si la petición está relacionada con la página del Buzón de Transparencia.
 * - Visualización de la página con slug 'buzon-transparencia'
 * - Envío de CF7 desde esa página (referer)
 */
function edv_bt_is_bt_context() {
	// En frontend y es la página del slug
	if ( ! is_admin() && function_exists( 'is_page' ) && is_page( 'buzon-transparencia' ) ) {
		return true;
	}

	// Envia CF7 (AJAX o no) y el referer apunta a /buzon-transparencia
	if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
		$ref = esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) );
		if ( false !== stripos( $ref, '/buzon-transparencia' ) ) {
			return true;
		}
	}

	return false;
}

function configuracion_phpmailer_smtp( $phpmailer ) {
	// Ejecutar solo en el contexto del Buzón de Transparencia
	if ( ! edv_bt_is_bt_context() ) {
		return;
	}

	// Obtencion de usuario y contraseña de la cuenta de correo BT
	$email_usuario_bt = sanitize_email( get_option( 'institutional_email_send', '' ) );
	$email_pass_bt    = sanitize_text_field( get_option( 'institutional_email_send_pass', '' ) );

	if ( empty( $email_usuario_bt ) || empty( $email_pass_bt ) ) {
		return;
	}

	// Asegurar codificación y formato
	$phpmailer->CharSet   = 'UTF-8';
	$phpmailer->Encoding  = 'base64';
	$phpmailer->isHTML( true );

	// Asignacion de parametros de conexion al Servidor de Correos Institucional (Office 365)
	$phpmailer->isSMTP();
	$phpmailer->Host        = 'smtp.office365.com';
	$phpmailer->SMTPAuth    = true;
	$phpmailer->Port        = 587;
	$phpmailer->Username    = $email_usuario_bt;
	$phpmailer->Password    = $email_pass_bt;
	$phpmailer->SMTPSecure  = 'tls';   // STARTTLS
	$phpmailer->AuthType    = 'LOGIN'; // OAUTH/NTLM no usados aquí
	$phpmailer->SMTPAutoTLS = true;
	$phpmailer->Timeout     = 30;

	// Opciones SSL para entornos con inspección TLS o certificados intermedios
	$phpmailer->SMTPOptions = array(
		'ssl' => array(
			'verify_peer'       => true,
			'verify_peer_name'  => true,
			'allow_self_signed' => false,
		),
	);

	// Si CF7 no definió un remitente válido, usar la cuenta SMTP como From
	if ( empty( $phpmailer->From ) || ! is_email( $phpmailer->From ) ) {
		$phpmailer->setFrom( $email_usuario_bt, __( 'EDV Buzón de Transparencia', 'edv-buzon-transparencia' ), false );
	} else {
		// Forzar que el From coincida con la cuenta autenticada para evitar rechazos por SPF/DMARC
		// y mover el remitente original a Reply-To si es diferente
		$fromEmail = $phpmailer->From;
		$fromName  = $phpmailer->FromName;
		if ( strtolower( $fromEmail ) !== strtolower( $email_usuario_bt ) ) {
			// Conservar nombre visible, pero From debe ser el autenticado
			$phpmailer->setFrom( $email_usuario_bt, ! empty( $fromName ) ? $fromName : __( 'EDV Buzón de Transparencia', 'edv-buzon-transparencia' ), false );
			// Asegurar Reply-To al email original del formulario (si es válido)
			if ( is_email( $fromEmail ) ) {
				// Evitar duplicado de reply-to
				$phpmailer->clearReplyTos();
				$phpmailer->addReplyTo( $fromEmail, $fromName );
			}
		}
	}

	// Si no hay destinatarios por alguna razón, evitar fallo y establecer a admin_email
	if ( empty( $phpmailer->getToAddresses() ) ) {
		$fallback_to = get_option( 'admin_email' );
		if ( is_email( $fallback_to ) ) {
			$phpmailer->addAddress( $fallback_to );
		}
	}
}

add_action( 'phpmailer_init', 'configuracion_phpmailer_smtp', 1 );