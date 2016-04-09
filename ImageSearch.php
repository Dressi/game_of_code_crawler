<?php

class ImageSearch
{
	private $apiKey = '';

	public function __construct()
	{
		$this->apiKey = file_get_contents(__DIR_ . '/api_key');
	}

	public function search($subject, $limitPerPage, $page)
	{
		$url = 'https://pixabay.com/api/';
		$parameters = [
			'key' => $this->apiKey,
			'q'   => $subject,
			'per_page' => $limitPerPage,
			'page' => $page,
		];

		$url .= '?' . http_build_query($parameters);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$body = curl_exec($ch);
		curl_close($ch);

		file_put_contents('test', $body);

		$result = json_decode($body, true);

		return $result;
	}
}