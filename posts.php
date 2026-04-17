<?php
	require_once('conn.php');
	require_once('utils.php');

  $sql = "SELECT * FROM `categories` ORDER BY id DESC";
  $categoriesResult = $conn->query($sql);
  if (!$categoriesResult) {
    exit('後台發生問題');
  }
  $sql = "SELECT * FROM `posts` WHERE status = ? ORDER BY created_at DESC";
  $postsResult = executeQuery($conn, $sql, 's', PUBLISHED_POST_STATUS);
  if (!$postsResult) {
    exit('後台發生問題');
  }
  $categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
  $posts = $postsResult->fetch_all(MYSQLI_ASSOC);
  $categoryPosts = [];
  foreach($categories as $category) {
    $categoryPosts[$category['name']] = [];
    foreach($posts as $post) {
      if ($post['category_id'] == $category['id']) {
        $categoryPosts[$category['name']][] = $post;
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = '文章列表';
    $localCSS = [
      'css/dist/posts.css',
    ];
    $extraCSS = [
    	'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap'
    ];
    require_once('template/head.php');
  ?>
  <body>
    <div class="content-wrapper">
        <?php
          $active = 'Posts';
          require_once('template/nav.php');
        ?>
        <div class="intro">
          <h1>Articles</h1>
          <h2>IT related topics, mostly for web development.</h2>
          <p>I started getting into web development back in 2020 and used to publish my technical articles on a platform called coderBridge. Unfortunately, the platform shut down without much warning, which caused me quite a headache trying to salvage my data. So I eventually decided to host everything here instead. I also write a lot about travel and outdoor activities, and I plan to keep those separate from this space.</p>
        </div>
        <div class="categories">
          <?php foreach($categoryPosts as $categoryName => $posts): ?>
            <?php if(!empty($posts)): ?>
              <div class="category">
                <div class="category__intro">
                  <h1><?= escape($categoryName) ?></h1>
                  <div class="carousel-controls">
                    <div class="control prev-control invisible">‹</div>
                    <div class="control next-control">›</div>
                  </div>
                </div>
                <div class="carousel" data-translate="0">
                  <?php foreach($posts as $post): ?>
                      <a class="post" href="post.php?id=<?= (int)$post['id'] ?>">
                        <div class="post__image">
                          <img src="<?= $post['cover_image'] ? UPLOAD_COVERS_URL . escape($post['cover_image']) : BASE_URL . 'images/default-cover.webp' ?>" />
                        </div>
                        <div class="post__title"><?= escape($post['title']) ?></div>
                      </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
    </div>
    <?php require_once('template/footer.php'); ?>
  </body>
  <script>
    const container = document.querySelector('.categories');

    container.addEventListener('click', (e) => {
      if (e.target.classList.contains('next-control')) {
        const category = e.target.closest('.category');
        const prevControl = category.querySelector('.prev-control')
        if (prevControl.classList.contains('invisible')) {
          prevControl.classList.remove('invisible')
        }
        const carousel = category.querySelector('.carousel');

        const containerWidth = category.offsetWidth; // ✅ 改這裡
        const contentWidth = carousel.scrollWidth;

        let translate = Number(carousel.dataset.translate || 0);

        // 👉 先計算下一步
        let nextTranslate = translate - 150;

        // 👉 判斷是否到底
        if (Math.abs(nextTranslate) + containerWidth >= contentWidth) {
          nextTranslate = -(contentWidth - containerWidth);

          carousel.style.transform = `translateX(${nextTranslate}px)`;
          carousel.dataset.translate = nextTranslate;

          e.target.classList.add('invisible'); // 到底就隱藏按鈕
          return;
        }

        // 👉 正常移動
        carousel.style.transform = `translateX(${nextTranslate}px)`;
        carousel.dataset.translate = nextTranslate;
      }

      if (e.target.classList.contains('prev-control')) {
        const category = e.target.closest('.category');
        const nextControl = category.querySelector('.next-control')

        // 有往前的話, 就可以讓往後的按鈕出現(代表有往後的空間)
        if (nextControl.classList.contains('invisible')) {
          nextControl.classList.remove('invisible')
        }
        const carousel = category.querySelector('.carousel')

        const containerWidth = category.offsetWidth
        const contentWidth = carousel.scrollWidth

        let translate = Number(carousel.dataset.translate || 0);

        // 👉 先計算下一步
        let nextTranslate = translate + 150

        // 👉 判斷是否到底
        if (nextTranslate >= 0) {
          nextTranslate = 0;

          carousel.style.transform = `translateX(${nextTranslate}px)`
          carousel.dataset.translate = nextTranslate

          e.target.classList.add('invisible'); // 到底就隱藏按鈕
          return
        }

        // 👉 正常移動
        carousel.style.transform = `translateX(${nextTranslate}px)`;
        carousel.dataset.translate = nextTranslate;
      }
    });
  </script>
</html>