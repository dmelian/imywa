<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * Validación de usuario en la base de datos.
 * @package I0000
 */
class prglogin{
	private $user, $password;
	public $errormsg;
	
	public function __construct($user, $password){
		$this->user = $user;
		$this->password = $password;
	}
	
	public function checkuser(){
		global $ICONFIG;
		global $_APPLICATION;
	
		$my = mysqli_init();
		$connected = @$my->real_connect($ICONFIG['HOST'],$this->user,$this->password,$ICONFIG['DATABASE']);
		#La @ delante de cualquier función hace que no se muestren errores ni warning en el navegador.
		#lo digo porque es dificil buscarlo en la documentación del php.
		if (!$connected) {
			$this->errormsg = mysqli_connect_error();
		
		} else {
			$_APPLICATION->user = $this->user;
			$_APPLICATION->password = $this->password;
		}

		if(isset($ICONFIG['ACCESSLOGFILENAME'])) {
			$clientIp= $this->getClientIp();
			$accessLog= fopen($ICONFIG['ACCESSLOGFILENAME'], 'a');
			if (!$connected) {
				fwrite($accessLog, "Error\t".date("d/m/Y\tH:i:s")."\t$clientIp\t{$this->user}\t{$this->password}\n"); 
			} else if(isset($ICONFIG['FULLACCESSLOG']) && $ICONFIG['FULLACCESSLOG']) {
				fwrite($accessLog, "Login\t".date("d/m/Y\tH:i:s")."\t$clientIp\t{$this->user}\n"); 
			}
			fclose($accessLog);
		}
		$my->close(); 
		
		return $connected;
		
	}

	private function getClientIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else return $_SERVER['REMOTE_ADDR'];
	}
	
}
?>
