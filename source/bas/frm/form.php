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
class bas_frm_form {
	// Esta clase pretende ser un formulario compuesto por diferentes componentes tipo lista o card o ???? lo que venga.
	protected $toolbar=array();
	protected $components=array();
	protected $title;
	protected $menu;


	public function __construct(){
		if (class_exists("appmainmenu")) $this->menu = new appmainmenu();
		else $this->menu = new bas_htm_mainmenu();
	}
	
	
	public function add(&$component){
		$this->components[$component->id] = $component;
	}
	
	public function OnPaint(){
		$frm = new bas_htm_form2($this->title, $this->menu, $this->toolbar, 'form2');
		$frm->opendiv('content');
		foreach($this->components as $component) $component->OnPaint($frm);
		$frm->closediv();
		$frm->printme();
	}
	
	public function OnAction($action, $data){
		
		if ($ret = $this->menu->OnAction($action,$data)) return $ret;
		
		if (strpos($action,'#') !== false){
			list($id, $action) = explode('#',$action);
			$this->components[$id]->OnAction($action, $data);
		} else {
			switch ($action){
				
			}
		}
	}
}
?>
