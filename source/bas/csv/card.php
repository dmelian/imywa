<?php
class bas_csv_card extends bas_csv_form{
	private $micard;
	private $result;
	
	public function __construct(){
		$this->micard="";
		$this->result="";
	}
	
	function loadcard($card){
		$this->micard=$card;
		$this->prepare();
	}

	function prepare(){
		$ilength=$this->micard->grid["height"];
		$jlength=$this->micard->grid["width"];
		$vectororder=$this->micard->sortComponents();
		$ttab=count($this->micard->tabs);//total tabs
		$tabs=$this->micard->tabs;
		$index=0;
		$auxi=0;
		for($tab=0;$tab<$ttab;$tab++){
			//aqui tengo que insertar el nombre de la tab pero aun no se como.
			$indicetab=$tabs[$tab];
			$this->result[$auxi][0]=$indicetab;
			$auxi++;
			$index=0;
			for($i=1;$i<=$ilength;$i++){
				$auxj=0;
				for($j=1;$j<=$jlength;$j++){
					$index=$vectororder[$indicetab][$i][$j];
					if($index!=-1){
						if($index==-2){
							$this->result[$auxi][$auxj]="";
							$auxj++;
							$this->result[$auxi][$auxj]="";
						}
						else{
							//$campo=$this->micard->components[$index]["field"];
							$campo=$this->micard->getComponent($index);
							//$label=$this->micard->getComponent($index)->Onformat($this->micard->getComponent($index)->caption);//\\
							//$value=$this->micard->record->current[$campo->id];
							if (isset($this->micard->record->current[$campo->id]))$value=utf8_decode($this->micard->record->current[$campo->id]);
							else $value=utf8_decode("");
							$label=$campo->caption.":";
							$this->result[$auxi][$auxj]=$label;
							$auxj++;
							$this->result[$auxi][$auxj]=$value;
						}
						$auxj++;
					}
				}
				$auxi++;
			}
			$this->result[$auxi][0]="";
			$auxi++;
		}
		$this->result[$auxi][0]="";
		$auxi++;
		$this->result[$auxi][0]="";
	}
	
	function Onprint($csv){
		if($csv->success){
			$number=count($this->result);
			for($irow=0;$irow<$number;$irow++){
				//fputcsv($fp,$this->result[$irow],';');
				$csv->write($this->result[$irow]);
			}
		}
	}
}
?>