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
class bas_htm_template{
	
	public function savingrecode(&$tbdef, &$rec){
		# When retrieve data form internet, need reconding some data.
		# Sólo devuelve las columnas cambiadas.
		$rt = array();
		foreach ($tbdef->getcols() as $col){
			$rt[$col['id']] = $rec[$col['id']];
			
			switch($col['template']){
/*				
				case 'option':
					if (isset($col['template_properties']['query'])){
						$values = array();
						$ds = new bas_sql_myqrydataset($col['template_properties']['query']);						
						$r = $ds->reset();
						while ($r) {
							$values[$r['id']] = $r['value']; 
							$r = $ds->next();
						}	
						$ds->close();
						
					} elseif (isset($col['template_properties']['optionstring'])) {
						$values = explode(',',$col['template_properties']['optionstring']);
						
					} elseif (isset($col['template_properties']['values'])) {
						$values = $col['template_properties']['values'];
						
					}
						
					if (isset($values[$rec[$col['id']]])) $rt[$col['id']] = $values[$rec[$col['id']]];
					
					break;
*/						
				case 'checkbox':
					$rt[$col['id']] = isset($rec[$col['id']]);
					break; 	
					
			}
		}
		return $rt;
	}
	
	public function loadingrecode(){
		
	}
	
	
	public function stamp(&$tbdef, $colid, &$rec){
		global $_LOG;
		
		$properties = $tbdef->getcol($colid); //cols + template + template properties
		
		// Nombre del control.
		if (!isset($properties['name'])){
			if (isset($properties['dynamic_name'])) {
				eval ("\$name = \"{$properties['dynamic_name']}\";");
				$properties['name'] = $name;
			} else $properties['name'] =  $properties['id'];
		}
		// Valor	
		if (!isset($properties['value'])){
			if (isset($properties['dynamic_value'])) {
				eval ("\$value = \"{$properties['dynamic_value']}\";");	
				$properties['value'] = $value;
			} elseif (isset($rec[$properties['id']])) {
				$properties['value'] = $rec[$properties['id']];
			} elseif (isset($rec[$properties['name']])) {
				$properties['value'] = $rec[$properties['name']];
			} else $properties['value'] = '';
		}
		if (isset($properties['dynamic_enumstring'])) {
			eval ("\$properties['enumstring'] = \"{$properties['dynamic_enumstring']}\";");
		}	
		if (isset($properties['enumstring'])){
			$enum = explode(',', $properties['enumstring']);
			if (isset($enum[$properties['value']])) $properties['value'] = $enum[$properties['value']]; 
		}
		if (isset($properties['format'])){
			$format = new bas_dat_format($properties['format']);
			$properties['formated_value'] = $format->format($properties['value']);
		} elseif (isset($properties['dynamic_format'])){
			eval ("\$format = new bas_dat_format(\"{$properties['dynamic_format']}\");");	
			$properties['formated_value'] = $format->format($properties['value']);			
		} else $properties['formated_value'] = $properties['value'];

		
		
		// Caption and title.
		if (isset($properties['link_text']) && isset($rec[$properties['link_text']])) {
			$properties['link_text'] = $rec[$properties['link_text']];
		}
		if (isset($properties['link_title']) && isset($rec[$properties['link_title']])) {
			$properties['link_title'] = $rec[$properties['link_title']];
		}
		
		// Key Select
		if (isset($properties['template_properties']['keyselect'])){
			$properties['template_properties']['checked'] = method_exists($tbdef, 'iskeyselected') ? $tbdef->iskeyselected($rec) : false;
		}
		
		// Template type
		if ($properties['template'] == 'dynamic'){
			eval ("\$type = \"{$properties['dynamic_template']}\";");
			foreach($properties['template_properties'] as $dynkey => $dynvalue){
				if (is_object($dynvalue) || is_array($dynvalue)){
					$properties['template_properties'][$dynkey] = $dynvalue;
				} else eval ("\$properties['template_properties']['$dynkey'] = \"$dynvalue\";");
			}	
		} else $type = $properties['template'];
		 
		switch($type){
			case 'input': 
				$type = 'text';
			case 'password': case 'hidden':
				return $this->stamp_input($properties, $type);
			
			case 'option': 		return $this->stamp_option($properties);
			case 'button': 		return $this->stamp_button($properties);
			case 'checkbox': 	return $this->stamp_checkbox($properties);
			case 'radio': 		return $this->stamp_radio($properties);
			case 'upload':		return $this->stamp_upload($properties);
			case 'textarea':	return $this->stamp_textarea($properties);
			case 'image':		return $this->stamp_image($properties);
			case 'imagemap':	return $this->stamp_imagemap($properties);
			case 'anchor':		return $this->stamp_anchor($properties);
			case 'richtext':	return $this->stamp_richtext($properties);
			case 'none':		return $properties['formated_value'];
			default: 			$_LOG->log("ERROR: No se encuentra template de tipo $type. STAMP por defecto.");
								return $properties['formated_value'];
		}
	}

