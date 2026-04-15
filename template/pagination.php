<?php
  // 產生特定頁的 URL
  $baseQueryParams = isset($baseQueryParams) ? $baseQueryParams : [];
  function buildPageUrl($page, $baseParams) {
      return '?' . http_build_query(array_merge($baseParams, ['page' => $page]));
  }
?>

<div class="pagination">
  <a href="<?= buildPageUrl($currentPage - 1, $baseQueryParams) ?>" class="arrow <?= $prevDisabled ?>">&#8249;</a>
  <?php if ($currentPage >= 3) { ?>
    <a href="<?= buildPageUrl(1, $baseQueryParams) ?>" class="page">1</a>
  <?php } ?>
  <?php if ($currentPage >= 4) { ?>
    <span class="dots">...</span>
  <?php } ?>
  <?php if ($currentPage >= 2) { ?>
    <a href="<?= buildPageUrl($currentPage - 1, $baseQueryParams) ?>" class="page"><?= $currentPage - 1 ?></a>
  <?php } ?>
    <a href="<?= buildPageUrl($currentPage, $baseQueryParams) ?>" class="page active"><?= $currentPage ?></a>
  <?php if ($currentPage < $totalPages) { ?>
    <a href="<?= buildPageUrl($currentPage + 1, $baseQueryParams) ?>" class="page"><?= $currentPage + 1 ?></a>
  <?php } ?>
  <?php if ($currentPage + 3 <= $totalPages) { ?>
    <span class="dots">...</span>
  <?php } ?>
  <?php if ($currentPage + 2 <= $totalPages) { ?>
    <a href="<?= buildPageUrl($totalPages, $baseQueryParams) ?>" class="page"><?= $totalPages ?></a>
  <?php } ?>
  <a href="<?= buildPageUrl($currentPage + 1, $baseQueryParams) ?>" class="arrow <?= $nextDisabled ?>">&#8250;</a>
</div>