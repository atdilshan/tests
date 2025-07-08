<?php
function PublicLayout($render)
{
  include __DIR__ . '/../../components/layouts/navbar.php';
  $render();
  include __DIR__ . '/../../components/layouts/footer.php';
}
?>
