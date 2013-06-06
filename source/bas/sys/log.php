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
 * Log
 * @package system
 */
class bas_sys_log{
	private $logFilename;
	private $curLevel;
	private $debug;
	private $fp;
	
	public function __construct($logfilename, $curlevel, $debug){
		$this->debug = $debug;
		$this->logFilename = $logfilename;
		$this->curLevel = $curlevel;
		if (!$this->debug){
			$this->fp = fopen($this->logFilename, 'a');
		}
	}
	
	public function log($msg, $level=5){
		if ($level <= $this->curLevel){
			if ($this->debug) $this->fp = fopen($this->logFilename, 'a');
			if (is_array($msg)){
				fwrite($this->fp, "ARRAY:\n");
				foreach($msg as $key => $value){
					fwrite($this->fp, "\t[$key]=$value.\n");
				}
				fwrite($this->fp, "END ARRAY\n");
			} elseif(is_object($msg)) {
				$msg = print_r($msg, TRUE);
				fwrite($this->fp, "OBJECT:\n{$msg}END OBJECT\n");
			} else {
				fwrite($this->fp, date('Y-m-d H:i:s')." $msg\n");
			}
			if ($this->debug) {
				fclose($this->fp);
//				chmod($this->logFilename, 0666);
			}
		}
	}
	
	public function debug($caption, $object){
		if ($this->debug) $this->fp = fopen($this->logFilename, 'a');
		$msg = print_r($object, TRUE);
		fwrite($this->fp, "DEBUG: OBJECT[$caption] :\n{$msg}END DEBUG [$caption]\n");
		if ($this->debug) {
			fclose($this->fp);
//			chmod($this->logFilename, 0666);
		}
	}
	
	public function close(){
		if (!$this->debug) {
			fclose($this->fp);
//			chmod($this->logFilename, 0666);
		}	
	}

}
?>
