<?php

require_once 'ImageSearch.php';
require_once 'ImageProcessor.php';

$limit = 200;
//$subjectList = ['dog', 'cat']; //, 'fish', 'horse', 'bird', 'goat', 'sheep', 'mouse', 'elephant', 'fox', 'wolf'];
$subjectList = ['landscape', 'mountains', 'river', 'lake'];

$imageSearch = new ImageSearch();
$imageProcessor = new ImageProcessor();

$colorIndexCounters = [];

foreach ($subjectList as $subject) {
	$page = 1;
	while (true) {
		$result = $imageSearch->search($subject, $limit, $page);

		if (empty($result['hits'])) {
			break;
		}

		foreach ($result['hits'] as $image) {
			$data = $imageProcessor->processImage($image);

			if (empty($data['path'])) {
				continue;
			}

			$id = $data['colors']['index'];

			$newFilePath = '/home/tessera/source_images/nature/' . $id;
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

			if (copy($data['path'], $name)) {
				echo PHP_EOL . $subject . ' - ' . $result['total'] . ' - ' . $name;
			}
		}

		$page++;
	}
}







