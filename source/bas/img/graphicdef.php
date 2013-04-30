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

define('BIGT_BAR',0);
define('BIGT_LINE',1);
define('BIGT_BAR_IMG',16);
define('BIGT_LINE_IMG',17);

class bas_img_graphicdef{
	public $title;
	public $index;
	public $serie=array();
	public $margin;
	public $horzline=array();
	
	protected $colors=array();
	protected $defaultcolor;
	
	public function __construct(){
		$this->margin = 20;
		$this->x_axis = array('padding' => '10', 'captionheight' => 40);
		$this->x_axis = array('padding' => '10', 'captionwidth' => 80);
		$this->serie = array();
		$this->defaultcolor = array(
			0xdc143c   # Crimson
			, 0x6495ed # CornflowerBlue
			, 0x228b22 # ForestGreen
			, 0xffd700 # Gold
			, 0xda70d6 # Orchid
			, 0xcd853f # Peru
			, 0xff4500 # OrangeRed
			, 0x4169e1 # Royal Blue
			, 0x2e8b57 # Ocean Green
			, 0xffff00 # Yellow
			, 0xc71585 # MediumVioletRed
			, 0xa0522d # Sienna
			, 0xb22222 # FireBrick
			, 0x00bfff # Deepskyblue
			, 0x00ff7f # SpringGreen
			, 0xffa500 # Orange
			, 0xff69b4 # Hotpink
			, 0xbc8f8f # RosyBrown
			);
	}
	
	public function getcolor($id){
		switch($id){
			case 'BACKGROUND':
			case 'AXES':
			case 'PEN':
			default:
				return isset($this->colors[$id]) ? $this->colors[$id] : false;
		}
	}
	
	public function getmargin($id){
		return $this->margin;
	}

	
	public function newserie($title, $type, $data, $color=false){
		$id = count($this->serie);
		if ($color !== false) $this->colors["SERIE$id"] = $color;
		else $this->colors["SERIE$id"] = isset($this->defaultcolor[$id]) ? $this->defaultcolor[$id] : 0; 
		if (!isset($this->index)) $this->autoindex($data); else $this->autocomplete($data);
		$this->serie[] = array('id' => "SERIE$id", 'title' => $title, 'type' => $type, 'data' => $data, 'color' => "SERIE$id");
	}
	
	public function autocomplete(&$data){
		$countindex=count($this->index);
		$aux=$data;
		for($cols=1;$cols<=$countindex;$cols++){
			if(isset($data[$cols])){
				$aux[$cols]=$data[$cols];
			}else{
				$aux[$cols]=0;
			}
		}
		
		$data = array();
		for($cols=1;$cols<=$countindex;$cols++){
			$data[$cols]=$aux[$cols];
		}
	}
	
	public function serieimages($image, $imagedata){
		$id = count($this->serie)-1;
		$this->serie[$id]['image'] = $image;
		$this->serie[$id]['imagedata'] = $imagedata;
	}
	
	public function autoindex($data){
		if (isset($this->index)) unset ($this->index);
		$this->index = array();
		foreach(array_keys($data) as $key) $this->index[$key] = $key;
	}
	
	public function setindex($index){
		if (isset($this->index)) unset ($this->index);
		$this->index = array();
		foreach($index as $value) $this->index[$value] = $value;
	}
	
}
?>
