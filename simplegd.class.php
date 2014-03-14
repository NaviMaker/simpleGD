<?php
/*
 *  simpleGD by NaviMaker
 *  https://github.com/NaviMaker/simpleGD
 *
 *	Licence: GPL V2
 *  Description:
 *	This is a simple PHP class I wrote to handle images 
 *
 */

class simpleGD{

	public $source;
	public $destination;
	
	public $width;
	public $height;
	public $format 	= "jpg"; 		// jpg, png, gif
	public $quality = 90; 			// 1-100 only for jpg
	
	public $resizeMode = "crop"; 	//crop, warp
	public $rotate 	   = 0; 		//0, 90, 180, 270
	
	public $resizeIfSmaller = false;
	public $enableCache = false;
	public $cachedir 	= "";
	public $deleteOriginal = false;
	public $printDebug = true;
	
	/* The following variables are for internal use only don't edit them! */
	private $resourceImage;
	private $tempImage;
	private $originalWidth;
	private $originalHeight;
	private $debugTime;
	
	private function loadResource($resource)
	{
		if(!file_exists($resource))
		{
			throw new Exception ("simpleGD :: This resource does not exist");
		}
		else
		{
			$resource_ext = pathinfo($resource, PATHINFO_EXTENSION);
			
			if($resource_ext == "jpg")
			{
				$this->resourceImage = imagecreatefromjpeg($resource);
			}
			elseif($resource_ext == "png")
			{
				$this->resourceImage = imagecreatefrompng($resource);
			}
			elseif($resource_ext == "gif")
			{
				$this->resourceImage = imagecreatefromgif($resource);
			}
			else
			{
				throw new Exception ("simpleGD :: I can't handle this file type");
			}
		}
	}
	
	public function render()
	{
		$this->debug(0);
		$this->loadResource($this->source);
		$this->getSourceSize();
		if(!$this->resizeIfSmaller)
		{
			if( $this->originalWidth < $this->width || $this->originalHeight < $this->height )
			{
				return saveResource();
			}
		}
		$this->tempImage = $this->resourceImage;
		$this->debug(1);
		$this->saveResource();
		
	}
	
	private function getSourceSize()
	{
		list($width, $height) = getimagesize($this->source);
		$this->originalWidth  = $width;
		$this->originalHeight = $height;
	}
	
	private function getProportionals()
	{
		
	}
	
	private function saveResource()
	{
		if($this->format == "jpg")
		{
			header('Content-type: image/jpg');
			imagejpeg($this->tempImage, NULL, $this->quality);
		}
		elseif($this->format == "png")
		{
			imagepng($this->tempImage);
		}
		elseif($this->format == "gif")
		{
			imagegif($this->tempImage);
		}
		else
		{
			throw new Exception ("simpleGD :: Your output format is not supported");
		}
	}
	private function debug($step)
	{
		if($step == 0)
		{
			$microtime = explode(" ",microtime()); 
			$this->debugTime = $microtime[1] + $microtime[0];	
		}
		elseif($step == 1)
		{
			$microtime = explode(" ",microtime()); 
			$now = $microtime[1] + $microtime[0];
			$total= $now - $this->debugTime;
			
			$back = imagecolorallocatealpha($this->tempImage, 255, 255, 255, 70);
			$text_color = imagecolorallocate($this->tempImage, 0, 0, 0);
			imagefilledrectangle($this->tempImage, 0, 0, $this->originalWidth, 35, $back);
			imagestring($this->tempImage, 5, 10, 10,  'Image generated in '.$total.' sec', $text_color);
		}
	}
}
?>