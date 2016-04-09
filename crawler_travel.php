<?php

require_once 'ImageSearch.php';
require_once 'ImageProcessor.php';

$idListJson = file_get_contents('id_list.json');

$imageIdList = json_decode($idListJson);

$limit = 200;
$colors = ['red', 'green', 'blue', 'yellow', 'purple', 'black', 'brown', 'gold', 'pink', 'white', 'orange', 'cyan'];

$categoryList = ['sports'];

//	'fashion', 'nature', 'backgrounds', 'science', 'education', 'feelings', 'religion', 'health', 'places', 'animals', 'industry',
//	'food', 'computer', 'sports', 'transportation', 'travel', 'buildings', 'business', 'music'];

$imageSearch = new ImageSearch();
$imageProcessor = new ImageProcessor();

$colorIndexCounters = [];

foreach ($categoryList as $category)
{
	foreach ($colors as $color) {
		$page = 1;
		while (true) {
			$result = $imageSearch->search($color, $limit, $page, $category);

			if (empty($result['hits'])) {
				break;
			}

			foreach ($result['hits'] as $image)
			{
				if (in_array($image['id'], $imageIdList))
				{
					continue;
				}
				$imageIdList[] = $image['id'];

				$data = $imageProcessor->processImage($image);

				if (empty($data['path'])) {
					continue;
				}

				$id = $data['colors']['index'];

				$newFilePath = '/mnt/resource/source_images/new_' . $category . '/' . $id;
				$counterFileName = $newFilePath . '/counter';

				if (!file_exists($newFilePath)) {
					mkdir($newFilePath, 0777, true);
				}

				if (!isset($colorIndexCounters[$id])) {
					if (file_exists($counterFileName)) {
						$colorIndexCounters[$id] = file_get_contents($counterFileName);
					} else {
						$colorIndexCounters[$id] = 0;
					}
				}

				if (file_exists($counterFileName))
				{
					unlink($counterFileName);
				}
				$name = $newFilePath . '/' . $colorIndexCounters[$id]++ . '.jpg';

				file_put_contents($counterFileName, $colorIndexCounters[$id]);

				if (copy($data['path'], $name))
				{
					echo PHP_EOL . $category . ' - ' . $color . ' - ' . $result['total'] . ' - ' . $name;
				}
			}

			unlink('id_list.json');

			file_put_contents('id_list.json', json_encode($imageIdList));

			$page++;
		}
	}
}








