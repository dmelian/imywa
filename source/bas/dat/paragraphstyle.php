<?php
class bas_dat_paragraphstyle{
	public $id;
	public $properties=array();

	
	
	public function __constructor(){
//text
		$id= 'default';
		$this->properties[$id]=array();
		$this->properties[$id]['fontfamily']='';
		$this->properties[$id]['fontsize']='';
		$this->properties[$id]['fontbold']='';
		$this->properties[$id]['fontunderline']='';
		$this->properties[$id]['fontitalic']='';
		$this->properties[$id]['forecolor']='';
		$this->properties[$id]['']='';
//paragraph
		$this->properties[$id]['align']='';
		$this->properties[$id]['indent']='';
		$this->properties[$id]['firstlineindent']='';
//box
		$this->properties[$id]['backcolor']='';
		$this->properties[$id]['backimage']='';
		$this->properties[$id]['scopepdf']='';
		$this->properties[$id]['scopehtml']='';
		$this->properties[$id]['horigin']='';
		$this->properties[$id]['vorigin']='';
		$this->properties[$id]['width']='';
		$this->properties[$id]['height']='';
//command
		$this->properties[$id]['avpag']='';
		$this->properties[$id]['']='';
		$this->properties[$id]['']='';
		$this->properties[$id]['']='';
		$this->properties[$id]['']='';
		
	}
	
}

?>