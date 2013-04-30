<?php
class test_frmx_htmlframe_form extends bas_frmx_form{

	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,close');
		$this->title= 'HTML Frame Test';
		//$this->addFrame(new test_frmx_htmlframe_form_frame1('frame1','Frame 1'));
		$this->addFrame(new test_frmx_htmlframe_form_frame2('frame2','Frame 2'));
		$this->addFrame(new test_frmx_htmlframe_form_frame3('frame3','Frame 3'));
		$this->addFrame(new test_frmx_htmlframe_form_frame2('frame2bis','Frame 2(Copia)'));
		$this->buttonbar = new bas_frmx_buttonbar();
		$this->buttonbar->addAction("aceptar");
		$this->buttonbar->addAction("eliminar");
		$this->buttonbar->addAction("nuevo");
	}
	
	public function OnHeader(){
		$this->addDiv('test_logo');
		$this->nextDiv('test_panelgris');
	}

	
	public function OnFooter(){
		$this->addDiv('test_info');
	
		echo '<div class="footer">
			<div class="fila">
				<div class="col" style="width:25%;max-width:1600px;color:#fff">COPYRIGHT & POWERED BY A&M EDITA S.L. 2.012</div>
				<div class="col" style="width:25%;max-width:1600px;color:#fff">TELÉFONO</div>
				<div class="col" style="width:25%;max-width:1600px;color:#fff">FAX </div>
				<div class="col" style="width:20%;max-width:1600px;color:#fff"> WEB</div>
			</div>
		
			<div class="fila" >
				<div class="col" style="width:25%;max-width:1600px;color:#fff">CL Pío XII, 64-Las Palmas de G.C. CP 35006</div>
				<div class="col" style="width:25%;max-width:1600px;color:#fff">928 260 540</div>
				<div class="col" style="width:25%;max-width:1600px;color:#fff">928 494 669</div>
				<div class="col" style="width:20%;max-width:1600px;color:#fff">www.amedita.es</div>
			</div>
		</div>';
		$this->nextDiv('test_panelgris');
	}
	
	
}
?>
