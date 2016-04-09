<?php

class ImageProcessor
{
	private $path            = '/mnt/resource/source_images';
	private $temporaryFolder = '/temporary';
	private $temporaryPath   = '';

	private $cropSize = 256;

	public function __construct()
	{
		$this->temporaryPath = $this->path . $this->temporaryFolder;
	}

	public function processImage(array $imageData)
	{
		$id = $imageData['id'];
		$url = $imageData['webformatURL'];

		$imagePath = $this->downloadImage($id, $url);

		$imagePath = $this->cropImage($imagePath);

		if (false === $imagePath)
		{
			return false;
		}

		$colors = $this->getColorByAverage($imagePath);

		return [
			'path'   => $imagePath,
			'colors' => $colors,
		];
	}

	private function getColor($imagePath)
	{
		$original = imagecreatefromjpeg($imagePath);
		$image = imagecreate(1,1);
		imagecopyresampled($image, $original, 0, 0, 0, 0, 1, 1, imagesx($original), imagesy($original));

		imagejpeg($image, $this->temporaryPath . '/' . 'temp.jpg', 100);
		$image = imagecreatefromjpeg($this->temporaryPath . '/' . 'temp.jpg');
		$rgb   = imagecolorat($image, 0, 0);

		$colors = [
			($rgb >> 16) & 0xFF,
			($rgb >> 8) & 0xFF,
			$rgb & 0xFF
		];

		$palette = imagecreatefrompng('/home/tessera/crawler/ref.png');

		imagetruecolortopalette($palette, false, 256);

		$result = imagecolorclosest($palette, $colors[0], $colors[1], $colors[2]);
		$colorsPalette = imagecolorsforindex($palette, $result);

		return [
			'index' => $result,
			'rgb'   => $colorsPalette
		];
	}

	private function getColorByAverage($imagePath)
	{
		$original = imagecreatefromjpeg($imagePath);
		$width = imagesx($original);
		$height = imagesy($original);

		$colorsSum = [0,0,0];

		$n = $width * $height;

		for ($i=0; $i< $width; $i++)
		{
			for ($j=0; $j< $height; $j++)
			{
				$rgb = ImageColorAt($original, $i, $j);

				$colorsSum[0] += ($rgb >> 16) & 0xFF;
				$colorsSum[1] += ($rgb >> 8) & 0xFF;
				$colorsSum[2] += $rgb & 0xFF;
			}
		}

		$colors = [
			$colorsSum[0] / $n,
			$colorsSum[1] / $n,
			$colorsSum[2] / $n,
		];

		$palette = imagecreatefrompng('/home/tessera/crawler/ref.png');

		imagetruecolortopalette($palette, false, 256);

		$result = imagecolorclosest($palette, $colors[0], $colors[1], $colors[2]);
		$colorsPalette = imagecolorsforindex($palette, $result);

		return [
				'index' => $result,
				'rgb'   => $colorsPalette
		];
	}


	private function downloadImage($id, $url)
	{
		if (!file_exists($this->temporaryPath))
		{
			mkdir($this->temporaryPath, 0777, true);
		}

		$urlParts = explode('.', $url);
		$fileExtension = array_pop($urlParts);

		$fileExtension = strtolower($fileExtension);

		$temporaryImagePath = $this->temporaryPath . '/' . $id . '.' . $fileExtension;
		file_put_contents($temporaryImagePath, file_get_contents($url));

		return $temporaryImagePath;
	}

	private function cropImage($imagePath)
	{
		$fileParts     = explode('.', $imagePath);
		$fileExtension = array_pop($fileParts);

		$image = $this->createImage($fileExtension, $imagePath);

		if (false === $image)
		{
			return false;
		}

		list($width, $height, $type, $attr) = getimagesize($imagePath);

		if ($width < $this->cropSize || $height < $this->cropSize)
		{
			return false;
		}

		$newWidth  = $width;
		$newHeight = $height;

//		if ($width > $height)
//		{
//			$rate = $this->cropSize / $height;
//
//			$newWidth  = (int)($rate * $width);
//			$newHeight = (int)($this->cropSize);
//
//			$image2 = imagescale($image, $newWidth, $newHeight);
//		}
//		else
//		{
//			$rate = $this->cropSize / $width;
//
//			$newWidth  = (int)($this->cropSize);
//			$newHeight = (int)($rate * $height);
//
//			$image2 = imagescale($image, $newWidth, $newHeight);
//		}


//		$startX = (int)(($newWidth - $this->cropSize) / 2);
//		$startY = (int)(($newHeight - $this->cropSize) / 2);
//
//		$toCropArray = array('x' => $startX , 'y' => $startY, 'width' => $this->cropSize, 'height'=> $this->cropSize);
//		$croppedImage  = imagecrop($image, $toCropArray);
		$croppedImage  = imagescale($image, $this->cropSize, $this->cropSize);

		unlink($imagePath);

		$imagePath = implode('.', $fileParts) . '.jpg';

		if (!imagejpeg($croppedImage, $imagePath, 100))
		{
			return false;
		}

		return $imagePath;
	}

	private function createImage($fileExtension, $imagePath)
	{
		switch ($fileExtension)
		{
			case 'jpg':
			case 'jpeg':
				return imagecreatefromjpeg($imagePath);
				break;
			case 'png':
				return imagecreatefrompng($imagePath);
				break;
			case 'gd':
				return imagecreatefromgd($imagePath);
				break;
			case 'gd2':
				return imagecreatefromgd2($imagePath);
				break;
			case 'gif':
				return imagecreatefromgif($imagePath);
				break;
			case 'wbmp':
				return imagecreatefromwbmp($imagePath);
				break;
		}

		return false;
	}
}
