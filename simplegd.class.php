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
	
	/* The following variables are for internal use only don't edit them! */
	private $resourceImage;
	private $originalWidth;
	private $originalHeight;
	
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
		$this->loadResource($this->source);
		$this->getSourceSize();
		if(!$this->resizeIfSmaller)
		{
			if( $this->originalWidth < $this->width || $this->originalHeight < $this->height )
			{
				return saveResource();
			}
		}
	}
	
	private function getSourceSize()
	{
		list($width, $height) = getimagesize($this->source);
		$this->originalWidth  = $width;
		$this->originalHeight = $height;
	}
	
	private function saveResource()
	{
		
	}
}
?>