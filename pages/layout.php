<?php
function RootLayout($render)
{
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Booking Application</title>
    <base href="/public/">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
      @theme {
        --color-theme-primary: #005d00;
        --color-theme-secondary: #023d02;
      }
    </style>

  </head>

  <body>

    <?php $render(); ?>

  </body>

  </html>
<?php } ?>