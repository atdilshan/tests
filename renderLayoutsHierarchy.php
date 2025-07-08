<?php
function renderLayoutsHierarchy(string $pageDir, callable $pageRenderCallback): void {
    $baseDir = realpath(__DIR__ . '/pages');
    $targetDir = realpath($pageDir);

    if (!$targetDir || strpos($targetDir, $baseDir) !== 0) {
        http_response_code(500);
        echo "Invalid page path.";
        exit;
    }

    $layoutFns = [];

    $rootLayoutFile = $baseDir . DIRECTORY_SEPARATOR . 'layout.php';
    if (file_exists($rootLayoutFile)) {
        require_once $rootLayoutFile;
        if (function_exists('RootLayout')) {
            $layoutFns[] = 'RootLayout';
        }
    }

    $relativePath = str_replace($baseDir, '', $targetDir);
    $segments = array_filter(explode(DIRECTORY_SEPARATOR, $relativePath));

    $currentPath = $baseDir;
    foreach ($segments as $segment) {
        $currentPath .= DIRECTORY_SEPARATOR . $segment;
        $layoutFile = $currentPath . DIRECTORY_SEPARATOR . 'layout.php';

        if (file_exists($layoutFile)) {
            require_once $layoutFile;
            $funcName = ucfirst(trim($segment, '()')) . 'Layout';
            if (function_exists($funcName)) {
                $layoutFns[] = $funcName;
            }
        }
    }

    $final = $pageRenderCallback;
    foreach (array_reverse($layoutFns) as $layoutFn) {
        $inner = $final;
        $final = function () use ($layoutFn, $inner) {
            $layoutFn($inner);
        };
    }

    $final();
}
