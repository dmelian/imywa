<?php

ini_set('display_errors', 1);

include 'init.inc';

//TODO: -- UPLOAD MAX SIZE --
// COMPROBAR SI SE HA INTENTADO SUBIR UN UPLOAD MAYOR DE LO PERMITIDO
// SI $_SERVER['CONTENT_LENGTH'] > ? ENTONCES MANDAR UN MENSAJE DE ERROR E INICIAR SESIÓN DE NUEVO.
// SI EL CONTENIDO ES MAYOR QUE ALGO EL $_POST viene a array en blanco.

$sessionfoldererror = false; $user = $password = '';
$installationid = 'syslogin';
if (isset($_POST['sessionno'])) {
	if (isset($_POST['installationid'])) $installationid = $_POST['installationid'];
	$sessionfolder = "${CONFIG['RUNDIR']}$installationid/${_POST['sessionno']}/";
	$cfgfile = "${CONFIG['CFGDIR']}$installationid.cfg.inc";
	$sessionfoldererror = !file_exists("${sessionfolder}SESDATA");

} else {
	if (isset($_GET['setinstallation'])) $installationid =  $_GET['installationid'];
	elseif (isset($_POST['setinstallation'])) {
		$installationid = $_POST['installationid'];
		$user = isset($_POST['user']) ? $_POST['user']: ''; 
		$password = isset($_POST['password']) ? $_POST['password'] : '';
	}
	$cfgfile = "${CONFIG['CFGDIR']}$installationid.cfg.inc";
}
if (isset($cfgfile)) include $cfgfile;

$_LANG= new bas_sys_lang();
if (isset($ICONFIG['LOGLEVEL'])) {$loglevel=$ICONFIG['LOGLEVEL'];} else {$loglevel=$CONFIG['LOGLEVEL'];}
$_LOG= new bas_sys_log($CONFIG['LOGFLN'],$loglevel,$CONFIG['DEBUGER']);

if ($sessionfoldererror) {
	$dlg = new bas_htm_dialog("ERROR DE SESION",'error',array('Iniciar otra sesión'));
	$dlg->p('No se han encontrado sus datos de sesión.');
	$dlg->p('Esto puede ser causado por una actualización de la aplicación.');
	$dlg->p('Debe iniciar una nueva sesión.');
	$dlg->printme();
	
} elseif (isset($sessionfolder)) {
	$_APPLICATION = unserialize(file_get_contents("${sessionfolder}APPDATA"));
	if (isset($_POST['XHR'])) $_APPLICATION->xhrwakeUp(); else $_APPLICATION->wakeUp();

} else {
	$_APPLICATION = new bas_sys_application();
	$_APPLICATION->newSession($installationid, $user, $password);

}

$_LOG->close();
?>
