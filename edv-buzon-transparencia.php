<?php
/**
 * Plugin Name:       edv-buzon-transparencia
 * Plugin URI:		  https://edv.com.bo
 * Description:       Modulo Buzon Transparencia para recepcion datos de usuarios externos via formulario web.
 * Version:           1.0.0
 * Author:            Wilmer Alvarez M.
 * Author URI:		  https://github.com/walvarezm
 * Text Domain:       edv-buzon-transparencia
 * Domain Path:       /languages
 * Requires PHP: 	  7.4
 * License:     	  GPL-2.0-or-later
 */

// Bloquear acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EDV_BT_PATH' ) ) {
	define( 'EDV_BT_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'EDV_BT_URL' ) ) {
	define( 'EDV_BT_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Cargar textdomain
 */
function edv_bt_load_textdomain() {
	load_plugin_textdomain( 'edv-buzon-transparencia', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'edv_bt_load_textdomain' );

/**
 * Activación / Desactivación
 */
function edv_bt_activate() {
	// Registrar opciones si no existen
	add_option( 'institutional_email_send', '' );
	add_option( 'institutional_email_send_pass', '' );
	add_option( 'institutional_email_1', '' );
}
function edv_bt_deactivate() {
	// No borrar opciones automáticamente por seguridad
}
register_activation_hook( __FILE__, 'edv_bt_activate' );
register_deactivation_hook( __FILE__, 'edv_bt_deactivate' );

/**
 * Encolar assets solo en front cuando exista CF7
 */
function edv_bt_enqueue_assets() {
	if ( ! is_admin() ) {
		wp_register_style( 'edv-bt', EDV_BT_URL . 'assets/css/edv-bt.css', array(), '1.0.0' );
		wp_register_script( 'edv-bt', EDV_BT_URL . 'assets/js/edv-bt.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_style( 'edv-bt' );
		wp_enqueue_script( 'edv-bt' );
	}
}
add_action( 'wp_enqueue_scripts', 'edv_bt_enqueue_assets' );

/**
 * Incluir archivos del plugin
 */
require_once EDV_BT_PATH . 'include/class-settings.php';
require_once EDV_BT_PATH . 'include/cf7-hooks.php';
require_once EDV_BT_PATH . 'include/config-phpmailer.php';