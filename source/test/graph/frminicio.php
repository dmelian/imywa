<?php
class frm_inicio{
#prueba de bas_img_graphic

	public function OnLoad(){
		
	}

	public function OnPaint(){
		$def = new bas_img_graphicdef();
		$def->title = "Este es el caballo que viene de Bonanza";
		$def->index = array(1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR'
			, 5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO'
			, 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
		$def->newserie('Rancho Grande', BIGT_BAR_IMG, array(1=>100,2=>450,3=>785,4=>215,5=>895,6=>310,7=>555,8=>1012,9=>415,10=>712,11=>112,12=>203),0x009910);
		$def->serieimages(array(1 => '/var/www/uploads/warning.png'), array(1=>0,2=>0,3=>0,4=>0,5=>1,6=>0,7=>0,8=>1,9=>0,10=>0,11=>0,12=>0));
		$def->newserie('Río Turbulento', BIGT_BAR, array(1=>191,2=>273,3=>412,4=>795,5=>714,6=>335,7=>714,8=>312,9=>480,10=>256,11=>151,12=>211));
		$def->horzline[]=200;
		$def->horzline[]=300;
		$def->horzline[]=500;
		
		$graph = new bas_img_graphic(700,300);
		$graph->generate($def);
		$graph->saveto('/var/www/uploads/imagen.png');
		
		echo "<html><body>\n";
		echo "<img src=\"http://localhost/uploads/imagen.png\">";
		echo "</body></html>\n";
		
	}

	public function OnAction($action, $data){
		
	}
	
}


?>