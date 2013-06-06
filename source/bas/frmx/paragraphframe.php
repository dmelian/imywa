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

class bas_frmx_paragraphframe extends bas_frmx_frame{
	public $jsClass= "bas_frmx_paragraphframe";
	
	public function OnPaintContent(){
		$painter= new bas_htm_paragraph();
		$this->OnParagraph($painter);
		
	}
	
	public function OnPdf($form){
		$painter= new bas_pdf_paragraph($form);
		$this->OnParagraph($painter);
	}
	
}

?>