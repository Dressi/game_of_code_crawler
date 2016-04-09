<?php

//$folder = 'mnt/resource/source_images/';
//
//$category = 'animals_2';
//
//$refImage = '/home/tessera/crawler/ref.png';
//
//
//
//$palette = imagecreatefrompng('/home/tessera/crawler/ref.png');
//imagetruecolortopalette($palette, false, 256);
//
//$width  = imagesx($palette);
//$height = imagesy($palette);
//
//$colorsSum = [0,0,0];
//
//$n = $width * $height;
//
//for ($i=0; $i< $width; $i++)
//{
//	for ($j=0; $j< $height; $j++)
//	{
//		$rgb = ImageColorAt($original, $i, $j);
//
//		$colorsSum[0] += ($rgb >> 16) & 0xFF;
//		$colorsSum[1] += ($rgb >> 8) & 0xFF;
//		$colorsSum[2] += $rgb & 0xFF;
//	}
//}