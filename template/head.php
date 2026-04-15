<?php
  require_once(__DIR__ . '/../utils.php');

  if (!isset($title)) {
    $title = "波波技術之地";
  }

  if (!isset($localCSS)) {
    $localCSS = [];
  }

  if (!isset($extraCSS)) {
    $extraCSS = [];
  }
?>
<head>
  <link rel="icon" href="<?= BASE_URL . 'favicon.ico' ?>" type="image/x-icon">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>

  <!-- 動態附加 CSS -->
  <?php foreach ($localCSS as $css): ?>
    <link rel="stylesheet" href="<?= BASE_URL . escape($css); ?>">
  <?php endforeach; ?>
  <?php foreach ($extraCSS as $css): ?>
    <link rel="stylesheet" href="<?= escape($css); ?>">
  <?php endforeach; ?>
</head>