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
class cls_codetemplate{
	private $ncharcursor;
	private $template;
	private $values;
	private $commander;
	
	public function stamp(&$template, &$values, &$commander){
		# template - string with code to stamp.
		# values - asoc array of identier => value.
		# commander - object to execute the commands in the template.

		$stamp = '';
		$pcharcursor = 0;
		$this->template =& $template;
		$this->values =& $values;
		$this->commander =& $commander;
		
		while (($ncmdcursor = strpos($this->template, '<!', $pcharcursor)) !== false){
			$stamp .= $this->substvalues(substr($this->template, $pcharcursor, $ncmdcursor - $pcharcursor), $values);
			$this->ncharcursor = $pcharcursor+2;
			$pcharcursor = $this->ncharcursor;
			
			$stamp .= $this->parsecommand();
			$pcharcursor = $this->ncharcursor;
		}
		$stamp .= $this->substvalues(substr($this->template, $pcharcursor), $values);
		
		return $stamp;
	}
	
	private function substvalues($string, $values){
		extract($values, EXTR_PREFIX_ALL,'');
		eval ('$code = "'. $string . '";');
		return $code;
		
	}
	
	private function parsecommand($commander){
		$this->ncharcursor = strpos($this->template, '>', $this->ncharcursor) + 1;
		return '';
	}
	
	private function lex(){
		
	}
}