	public function stamp_input(&$properties, $type='text'){
		$maxlength = isset($properties['length']) ? " maxlength=\"{$properties['length']}\"" : '';
		$size = isset($properties['width']) ? " size=\"{$properties['width']}\"" : '';
		if (isset($properties['lookup'])) {
			$lookupid = ($properties['lookup'] === true) ? $properties['name'] : $properties['lookup'];
			$lookupbutton = " &nbsp;&nbsp;&nbsp; <input type=\"button\" value=\"$lookupid\""
				. " onclick=\"javascript:submitlookup('$lookupid');\">";
		} else $lookupbutton = '';
		if (isset($properties['spinbutton'])) {
			$spinid = ($properties['spinbutton'] === true) ? $properties['name'] : $properties['spinbutton'];
			$spinbutton = " &nbsp; <input type=\"button\" value=\"<<\" onclick=\"javascript:addhidden('spinid','$spinid');submitaction('spindown');\">"
				. "<input type=\"button\" value=\">>\" onclick=\"javascript:addhidden('spinid','$spinid');submitaction('spinup');\">";
		} else $spinbutton = '';
		$hiddentext = $type == 'hidden' ? $properties['formated_value']: '';
		//return "<input name=\"{$properties['name']}\" type=\"$type\"$maxlength$size value=\"{$properties['value']}\">$hiddentext$spinbutton$lookupbutton";
		return "<input name=\"{$properties['name']}\" type=\"$type\"$maxlength$size value=\"{$properties['formated_value']}\">$hiddentext$spinbutton$lookupbutton";
	}

	public function stamp_textarea(&$properties){
		return "<textarea name=\"{$properties['name']}\" rows=\"{$properties['template_properties']['rows']}\" cols=\"{$properties['template_properties']['cols']}\">"
			. $properties['value']
			. "</textarea>"
			;
	}
	
	private function createdataset($values){
		$recs = array();
		foreach ($values as $key => $value)	$recs[] = array('id'=>$key, 'value'=>$value);
		return new bas_dat_arraydataset($recs);		
	}
	
	public function stamp_option(&$properties){
		
		$ret = "<select name=\"{$properties['name']}\">";
		if (isset($properties['template_properties']['query'])){
			$dataset = new bas_sql_myqrydataset($properties['template_properties']['query']);
		} elseif (isset($properties['template_properties']['optionstring'])) {
			$dataset = $this->createdataset(explode(',',$properties['template_properties']['optionstring'])); 
		} elseif (isset($properties['template_properties']['notindexedoptstring'])) {
			$values = array();
			foreach(explode(',',$properties['template_properties']['notindexedoptstring']) as $v) $values[$v]=$v;
			$dataset = $this->createdataset($values); 
		} elseif (isset($properties['template_properties']['values'])) {
			$dataset = $this->createdataset($properties['template_properties']['values']);
		} else $dataset = false;
		
		if ($dataset){
			$rec = $dataset->reset();
			while ($rec){
				$id = isset($properties['template_properties']['notindexed']) ? $rec['value'] : $rec['id'];
				$selected = $properties['value'] == $id ? " selected" : '';
				$ret .= "<option value=\"$id\"$selected>{$rec['value']}</option>";
				$rec = $dataset->next();
			}
		}
		$ret .= "</select>"; 
		return $ret;
	}
	
