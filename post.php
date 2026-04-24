<?php
	require_once('conn.php');
	require_once('utils.php');
	require_once('configs/config.php');
	session_start();

	// 如果有 userToken，注入到 js 以後要從 session 清除
	$userToken = getValue($_SESSION, 'userToken');
	if ($userToken !== '') unset($_SESSION['userToken']);

	// 將 Markdown 渲染成 html
	require __DIR__ . '/vendor/autoload.php';
	use League\CommonMark\Environment\Environment;
	use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
	use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
	use League\CommonMark\MarkdownConverter;

	// 不允許 HTML 注入
  // 不允許惡意連結
  // 防止 XSS 攻擊
	$environment = new Environment([
	    'html_input' => 'strip',
	    'allow_unsafe_links' => false,
	]);

	$environment->addExtension(new CommonMarkCoreExtension());
	$environment->addExtension(new GithubFlavoredMarkdownExtension());
	$converter = new MarkdownConverter($environment);

	$id = getValue($_GET, 'id');
	if ($id === '') {
		header('Location: index.php');
		exit();
	}
	$id = (int)$id;

	// 草稿不能看
	$sql = "
		SELECT posts.id as id, title, subTitle, content, status, cover_image, posts.category_id as categoryId, GROUP_CONCAT(tags.name) as tags FROM `posts`
    LEFT JOIN `categories` as c on posts.category_id = c.id
    LEFT JOIN `post_tags` as pt on posts.id = pt.post_id
    LEFT JOIN `tags` on pt.tag_id = tags.id
    WHERE posts.id = ? AND status = ?
    GROUP BY posts.id
	";
	$result = executeQuery($conn, $sql, 'is', $id, PUBLISHED_POST_STATUS);
	if (!$result || $result->num_rows === 0) {
		header('Location: index.php');
		exit();
	}
	$post = $result->fetch_assoc();
	$tags = $post['tags'] ? explode(',', $post['tags']) : [];
	$content = $converter->convert($post['content']);

	// 取得同分類的前一篇以及下一篇文章 ID
	$sql = "SELECT id FROM `posts` WHERE category_id = ? AND status = ? ORDER BY created_at ASC";
	$result = executeQuery($conn, $sql, 'is', (int)$post['categoryId'], PUBLISHED_POST_STATUS);
	$idsResult =  $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
	$ids = [];
	foreach($idsResult as $item) {
		$ids[] = $item['id'];
	}
	$current = array_search($post['id'] , $ids);

	$nextExit = isset($ids[$current + 1]);
	if ($nextExit) $nextId = $ids[$current + 1];
	$prevExit = isset($ids[$current - 1]);
	if ($prevExit) $prevId = $ids[$current - 1];

	// 處理讀者登入留言板失敗
  $flash = null;
  if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
  }
?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = '單一文章';
    $localCSS = [
      'css/dist/post.css',
    ];
    $extraCSS = [
    	'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap',
      "{$_ENV['COMMENT_BOARD']}/plugin/dist/style.css"
    ];
    require_once('template/head.php');
  ?>
  <body>
    <div class="content-wrapper">
	      <?php
	        $active = 'Posts';
	        require_once('template/nav.php');
	      ?>
      	<div class="back-to-posts"><a href="index.php">← All posts</a></div>
      	<div class="information">
      		<div class="tags">
	      		<?php foreach($tags as $tag): ?>
	      			<span class="tag"><?= escape($tag) ?></span>
	      		<?php endforeach; ?>
	      	</div>
	      	<div class="information__detail">
	      		<span>December 1, 2024</span>
	      		<span>8 min Read</span>
	      	</div>
	      	<div class="title"><?= escape($post['title']) ?></div>
	      	<div class="subtitle"><?= escape($post['subTitle']) ?></div>
      	</div>
      </div>
	    	<?php if ($post['cover_image']): ?>
	      	<div class="cover_image">
	      		<img src="<?= UPLOAD_COVERS_URL . $post['cover_image'] ?>" />
	      	</div>
	      <?php else: ?>
	      	<div class="cover_image cover_image--default"
	      	     style="background: <?= categoryGradient($post['categoryId']) ?>">
	      		<div class="cover_image__title"><?= escape($post['title']) ?></div>
	      	</div>
	      <?php endif; ?>
      <div class="content-wrapper content">
      	<?= $content ?>
      </div>
      <div class="content-wrapper" id="comment-area-wrapper">
      </div>
      <div class="content-wrapper">
	      <div class="indicators">
	      	<?php if ($prevExit): ?>
	      		<div class="btn prev-post"><a href="post.php?id=<?= $prevId ?>">← Prev post</a></div>
	      	<?php endif; ?>
	      	<?php if ($nextExit): ?>
	      		<div class="btn next-post"><a href="post.php?id=<?= $nextId ?>">Next post →</a></div>
	      	<?php endif; ?>
	      </div>
	    </div>
    <?php require_once('template/footer.php'); ?>
  </body>
  <script src="<?= $_ENV['COMMENT_BOARD'] ?>/plugin/dist/comment-board.js"></script>
  <script>
    const POST_ID = <?= json_encode($id) ?>;
    <?php if ($userToken !== ''): ?>
      localStorage.setItem('userToken', <?= json_encode($userToken) ?>)
    <?php endif; ?>

    const GET_APP_TOKEN_URL = "<?= DOMAIN ?>/get_app_token.php"
    const loginState = { state: `<?= DOMAIN ?>/post.php?id=${POST_ID}` }

    // --- Utilities ---
    function isJWTExpired(token) {
      const exp = JSON.parse(atob(token.split('.')[1])).exp
      return Date.now() > exp * 1000
    }

    // --- Token helpers ---
    function getUserToken() { return localStorage.getItem('userToken') }

    // --- Init ---
    let userToken = getUserToken()
    if (userToken !== null) {         // 改這裡
      if (isJWTExpired(userToken)) {  // 改這裡
        localStorage.removeItem('userToken')
        userToken = null
      }
    }

    CommentBoard.init({
      container: document.querySelector('#comment-area-wrapper'),
      identifier: POST_ID,
      getAppTokenUrl: GET_APP_TOKEN_URL,
      loginParams: loginState,
      appKey: '<?= BOARD_APP_KEY ?>',
      userToken: userToken
    })
  </script>
</html>
