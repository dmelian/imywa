<?php

// Por problemas de codificación se omitiran las tildes.
// Nota: Esta clase podria heredar desde la clase frame (a incluir)

/* Descripcion:
	El atributo container es el objeto que contendra toda la informacion
	Los campos de este seran:
		# Parrafo y Encabezado
			- Tipo. P o E
			- Texto enriquecido.
			- Podria añadirse el align o dejarlo en el texto enriquecido.
		# Imagenes
			- Tipo. I
			- Ruta del fichero.
			- Contenido para el alt.
			- Tamaño mostrar, 0 si se usara el tamaño real de la imagen.
			- Disposicion (align) de la imagen.
*/

class bas_htm_paragraph{

        private $size,$family,$indent_size;
        
	
	public function __construct(){
		/*$this->id= $id;
		$this->title= $title;
		$this->container = $container;*/
		$this->indent_size = 30;
		$this->size= 18;
		$this->family = "serif";
	}
	
	public function setFont($family, $size){
	    $this->size= $size;
	    $this->family = $family;
	}
	
	public function text ($text, $align="left", $style="", $indent=TRUE){ 
	
	// Introducimos el parrafo con la alineación deseada y el tamaño y fuente confiurada.
	
	    echo "<p align= \"{$align}\" style=\"font-size:{$this->size}pt;font-family:{$this->family}";
	    
	    // Nota: Podria utilizarse una clase de css para contolar el tamaño.
	    
	    if ($indent){ // Introducimos el indentado, o no, del texto.
		echo " ;text-indent:{$this->indent_size}pt\">";
	    }
	    else {
		echo "\">";
	    }
	    
	    // Introcudimos las etiquetas necesarias para representar el estilo solicitado.
	    if ($style == ""){
		echo "{$text} </p>";
	    }
	    else{
		switch ($style){
		case "bold":
		    $st= "b";
		break;
		case "underline":
		    $st= "u";	  
		break;

		case "italic":
		    $st= "i";	  
		break;
		}
		
		echo "<{$st}> {$text} </{$st}> </p>";
	    }   
	}

	public function image($url, $align){
	  if ($align == "center"){
	      echo "<div align=\"center\">";
	      echo "<img src=\"{$url}\" alt= \"Big Boat\" \>";
	      echo "</div>";
	  }
	  else{
	      echo "<img src=\"{$url}\" alt= \"Big Boat\" align= \"{$align}\" \>";
	  }
	
	}
	
	public function forwardPage(){
	    echo "<HR width=95.8% align=\"center\">";
	}
	

	// Funcion auxiliar/temporal utilizada para probar el correcto funcionamiento con la prueba week1
	public function OnPaint(){
		$texto = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla dictum malesuada nunc eget faucibus. Nullam ut aliquet sapien. Nam auctor consectetur dui, sed tristique turpis eleifend et. Fusce laoreet mi nec dui interdum eget ultricies felis placerat. Suspendisse faucibus pretium fringilla. Integer augue ante, rhoncus nec tristique eget, sollicitudin at lorem. Curabitur sollicitudin sodales dolor at pellentesque. Nam vitae nibh nulla. Ut leo enim, faucibus id lobortis laoreet, tincidunt in enim. Morbi ultrices tortor ac felis pellentesque sed elementum magna blandit. Praesent condimentum, nulla vel venenatis laoreet, risus orci egestas nibh, ac porta elit est eu lorem. Vestibulum posuere feugiat.";
		
		echo "<div class=\"frmx_frame\">";
		echo "<div class=\"frmx_frame_content\">";
		// Se obserara el tipo de informacion a tratar mediante un CASE. Se utilizan los handles oportunos.
		
		$this->text($texto,"justify","",TRUE);
		$this->image("","center");
		$this->text($texto,"justify","italic",false);
		echo "</div></div>";
		$this->forwardPage();
	}
	
}

?>