<?php
function RootLayout($render)
{
  $title = $GLOBALS['__page_title'] ? $GLOBALS['__page_title'] . " | Booking Application" : "Booking Application";
  $headTags = $GLOBALS['__page_head_tags'] ?? '';
  $bodyScripts = $GLOBALS['__page_body_scripts'] ?? '';

?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= htmlspecialchars($title) ?></title>

    <?php if (isProduction()): ?>
      <base href="/public/" />
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
      @theme {
      --color-theme-primary: #005d00;
      --color-theme-secondary: #023d02;
    }
  </style>

    <?= $headTags ?>

  </head>

  <body>

    <?php $render(); ?>

    <?= $bodyScripts ?>

  </body>

  </html>
<?php } ?>