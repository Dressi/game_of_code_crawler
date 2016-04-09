<?php


$folder = '/mnt/resource/source_images/';

//$sourceCategories = ['animals_2', 'new_animals'];
//
//$target = 'animals_merged';

$sourceCategories = ['animals_merged', 'new_people', 'buildings', 'music', 'new_travel', 'new_fashion', 'new_science'];

$target = 'all_2';

$targetFolder = $folder . $target . '/';

$idCounters[] = 0;

$i = 0;

foreach ($sourceCategories as $category)
{
	$parentFolder = $folder . $category;

	$imageColorFolderList = getFolderContentList($parentFolder);

	if (empty($imageColorFolderList))
	{
		continue;
	}

	foreach ($imageColorFolderList as $colorId)
	{
		$targetColorFolder = $targetFolder . $colorId;

		if (!isset($idCounters[$colorId]))
		{
			$idCounters[$colorId] = 0;
		}

		$colorFolder = $parentFolder . '/' . $colorId;
		$files = getFolderContentList($colorFolder);

		if (!file_exists($targetColorFolder))
		{
			mkdir($targetColorFolder, 0777, true);
		}

		foreach ($files as $file)
		{
			$oldFile = $colorFolder . '/' . $file;
			$newFile = $targetColorFolder . '/' . $idCounters[$colorId]++ . '.jpg';

			copy($oldFile, $newFile);

			echo ' ' . $i++;
		}
	}
}

function getFolderContentList($folder)
{
	$list = [];

	if ($handle = opendir($folder))
	{
		while (false !== ($entry = readdir($handle)))
		{
			if ($entry != "." && $entry != "..")
			{
				$list[] = $entry;
			}
		}
		closedir($handle);
	}

	return $list;
}