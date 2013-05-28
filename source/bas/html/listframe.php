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
class bas_html_listframe{

	public $frame;
	protected $top;
	protected $measure;
	protected $height;
	protected $selector;
	protected $cssComp;
	protected $autosize;
	
    protected $footer;
    
	public $initialrowcount=10;

	public function __construct($list,$selector=true){
		$this->frame = $list;
		
		$this->selector = $selector;
		$this->top = 4;
		$this->measure = "pt";
		$this->height = 18;
		$this->cssComp = $this->frame->getCssComponent();
		$this->footer = null;
	}
	
	
	public function autoSize(){
		$this->autosize = floor(96/count($this->frame->components));
	
	}

	public function setHeight($height=18){
        $this->height = $height;
	}
	
	protected function PaintSelector($nelem){
	
		echo "<div class =\" selector_colom \"style=\"position:relative; top 0{$this->measure};height:".$this->height."{$this->measure};\">";
// 		      echo "Nº fila";
		echo "</div>";
		
		$class= "selector_row ia_select_box";
		
		for($index=0;$index < $this->frame->n_item;$index++){
				if ($index == $nelem) $class = " ia_select_box ";
			    echo "<div   style=\"position:relative; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";
					echo "<div id=\"". ($index+1) ."\" class =\"". $class ." row_".($index+1) ."\"> </div>" ;
				echo "</div>";
		}
	}
	
