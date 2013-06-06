<?php
ini_set('display_errors', 1);

include 'init.inc';

$sessionfoldererror = false;
if (isset($_POST['sessionno']) && isset($_POST['installationid'])) {
	$sessionfolder = "${CONFIG['RUNDIR']}{$_POST['installationid']}/${_POST['sessionno']}/";
	if (file_exists("${sessionfolder}SESDATA")) $cfgfile = "${CONFIG['CFGDIR']}{$_POST['installationid']}.cfg.inc";
	else {
		$sessionfoldererror = true;
		$cfgfile = "${CONFIG['CFGDIR']}{$_POST['installationid']}.cfg.inc";
	}
}
if (isset($cfgfile)) include $cfgfile;

$_LANG= new bas_sys_lang();
$loglevel = 10;
$_LOG = new bas_sys_log($CONFIG['LOGFLN'],$loglevel,$CONFIG['DEBUGER']);


if (!$sessionfoldererror && isset($sessionfolder)) {
	$_APPLICATION = unserialize(file_get_contents("${sessionfolder}APPDATA"));
	$_APPLICATION->xhrwakeUp();
}

$_LOG->close();

?>
