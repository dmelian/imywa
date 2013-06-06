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
class bas_htm_tdalign{
/* Se trata de una clase auxiliar para la generación de html.
 * Como parámetro de entrada se envía la alineación tanto vertical como horizontal separada por un espacio
 * Devuelve en la propiedad $this->htmltext el código de estilo para esa alineación. 
 * Normalmente este se toma de las propiedades de columna de la definición de query (bas_sql_querydef) tabla col['align'].
 *  	
 * Alineación Horizontal: left, right, center, justify, char(x)
 * Alineación Vertical: top, middle, bottom, baseline
	
*/
	public $htmltext;
	
	public function __construct($align){
		// Horizontal
		$this->htmltext = '';
		if (strpos($align,'left')!==false) $this->htmltext = ' align = "left"';
		elseif (strpos($align,'right')!==false) $this->htmltext = ' align = "right"';
		elseif (strpos($align,'center')!==false) $this->htmltext = ' align = "center"';
		elseif (strpos($align,'justify')!==false) $this->htmltext = ' align = "justify"';
		elseif (strpos($align,'char')!==false) {
			$char = strrchr($align,"(");
			$char = trim(strstr($char,")")," ()");
			$this->halign = " align = \"char\" char = \"$char\"";	
		}
		// Vertical		
		if (strpos($align,'top')!==false) $this->htmltext = ' valign = "top"';
		elseif (strpos($align,'middle')!==false) $this->htmltext = ' valign = "middle"';
		elseif (strpos($align,'bottom')!==false) $this->htmltext = ' valign = "bottom"';
		elseif (strpos($align,'baseline')!==false) $this->htmltext = ' valign = "baseline"';
				
	}
}
