<?php
/**
 * Fichero de configuración
 * @package global
 */

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

?>
