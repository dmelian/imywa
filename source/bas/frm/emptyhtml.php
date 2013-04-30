<?php
class bas_frm_emptyhtml{
	
	public function OnPaint(){
		global $CONFIG;
		include("{$CONFIG['BASDIR']}source/".str_replace('_','/',get_called_class()).".inc");
	}
	
}
?>
