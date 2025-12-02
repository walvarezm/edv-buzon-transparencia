<?php
/**
 * Shortcodes y hooks de Contact Form 7 para Buzón de Transparencia.
 * Autor: @walvarez - 2024
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode: correo remitente (para campo "De")
 */
function edv_bt_get_admin_email_send() {
	$email_send = get_option( 'institutional_email_send', '' );
	return esc_html( $email_send );
}
add_shortcode( '_bt-sender_email', 'edv_bt_get_admin_email_send' );

/**
 * Shortcode: correos destinatarios (para campo "Para")
 */
function edv_bt_get_bt_correos_recepcion() {
	$email_1 = get_option( 'institutional_email_1', 'usuario1.recepcion@edv.com.bo' );
	return esc_html( $email_1 );
}
add_shortcode( '_bt-recipient_email', 'edv_bt_get_bt_correos_recepcion' );

/**
 * Reemplaza shortcodes en componentes de CF7
 */
function edv_bt_cf7_replace_shortcodes_in_mail( $form_tag ) {
	if ( isset( $form_tag['recipient'] ) && $form_tag['recipient'] === '[_bt-recipient_email]' ) {
		$form_tag['recipient'] = do_shortcode( '[_bt-recipient_email]' );
	}
	if ( isset( $form_tag['sender'] ) && $form_tag['sender'] === '[_bt-sender_email]' ) {
		$doShortcodeBT      = do_shortcode( '[_bt-sender_email]' );
		$titleBT            = __( 'EDV Buzón de Transparencia', 'edv-buzon-transparencia' );
		$form_tag['sender'] = $titleBT . ' <' . $doShortcodeBT . '>';
	}
	return $form_tag;
}
add_filter( 'wpcf7_mail_components', 'edv_bt_cf7_replace_shortcodes_in_mail', 9999, 1 );

/**
 * Shortcodes auxiliares
 */
function edv_bt_cf7_get_admin_emails_shortcode() {
	return edv_bt_get_bt_correos_recepcion();
}
add_shortcode( 'bt-recipient-emails-pro', 'edv_bt_cf7_get_admin_emails_shortcode' );

function edv_bt_cf7_get_admin_email_send_shortcode() {
	return edv_bt_get_admin_email_send();
}
add_shortcode( 'bt-sender-email-pro', 'edv_bt_cf7_get_admin_email_send_shortcode' );