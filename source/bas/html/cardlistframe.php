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
class bas_html_cardlistframe extends bas_html_listframe{

	public $frame;
	protected $top;
	protected $measure;
	protected $height;

	protected $cssComp;
	protected $autosize;
	protected $contenido;

	public function __construct($list){
		$this->frame = $list;
		
		$this->top = 4;
		$this->measure = "pt";
		$this->height = 18;
		$this->cssComp = $this->frame->getCssComponent();
	}
	
	public function setHeight($height=18){
        $this->height = $height;
	}
	
	
	public function autoSize(){
		$this->autosize = floor(96/count($this->frame->colComponents));
	
	}
	
	protected function ContentColum($component,$rows, $type,$Poscolum=0){
		if ($type) $class="header_columStatic ";
		else $class="header_columDinamic ";
		
		$nelem = count($this->frame->colComponents);
		
		$margin_left= 20;
		$nelem = count($rows);
		
		
		echo "<div class =\" $class ia_header_colum \"style=\"position:relative;overflow:hidden; top 0{$this->measure};width:100%;height:".$this->height."{$this->measure};\">";
			echo "<label style=\"margin-left:".$margin_left."{$this->measure};\">";
					echo $component->caption;
			echo "</label>";		      
		echo "</div>";
		$class="";
		if ($component->type !=  "abstract"){ // La columna posee un typeDef.			
			for($index=0;$index < $nelem;$index++){
				if ( (isset($this->cssComp)) && ($rows[$index][$this->cssComp] != ""))	$classDyn = $rows[$index][$this->cssComp];
	// 			if (isset($rows[$index][$this->cssComp]))	$classDyn = $rows[$index][$this->cssComp];
				else	$classDyn = "";
				
				echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$component->id}";
                
                if ($component->reference === $this->frame->mainComp){
                    $component->description =  $this->frame->getRowType($rows[$index][$this->frame->mainComp])->description;
                    global $_LOG;
                    $_LOG->log("Se entra en el references. El valor es: ".$component->description);
                }
                else{
					$component->description = null;
                }
				if (isset($component->description)){
					echo " ia_descripcion_hover\" title=\"{$component->description}\"";
				}	
				else echo "\"";
				echo "style=\"position:relative;width:100%;text-align:{$component->align};overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";
// 				echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$component->id} \"style=\"position:relative;width:100%;text-align:{$component->align};overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";
						if (isset($rows[$index]) && isset($rows[$index][$component->id])){
// 							echo $component->OnFormat($rows[$index][$component->id]);
							$this->paintValue($rows[$index][$component->id],$component,(($nelem*$index)+$Poscolum));
						}
						else{
// 							echo $component->OnFormat("");
							$this->paintValue("",$component,(($nelem*$index)+$Poscolum));
						}
				echo "</div>";
				$component->description = null;
				
			}
		}
		else{ // la columna no posee un tipo específico.
			$currentID = $component->id;
			for($index=0;$index < $nelem;$index++){
	// 			if (isset($rows[$index][$this->cssComp]))	$classDyn = $rows[$index][$this->cssComp];
				if ( (isset($this->cssComp)) && ($rows[$index][$this->cssComp] != ""))	$classDyn = $rows[$index][$this->cssComp];
				else	$classDyn = "";
						
				$component = $this->frame->getRowType($rows[$index][$this->frame->mainComp]);
				$component->name = $component->id."/".$currentID;
				if ($component->reference === $this->frame->mainComp){
                    $component->description =  $this->frame->getRowType($rows[$index][$this->frame->mainComp])->description;
                    global $_LOG;
                    $_LOG->log("Se entra en el references. El valor es: ".$component->description);
                }
                else{
                    $component->description = null;
                }

				echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$currentID}";
				if ($component->description)echo " ia_descripcion_hover\" data=\"$component->description\"";
				else echo "\"";
				echo "style=\"position:relative;width:100%;text-align:{$component->align};overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";
						if (isset($rows[$index]) && isset($rows[$index][$currentID]))
							$this->paintValue($rows[$index][$currentID],$component,(($nelem*$index)+$Poscolum));
						else
							$this->paintValue("",$component,(($nelem*$index)+$Poscolum));
				echo "</div>";
				
			}		
		}
	
	}
	
	protected function paintValue($value,$component,$indexTab=0){
		if($this->frame->getMode()!= "read"){
            $component->indexTab= $indexTab;
            $component->OnPaintList($value,"edit");
        }
		else
			echo $component->OnFormat($value);
	}
	
	protected function Paintfixed($rows){
		//$nelem = count($this->fixedColums);
		for($index=0;$index < $this->frame->fixedColums;$index++){
			    echo "<div class=\"columStatic\" style=\"position:relative; top:0{$this->measure};height:100%;vertical-align:top;width:";
			    if (!isset($this->autosize)) echo $this->frame->getComponentWidth($index)."{$this->measure};display:inline-block;overflow:hidden;\">";
			    else echo $this->autosize."%;display:inline-block;overflow:hidden;\">";
				   $this->ContentColum($this->frame->getComponent($index),$rows,true,$index);
			    echo "</div>";
		}
	}
	
	protected function PaintDinamic($rows){
		$nelem = count($this->frame->colComponents);
		for($index=$this->frame->fixedColums;$index < $nelem;$index++){
			    echo "<div class=\"columDinamic\" style=\"position:relative; top:0{$this->measure};height:100%;vertical-align:top;width:";
			    if (!isset($this->autosize)) echo $this->frame->getComponentWidth($index)."{$this->measure};display:inline-block;overflow:hidden;\">";
			    else echo $this->autosize."%;display:inline-block;overflow:hidden;\">";
					global $_LOG;
					$_LOG->log("se alcanza la posicion: $index");
				  $this->ContentColum( $this->frame->getComponent($index),$rows,false,$index);
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
	
	public function OnPaintBlock(){
		// Calculamos el ancho total de cada división para evitar el conflicto del position relative.
		
	// Obtenemos los registros a mostrar.
		$rows = $this->frame->get_rows();
		$nreg = count($rows);
		echo "<div class=\"ia_list\" style=\"position:relative;white-space:nowrap;height:".(10+($nreg+1)*$this->height + ($nreg+1)*$this->top)."{$this->measure}\">";//TODO: A revisar correctamente (presentacion amedita)
		echo "<form class=\"ia_Form\" name=\"form_{$this->frame->id}\" method=\"post\" enctype=\"multipart/form-data\">";

			echo "<div class=\"ia_list_container\" style=\"position:absolute;height:100%;left:0{$this->measure};right:0{$this->measure};\">";
				$this->OnPaintList($rows);
			echo "</div>";
		echo "</form>";
		echo "</div>";
		
		
	}
	
	public function OnPaintList($rows){
	
// 		$size_components = count($this->frame->colComponents);
// 		$dinamic_width = $this->sizeColumns(0,$size_components);	
		
		
		
		$listDinamic_width = 60;
		$listFixed_width = 31;
		$size_components = count($this->frame->colComponents);
		$fixed_width = $this->sizeColumns(0,$this->frame->fixedColums);
		$dinamic_width = $this->sizeColumns($this->frame->fixedColums,$size_components);
		if ($fixed_width != 0){ //  TODO: Controlar que no se insertan fijas cuando se usa el autoSize
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
		
	// contenedor de los campos "dinámicos" de la lista.    
		echo "<div class=\"ia_listDinamic\" style=\"position:absolute;left:$listFixed_width%;overflow-x:auto;overflow-y: hidden;right:0px;height:100%\">"; // Contenedor con todas las columnas dinamicas.			
			echo "<div class=\"ia_Colums_dinamic\" style=\"position:absolute;white-space:nowrap;height:100%;width:";
				if (!isset($this->autosize)) echo $dinamic_width."{$this->measure}\">"; //.$dinamic_width."{$this->measure}\">"; // Contendrá las columnas fijas de la lista.
				else echo "100%\">";
				$this->PaintDinamic($rows);
			echo "</div>";					
		echo "</div>";
	
	
	}
	

}
?>
