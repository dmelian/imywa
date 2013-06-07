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

	public $card;
	private $top,$size_height;
	private $measure;

	public function __construct($card){
		$this->card = $card;
		$this->size_height = 31;
		$this->measure = "%";
	}
	
	public function OnPaint(){
		global $_SESSION;
		
		echo "<div style=\"width:100%;height:100%;\">";
            $this->paintComponents();
		echo "</div>";
	}
	
	private function paintComponents(){
	   
	    $percentX=(100 - $this->card->grid["width"])/$this->card->grid["width"];
        $percentY=100/$this->card->grid["height"];
        global $_LOG;

	    for($row = 1; $row<=$this->card->grid["height"];$row++){
			for($colom = 1; $colom<=$this->card->grid["width"];$colom++){
                
                echo "<div class=\"\" style=\"float:left;top:".(($row-1) * $percentY) ."{$this->measure};height:{$percentY}{$this->measure};left:".($colom-1)*$percentX."%;width:".$percentX."%\">";
                    if (isset($this->card->components[$row][$colom])){
                            //$id = $this->card->getComponent($pos)->id;
//                         echo "<button style=\"height:100%;width:100%;\"> </button>";
                        $component = $this->card->components[$row][$colom];
                        $onclick = "onclick=\"ajaxaction('{$component["event"]}', {'idPanel':'{$this->card->id}','idFrame':'grid'});\"";
                        echo "<button style=\"height:100%;width:100%;\" $onclick value=\"".$component["id"]."\">".$component["id"]." </button>";
//                             echo "<button style=\"height:100%;width:100%;\" value=\"".$id."\">".$id." </button>";

//                         echo "<button style=\"height:100%;width:100%;\" value=\"".$this->card->components[$row][$colom]['id']."\">".$this->card->components[$row][$colom]['id']." </button>";
                    }
                    else{
                        echo "<button style=\"height:100%;width:100%;\"> </button>";
                    }
                echo "</div>";                

// 				$_LOG->log("posicion Row: $row, Colum: $colom");
			}
	    }
	}
	
	private function paintTabs(){
		$nelem = count($this->card->tabs);
// 		$gridTab = $this->card->sortComponents();
		for($index=0;$index<$nelem;$index++){
		    echo "<div  id=\"tabs-".($index+1)."\"style=\"position:relative;width:100%;height: 100%;\" >";
		      $this->paintComponents();
		    echo "</div>";
		}
// 		echo "<div class=\"prueba\" style=\"height:".$this->card->grid['height']*$this->size_height ."pt\"> </div>";
		
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
//         echo "<div style=\"height:300pt;width:100%;\">";
	    $this->paintTabs();	   
//         echo "</div>";

	}
}
?>
