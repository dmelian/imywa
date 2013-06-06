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
class bas_dat_format{
	public $default_format;
	
	public function __construct($format = '') { $this->default_format = trim($format); }
	
	public function format($data, $format = ''){
		$format = trim($format);
		if (!$format) $format = $this->default_format;
		switch ($format){
			case 'currency': if (is_numeric($data)) return number_format($data, 2, ',', '.'); else return $data;
			case 'real': if (is_numeric($data)) return sprintf('%.5f',$data); else return $data;
			case 'percent': if (is_numeric($data)) return sprintf('%.2f %%',$data*100); else return $data;
			case 'boolean': if (is_numeric($data)) return $data ? 'sí':'no'; else return $data;
			case 'date': if (strlen($data)) return substr($data, 8).'/'.substr($data, 5,2).'/'.substr($data, 0,4); else return '';
			default:
				if (substr($format, 0, 6) == 'option'){
					$values = explode(',',trim(strrchr($format,"(")," ()"));
					return isset($values[$data]) ? $values[$data] : $data;
				} else return $data;
		}
	}
	
	public function validate($data, $format = ''){
		$format = trim($format);
		if (!$format) $format = $this->default_format;
		switch($format){
			case 'date': return $this->date_validate($data);
			default: return $data;		
		}
	}
	
	private function date_validate($data){
		$i = 0; $len = strlen($data);
		if ($len == 0) return '';
		
		$day = '';
		while (($i < $len) && !is_numeric($day)) $day = $data[$i++];
		if (($i+1 < $len) && ($day>='0') && ($day<='3') && is_numeric($data[$i])) $day .= $data[$i++];
		if (($i < $len) && !is_numeric($data[$i])) $i++;

		$month = '';
		while (($i < $len) && !is_numeric($month)) $month = $data[$i++];
		if (($i+1 < $len) && ($month >= '0') && ($month<='1') && is_numeric($data[$i])) $month .= $data[$i++];
		if (($i < $len) && !is_numeric($data[$i])) $i++;
		
		$year = '';
		while (($i < $len) && is_numeric($data[$i])) $year .= $data[$i++];
		
		if (strlen($year) == 0) $year = date('Y');
		if (strlen($month) == 0) $month = date('m');
		if (strlen($day) == 0) $day = date('d');
		
		return "$year/$month/$day";		
	}
	
}
?>
