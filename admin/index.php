<?php
  require_once('../conn.php');
  require_once('../utils.php');

  session_start();
  // 檢查是否是 Admin
  isAdmin();
  // 引入 siebar 模板指定 All posts
  $active = 'All Posts';

  // 計算分頁顯示數量
  $currentPage = (int)($_GET['page'] ?? 1);
  $currentPage = max(1, $currentPage); // 先夾住 >= 1，等算完 totalPages 再夾上限
  $perPage = 5;
  $sql = "SELECT count(id) as count FROM posts";
  $categoryId = getValue($_GET, 'category_id');
  $status = getValue($_GET, 'status');
  $partialMatch = getValue($_GET, 'partial_match');
  if ($categoryId !== '')  $categoryId = (int)$categoryId;

  $filters = [];
  $parameters = [];
  $placeHolder = '';

  if ($categoryId !== '') {
    $filters[] = 'category_id = ?';
    $placeHolder .= 'i';
    $parameters[] = $categoryId;
  }
  if ($status !== '') {
    $filters[] = 'status = ?';
    $placeHolder .= 's';
    $parameters[] = $status;
  }
  if ($partialMatch !== '') {
    $filters[] = '(title LIKE ? OR subTitle LIKE ? OR content LIKE ?)';
    $placeHolder .= 'sss';
    $search = "%$partialMatch%";
    $parameters[] = $search;
    $parameters[] = $search;
    $parameters[] = $search;
  }
  $baseQueryParams = [
      'category_id'   => $categoryId,
      'status'        => $status,
      'partial_match' => $partialMatch,
  ];

  if (count($filters) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $filters);
  }
  if ($placeHolder === '') {
    $countResult = $conn->query($sql);
  } else {
    $countResult = executeQuery($conn, $sql, $placeHolder, ...$parameters);
  }
  if (!$countResult) {
    die('Error' . $conn->error);
  }
  $row = $countResult->fetch_assoc();

  // 取得總筆數
  $total = $row['count'];
  if ($total !== 0) {
    $totalPages = ceil($total / $perPage);
    $currentPage = min($currentPage, $totalPages); // 防止超出總頁數
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $offset = ($currentPage - 1)*$perPage;
    $placeHolder .= 'ii';

    // 整理 query
    $sql = "
      SELECT 
      p.id AS postId,
      p.title, 
      p.subTitle, 
      p.content, 
      p.status, 
      c.name AS categoryName, 
      p.category_id,
      p.created_at, 
      IFNULL(GROUP_CONCAT(DISTINCT t.name), '') AS tags
      FROM posts p
      LEFT JOIN categories c ON p.category_id = c.id
      LEFT JOIN post_tags pt ON p.id = pt.post_id
      LEFT JOIN tags t ON pt.tag_id = t.id
    ";
    if (count($filters) > 0) {
      $sql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $sql .= ' GROUP BY p.id';
    $sql .= ' ORDER BY p.created_at ASC LIMIT ? OFFSET ?';
    // 查詢
    $postsResult = executeQuery($conn, $sql, $placeHolder, ...[...$parameters, $perPage, $offset]);
    $isPostFind = $postsResult && $postsResult->num_rows > 0;
  } else {
    $isPostFind = false;
  }

  // 取得所有 tags
  $sql = "SELECT * FROM `tags`";
  $result = $conn->query($sql);
  $tags = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

  // 取得所有 categories
  $sql = "SELECT * FROM `categories`";
  $result = $conn->query($sql);
  $categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<?php
  $title = '後台首頁';
  $localCSS = [
    'css/dist/admin-main.css',
  ];
  $extraCSS = ['https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Noto+Sans+TC:wght@400;500&display=swap'];
  require_once('../template/head.php');
?>
  <body>
    <?php require_once('../template/admin/header.php'); ?>
      <?php require_once('../template/admin/side.php'); ?>
      <main>
        <div class="main-content__wrapper">
          <a class="btn btn-add" href="add.php">+ New Post</a>
          <div class="title">All Posts</div>
          <form method="GET" action="index.php"> 
            <div class="filters">
              <select class="selector category-selector" name="category_id">
                <option <?= $categoryId === '' ? 'selected' : '' ?> value="">All Categories</option>
                <?php foreach($categories as $category): ?>
                  <option <?= $categoryId == $category['id'] ? 'selected' : '' ?> value="<?= (int)$category['id'] ?>"><?= escape($category['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <select class="selector status-selector"  name="status">
                <option <?= $status === '' ? 'selected' : '' ?> value="">All Status</option>
                <option <?= $status == PUBLISHED_POST_STATUS ? 'selected' : '' ?> value="<?= PUBLISHED_POST_STATUS ?>">Published</option>
                <option <?= $status === DRAFT_POST_STATUS ? 'selected' : '' ?> value="<?= DRAFT_POST_STATUS ?>">Draft</option>
              </select>
              <input name="partial_match" class="post-content-selector" type="text" placeholder="Search posts.." value="<?= escape($partialMatch) ?>" />
              <!-- 搜尋的時候重置回第一頁 -->
              <input type="hidden" name="page" value="1">
              <button class="btn-search">Search</button>
            </div>
          </form>
          <?php if ($isPostFind): ?>
            <table class="post-table">
              <thead>
                <tr>
                  <th>TITLE</th>
                  <th>CATEGORY</th>
                  <th>TAGS</th>
                  <th>STATUS</th>
                  <th>DATE</th>
                  <th>OPERATION</th>
                </tr>      
              </thead>
              <tbody>
                <?php while ($row = $postsResult->fetch_assoc()): ?>
                  <tr>
                    <td><?= escape($row['title']) ?></td>
                    <td><?= escape($row['categoryName']) ?></td>
                    <?php
                      $tags = empty($row['tags']) ? [] : explode(',', $row['tags']);
                    ?>
                    <td>
                      <?php foreach($tags as $tag): ?>
                        <span class="tag-chip"><?= escape($tag) ?></span>
                      <?php endforeach; ?>
                    </td>
                    <?php
                      $statusType = $row['status'] === 'published' ? 'Published' : 'Draft';
                      $statusClass = $row['status'] === 'published' ? 'status-published' : 'status-draft'
                    ?>
                    <td><span class="status-pill <?= $statusClass ?>"><?= $statusType ?></span></td>
                    <td><?= date('Y/m/d', strtotime(escape($row['created_at']))) ?></td>
                    <td class="tools">
                      <a href="edit.php?id=<?= (int)$row['postId'] ?>" class="btn-edit">Edit</a> 
                      <form method="POST" action="delete.php?id=<?= (int)$row['postId'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <button type="submit" class="btn-delete">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="nothing-found">nothing is found</div>
          <?php endif; ?>
        </div>
        <?php if ($isPostFind): ?>
          <?php require_once('../template/pagination.php'); ?>
        <?php endif; ?>
      </main>
  </body>
</html>
