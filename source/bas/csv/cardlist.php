<?php
class bas_csv_cardlist extends bas_csv_form{
	private $info_campo=array();
	private $Resultquery=array();
	private $milist;
	
	public function __construct(){
		$this->milist="";
	}
	
	function loadcardlist($list){
		$this->milist=$list;
		$this->prepare();
	}
	
	function prepare(){
		global $_LOG;
		$ncolumns=count($this->milist->colComponents);//count($this->milist->components);
		$rows=$this->milist->get_Allrows();
		$nrows=count($rows);
		$aux=array();
		for($i=0;$i<$ncolumns;$i++){
			$component=$this->milist->getComponent($i);
			$this->info_campo[$i]=$component->caption;//$component->Onformat($component->caption);
			for($j=0;$j<$nrows;$j++){
				if (isset($rows[$j][$component->id])){
					$this->Resultquery[$j][$i]=$rows[$j][$component->id];// $component->OnFormat($rows[$j][$component->id]);
				}
				else $this->Resultquery[$j][$i]=" ";
				$_LOG->debug("ESTO ES LO QUE HAY",$this->Resultquery[$j][$i]);
			}
		}
	}
	
	function Onprint($csv){//($db,$dbtable,$filename)
		if($csv->success){
			$csv->write($this->info_campo);
			$number=count($this->Resultquery);
			for($irow=0;$irow<$number;$irow++){
				$csv->write($this->Resultquery[$irow]);
			}
		}
	}
}
?>

?>