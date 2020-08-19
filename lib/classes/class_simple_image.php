<?php
/*
 * File: SimpleImage.php 
 * Author: Simon Jarvis 
 * Copyright: 2006 Simon Jarvis 
 * Date: 08/11/06 
 * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details: http://www.gnu.org/licenses/gpl.html
 */
class SimpleImage {
	var $image;
	var $image_type;
	/**
	 * @param unknown $filename
	 */
	function load($filename) {
		$image_info = getimagesize ( $filename );
		$this->image_type = ($image_info)?$image_info [2]:0;
		$this->image_info = $image_info;
		
		if ($this->image_type == IMAGETYPE_JPEG) 
		{
			$this->image = imagecreatefromjpeg ( $filename );
		} 
		elseif ($this->image_type == IMAGETYPE_GIF) 
		{
			$this->image = imagecreatefromgif ( $filename );
		} 
		elseif ($this->image_type == IMAGETYPE_PNG) 
		{
			$this->image = imagecreatefrompng ( $filename );
		}
		else {
			$this->image = null;
		}
		return $image_info;
	}
	
	function copy() {
		$new = new SimpleImage();
		$new->image_type = $this->image_type;
		$new->image_info = $this->image_info;
		
		if ($this->image == null)
			$new->image = null;
		else {
			$w = imagesx($this->image);
			$h = imagesy($this->image);
			$new->image = imagecreatetruecolor($w, $h);
			imagecolortransparent($new->image, imagecolorallocatealpha($new->image, 255, 255, 255, 127));
			imagealphablending($new->image, false);
			imagesavealpha($new->image, true);
			imagecopy($new->image, $this->image, 0, 0, 0, 0, $w, $h);
		}
		return $new;
	}
	
