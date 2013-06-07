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
class bas_html_cronoFrame extends bas_html_listframe{

	public function __construct($list,$selector=true){
		$this->frame = $list;
		
		$this->selector = $selector;
		$this->top = 4;
		$this->measure = "pt";
		$this->height = 18;
		$this->cssComp = $this->frame->getCssComponent();
		
		$this->footer=1;
	}
	
	
	public function autoSize(){
		$this->autosize = floor(96/(count($this->frame->components)+count($this->frame->cronoHeader)));
	
	}

	protected function PaintDinamic($rows){
		$nelem = count($this->frame->components);
		for($index=$this->frame->fixedColums;$index < $nelem;$index++){
			    echo "<div class=\"columDinamic\" style=\"position:relative; top:0{$this->measure};height:100%;vertical-align:top;width:";
			    if (!isset($this->autosize)) echo $this->frame->getComponentWidth($index)."{$this->measure};display:inline-block;overflow:hidden;\">";
			    else echo $this->autosize."%;display:inline-block;overflow:hidden;\">";
				  $this->ContentColum( $this->frame->getComponent($index),$rows,false);
			    echo "</div>";
		}
// 		$nelem = count($this->frame->cronoHeader);
		$component = new bas_sqlx_fieldnumber("temp","temp");
		$width = 100;
// 		for($index=0;$index < $nelem;$index++){
		foreach($this->frame->cronoHeader as $header){
                $component->caption = $component->id = $header;
                echo "<div class=\"columDinamic\" style=\"position:relative; top:0{$this->measure};height:100%;vertical-align:top;width:";
                
                    if (!isset($this->autosize)) echo $width."{$this->measure};display:inline-block;overflow:hidden;\">";
                    else echo $this->autosize."%;display:inline-block;overflow:hidden;\">";
                    
                    $this->ContentColum( $component,$rows,false);
                echo "</div>";
        }
	}
	
	
	protected function sizeColumns($begin,$end){
		$size = 0;
		for ($ind=$begin;$ind<$end;$ind++){
			$size = $size + $this->frame->getComponentWidth($ind)+2;
		}
// 		global $_LOG;
// 		$_LOG->log("HTML_listFrame::sizeColumns. valor del size: $size");
		return $size;	
	}
	
	public function OnPaintList($rows){
	
		$listDinamic_width = 60;
		$listFixed_width = 31;
		$size_components = count($this->frame->components);
		$fixed_width = $this->sizeColumns(0,$this->frame->fixedColums);
		$dinamic_width = $this->sizeColumns($this->frame->fixedColums,$size_components)+count($this->frame->cronoHeader);
		if ($fixed_width != 0){
	// contenedor de los campos "estáticos" de la lista.    	
			echo "<div class=\"ia_listFixed\" style=\"overflow:scroll;width:31%;position:absolute;overflow-x:auto;overflow-y: hidden;height:100%\">"; // Será el contenedor de los campos fijos de la lista. Poseera un overflow scroll para mostrar el contenido restante
				echo "<div class=\"ia_Colums_fixed\" style=\"position:relative;white-space:nowrap;height:100%;width:".$fixed_width."{$this->measure}\">"; // Contendrá las columnas fijas de la lista.
					$this->Paintfixed($rows);
				echo "</div>";
			echo "</div>";
		}
		else{
			$listDinamic_width += 36; //modificado inma
			$listFixed_width = 0;
		}
		
		if ($dinamic_width != 0){
	// contenedor de los campos "dinámicos" de la lista.    
			echo "<div class=\"ia_listDinamic\" style=\"position:absolute;left:$listFixed_width%;overflow-x:auto;overflow-y: hidden;right:0px;height:100%\">"; // Contenedor con todas las columnas dinamicas.			
				echo "<div class=\"ia_Colums_dinamic\" style=\"position:relative;white-space:nowrap;height:100%;width:";
					if (!isset($this->autosize)) echo $dinamic_width."{$this->measure}\">"; //.$dinamic_width."{$this->measure}\">"; // Contendrá las columnas fijas de la lista.
					else echo "100%\">";
					
					$this->PaintDinamic($rows);
				echo "</div>";			
				
			echo "</div>";
		}	
	}
	
    public function OnPaintFooter(){
//         parent::OnPaint();
        echo "<div style=\"margin-left: 40%;\">";
            echo "<button onclick=\"ajaxaction('cronoPrev');\" ><span class=\"ui-icon ui-icon-triangle-1-w\"></span></button>";
            echo "<input type='text' onchange=\"ajaxaction('changePeriod',{'value':this.value});\" value='{$this->frame->curDate}'></input>";
            echo "<button onclick=\"ajaxaction('cronoNext');\"><span class=\"ui-icon ui-icon-triangle-1-e\"></span></button>";

            
            echo "<select onchange=\"ajaxaction('changePeriod',{'period':this.value});\" name=\"PEPE\" >";
            $enum= $this->frame->getPeriods();
            $curEnum = $this->frame->periodSelected();
            foreach ($enum as $field => $caption){      
                echo "<option  name=\"$field\" value=\"".$field."\"";
                if ($curEnum == $field){
                    echo " selected=\"selected\"";
                }
                echo ">".$caption."</option>";
            }
            echo "</select>";

        echo "</div>";
        
    }
	

}
?>
