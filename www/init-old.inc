<?php

// SYSTEM CONFIGURATION

/**
 * Nivel del Log.
 * 0. No se imprime nada.
 * 10. Maximo nivel de detalle.
 * @global integer $CONFIG['LOGLEVEL'] 
 */
$CONFIG['LOGLEVEL'] = 10;

/**
 * Fichero de log.
 * Path y nombre del fichero de log.
 * @global string $CONFIG['LOGFLN'];
 */
$CONFIG['LOGFLN'] = '/usr/local/imywa/log';

/**
 * DEBUGER
 * Si está activado, cada log cierra el fichero, así todos los mensajes son vistos aunque el programa no finalice.
 * @global boolean $CONFIG['DEBUGER']
 */
$CONFIG['DEBUGER'] = true;

/**
* Directorio de ejecución.
* Directorio donde se almacenarón todos los datos temporales de ejecución de las distintas sesiones
* @global string $CONFIG['RUNDIR']
*/
$CONFIG['RUNDIR'] = '/usr/local/imywa/run/';
$CONFIG['RUNDIRMOD'] = 0777; //Permisos de acceso al directorio run.
$CONFIG['FILERUNDIRMOD'] = 0666; //Permisos para los ficheros del directorio run.

/**
* Directorio de con las fuentes del sistema.
* Directorio donde se almacena el código fuente común a todos los aplicativos.
* @global string $CONFIG['SYSSRCDIR']
*/
$CONFIG['SRCDIR'] = '/usr/local/imywa/source/';

/**
 * Directorio de configuración de instalaciones
 * Directorio donde se guardan todos los archivos de configuración de todas las instalaciones que se hayan realizado.
 * @global string $config['CFGDIR']
 */
$CONFIG['CFGDIR'] = '/usr/local/imywa/config/';


# Directorio raiz www. Todo el contenido web se encuentra a partir de este directorio.
$CONFIG['WWWDIR'] = '/var/www/apps/';

/**
* Separador de path.
* Separador de pah usado por el sistema operativo (soporte multiplataforma)
* @global char $CONFIG['PS']
*/
$CONFIG['PS'] = '/';

/**
 * Tipo de listas
 * Las listas en web aparecen por páginas, si se trata de una aplicación en local aparece completa.
 * @global int $CONFIG['LSTTYPE'] 
 */
define('LT_FULL_LIST',0);
define('LT_PAGE_LIST',1);
$CONFIG['LSTTYPE'] = constant('LT_FULL_LIST');

/**
 * Registros por página
 * En el caso que las listas sean por páginas ¿Cuantos registros me caben por cada página?
 * @global int $CONFIG['RECSXPAG']
 */
$CONFIG['RECSXPAG'] = 100000;

/**
 * Servidor MYSQL por defecto. 
 */
$CONFIG['DEFAULTMYSQLSERVER'] = 'localhost';

$CONFIG['DEFAULTLANGUAGE']= 'es';

$CONFIG['TERMINAL'] = isset($_GET['terminal']) ? $_GET['terminal'] : 'UNKNOWN';
$CONFIG['BROWSER'] = preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? 'IE' : '';


// GLOBAL FUNCTIONS

/**
 * Carga dinámica de clases.
 * Especifica al interprete php donde encontrar las clases (un fichero por clase), cuando le haga falta.
 */
function __autoload($class_name) {
	global $CONFIG;
	global $ICONFIG;
	global $_LANG;

	if (isset($_LANG)){
		$langId='lang';
		if ($last_= strrpos($class_name,'_')) $langId= substr($class_name, 0, $last_).'_lang';
		$_LANG->load($langId);
	}
	$class= explode('_',$class_name);

	switch($class[0]){
	case 'bas':
		array_shift($class);
		require_once "${CONFIG['SRCDIR']}".implode('/',$class).".php";
		break;
			
	case 'lib':
		array_shift($class);
		require_once "${CONFIG['SRCDIR']}libs/".implode('/',$class).".php";
		break;
			
	default:
		require_once "${ICONFIG['SRCDIR']}".implode('/',$class).".php";
	}
}


function getsessionstamp(){
	global $_APPLICATION;
	
	if(is_object($_APPLICATION) && $_APPLICATION->session->sessionno) {
		$ret = "<input type=\"hidden\" name=\"sessionno\" value=\"".$_APPLICATION->session->sessionno."\">";
		if($_APPLICATION->installationid) $ret .= "<input type=\"hidden\" name=\"installationid\" value=\"$_APPLICATION->installationid\">";
		if($_APPLICATION->nextsequenceno) $ret .= "<input type=\"hidden\" name=\"sequenceno\" value=\"$_APPLICATION->nextsequenceno\">";
		return $ret;
	}
}

function getBreadCrumbStamp(&$form= false){
	global $_APPLICATION;
	$captions= $_APPLICATION->session->getBreadCrumbCaptions();
	if ($form && method_exists($form,'getBreadCrumbCaption')){
		array_push($captions, $form->getBreadCrumbCaption());
	}

	$jump= count($captions);
	$ret='<ul class="ia_breadcrumb">';
	foreach($captions as $caption){
		$jump--;
		$ret.="<li onclick=\"submitaction('close', {'jump':$jump});\"><span>$caption</span></li>";
	}
	$ret.='</ul>';
	return $ret;
}

function T($id){
	global $_LANG;
	return $_LANG->getCaption($id);
}

function TD($id){
	global $_LANG;
	return $_LANG->getDescription($id);
}

?>