	public function stamp_checkbox(&$properties){
		$checked = $properties['value'] ? ' checked="checked"' : '';
		return "<input name=\"{$properties['name']}\" type=\"checkbox\"$checked>";
	}

	public function stamp_radio(&$properties){
		$checked = isset($properties['template_properties']['checked']) && $properties['template_properties']['checked'] ? ' checked="checked"' : '';
		return "<input name=\"{$properties['name']}\" type=\"radio\" value=\"{$properties['value']}\"$checked>";
	}
	
	
	public function stamp_button(&$properties){
		$code = $properties['value'] ? $properties['value'] : "addhidden('action','{$properties['name']}'); submit();";			
		return "<input type=\"button\" value=\"{$properties['name']}\" onclick=\"javascript:$code\">";
	}

	public function stamp_image(&$properties){
		global $_LOG;
		
		if ($properties['value']){
			$imgdir = isset($properties['template_properties']['imagedir']) ? $properties['template_properties']['imagedir'] : 'images/'; 
			$height = isset($properties['template_properties']['height']) ? " height=\"{$properties['template_properties']['height']}\"" : '';
			$width = isset($properties['template_properties']['width']) ? " width=\"{$properties['template_properties']['width']}\"" : '';
			$ret = '';
			foreach(explode(',', $properties['value']) as $image) {
				if ($image)	{
					if (isset($properties['template_properties']['imagearray'])){
						if (isset($properties['template_properties']['imagearray'][$image])) {
							$image = $properties['template_properties']['imagearray'][$image];  
						} else {
							$_LOG->log("No se encuentra imagen para $image en el array.");
						}
					} elseif (isset($properties['template_properties']['imagepattern'])){
						$image = str_replace('$', $image, $properties['template_properties']['imagepattern']);
					}
					$ret .= ($ret ? ',' : '') . "<img id=\"{$properties['name']}\" src=\"$imgdir$image\" alt=\"$image\"$height$width>";
				}
			}
		} else $ret = "&nbsp;";
		
		if (isset($properties['lookup'])) {
			$lookupid = ($properties['lookup'] === true) ? $properties['name'] : $properties['lookup'];
			$lookupbutton = " &nbsp;&nbsp;&nbsp; <input type=\"button\" value=\"$lookupid\""
				. " onclick=\"javascript:submitlookup('$lookupid');\">";
		} else $lookupbutton = '';
		
		return "$ret$lookupbutton";
		
	}
	
	public function stamp_imagemap(&$properties){
		$ret = substr($this->stamp_image($properties),0,-1) . " usemap=\"#{$properties['name']}_map\">\n";
		
		$ret .= "<map name=\"{$properties['name']}_map\">\n";
		
		if (isset($properties['template_properties']['query'])){
			$dataset = new bas_sql_myqrydataset($properties['template_properties']['query']);
		} elseif (isset($properties['template_properties']['values'])) {
			$dataset = new dat_arraydataset($properties['template_properties']['values']);
		} else $dataset = false;

		if ($dataset){
			if ($rec = $dataset->reset()) do {
				if (isset($properties['template_properties']['action'])){ 
					$code = " onclick=\"javascript:addhidden('mapselected','{$rec['id']}');"
						. " addhidden('action','{$properties['template_properties']['action']}'); submit();\"";
				} elseif (isset($rec['action'])) {
					$code = " onclick=\"javascript:addhidden('action','{$rec['action']}'); submit();\"";
				} else $code = '';
				$href = isset($rec['href']) ? " href=\"{$rec['href']}\"" : ''; 
				$alt = isset($rec['alt']) ? " alt=\"{$rec['alt']}\"" : ''; 
				$ret .= "<area shape=\"rect\" coords=\"{$rec['left']},{$rec['top']},{$rec['right']},{$rec['bottom']}\"$href$alt$code>";
			} while($rec = $dataset->next());
		}
		
		$ret .= "</map>"; 
		return $ret;		
	}
	
