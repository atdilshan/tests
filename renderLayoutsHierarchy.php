<?php
function renderLayoutsHierarchy(string $pageDir, callable $pageRenderCallback): void
{
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

    $rawHtml = (function () use ($pageRenderCallback) {
        ob_start();
        $pageRenderCallback();
        return ob_get_clean();
    })();

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<meta charset="UTF-8">' . $rawHtml);

    $headTags = '';
    $bodyScripts = '';
    $finalContent = '';

    $body = $doc->getElementsByTagName('body')->item(0);
    if ($body) {
        foreach (iterator_to_array($body->childNodes) as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $tag = $node->nodeName;
                if ($tag === 'script') {
                    $bodyScripts .= $doc->saveHTML($node) . "\n";
                } elseif (in_array($tag, ['link', 'style', 'meta'])) {
                    $headTags .= $doc->saveHTML($node) . "\n";
                } else {
                    $finalContent .= $doc->saveHTML($node);
                }
            } elseif ($node->nodeType === XML_TEXT_NODE) {
                $finalContent .= $doc->saveHTML($node);
            }
        }
    } else {
        $finalContent = $rawHtml;
    }

    $GLOBALS['__page_content'] = $finalContent;
    $GLOBALS['__page_title'] = $GLOBALS['title'] ?? null;
    $GLOBALS['__page_head_tags'] = $headTags;
    $GLOBALS['__page_body_scripts'] = $bodyScripts;

    $final = function () {
        echo $GLOBALS['__page_content'];
    };

    foreach (array_reverse($layoutFns) as $layoutFn) {
        $inner = $final;
        $final = function () use ($layoutFn, $inner) {
            $layoutFn($inner);
        };
    }

    $final();
}
