<?php
require_once __DIR__ . '/../renderLayoutsHierarchy.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($uri, '/');
if ($path === '') $path = '/';

$pageBasePath = realpath(__DIR__ . '/../pages');
$segments = array_filter(explode('/', $path));

$possiblePaths = [];
if (empty($segments)) {
    $possiblePaths[] = $pageBasePath . '/(public)/index.php';
} else {
    $layouts = ['(auth)', '(main)', '(public)', ''];
    foreach ($layouts as $layout) {
        $test = $pageBasePath;
        foreach ($segments as $segment) {
            $test .= '/' . $segment;
        }
        if ($layout !== '') {
            $test = dirname($test) . "/$layout/" . basename($test);
        }
        $test .= '/index.php';
        $possiblePaths[] = $test;
    }
}

foreach ($possiblePaths as $file) {
    if (file_exists($file)) {
        renderLayoutsHierarchy(dirname($file), function () use ($file) {
            include $file;
        });
        exit;
    }
}

http_response_code(404);
echo "<h1>404 Not Found</h1>";