	public function stamp_upload(&$properties){
		//$maxsizecheck = '<input type="hidden" name="MAX_FILE_SIZE" value="500">'; -- maximo upload por formulario si sobrepasa da $_FILES[]['error'] == 2
		$maxformsizecheck = '';		
		return  "$maxformsizecheck<input name=\"{$properties['name']}\" type=\"file\">";
	}
	
	public function stamp_anchor(&$properties){
		global $ICONFIG;
		global $_LOG;
/*		
		$href = $properties['value'];
		$folderseparator = strpos($href,':');
		if ($folderseparator !== false){
			$folderid = substr($href,0,$folderseparator);
			if (substr($folderid,0,4) == 'HOST') $folderid = 'HTTP'.substr($folderid, 4);
			$href = substr($href, $folderseparator+1);
			$defaulttext = $href;
			$href = $ICONFIG[$folderid] . $defaulttext;
		} else $defaulttext = $href;
*/
		$text = $properties['value'];
		$upload = new bas_dat_upload();
		$href = $upload->getwebfilename($text);
		if (isset($properties['link_text'])) $text = $properties['link_text'];
		
		$href = isset($properties['template_properties']['nolink']) ? '' : " href=\"$href\"";
		$title = isset($properties['link_title']) ? " title=\"{$properties['link_title']}\"" : '';
		if (isset($properties['template_properties']['target'])){
			$target = $properties['template_properties']['target'] != 'notarget'  
					? " target=\"{$properties['template_properties']['target']}\"" 
					: ''; 	
		} else $target = " target=\"_blank\"";
		
/*		else $text = $defaulttext; */
		return "<a$href$title$target>$text</a>";
	}
	

/* Parsed text process some rules:
 *
 * LINK[ID:some text] -> <a href="link al upload con id ID">some text</a>
 * LINK[ID]           -> <a href="link al upload con id ID">observaciones el upload con id ID</a>
 */
	public function stamp_richtext(&$properties){
		global $_LOG;
		$result = '';
		$upload = new bas_dat_upload();
		$matches = array();
		$texto = $properties['value'];
		
		$i=0; $bi=0;
		$offset = 0;
		while (($i<100) && ($bi<100) && (preg_match('%LINK\[([^\]:]+)\]%',$texto,$matches,PREG_OFFSET_CAPTURE,$offset)>0)) {
			if ($uploadinfo = $upload->getuploadinfo($matches[1][0])){
				$i++;
				//$link = "<a href=\"{$matches[1][0]}\">UNDEFINED TEXT<a>";
				$link = "<a href=\"{$uploadinfo['webfilename']}\">{$uploadinfo['observaciones']}<a>";
				$result .=  $link; 	
				$texto = substr($texto, 0, $matches[0][1]) . $link . substr($texto, $matches[0][1] + strlen($matches[0][0]));
				$offset = $matches[0][1] + strlen($link);
			} else {
				$bi++;
				$offset = $matches[0][1] + strlen($matches[0][1]);
			}
		}
		if ($i==0) $result = $texto;
		
		$i=0; $bi=0;
		$offset = 0;
		while (($i<100) && ($bi<100) && (preg_match('%LINK\[([^:]+):([^\]]+)\]%',$texto,$matches,PREG_OFFSET_CAPTURE,$offset)>0)) {
			if ($uploadinfo = $upload->getuploadinfo($matches[1][0])){
				$i++;
				//$link = "<a href=\"{$matches[1][0]}\">{$matches[2][0]}<a>";
				$link = "<a href=\"{$uploadinfo['webfilename']}\">{$matches[2][0]}<a>";
				$result .= $link;
				$texto = substr($texto, 0, $matches[0][1]) . $link . substr($texto, $matches[0][1] + strlen($matches[0][0]));
				$offset = $matches[0][1] + strlen($link); 
			} else {
				$bi++;
				$offset = $matches[0][1] + strlen($matches[0][1]);
			}
		}
		if ($i==0) $result = $texto;
		//return "rich-text";
		return $texto;
	}

}
?>
