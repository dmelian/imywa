<?php
class paper{
	public $contents=array();
	
	public function setLogo($logo){ 
		$this->contents['.ia_logo']= "<img id='ia_img_logo' src='{$this->logo}' height='150px' border='0' >";
	} 
	
	public function setBreadCrumb($breadCrumb){
		$this->contents['.ia_breadcrumb']= $breadCrumb;
	}	
	
	public function setToolbar($toolbar){
		if (isset($this->toolbar)){
			$this->contents['.ia_toolbar']= $toolbar->stamp();
			$this->contents['.ia_menuContainer'] = $toolbar->OnPaintMenu();
		}
	}
	
	public function openFrame($type, $id){
		
	}
	
	public function closeFrame($id= ''){
		
	}
	
	public function open(){
		
	}
	
	public function close(){
		$ret['.ia_framecontainer']= ob_get_contents();
		
	}

/*
		if (isset($this->buttonbar)){
			ob_clean();
			$this->buttonbar->OnPaint();
			$ret['.ia_buttonbar']= ob_get_contents();
			ob_clean();
			if (isset($ret['.ia_menuContainer']))	{$ret['.ia_menuContainer'] .= $this->buttonbar->OnPaintMenu();
				$_LOG-log("ya existe el menuContainer");
			}
			else $ret['.ia_menuContainer'] = $this->buttonbar->OnPaintMenu();
		}
		else $ret['.ia_buttonbar']= "";
		
		$ret[".ia_statusbar"] = "";
*/
	
/* 	The scripts is also paper job.
 			
		if (method_exists($this, 'myscript')) {
			ob_clean();
			$this->myscript();
			$ret['#myscript']= ob_get_contents();
		}
	
*/	
	
}
	
