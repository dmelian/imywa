<?php

class frmx_paragraphframe_form_frame1 extends bas_frmx_paragraphframe{

	public function OnParagraph($painter){
		$painter->setFont('serif', 14);
		$painter->text('Encabezado primero a 14 pt', 'center','bold');
		$painter->setFont('serif', 10);
		$painter->text('Este es el caballo que viene por la pradera de Bonanza');
		$painter->image('image/icon_dlginfo.png', 'left');
		$painter->text('Información asociada al frame. (A la derecha de la imagen)');
		$painter->forwardPage();
		$painter->text('Este es el otro caballo. Este no viene por la pradera.');
	}
	
	
}

?>