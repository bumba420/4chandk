<?php

  /******************************************************************

   Projectname:   CAPTCHA 2
   Version:	  0.9	
   Author:        Cristian Navalici cristian.navalici at gmail dot com
   Last modified: 22-feb-2007
   Copyright (C): 2007 Cristian Navalici, All Rights Reserved

   This class is released under a BSD Licence for use in
   4chandk software (http://code.google.com/p/4chandk/)

   Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in 
    the documentation and/or other materials provided with the distribution.
    * Neither the name of the <ORGANIZATION> nor the names of its contributors may be used to endorse or promote products 
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, 
INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

  ******************************************************************/

class Captcha {

// here you can make some adjustments
private $font_size 	= 24; // if GD2 in points / GD1 in pixels
private $font 		= 'gibberish.ttf';
private $img_height 	= 70;
private $save_path	= 'gdtest'; // without trailing slash
private $secret_key	= 'Insert a complicated string here';
// until here

private $gd_enabled = true;
private $allow_jpg_output = false;
private $allow_png_output = false;
private $length;

//======================================================================
//	CONSTRUCTOR
//======================================================================
function __construct ($length = 6, $type = 'png', $letter = '') {
	
	// Set some $this->font, $this->save_path and $this->secret_key dynamically
	$this->font			=	 Config::get('font_folder');
	$this->save_path	=	 Config::get('captcha_folder');
	$this->secret_key	=	 Config::get('salt');

	$check_gd = gd_info();
	if (!$check_gd['GD Version']) {
		$this->gd_enabled = false;
	}
	
	// check for JPG capability
	if ($check_gd['JPG Support']) {
		$this->allow_jpg_output = true;
	}
	
	// check for PNG capability
	if ($check_gd['PNG Support']) {
		$this->allow_png_output = true;
	}
	
	if(!session_id()){
		session_start();
	} else {
		session_regenerate_id();
	}
}

//======================================================================
// MAIN FUNCTION: createCaptcha
// create a captcha image based on supplied arguments
// if GD is not enabled it switch to TEXT MODE
//
// @arg:	$length (int) - length of generated string
//		$type (string) - type of generated picture (jpg or png)
//		$draw_lines (bool) - optional lines on picture
// @return:	none (picture saved)
//======================================================================
public function createCaptcha($length = 6, $type = 'png', $draw_lines = 'true') {

if ($this->gd_enabled) {
	$img_length = $length * ($this->font_size+5);
	$image       = imagecreatetruecolor($img_length, $this->img_height) or die("Cannot Initialize new GD image stream");;

	//  ----- TRANSFORMATIONS PART -----------------------------------
	// set background
	$bgcolor     = imagecolorallocate($image, 255, 255, 255);
	imagefill($image,0,0,$bgcolor);
	
	$random_pixels = $img_length * $this->img_height / 2;

        for ($i = 0; $i < $random_pixels; $i++) {
		$color_pixel  = ImageColorAllocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
		ImageSetPixel($image, rand(0, $img_length), rand(0, $this->img_height), $color_pixel);
        }
	
	// smooth image
        imagefilter($image, IMG_FILTER_SMOOTH, 10);
	
	// add text
	$gens = $this->stringGenerator($length);
	for ($i = 0 ; $i < $length ; $i++) {
		$stringcolor = imagecolorallocate($image, mt_rand(0, 180), mt_rand(0, 100), mt_rand(0, 180));
		imagettftext($image, $this->font_size, mt_rand(-40,40), 10 + (($this->font_size + 4) * $i), mt_rand(40, 60),
                   $stringcolor,
                   $this->font,
                   $gens{$i});
	}

	// default draw lines
	if ((bool)$draw_lines) {
		$img = $this->drawLines ($image, $img_length);
	} else {
		$img = $image;
	}
	
	//  ----- EOS TRANSFORMATIONS PART -----------------------------------

	//create name for saved files (must be unique)
	$sname = $this->save_path .'/'. substr(time(), -5);

	// if you want a jpeg or png but the option is not available
	// automated change to the other type
	if ((!$this->allow_jpg_output) && (!$this->allow_png_output)) {
		echo "We have a problem! We can't save jpg or png either. Check your GD configuration.";
		exit (0);
	} else {
		if ($type == 'jpeg') {
			if ($this->allow_jpg_output) { 
				$sname .= '.jpg';
        			imagejpeg($img, $sname);
        		} else {
        			$sname .= '.png';
        			imagepng($img, $sname);
        		}
		} elseif ($type == 'png') {
	        	if ($this->allow_png_output) { 
        			$sname .= '.png';
        			imagepng($img, $sname);
        		} else {
				$sname .= '.jpg';
        			imagejpeg($img, $sname);
        		}
		}
	} // if-else
	
	$_SESSION['savedfile'] = $sname;
	imagedestroy($img);
} else { 
	// if GD in not installed we switch to text mode
	echo $this->stringGenerator($length);
}// ifelse ($gd_enabled)
	
}

//======================================================================
// SHOW CAPTCHA
// function to show captcha image on website page
// 
// @arg:	none
// @return:	echo image
//======================================================================
public function showCaptcha() {

	echo "<img src='" .$_SESSION['savedfile']. "' border='0' alt='captcha code' title='captcha code' />";

}

//======================================================================
// VERIFY CAPTCHA
//
// @arg:	$txt (string) - entered string from user
// @return:	bool (true / false)
//======================================================================
public function verifyCaptcha($txt) {

	// remove generated image
	if (is_file($_SESSION['savedfile'])) unlink ($_SESSION['savedfile']);
	
	// DECRYPTION PART
	$decrypted_data = $this->cryptDecrypt($_SESSION['captcha'], 'DECRYPT');

	return (strcmp ($txt, $decrypted_data) == 0) ? true : false;

}


//============================================================================================
//	P R I V A T E    F U N C T I O N S
//============================================================================================

//======================================================================
// STRING GENERATOR
// generates a random string of alphanumerics for captcha
//
// @arg:	$length (integer) - length of generated string
// @return:	$gen_string (string) - generated string (mixture of alphanumerics)
//======================================================================
private function stringGenerator($length) {

	// mix some letters and some digits
	$alphanumerics   = array_merge(range('A', 'Z'), range(2, 9));
	$alphanumerics_len = count($alphanumerics) - 1;

	$gen_string = '';
	for ($i = 0; $i < $length; $i++) {
		$gen_string .= $alphanumerics[mt_rand(0, $alphanumerics_len)];
	}
	
	// ENCRYPTION PART 
	$encrypted_data = $this->cryptDecrypt($gen_string, 'CRYPT');
	$_SESSION['captcha'] = $encrypted_data;
	
	return $gen_string;
	
}

//======================================================================
// DRAW SOME LINES ON PICTURE
// draw some lines to image to make it more complicated
//
// @arg:	$image (resource)
//		$imagelength (int)
// @return:	$image (resource) - an image painted with lines
//======================================================================
private function drawLines($image, $imagelength) {
	
	for ($i =0 ; $i<4 ; $i++) {
		// define random colors  for lines
		$cColor = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		
		// first coodinates are in the first half of image
		$x0coordonate = mt_rand (0, 0.5 * $imagelength);
		$y0coordonate = mt_rand (0, $this->img_height);
		// second coodinates are in the second half of image
		$x1coordonate = mt_rand (0.5 * $imagelength,$imagelength);
		$y1coordonate = mt_rand (0, $this->img_height);
		
		imageline ($image, $x0coordonate, $y0coordonate, $x1coordonate, $y1coordonate,$cColor );
		imageline ($image, $x0coordonate-1, $y0coordonate-1, $x1coordonate-1, $y1coordonate-1,$cColor );
	}
	
	return $image;
}

//======================================================================
// CRYPT OR DECRYPT A STRING
// 
// we encrypt the string with libmcrypt > 2.4.x
// @arg:	$image (resource)
//		$imagelength (int)
// @return:	$image (resource) - an image painted with lines
//======================================================================
private function cryptDecrypt($txt, $flag) {
	$td = mcrypt_module_open('tripledes', '', 'ecb', '');
   	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
   	$ks = mcrypt_enc_get_key_size($td);
	
   	$key = substr(md5($this->secret_key), 0, $ks); 
	
	// Intialize encryption 
   	mcrypt_generic_init($td, $key, $iv);

	switch ($flag) {
		case 'CRYPT': 	$result = mcrypt_generic($td, $txt); break;
		case 'DECRYPT': $result = trim (mdecrypt_generic($td, $txt)); break;
	}

   	// Terminate encryption handler 
	mcrypt_generic_deinit($td);
   	mcrypt_module_close($td);
   	
   	return $result;
}


} // EOF class

?>