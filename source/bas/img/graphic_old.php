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
class bas_img_graphic{
	private $image;
	private $graphdata = array();
	
	public function __construct($width, $height){
		$this->graphdata['imagewidth'] = $width;
		$this->graphdata['imageheight'] = $height;
		$this->image = imagecreatetruecolor($width, $height); 
	}
	public function __destruct(){ $this->close(); }
	
	public function saveto($filename){
		return imagepng($this->image, $filename, 9);
	}
	
	public function close(){
		if ($this->image){ imagedestroy($this->image); $this->image = false;} 
	}
	
	public function generate($gdef){
		# Colors
		$defcol = array('BACKGROUND' => 0xFFFFFF
			, 'AXES' => 0x000000
			, 'PEN' => 0x000000
			);
		$color = array();
		foreach($defcol as $name => $defvalue){
			if (($value = $gdef->getcolor($name)) === false ) $value = $defvalue; 
			$color[$name] = imagecolorallocate($this->image, ($value & 0xFF0000)>>16, ($value & 0xFF00)>>8, $value & 0xFF);
		}
		
		$this->graphdata['padding'] = 5;
		
		$this->graphdata['titlefont'] = 5;
		$this->graphdata['leyendfont'] = 3;
		$this->graphdata['indexfont'] = 2;
		$this->graphdata['valuefont'] = 2;
		
		$this->graphdata['marginleft'] = $gdef->getmargin('left');
		$this->graphdata['marginright'] = $gdef->getmargin('right');
		$this->graphdata['margintop'] = $gdef->getmargin('top');
		$this->graphdata['marginbottom'] = $gdef->getmargin('bottom');

		$this->graphdata['barcount'] = 0;
		$firstvalue = 1;
		$this->graphdata['seriecount'] = count($gdef->serie);
		$maxtitlelen = 0;
		foreach($gdef->serie as $serie) {
			if (($serie['type'] == BIGT_BAR) || ($serie['type'] == BIGT_BAR_IMG)) $this->graphdata['barcount']++;
			if (strlen($serie['title']) > $maxtitlelen) $maxtitlelen = strlen($serie['title']);
			foreach($serie['data'] as $value){
				if ($firstvalue){
					$firstvalue = 0; 
					$this->graphdata['maxvalue'] = $this->graphdata['minvalue'] = $value;
				} else {
					if ($value > $this->graphdata['maxvalue']) $this->graphdata['maxvalue'] = $value;
					if ($value < $this->graphdata['minvalue']) $this->graphdata['minvalue'] = $value;
				}
			}
			$seriecolor = $gdef->getcolor($serie['id']); 
			$color[$serie['color']] = imagecolorallocate($this->image, ($seriecolor & 0xFF0000)>>16, ($seriecolor & 0xFF00)>>8, $seriecolor & 0xFF);
		}
		if ($this->graphdata['minvalue'] > 0){
			$this->graphdata['minpositivevalue'] = $this->graphdata['minvalue'];
			$this->graphdata['minvalue'] = 0; 
		} 
		
		$this->graphdata['titlewidth'] = imagefontwidth($this->graphdata['titlefont']) * strlen($gdef->title);
		$this->graphdata['titleheight'] = imagefontheight($this->graphdata['titlefont']);
		$this->graphdata['leyendwidth'] = imagefontwidth($this->graphdata['leyendfont']) * $maxtitlelen + 2*$this->graphdata['padding'];
		$this->graphdata['leyendlineheight'] = imagefontheight($this->graphdata['leyendfont']);
		$this->graphdata['leyendheight'] = $this->graphdata['leyendlineheight'] * $this->graphdata['seriecount'];
		$this->graphdata['indexheight'] = imagefontheight($this->graphdata['indexfont']);
		$this->graphdata['valuewidth'] = 8*imagefontwidth($this->graphdata['valuefont']);
		$this->graphdata['valueheight'] = imagefontheight($this->graphdata['valuefont']);
		
		if (($margin=$this->graphdata['titleheight'] + $this->graphdata['padding']) > $this->graphdata['margintop']) $this->graphdata['margintop'] = $margin;
		if (($margin=$this->graphdata['valuewidth'] + $this->graphdata['padding']) > $this->graphdata['marginleft']) $this->graphdata['marginleft'] = $margin;
		if (($margin=$this->graphdata['leyendwidth'] + $this->graphdata['padding']) > $this->graphdata['marginright']) $this->graphdata['marginright'] = $margin;
		if (($margin=$this->graphdata['indexheight'] + $this->graphdata['padding']) > $this->graphdata['marginbottom']) $this->graphdata['marginbottom'] = $margin;
		
		$this->graphdata['originx'] = $this->graphdata['marginleft'];
		$this->graphdata['originy'] = $this->graphdata['imageheight'] - $this->graphdata['marginbottom'];
		$this->graphdata['indexaxiswidth'] = $this->graphdata['imagewidth'] - $this->graphdata['marginright'] 
			- 2*$this->graphdata['padding'] - $this->graphdata['originx'];
		$this->graphdata['indexwidth'] = $this->graphdata['indexaxiswidth'] / count($gdef->index);
		$this->graphdata['barwidth'] = $this->graphdata['barcount'] != 0 
			? ($this->graphdata['indexwidth'] - 2 * $this->graphdata['padding']) / $this->graphdata['barcount']
			: 1;
		if ($this->graphdata['barwidth'] > 30) $this->graphdata['barwidth'] = 30;
		$this->graphdata['baroffset'] = $this->graphdata['indexwidth'] / 2 - $this->graphdata['barcount'] * $this->graphdata['barwidth'] / 2;
		$this->graphdata['minpos'] = $this->graphdata['originy'] - $this->graphdata['padding'];
		$this->graphdata['maxpos'] = $this->graphdata['margintop'] + $this->graphdata['padding'];
		
		# Background
		imagefilledrectangle($this->image, 0,0, $this->graphdata['imagewidth'],$this->graphdata['imageheight']
			, $color['BACKGROUND']);
			
		# Axes
		$xaxiswidth = $this->graphdata['imagewidth'] - $this->graphdata['marginright']; 
		imageline($this->image, $this->graphdata['originx'], $this->graphdata['originy']
			,$xaxiswidth , $this->graphdata['originy']
			, $color['AXES']);
		imageline($this->image, $this->graphdata['originx'], $this->graphdata['originy']
			, $this->graphdata['originx'], $this->graphdata['margintop']
			, $color['AXES']);
			
			
		# Leyend
		$x = $this->graphdata['imagewidth'] - $this->graphdata['leyendwidth']; 
		$y = $this->graphdata['imageheight'] /2 - $this->graphdata['leyendheight']/2;
		$pad = $this->graphdata['padding']/2;
		$yoff = $this->graphdata['leyendlineheight'] / 2;
		foreach($gdef->serie as $serie){
			imagefilledrectangle($this->image, $x - $pad, $y - $pad + $yoff, $x + $pad, $y + $pad + $yoff, $color[$serie['color']]);
			imagerectangle($this->image, $x - $pad, $y - $pad + $yoff, $x + $pad, $y + $pad + $yoff, $color['PEN']);
			imagestring($this->image, $this->graphdata['leyendfont'], $x + $this->graphdata['padding']*2, $y
				, iconv('UTF-8','Latin2',$serie['title']), $color['PEN'] );
			$y += $this->graphdata['leyendlineheight'];
		}
		
		# Indexes
		$fw = imagefontwidth($this->graphdata['indexfont']);
		$x = $this->graphdata['originx'] + $this->graphdata['padding'] + $this->graphdata['indexwidth'] / 2;
		$y = $this->graphdata['imageheight'] - $this->graphdata['indexheight'];
		foreach($gdef->index as $index){
			imagestring($this->image, $this->graphdata['indexfont'], $x -(strlen($index)*$fw)/2, $y
				, iconv('UTF-8','Latin2',$index), $color['PEN'] );
			$x += $this->graphdata['indexwidth'];
		}
		
		# Values
		$fw = imagefontwidth($this->graphdata['valuefont']);
		$x = $this->graphdata['originx'] - $this->graphdata['padding']; 
		$text = intval($this->graphdata['minvalue']); $y = $this->map($text);
		imagestring($this->image, $this->graphdata['valuefont'], $x - strlen($text) * $fw, $y - $this->graphdata['valueheight']/2, iconv('UTF-8','Latin2',$text), $color['PEN'] );
		
		$text = intval($this->graphdata['maxvalue']); $y = $this->map($text);
		imagestring($this->image, $this->graphdata['valuefont'], $x - strlen($text) * $fw, $y - $this->graphdata['valueheight']/2, iconv('UTF-8','Latin2',$text), $color['PEN'] );
		
		if ($this->graphdata['minvalue'] < 0){
			$text = '0'; $y = $this->map($text);
			imagestring($this->image, $this->graphdata['valuefont'], $x - strlen($text) * $fw, $y - $this->graphdata['valueheight']/2, iconv('UTF-8','Latin2',$text), $color['PEN'] );
		} else {
			$text = intval($this->graphdata['minpositivevalue']); $y = $this->map($text);
			imagestring($this->image, $this->graphdata['valuefont'], $x - strlen($text) * $fw, $y - $this->graphdata['valueheight']/2, iconv('UTF-8','Latin2',$text), $color['PEN'] );
		}
			# Horzlines	
		foreach ($gdef->horzline as $value){
			if ($value > $this->graphdata['minvalue'] && $value < $this->graphdata['maxvalue']){
				$y = $this->map($value);
				imagestring($this->image, $this->graphdata['valuefont'], $x - strlen($value) * $fw, $y - $this->graphdata['valueheight']/2, iconv('UTF-8','Latin2',$value), $color['PEN'] );
				imageline($this->image, $this->graphdata['originx'], $y, $xaxiswidth, $y, $color['PEN']);
			}
		}
			
		# Series
		$baroffx = $this->graphdata['baroffset'];
		foreach($gdef->serie as $serie){
			switch ($serie['type']){
			case BIGT_BAR:
				$indexx = $this->graphdata['originx'] + $this->graphdata['padding'] + $baroffx;
				foreach(array_keys($gdef->index) as $index){
					if (isset($serie['data'][$index])){
						imagefilledrectangle($this->image, $indexx, $this->map(0), $indexx + $this->graphdata['barwidth'], $this->map($serie['data'][$index]), $color[$serie['color']]);
					}
					$indexx += $this->graphdata['indexwidth'];
				}
				$baroffx += $this->graphdata['barwidth'];
				break;

			case BIGT_BAR_IMG:
				$indexx = $this->graphdata['originx'] + $this->graphdata['padding'] + $baroffx;
				unset($images);	$images = array();
				foreach($serie['image'] as $index => $imagefile) {
					switch ( file_exists($imagefile) 
							? strtolower(pathinfo($imagefile,PATHINFO_EXTENSION))
							: 'none' ) {
					case 'png':
							$images[$index] = imagecreatefrompng($imagefile);
							break;
							
					case 'jpeg': case 'jpg':
							$images[$index] = imagecreatefromjpeg($imagefile);
							break;
							
					case 'gif':
							$images[$index] = imagecreatefromgif($imagefile);
							break;
							
					default:
							$images[$index] = imagecreatetruecolor(imagefontwidth(5),imagefontheight(5));
							imagefilledrectangle($images[$index], 0, 0, imagefontwidth(5), imagefontheight(5), imagecolorallocate($images[$index], 255, 255, 255));
							imagestring($images[$index],5,0,0,'*',imagecolorallocate($images[$index], 0, 0, 0));
					}
				}
				
				foreach(array_keys($gdef->index) as $index){
					if (isset($serie['data'][$index])){ 
						imagefilledrectangle($this->image, $indexx, $this->map(0), $indexx + $this->graphdata['barwidth'], $this->map($serie['data'][$index]), $color[$serie['color']]);
						if (isset($serie['imagedata'][$index]) && isset($images[$serie['imagedata'][$index]])){
							$y = $this->map($serie['data'][$index]) - ($this->map($serie['data'][$index]) - $this->map(0))/2 - imagesy($images[$serie['imagedata'][$index]])/2;
							imagecopyresampled($this->image, $images[$serie['imagedata'][$index]]
								, $indexx, $y
								, 0, 0
								, $this->graphdata['barwidth'], $this->graphdata['barwidth']
								, imagesx($images[$serie['imagedata'][$index]]), imagesy($images[$serie['imagedata'][$index]]));
						}
					}

					$indexx += $this->graphdata['indexwidth'];
				}
				
				foreach($images as $image) imagedestroy($image);
				
				$baroffx += $this->graphdata['barwidth'];
				break;
				
				
				
			case BIGT_LINE:
				$indexx = $this->graphdata['originx'] + $this->graphdata['padding'] + $this->graphdata['indexwidth'] / 2;
				$px = $py = 0;
				foreach(array_keys($gdef->index) as $index){
					$x = $indexx; $y = $this->map(isset($serie['data'][$index])?$serie['data'][$index]:0);
					$pad = $this->graphdata['padding']/2;
					
					if ($px) {
						imagesetthickness($this->image,2);
						imageline($this->image, $px, $py, $x, $y, $color[$serie['color']]);
						imagesetthickness($this->image,1);
					}
					imagefilledrectangle($this->image, $x - $pad, $y - $pad, $x + $pad, $y + $pad, $color[$serie['color']]);
					imagerectangle($this->image, $x - $pad, $y - $pad, $x + $pad, $y + $pad, $color['PEN']);
					
					$px= $x; $py = $y;
					$indexx += $this->graphdata['indexwidth'];
				}
				break;
				
			case BIGT_LINE_IMG:
				$indexx = $this->graphdata['originx'] + $this->graphdata['padding'] + $this->graphdata['indexwidth'] / 2;
				# carga imagen
				unset($images);	$images = array();
				foreach($serie['image'] as $index => $imagefile) {
					switch ( strtolower(pathinfo($imagefile,PATHINFO_EXTENSION)) ) {
					case 'png':
							$images[$index] = imagecreatefrompng($imagefile);
							break;
							
					case 'jpeg': case 'jpg':
							$images[$index] = imagecreatefromjpeg($imagefile);
							break;
							
					case 'gif':
							$images[$index] = imagecreatefromgif($imagefile);
							break;
							
					default:
							$images[$index] = imagecreatetruecolor(imagefontwidth(5),imagefontheight(5));
							imagefilledrectangle($images[$index], 0, 0, imagefontwidth(5), imagefontheight(5), imagecolorallocate($images[$index], 255, 255, 255));
							imagestring($images[$index],5,0,0,'*',imagecolorallocate($images[$index], 0, 0, 0));
					}
				}
				#
				
				$px = $py = 0;
				foreach(array_keys($gdef->index) as $index){
					$x = $indexx;
					$y = $this->map(isset($serie['data'][$index])?$serie['data'][$index]:0);
					$pad = $this->graphdata['padding']/2;
					
					if ($px) {
						imagesetthickness($this->image,2);
						imageline($this->image, $px, $py, $x, $y, $color[$serie['color']]);
						imagesetthickness($this->image,1);
					}
					imagefilledrectangle($this->image, $x - $pad, $y - $pad, $x + $pad, $y + $pad, $color[$serie['color']]);
					imagerectangle($this->image, $x - $pad, $y - $pad, $x + $pad, $y + $pad, $color['PEN']);
					#pinta imagen
					if (isset($serie['imagedata'][$index]) && isset($images[$serie['imagedata'][$index]])){
						imagecopyresampled($this->image, $images[$serie['imagedata'][$index]]
							, $indexx - imagesx($images[$serie['imagedata'][$index]])/2, $y - imagesy($images[$serie['imagedata'][$index]])/2
							, 0, 0
							, imagesx($images[$serie['imagedata'][$index]]), imagesy($images[$serie['imagedata'][$index]])
							, imagesx($images[$serie['imagedata'][$index]]), imagesy($images[$serie['imagedata'][$index]]));
					}
					#
					$px= $x; $py = $y;
					$indexx += $this->graphdata['indexwidth'];
				}
				
				#Destruye las imagenes.
				foreach($images as $image) imagedestroy($image);
				
				break;
				
			}
			
		# Title
		imagestring($this->image, $this->graphdata['titlefont'], $this->graphdata['imagewidth']/2 - $this->graphdata['titlewidth']/2, 0
			, iconv('UTF-8','Latin2',$gdef->title), $color['PEN'] );
			
		}
						
	}
	
	private function map($value){
		return ($value - $this->graphdata['minvalue']) 
			* ($this->graphdata['maxpos'] - $this->graphdata['minpos']) 
			/ ($this->graphdata['maxvalue'] - $this->graphdata['minvalue']) 
			+ $this->graphdata['minpos'];
		
	}
}



?>
