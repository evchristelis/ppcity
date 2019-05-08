<?php

$start_time = time();

define("LIB_DIRECTORY", __DIR__);

define("__IS__", "script");
define("IS_LIVE", (isset($argv[1]) and $argv[1]=='live'));
define("IS_REDIRECT", (isset($argv[1]) and $argv[1]=='redirect'));
define("IS_TEST", !(IS_LIVE || IS_REDIRECT));

define("SUCCESS", 200);
define("ERROR", 666);

error_reporting(E_ALL);
ini_set('display_errors', 1);

// $locale='el_EL.UTF-8';
// setlocale(LC_ALL,$locale);
// putenv('LC_ALL='.$locale);
// echo exec('locale charmap');

$logs = array();

$has_errors = false;

function logit($msg, $level="DEBUG")
{
    global $logs;

    $log = "[".strtoupper($level)."] ".date("y-m-d H:i:s")." : ".$msg;

    $logs[] = $log;

    if (!IS_LIVE or !in_array($level, array("DEBUG")))
        echo "\n".$log;

    if ($level=="ERROR")
    {
        global $has_errors;

        $has_errors = true;
    }

    return $msg;
}

function file_is_just_updated($filename)
{
    global $start_time;
    return (file_time($filename)-$start_time) > 600;
}

function script_error($message)
{
    global $is_test;

    if ($is_test)
    {
        echo $message;
        die();
    }
    else
    {
        die("ERROR: $message");
    }
}

function total_time()
{
    global $start_time;

    return time()-$start_time;
}

function script_is_recently_modified()
{
    global $start_time;
    $diff = $start_time-filemtime(get_included_files()[0]);
    return ($diff>0 and $diff<86400);
}

function script_done()
{
    global $logs, $has_errors;

    $file = get_included_files();
    $file = basename($file[0]);

    if (IS_LIVE and (script_is_recently_modified() or $has_errors))
    {


    }

    $logs = implode("\n", $logs);

    file_put_contents(__DIR__."/report-".str_replace(".php", ".txt", $file), $logs);

    if ($has_errors)
    {
        file_put_contents(__DIR__."/error-".str_replace(".php", ".txt", $file), $logs);
    }

	die("\nok\n");
}

//
// json
//

function jsonit($value)
{
    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

//
// shortcuts
//

function if_set($index, $data, $default='')
{
    return isset($data[$index]) ? $data[$index] : $default;
}

function starts_with($str, $start)
{
    return (substr($str, 0, strlen($start)) === $start);
}

function ends_with($str, $ending)
{
    $length = strlen($ending);
    return $length === 0 || (substr($str, -$length) === $ending);
}

function html_to_text($html)
{
    return strip_tags(str_replace("<br>", "\n", $html));
}
