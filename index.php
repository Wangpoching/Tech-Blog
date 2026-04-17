<?php
	require_once('conn.php');
	require_once('utils.php');

  // Hint: Posts Count = 0 不顯示文章
  // 選出精選文章
  $sql = "SELECT Count(id) as postsCount FROM `posts` WHERE selected = 1";
  $result = $conn->query($sql);
  if (!$result) {
    $postsCount = 0;
  } else {
    $postsCount = $result->fetch_assoc()['postsCount'];
  }
  if ($postsCount != 0) {
    // 分頁邏輯
    $page = getValue($_GET, 'page');
    if ($page === '') {
      $page = 1;
    } else {
      $page = (int)$page;
      $page = max(1, $page);
    }
    $perPage = 5;
    $totalPages = ceil($postsCount / $perPage);
    $page = min($totalPages, $page);
    $offset = ($page - 1) * $perPage;
    // 選出精選文章
    $sql = "SELECT id, title, subTitle, created_at FROM `posts` WHERE selected = 1 ORDER BY created_at ASC LIMIT ? OFFSET ?";
    $result = executeQuery($conn, $sql, 'ii', $perPage, $offset);
    if (!$result) {
      $postsCount = 0;
    }
    $prevDisabled = $page == 1 ? 'disabled' : '';
    $nextDisabled = $page == $totalPages ? 'disabled' : '';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = '首頁';
    $localCSS = [
      'css/dist/landing.css',
    ];
    $extraCSS = [
    	'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap'
    ];
    require_once('template/head.php');
  ?>
  <body>
    <div class="content-wrapper">
        <?php
          $active = 'Home';
          require_once('template/nav.php');
        ?>
        <div class="intro">
          <div class="intro__title">Pocyun Wang is an omnivore who codes</div>
          <div class="intro__content">Welcome to my little corner of the internet. I'm a curious <span class="strong">omnivore</span> — math, history, design, sports, geography, science... I'll take a bite of anything. So <span class="strong">expect the unexpected</span> here.

I get bored easily and love exchanging ideas with people who spark new ones. If you're the kind of person who gets excited about everything, you'll fit right in — come back often. <a href="about.php">Read more »</a></div>
        </div>
        <div class="profiles">
          <div class="profiles-wrapper">
            <div class="profile">Travel around Europe & Africa (4.5 months)</div>
            <div class="profile">Working Holiday in Ireland (8 months)</div>
            <div class="profile">Motorcycle Travel Indochina Peninsula (1.5 month)</div>
            <div class="profile">CacDi (Full-Stack Developer)</div>
            <div class="profile">Academic Sinica (Biodiversity Research Center)</div>
            <div class="profile last">National Taiwan University (Department of Geography)</div>
          </div>
          <button>KEEP GOING</button>
        </div>
        <div class="posts">
          <?php if ($postsCount != 0): ?>
            <div class="posts__overview">
              <div class="posts__title">Selected Posts</div>
              <div class="posts__count">
                <?= min($offset + $perPage, $postsCount) ?> of <?= $postsCount ?> posts
                <a class="btn btn-prev <?= $prevDisabled ?>" href="?page=<?= $page - 1 ?>">&#8249;</a>
                <a class="btn btn-next <?= $nextDisabled ?>" href="?page=<?= $page + 1 ?>">&#8250;</a>
              </div>
            </div>
            <div class="posts__container">
              <?php while($row = $result->fetch_assoc()): ?>
                <a class="post" href="post.php?id=<?= $row['id'] ?>">
                  <div class="post__title">
                    <?= escape($row['title']) ?>
                    <div class="post__date"><?= date('F d, Y', strtotime(escape($row['created_at']))) ?></div>
                  </div>
                  <div class="post__subTitle"><?= escape($row['subTitle']) ?></div>
                </a>
              <?php endwhile; ?>
            </div>
          <div class="posts__count">
            <a class="btn btn-prev <?= $prevDisabled ?>" href="?page=<?= $page - 1 ?>">&#8249;</a>
            <?= min($offset + $perPage, $postsCount) ?> of <?= $postsCount ?> posts
            <a class="btn btn-next <?= $nextDisabled ?>" href="?page=<?= $page + 1 ?>">&#8250;</a>
          </div>
          <?php else: ?>
            <div class="posts__empty">
              <div class="posts__empty-icon">✦</div>
              <div class="posts__empty-text">尚未有精選文章</div>
            </div>
          <?php endif; ?>
        </div>
    </div>
    <?php require_once('template/footer.php'); ?>
  </body>
  <script>
    const slider = document.querySelector('.profiles-wrapper')
    const original = slider.innerHTML

    document.querySelector('.profiles button').addEventListener('click', () => {
      slider.scrollBy({ top: 30, behavior: 'smooth' })
    })

    slider.addEventListener('scrollend', () => {
      const scrollGap = slider.scrollHeight - slider.clientHeight
      // 滾到底，加一份
      if (Math.abs(slider.scrollTop - scrollGap) < 5) {
        slider.insertAdjacentHTML('beforeend', original)
      }
    })
  </script>
</html>