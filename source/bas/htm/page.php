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
/**
 * 
 * @package html
 */
class bas_htm_page extends bas_htm_elements {
	protected $style;
	protected $script;
	protected $title;
	public $bodyevents=array();
	
	public function __construct($title, $style='', $script=''){
		global $ICONFIG;
		global $CONFIG;
		
		parent::__construct();
		if (isset($ICONFIG['ONPDA'])){
			$this->style = $style ? $style : 'pda';
			$this->script = $script ? $script : $CONFIG['BROWSER'] == 'IE' ? 'pda_ie' : 'pda';
			
		} else {
			$this->style = $style ? $style : 'normal';
			$this->script = $script ? $script : 'base';
		}
		$this->title = $title;
	}
	
	public function addevent($event, $code){$this->bodyevents[$event] = $code;}
	
	public function printme(){
		header("Content-Type: text/html; charset=utf-8");
		echo "<html>\n";
		echo "<head>\n";
		$this->printhead();
		echo "</head>\n";
		
		$events = '';
		foreach($this->bodyevents as $event => $code) $events .= " $event = \"$code\"";
		echo "<body$events>\n";
		parent::printme();
		echo "</body>\n";
		echo "</html>\n";
	}
	
	protected function printhead(){
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">\n";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"style/{$this->style}.css\">\n";
		echo "<script type=\"text/javascript\" src=\"script/{$this->script}.js\"></script>\n";
		echo "<title>{$this->title}</title>\n";
	}

	public function setstyle($style){
		$this->style = $style;
	}

	public function setscript($script){
		$this->script = $script;
	}

}
?>
