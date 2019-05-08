<?php

function post_channel($url)
{
	$ch = isset($GLOBALS[__FUNCTION__]) ? $GLOBALS[__FUNCTION__] : curl_init();

	curl_setopt($ch, CURLOPT_POST, 1);

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	return $ch;
}

function http_code_is($ch, $code)
{
	return curl_getinfo($ch, CURLINFO_HTTP_CODE)===$code;
}