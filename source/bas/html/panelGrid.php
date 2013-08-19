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
class bas_html_panelGrid{

	public $card; //what card?. It is a pannel, isn't it?
	private $top,$size_height; //we don't use this.
	private $measure; // Is this really needed?
	private $parentFrameId; //$mainGrid;
	
	protected $typePanel;
	

	public function __construct($card){
		$this->card = $card;
		$this->size_height = 31;
		$this->measure = "%";
		$this->typePanel = $card->type;
	}
	
	public function OnPaint($parentFrameId=""){
		$this->parentFrameId = $parentFrameId;
		echo "<div class=\"ia_containerGrid\"style=\"position: absolute;width:100%;height:100%;\">";
            $this->paintComponents();
		echo "</div>";
	}
	
	private function paintComponents(){
	   
// 	    $percentX=(100 - $this->card->grid["width"])/$this->card->grid["width"];
	    $percentX=100/$this->card->grid["width"];
        $percentY=100/$this->card->grid["height"];
        global $_LOG;

	    for($row = 1; $row<=$this->card->grid["height"];$row++){
			for($colom = 1; $colom<=$this->card->grid["width"];$colom++){
                
                echo "<div class=\"{$this->typePanel}_button\" style=\"float:left;top:".(($row-1) * $percentY) ."{$this->measure};height:{$percentY}{$this->measure};left:".($colom-1)*$percentX."%;width:".$percentX."%\">";
                    if (isset($this->card->components[$row][$colom])){
                        $component = $this->card->components[$row][$colom];
                        
                        $onclick = "onclick=\"ajaxaction('{$component["event"]}', {'idPanel':'{$this->card->id}','idFrame':'{$this->parentFrameId}','item':'{$component["id"]}'});\"";
//                         $class = (is_null($component["itemClass"]))?$component["itemClass"]:$this->card->gnrClass;

                        if ($component["itemClass"] == "") $class = $this->card->gnrClass;
                        else $class = $component["itemClass"];
                        
                        echo "<button class=\"$class\" style=\"height:100%;width:100%;position:relative;\" $onclick value=\"".$component["id"]."\">".
								"<label style=\"position: absolute;top: 0px;left: 50%;\"> {$component["subItem"]} </label>".
								"<label >{$component["caption"]}</label>".
								" </button>";
                    }
                    else{
                        echo "<button class=\"{$this->card->gnrClass}\" style=\"height:100%;width:100%;\"> </button>";
                    }
                echo "</div>";                
			}
	    }
	}
	
	private function paintTabs(){
		$nelem = count($this->card->tabs);
		for($index=0;$index<$nelem;$index++){
		    echo "<div  id=\"tabs-".($index+1)."\"style=\"position:relative;width:100%;height: 100%;\" >";
		      $this->paintComponents();
		    echo "</div>";
		}
		
	}
	
	private function includeTabs(){
	    echo "<ul class=\"ia_listtab ia_frame_header\">";

	    
	    $nelem = count($this->card->tabs);
	    for($index=0;$index<$nelem;$index++){
			echo "<li><a class=\"ia_tab_item\" href=\"#tabs-".($index+1)."\">";
			echo $this->card->tabs[$index]."</a></li>";
	    }
	    echo "</ul>";
	}
	
	public function OnPaintContent(){
	    $this->paintTabs();	   
	}
}
?>
