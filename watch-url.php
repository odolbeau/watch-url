<?php
require_once __DIR__.'/vendor/.composer/autoload.php';

$dataDir = __DIR__.'/cache';

if (!is_dir($dataDir)) {
    mkdir($dataDir);
}

if (!isset($argv[1])) {
    echo "Usage: php read-title.php mon-url\n";
    exit;
}

$url = $argv[1];
$hash = md5($url);

$file = $dataDir.'/'.md5($hash);

if (file_exists($file)) {
    list($statusCode, $content) = unserialize(file_get_contents($file));
} else {
    $statusCode = $content = null;
}

$browser = new Buzz\Browser();

$browser->call($url, 'GET');
$response = $browser->getLastResponse();

$newStatusCode = $response->getStatusCode();
$newContent    = $response->getContent();

if ($statusCode !== null && $content !== null) {
    if ($newStatusCode != $statusCode || $newContent != $content) {
        echo 'The page has changed !'."\n";
        echo 'Old status code: '.$statusCode."\n";
        echo 'New status code: '.$newStatusCode."\n";
        echo '------'."\n";
        echo $newContent;
        exec("google-chrome 'http://www.youtube.com/watch?v=dQw4w9WgXcQ'");
    }
}

file_put_contents($file, serialize(array($newStatusCode, $newContent)));
