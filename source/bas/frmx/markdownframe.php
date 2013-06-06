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

class bas_frmx_markdownframe extends bas_frmx_htmlframe{
	public $jsClass= "bas_frmx_markdownframe";  // Modifiqué el contenido (frmx_htmlframe)

	public function OnPaintContent(){
		global $CONFIG;

		$mark = new lib_markdown_markdown();
	
	// Leemos el contenido a transformar
		$fichero = $CONFIG['SOURCEDIR'].str_replace('_','/',get_called_class()).".inc";

	// Abrioms, leemos y cerramos el fichero el fichero
		$fichero_texto = fopen ($fichero, "r");
		$my_text = fread($fichero_texto, filesize($fichero));
		fclose($fichero_texto);
	
	// Lo transformamos y mostramos su resultado
		echo @$mark->transform($my_text);
		
//		echo Markdown($my_text);  // Esta sería la forma que te suministra la libreria

	}
	
	public function OnPdf($form){
		
	}	
}

?>