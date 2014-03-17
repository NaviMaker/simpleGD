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
	
	public $width 	= 0;
	public $height 	= 0;
	public $format 	= "jpg"; 		// jpg, png, gif
	public $quality = 90; 			// 1(lowest) - 100(best) only for jpg 
	
	public $resizeMode = "scale"; 	// crop, warp, scale
	public $rotate 	   = 0; 		// 0, 90, 180, 270
	
	public $addWatermark	  = true; 			// 0, 90, 180, 270
	public $watermarkType	  = "text"; 		// Image, Text
	public $watermarkFont	  = ""; 			// Font file to use when writing watermark
	public $watermarkImage 	  = ""; 			// Image file to print over the source image
	public $watermarkPosition = "right-bottom"; // left-top, left-bottom, right-top, right-bottom, center, random
	
	public $resizeIfSmaller = false;			// If a small image is enlarged the final quality will be low
	public $enableCache 	= false;			// Only use cache if you render the same images frequently!
	public $cachedir 		= "";				// Cache dir relative to the script folder
	public $deleteOriginal  = false;			// If true simpleGD will delete the original image file permanently!
	public $printDebug 		= true;				// Add to every image an overlay showing process time and memory usage
	
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
		if($this->printDebug){$this->debug(0);}
		$this->loadResource($this->source);
		$this->getSourceSize();
		
		if(!$this->resizeIfSmaller)
		{
			if($this->width > $this->originalWidth)
			{
				$this->width = $this->originalWidth;
			}
			
			if($this->height > $this->originalHeight)
			{
				$this->height = $this->originalHeight;
			}
		}
		
		if($this->resizeMode == "warp")
		{
			$this->tempImage = imagecreatetruecolor($this->width, $this->height);
			ImageCopyResampled($this->tempImage, $this->resourceImage, 0, 0, 0, 0, $this->width, $this->height, $this->originalWidth, $this->originalHeight);
        }
		elseif($this->resizeMode == "scale")
		{
			$this->getProportionals();
			$this->tempImage = imagecreatetruecolor($this->width, $this->height);
			ImageCopyResampled($this->tempImage, $this->resourceImage, 0, 0, 0, 0, $this->width, $this->height, $this->originalWidth, $this->originalHeight);
        }
		elseif($this->resizeMode == "crop")
		{
			
        }
		else
		{
			throw new Exception ("simpleGD :: Resize mode not supported");
		}
		
		if($this->printDebug){$this->debug(1);}
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
		if($this->originalWidth > $this->originalHeight)
		{
			$this->height = ($this->originalHeight * $this->width )/$this->originalWidth;	
		}
		else
		{		
			$this->width = ($this->originalWidth * $this->height )/$this->originalHeight;	
		}
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
			$this->debugTime = $this->microtimeGet();	
		}
		elseif($step == 1)
		{
			$time = substr($this->microtimeGet() - $this->debugTime, 0, 4);
			
			$background = imagecolorallocatealpha($this->tempImage, 255, 255, 255, 70);
			$text = imagecolorallocate($this->tempImage, 0, 0, 0);
			imagefilledrectangle($this->tempImage, 0, 0, $this->width, 35, $background);
			imagestring($this->tempImage, 5, 10, 10,  'Image processed in '.$time.'s using '.substr((memory_get_usage()/1024/1024),0,5).'Mb', $text);
		}
	}
	
	private function microtimeGet()
	{
		$microtime = explode(" ",microtime()); 
		return($microtime[1] + $microtime[0]);	
	}
}
?>