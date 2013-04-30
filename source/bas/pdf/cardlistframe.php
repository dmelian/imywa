<?php

class bas_pdf_cardlistframe extends bas_pdf_miclase{
// 	private $Resultquery=array();
// 	private $mix;
// 	private $miy;
// 	private $myhead=array();
// 	private $totalrow;
// 	private $totalheight;
	
	function load($frame){
		global $_LOG;
		$aux=array();
		$rows=$frame->get_Allrows();
		$this->mix=count($rows);//numero de filas
		$this->miy=count($frame->colComponents);//numero de columnas
		$resultindex=0;
		$_LOG->log("filas:".$this->mix);
		$_LOG->log("columnas".$this->miy);
		for($i=0;$i<$this->miy;$i++){
			$component=$frame->getComponent($i);
			$this->myhead[$i]=  $this->transformData($component->Onformat($component->caption));
			$_LOG->debug("registros",$rows);
			$idComponent = $component->id;
			for($j=0;$j<$this->mix;$j++){
			
				if ($component->type == "abstract"){ 
					$_LOG->log("se realiza el intercambio");
					$abstractComp = $component;
					$component = $frame->getRowType($rows[$j][$frame->mainComp]);
				}
				else	$abstractComp = NULL;
					
				$this->Resultquery[$j][$i]= $this->transformData($component->OnFormat($rows[$j][$idComponent]));
				
				if(isset($abstractComp)) $component = $abstractComp;
			}
		}
	}
	
}

?>