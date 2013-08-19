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
	private $baseFolder;
	private $level;
	private $indent;
	private $prefix;
	
	public function __construct($baseFolder, $level){
		$this->baseFolder= $baseFolder;
		$this->level= $level;
		$this->indent= "\t";
		$this->prefix= "* ";
	}
	
	public function log($msg, $level=5, $file='imywa'){
		if ($level <= $this->level){
			$filename= "{$this->baseFolder}/$file";
			if (!file_exists($filename)){
				$fp= fopen($filename, 'a+');
				chmod($filename, 0666);
			} else $fp= fopen($filename, 'a');
			
			$prefix= ($this->prefix.gmdate('Y-m-d H:i:s'));
// 			$prefix= @($this->prefix.date('Y-m-d H:i:s'));
			if (is_array($msg)){
				fwrite($fp, $prefix." ARRAY:\n");
				foreach($msg as $key => $value){
					fwrite($fp, "{$this->indent}[$key]=$value.\n");
				}
				fwrite($fp, "{$this->indent}END.\n");
				
			} elseif(is_object($msg)) {
				$msg= strtr($this->indent.print_r($msg, TRUE), array("\n"=>"\n{$this->indent}"));
				fwrite($fp, $prefix." OBJECT:\n{$msg}\n");
				
			} else {
				$msg= strtr($msg, array("\n"=>"\n{$this->indent}"));
				fwrite($fp, $prefix." $msg\n");
			}
			
			fclose($fp);
		}
	}
	
	public function debug($caption, $object, $file='debug'){
		$filename= "{$this->baseFolder}/$file";
		if (!file_exists($filename)){
			$fp= fopen($filename, 'a+');
			chmod($filename, 0666);
		} else $fp= fopen($filename, 'a');
		
		$msg= strtr($this->indent.print_r($object, TRUE), array("\n"=>"\n{$this->indent}"));
		
		fwrite($fp, $this->prefix.gmdate('Y-m-d H:i:s')." $caption\n$msg\n");
// 		fwrite($fp, $this->prefix.date('Y-m-d H:i:s')." $caption\n$msg\n");

		fclose($fp);
	}
	

}
?>
