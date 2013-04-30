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
class bas_htm_tptaction{
	private $action;
	
	public function __construct($action){
		if (is_array($action)) {$this->action = $action;}
		else {$this->action = array('id'=>$action, 'image'=>$action, 'description'=>"acción: $action"); }
	}
	
	public function stamp(){
		//TODO Poner la descripción de la imagen en la barra de títulos.
		if (!isset($this->action['type'])) $this->action['type'] = 'button';
		switch ($this->action['type']){
			case 'option': //campos 'value -array de valores y selected-valor seleccionado'
				$code = "addhidden('action','".$this->action['id']."'); submit();";			
				$ret = "<select name=\"" . $this->action['id'] . "\" onchange=\"javascript:$code\">\n";
				foreach ($this->action['value'] as $value){
					$ret .= "<option value=\"$value\"";
					if ($value == $this->action['selected']) $ret.=" selected";
					$ret .= ">$value</option>\n";
				}
				$ret .= "</select>\n";
				return $ret;
				
			case 'check': // selected - si chequeado o no
				$ret =  "<INPUT type=\"checkbox\" name=\"" . $this->action['id'] . '"';
				if ($this->action['selected']) $ret .= ' checked="checked"';
				$ret .=  ">\n";
				return $ret;
				
			case 'text': // value - valor a escribir.
				return '<span class="bartext">'.$this->action['value'].'</span>';
				break;
				
			default:
				if (false && file_exists('image/'.$this->action['image'].'.png')){
					return '<INPUT name="action" value="'.$this->action['id'].'" type="image"'
						.' src="image/'.$this->action['image'].'" onclick="javascript:submit();">\n';
				} else {
					$code = "addhidden('action','".$this->action['id']."'); submit();";			
					return "<input type=\"button\" value=\"".$this->action['image']."\" onclick=\"javascript:$code\">\n";
				}		
		}
	}
}
?>
