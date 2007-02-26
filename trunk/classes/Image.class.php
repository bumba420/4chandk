<?php
/*
http://php.net/manual/en/ref.image.php

IMAGETYPE_GIF (integer) 
IMAGETYPE_JPEG (integer) 
IMAGETYPE_PNG (integer) 
IMAGETYPE_SWF (integer) 
IMAGETYPE_PSD (integer) 
IMAGETYPE_BMP (integer) 
IMAGETYPE_WBMP (integer) 
IMAGETYPE_XBM (integer) 
IMAGETYPE_TIFF_II (integer) 
IMAGETYPE_TIFF_MM (integer) 
IMAGETYPE_IFF (integer) 
IMAGETYPE_JB2 (integer) 
IMAGETYPE_JPC (integer) 
IMAGETYPE_JP2 (integer) 
IMAGETYPE_JPX (integer) 
IMAGETYPE_SWC (integer)
*/
class Image
{
	private $filename	=	'';
	private $width		=	0;
	private $height		=	0;
	private $filesize	=	0;
	private $filetype	=	0;
	
	private $maxWidth	=	0;
	private $maxHeight	=	0;
	
	private $validates	= false;
	
	public function __construct($image, $filename, $maxWidth, $maxHeight, $board, $allowedTypes)
	{
		// Hack, I don't like this
		if (is_uploaded_file($image['tmp_name']))
		{
			// If it's a new uploaded file
			$file_path 		=	$image['tmp_name'];
			list($width, $height, $type, $attr) = getimagesize($file_path);
			$this->filetype		=	$type;
			
			$full_filename	=	$filename . '.' . $this->extension($image['name']);
			$filesize 		=	isset($image['filesize']) ? $image['filesize'] : 0;
		}
		else 
		{
			//die("hmm - wrong: ".var_dump($image));
			// If we just want to find an old file
			$full_filename	=	$filename;
			$file_path 		=	Config::get('image_folder').'/'.$full_filename;
			$filesize		=	filesize($file_path);
		}
		
		list($width, $height, $type, $attr) = getimagesize($file_path);

		$this->width		= 	$width;
		$this->height		=	$height;
		$this->filesize		= 	$filesize;
		$this->filetype		=	$type;
		$this->filename		= 	$full_filename;
		
		$this->maxWidth		=	$maxWidth;
		$this->maxHeight	=	$maxHeight;

		if (is_uploaded_file($image['tmp_name']) && $this->validate($board->getFilesizeInB(), $allowedTypes)) 
		{
			return $this->moveFile($image);
		}
		
		return false;
	}
	
	public function validate($maxFilesize, $allowedTypes)
	{
		// I brok this up so I would be easier to read
		if ($this->filesize > $maxFilesize)
		{
			return false;
		}
		
		if (!in_array($this->filetype, $allowedTypes))
		{
			return false;
		}
		
		$this->validates	=	true;
		return true;
	}
	
	private function extension($filename)
	{
    	/*
		$path_info = pathinfo($filename);
    	return $path_info['extension'];
    	*/
    	switch ($this->filetype) {
    		case IMAGETYPE_GIF:
    			return 'gif';
    			break;
    			
    		case IMAGETYPE_JPEG:
    			return 'jpg';
    			break;
    			
     		case IMAGETYPE_PNG:
    			return 'png';
    			break;
    			
    		case IMAGETYPE_BMP:
    			return 'bmp';
    			break;
    	
    		default:
    			return false;
    			break;
    	}
	}
	
	private function moveFile($image)
	{
		return move_uploaded_file($image['tmp_name'], Config::get('image_folder').'/'.$this->filename);
	}
	
	public function getThumbnailURL()
	{
		$thumbWidth		=	0;
		$thumbHeight	=	0;
		
		$thumbnailDestination	=	Config::get('thumbnail_folder').'/'.$this->filename;
		$sourceDestination		= 	Config::get('image_folder').'/'.$this->filename;

		if (file_exists($thumbnailDestination))
		{
			return Config::get('page_url').Config::get('thumbnail_url').'/'.$this->filename;
		}
		elseif (file_exists($sourceDestination)) 
		{
			if ($this->width > $this->height)
			{
				$thumbWidth 	= $this->maxWidth;
				$thumbHeight	= ($this->height / $this->width) * $thumbWidth;
			}
			else 
			{
				$thumbHeight	= $this->maxHeight;
				$thumbWidth		= ($this->width / $this->height) * $thumbHeight;
			}
			
    		switch ($this->filetype) {
    			case IMAGETYPE_GIF:
    				$imagecreatefromfile	=	'imagecreatefromgif';
    				break;
    			
    			case IMAGETYPE_JPEG:
   		 			$imagecreatefromfile	=	'imagecreatefromjpeg';
    				break;
    			
   	  			case IMAGETYPE_PNG:
   		 			$imagecreatefromfile	=	'imagecreatefrompng';
    				break;
    			
    			case IMAGETYPE_BMP:
    				$imagecreatefromfile	=	'imagecreatefromwbmp';
    				break;
    		}
    		
    		$thumbnail 	= 	imagecreatetruecolor($thumbWidth, $thumbHeight);
    		$original	=	$imagecreatefromfile($this->getPath());
			
    		imagecopyresampled($thumbnail, 
    						   $original, 
    						   0, 
    						   0, 
    						   0, 
    						   0, 
    						   $thumbWidth, 
    						   $thumbHeight, 
    						   $this->width, 
    						   $this->height);
    						   
    		imagejpeg($thumbnail, $thumbnailDestination, 100);

    		return Config::get('page_url').Config::get('thumbnail_url').'/'.$this->filename;
		}

		return	'';	
	}
	
	public function getURL() 
	{
		return Config::get('page_url').Config::get('image_url').'/'.$this->filename;
	}
	
	public function getPath() 
	{
		return Config::get('image_folder').'/'.$this->filename;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}
	
	public function getFilesize() 
	{
		return round($this->filesize/1024);
	}
	
	public function getWidth() 
	{
		return $this->width;
	}
	
	public function getHeight() 
	{
		return $this->height;
	}
	
	public function getType() 
	{
		return $this->filetype;
	}
}
?>