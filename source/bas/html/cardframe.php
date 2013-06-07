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
class bas_html_cardframe{

	public $card;
	private $top,$size_height;
	private $measure;

	public function __construct($card){
		$this->card = $card;
		$this->size_height = 31;
		$this->measure = "pt";
	}
	
	public function OnPaint($page){
		global $_SESSION;
		$page->addDiv('ia_cardframe', $this->card->id);
		$page->addDiv('ia_tabs');

		$this->includeTabs();
		$page->addDiv('ia_frame_content');
		echo "<form class=\"ia_Form\" style=\"display: inline-block;\" name=\"form_{$this->card->id}\" method=\"post\" enctype=\"multipart/form-data\">";
			"<input type=\"hidden\" name=\"sessionId\" value=\"".$_SESSION->sessionId."\">";
			$this->OnPaintContent();
		echo "</form>";
		$page->closeDiv('ia_cardframe');
	}
	
	private function paintComponents($grid){
	   
	    $percent=100/$this->card->grid["width"];

	    for($row = 1; $row<=$this->card->grid["height"];$row++){
			for($colom = 1; $colom<=$this->card->grid["width"];$colom++){
				$pos = $grid[$row][$colom];
				if ($pos >=0){
					$width = $this->card->components[$pos]["width"] * $percent;
					$height = $this->card->components[$pos]["height"] * $this->size_height;
					//### Tenemos que mirar si la altura (height) puede estar en el less(css)
					echo "<div class=\"ia_componentCard\" style=\"top:".(($row-1) * $this->size_height) ."{$this->measure};height:{$height}{$this->measure};left:".($colom-1)*$percent."%;width:".$width."%\">";
						//$id = $this->card->getComponent($pos)->id;
						$component= $this->card->getComponent($pos);
						if (!is_null($component)){
							$id= $component->id;
// 							if (isset($this->card->record->current[$id]) && ($this->card->GetMode() != "new")) {
							if (isset($this->card->record->current[$id])) {							
								$component->OnPaint($this->card->record->current[$id],$this->card->labelwidth,$this->card->components[$pos]["caption"],$this->card->GetMode());
							} else {
								$component->OnPaint("",$this->card->labelwidth,$this->card->components[$pos]["caption"],$this->card->GetMode());
							}
						} else {
							//TODO: Meter esto en fielddef o fieldtext o fielddefault-void-null-undefined
							echo "componente $pos desconocido";
						}
					echo "</div>";
				}
			}
	    }
	}
	
	private function paintTabs(){
		$nelem = count($this->card->tabs);
		$gridTab = $this->card->sortComponents();
		for($index=0;$index<$nelem;$index++){
		    echo "<div  id=\"tabs-".($index+1)."\"style=\"position:absolute;width:100%;\" >";
		      $this->paintComponents($gridTab[$this->card->tabs[$index]]);
		    echo "</div>";
		}
		echo "<div class=\"prueba\" style=\"height:".$this->card->grid['height']*$this->size_height ."{$this->measure}\"> </div>";
		
	}
	
	private function includeTabs(){
// 		echo "<div  class=\"ia_frame_header\">";
// 	    echo "<ul class=\"ia_listtab \">";
	    echo "<ul class=\"ia_listtab ia_frame_header\">";

	    
	    $nelem = count($this->card->tabs);
	    for($index=0;$index<$nelem;$index++){
			echo "<li><a class=\"ia_tab_item\" href=\"#tabs-".($index+1)."\">";
			echo $this->card->tabs[$index]."</a></li>";
	    }
	    echo "</ul>";
// 	    echo "</div>";
	}
	
	public function OnPaintContent(){
	    $this->paintTabs();	   
	}
}
?>
