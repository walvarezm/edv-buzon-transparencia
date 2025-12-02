<?php
/**
 * Configuracion de parametros de conexion del Servidor de Correos institucional para el envio y recepcion 
 * de correos para el Formulario de Buzon de Transparencia (BT)
 * Autor: @walvarez - 2024
 */
function configuracion_phpmailer_smtp( $phpmailer ) {
	
	if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
    }

	
    $phpmailer = new PHPMailer\PHPMailer\PHPMailer(true);
	
	//Obtencion de usuario y contraseña de la cuenta de correo BT 
	$email_usuario_bt = get_option('institutional_email_send','');
	$email_pass_bt = get_option('institutional_email_send_pass','');
	
	//Asignacion de parametros de conexion al Servidor de Correos Institucional
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.office365.com';
	$phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = $email_usuario_bt; // Cambia esto por tu dirección de correo
    $phpmailer->Password   = $email_pass_bt; // Cambia esto por tu contraseña
	$phpmailer->SMTPSecure = 'tls';
    //$phpmailer->From       = ''; // Cambia esto por tu dirección de correo
    //$phpmailer->FromName   = ''; // Cambia esto por el nombre que quieres mostrar
    //$phpmailer->SMTPDebug = 2;				// Para modo DESARROLLO - Visualiza el trace de la ejecucion del envio de correo
	//$phpmailer->Debugoutput = 'echo ';		// Para modo DESARROLLO
}


add_action( 'phpmailer_init', 'configuracion_phpmailer_smtp', 1 );