	/**
	 * @param unknown $filename
	 * @param string $image_type
	 * @param number $compression
	 * @param string $permissions
	 */
	function save($filename, $image_type = -1, $compression = 75, $permissions = null) {
		if ($image_type == -1) {
			$parts = explode('.',$filename);
			$ext = strtolower($parts[count($parts)-1]);
			if ($ext == 'png')
				$image_type = IMAGETYPE_PNG;
			elseif ($ext == 'gif')
				$image_type = IMAGETYPE_GIF;
			else /*($ext == 'jpeg' or $ext == 'jpg')*/
				$image_type = IMAGETYPE_JPEG;
		}
		
		switch ($image_type) {
			case IMAGETYPE_JPEG:
				imagejpeg ( $this->image, $filename, $compression );
				break;
			case IMAGETYPE_GIF:
				imagegif ( $this->image, $filename );
				break;
			case IMAGETYPE_PNG:
				imagepng ( $this->image, $filename, 9 );
				break;
		}
		if ($permissions != null) {
			chmod ( $filename, $permissions );
		}
	}
	/**
	 * @param string $image_type
	 */
	function output($image_type = IMAGETYPE_JPEG) {
		if ($image_type == IMAGETYPE_JPEG) 
		{
			imagejpeg ( $this->image );
		} 
		elseif ($image_type == IMAGETYPE_GIF) 
		{
			imagegif ( $this->image );
		} 
		elseif ($image_type == IMAGETYPE_PNG) 
		{
			imagepng ( $this->image );
		}
	}
	/**
	 * @return number
	 */
	function getWidth() {
		return imagesx ( $this->image );
	}
	/**
	 * @return number
	 */
	function getHeight() {
		return imagesy ( $this->image );
	}
	/**
	 * @param int $height
	 */
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight ();
		$width = $this->getWidth () * $ratio;
		$this->resize ( $width, $height );
	}
	/**
	 * @param int $width
	 */
	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth ();
		$height = $this->getheight () * $ratio;
		$this->resize ( $width, $height );
	}
	/**
	 * @param int $scale Skalierung in Prozent
	 */
	function scale($scale) {
		$width = $this->getWidth () * $scale / 100;
		$height = $this->getheight () * $scale / 100;
		$this->resize ( $width, $height );
	}
	
	function removeTransparency($r=255, $g=255, $b=255) {
		$w = imagesx($this->image);
		$h = imagesy($this->image);
		$new = imagecreatetruecolor($w, $h);
		$bg = imagecolorallocate($new, $r, $g, $b);
		imagefill($new, 0, 0, $bg);
		imagecopy($new, $this->image, 0, 0, 0, 0, $w, $h);
		$this->image = $new;
	}
	
	function resize($width, $height, $keepRatio=false) {
		$img = $this->image;
		$image_info = $this->image_info;
		
		// wenn Seitenverhältnis behalten werden soll,
		// neue Maße berechnen
		if ($keepRatio) {
			list($w, $h) = $image_info;
			$ratio = min($width/$w, $height/$h);
			$height = (int)($h * $ratio);
			$width = (int)($w * $ratio);
		}
		
		// Neues Bild erstellen
		$new = imagecreatetruecolor($width, $height);
		
		// Ggf. Transparenz aktivieren
		if ($this->image_type == IMAGETYPE_PNG or $this->image_type == IMAGETYPE_GIF) {
			imagecolortransparent($new, imagecolorallocatealpha($new, 255, 255, 255, 127));
			imagealphablending($new, false);
			imagesavealpha($new, true);
		}
		
		 imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $w, $h);
		 
		 $this->image = $new;
		 $image_info[0] = $width;
		 $image_info[1] = $height;
		 $this->image_info = $image_info;
	}
	
	/**
	 * @param unknown $width
	 * @param unknown $height
	 */
	static
	function image_resize($src, $dst, $width, $height, $crop=0, $totype=NULL)
	{
		//debug::print_d($src);
	  if(!list($w, $h) = getimagesize($src)){
		//debug::print_d("Unsupported picture type!");
		return "Unsupported picture type!";
		} 
	
	  $type = strtolower(substr(strrchr($src,"."),1));
	  //debug::print_d("TYPE:".$type);
	 // error_reporting(E_ALL);
	  if($type == 'jpeg') $type = 'jpg';
	  switch($type)
	  {
	    case 'bmp': $img = imagecreatefromwbmp($src); break;
	    case 'gif': $img = imagecreatefromgif($src); break;
	    case 'jpg': $img = imagecreatefromjpeg($src); break;
	    case 'png': $img = imagecreatefrompng($src); break;
	    default : return "Unsupported picture type!";
	  }
	  
	  if ($type == 'png') {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	  }
	//debug::Print_d("resize");
	  // resize
	  if($crop){
	    if($w < $width or $h < $height)
        {
           # debug::Print_d("Picture is too small!");
           # return "Picture is too small!";
            $width=$w;
            $height=$h;
        }
	    $ratio = max($width/$w, $height/$h);
	    $h = $height / $ratio;
	    $x = ($w - $width / $ratio) / 2;
	    $w = $width / $ratio;
	  }
	  else{
	    if($w < $width and $h < $height)
        {
           # debug::Print_d("Picture is too small! 2");
           # return "Picture is too small!";
            $width=$w;
            $height=$h;
        }
	    $ratio = min($width/$w, $height/$h);
	    $width = $w * $ratio;
	    $height = $h * $ratio;
	    $x = 0;
	  }
      //  debug::Print_d("new");
	  $new = imagecreatetruecolor($width, $height);
      //  debug::Print_d("transparency");
	  // preserve transparency
	  if($type == "gif" or $type == "png"){
	    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
	    imagealphablending($new, false);
	    imagesavealpha($new, true);
	  }
	
	  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
		
	  if ($totype)
		$type = $totype;
		
	//debug::print_d("TYPE:".$type);
	  switch($type){
	    case 'bmp': imagewbmp($new, $dst); break;
	    case 'gif': imagegif($new, $dst); break;
	    case 'jpg': imagejpeg($new, $dst); break;
	    case 'png': imagepng($new, $dst); break;
	  }
	  #error_reporting(0);
	  return true;
	}
}