	protected function ContentColum($component,$rows, $type){
		if ($type) $class="header_columStatic ";
		else $class="header_columDinamic ";
		
		
		$margin_left= 0;//20;
		$nelem = $this->initialrowcount;//count($rows);
		
		echo "<div class =\" $class ia_header_colum \"style=\"position:relative;overflow:hidden; top 0{$this->measure};width:100%;height:".$this->height."{$this->measure};\">";
			echo "<label style=\"margin-left:".$margin_left."{$this->measure};\">";
					echo $component->caption;
			echo "</label>";		      
		echo "</div>";
		$class="";
		
		global $_LOG;
		$_LOG->log("tipo enviado: " . $component->type);
		
		for($index=0;$index < $nelem;$index++){
			if ( (isset($this->cssComp)) && ($rows[$index][$this->cssComp] != ""))	$classDyn = $rows[$index][$this->cssComp];
// 			if (isset($rows[$index][$this->cssComp]))	$classDyn = $rows[$index][$this->cssComp];
			else	$classDyn = "";
				
			//echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$component->id} \"style=\"position:relative;width:100%;text-align:{$component->align};overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";
// 					echo "<label style=\"margin-left:".$margin_left."{$this->measure};\">";

            if ($component->type != "textarea"){
                echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$component->id} \"style=\"position:relative;width:100%;text-align:justify;text-indent:20%;overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";

                if (isset($rows[$index]) && isset($rows[$index][$component->id]))
                    echo $component->OnFormat($rows[$index][$component->id]);
                else
                    echo $component->OnFormat("");
            }
            else{
                echo "<div class =\"". $class ."$classDyn list_row row_".($index+1) ." {$component->id} \"style=\"position:relative;width:100%;text-align:justify;overflow:hidden; top:".($index+1)*$this->top ."{$this->measure};height:".$this->height."{$this->measure};\">";

                if (isset($rows[$index]) && isset($rows[$index][$component->id]))
                    echo $component->OnPaintList($rows[$index][$component->id],"read");
                else
                    echo $component->OnPaintList("","edit");
            }

// 					echo "</label>";
			echo "</div>";
		}
	
	}
	
	protected function Paintfixed($rows){
		//$nelem = count($this->fixedColums);
		for($index=0;$index < $this->frame->fixedColums;$index++){
			    echo "<div class=\"columStatic\" style=\"position:relative; top:0{$this->measure};height:100%;vertical-align:top;width:";
			    if (!isset($this->autosize)) echo $this->frame->getComponentWidth($index)."{$this->measure};display:inline-block;overflow:hidden;\">";
			    else echo $this->autosize."%;display:inline-block;overflow:hidden;\">";
				   $this->ContentColum($this->frame->getComponent($index),$rows,true);
			    echo "</div>";
		}
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
	
	public function OnPaint(){
		$this->OnPaintBlock();
		
		if(isset($this->footer)){
            $this->OnPaintFooter();	
		}
		
	}
	
	protected function OnPaintFooter(){
         echo "<div style=\"margin-left: 40%;\">";
            echo "<button onclick=\"ajaxaction('listPrev',{'idFrame':{$this->frame->id}});\" ><span class=\"ui-icon ui-icon-triangle-1-w\"></span></button>";
            echo "<input type='text' value='{$this->footer}' disabled=\"disabled\">";
            echo "<button onclick=\"ajaxaction('listNext',{'idFrame':{$this->frame->id}});\"><span class=\"ui-icon ui-icon-triangle-1-e\"></span></button>";
        echo "</div>";
	
	}
	
	public function setFooter($value){
        $this->footer = $value;
	}
	
	protected function OnPaintBlock(){
        // Calculamos el ancho total de cada división para evitar el conflicto del position relative.
        
        $size_components = count($this->frame->components);
        $fixed_width = $this->sizeColumns(0,$this->frame->fixedColums);
        $dinamic_width = $this->sizeColumns($this->frame->fixedColums,$size_components);
        
//      $size_components = count($this->frame->components);
//      $fixed_width = (100+2)*$this->frame->fixedColums ;
//      $dinamic_width = (100 +10)*($size_components - $this->frame->fixedColums);

    // Obtenemos los registros a mostrar.
        $rows = $this->frame->get_rows();

        
        if ($size_components < $this->frame->fixedColums){
            $this->frame->fixedColums= $size_components; // Lo hacemos mediante la funcion¿? o Solo lo almacenamos en local¿?
            global $_LOG;
            $_LOG->log("html_listFrame::OnPaint - Se han solicitado un número inexistente de columnas fijas");
        }
        
        
// 		$nreg = $this->initialrowcount;
		$nreg = count($rows);
		if ($nreg < $this->frame->n_item) $nreg++;
        echo "<div class=\"ia_list\" style=\"position:relative;white-space:nowrap;height:";//TODO: A revisar correctamente (presentacion amedita)
		echo (7+($nreg+1)*($this->height+$this->top))."{$this->measure};width:100%;\">";
        
//         if( $nreg >= 5)echo (7+($this->frame->n_item+1)*($this->height+$this->top))."{$this->measure};width:100%;\">";
//         else  echo (7+($nreg+1)*($this->height+$this->top))."{$this->measure};width:100%;\">";

        
            if ($this->selector){
                // Se crea el encapsulado de los selectores de fila.
                echo "<div class=\"ia_selector\" style=\"position:absolute;overflow:left:0px;hidden;height:100%; width:25{$this->measure};\">"; // Mostrará la fila seleccionada.
                    $this->PaintSelector($nreg);
                echo "</div>";
            }
            
            echo "<div class=\"ia_list_container\" style=\"position:absolute;white-space:nowrap;height:100%;";
            if ($this->selector) echo "left:25{$this->measure};";
            else echo "left:0{$this->measure};";
            echo "right:16{$this->measure};\">";
                $this->OnPaintList($rows);
            echo "</div>";
            
            echo "<div class=\"scroll_List\"style=\"position:absolute;top:0px;right: 0px;overflow:auto;width:16{$this->measure};height:100%\">";//($this->frame->n_item+1)*$this->height."{$this->measure};\">";
                echo "<div style=\"width:1px;height:".($this->frame->getQuerySize()+1)*($this->height+$this->top)."{$this->measure};\"></div>";
            echo "</div>";

        echo "</div>";
	
	}
	
	public function OnPaintList($rows){
	
		$listDinamic_width = 60;
		$listFixed_width = 31;
		$size_components = count($this->frame->components);
		$fixed_width = $this->sizeColumns(0,$this->frame->fixedColums);
		$dinamic_width = $this->sizeColumns($this->frame->fixedColums,$size_components);
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
	

}
?>
