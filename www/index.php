<?php
ini_set('display_errors', 1);
ini_set('memory_limit', -1);
include 'imywa.inc';
$_LOG= new bas_sys_log($CONFIG['BASDIR'].'log',10);
$_LANG= new bas_sysx_lang();
if (isset($_POST['sessionId']) && ($_POST['sessionId']) && ($_POST['sessionId']!='null')) {
	$sessionfolder = "{$CONFIG['BASDIR']}run/sessions/{$_POST['sessionId']}/";
	$sessionFile= new syncFile("{$sessionfolder}session");
	if ($sessionFile->getContent()) {
		$_SESSION= unserialize($sessionFile->content);
		$_SESSION->wakeUp();
	} else {
		$_LOG->log("Error: Se ha intentado entrar en la session {$_POST['sessionId']} y no tenemos acceso su fichero de sesión." );
		$msgBox= new bas_html_messageBox(false,'Error','Su sesión se ha terminado');
		echo $msgBox->jscommand();
	}
} else {
	$_SESSION= new bas_sysx_session();
	$_SESSION->begin();
}

?>
