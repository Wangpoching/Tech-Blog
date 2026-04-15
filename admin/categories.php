<?php
	require_once('../conn.php');
	require_once('../utils.php');
  session_start();
	// 管理員頁面
	isAdmin();
	$error = '';
	// 引入 siebar 模板指定 Categories
	$active = 'Categories';

	function handleEdit($conn) {
		verifyCsrfToken();
    $categoryId = getValue($_POST, 'id');
    $categoryName = getValue($_POST, 'name');
    if ($categoryId === '') return '發生錯誤';
    if ($categoryName === '') return '分類名稱不可為空';
    $sql = "UPDATE `categories` SET `name`=? WHERE id = ?";
    $result = executeUpdate($conn, $sql, 'si', $categoryName, (int)$categoryId);
    if ($result['success'] === false || $result['affected_rows'] === 0) return '發生錯誤';
	}

	function handleDelete($conn) {
		verifyCsrfToken();
    $categoryId = getValue($_POST, 'id');
    if ($categoryId === '') return '發生錯誤';
		$conn->begin_transaction();
		$categoryId = (int)$categoryId;
		try {
	    // 將原 Category 的文章改成未分類
	    $sql = "UPDATE `posts` SET `category_id`= 0 WHERE category_id = ?";
	    $result = executeUpdate($conn, $sql, 'i', $categoryId);
	    if ($result['success'] === false) {
          throw new Exception('發生錯誤');
      }

			// 刪除 Category
	    $sql = "DELETE FROM `categories` WHERE id = ?";
	    $result = executeUpdate($conn, $sql, 'i', $categoryId);
	    if ($result['success'] === false) throw new Exception('發生錯誤');
      $conn->commit();
	  } catch (Exception $e) {
      $conn->rollback();
      return $e->getMessage();
	  }
	}

	// 處理刪除分類請求
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'delete') {
    $error = handleDelete($conn);
  }
  // 處理編輯分類請求
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] === 'edit') {
    $error = handleEdit($conn);
  }

	// 分頁計算
	$currentPage = (int)($_GET['page'] ?? 1);	
	$perPage = 3;
	$sql = "SELECT COUNT(id) as categoriesCount FROM `categories`";
	$result = $conn->query($sql);
	if (!$result) {
		$error = '發生錯誤';
	}
	$total = $result->fetch_assoc()['categoriesCount'];
	$totalPages = ceil($total / $perPage);
	$currentPage = min(max(1, $currentPage), $totalPages);  // 夾出合理的頁數區間
	$prevDisabled = $currentPage == 1 ? 'disabled' : '';
	$nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
	$offset = ($currentPage - 1) * $perPage;

	// 取得所有 categories 以及對應文章的數量
	$sql = "
		SELECT COUNT(p.id) as postsCount,
		c.name as categoryName,
		c.id as categoryId
		FROM `categories` as c
		LEFT JOIN `posts` as p
		on c.id = p.category_id
		GROUP BY c.id
		ORDER BY postsCount DESC
		LIMIT ? OFFSET ?
	";
	$result = executeQuery($conn, $sql, 'ii', $perPage, $offset);
	if (!$result) {
		$error = '發生錯誤';
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php
  $title = '後台首頁';
  $localCSS = [
    'css/dist/admin-categories.css',
  ];
  $extraCSS = [
  	'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Noto+Sans+TC:wght@400;500&display=swap'
  ];
  require_once('../template/head.php');
?>
  <body>
    <?php require_once('../template/admin/header.php'); ?>
      	<?php require_once('../template/admin/side.php'); ?>
        <main>
	        <div class="main-content__wrapper">
	        	<?php if ($error): ?>
							<div class="error-box">⚠ <span><?= escape($error) ?></span></div>
						<?php endif; ?>
	          <div class="btn btn-add">+ New Category</div>
	          <div class="title">All Categories</div>
	          <div class="categories">
	          	<?php while($row = $result->fetch_assoc()): ?>
		          		<div class="category">
		          			<div class="category__content">
			          			<div class="category__dot"></div>
			          			<div class="category__name"><?= escape($row['categoryName']) ?></div>
			          			<div class="category__count"><?= escape($row['postsCount']) ?> posts</div>
			          		</div>
			          		<div class="tools">
			          				<a class="btn-edit">Edit</a>
			          				<form method="POST" action="categories.php">
					          			<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
					          			<input type="hidden" name="type" value="delete" />
					          			<input type="hidden" name="id" value="<?= $row['categoryId'] ?>" />
			                    <button type="submit" class="btn-delete">Delete</button>
			                  </form>
			          		</div>
		          		</div>
	          	<?php endwhile; ?>
	        	</div>
	        </div>
					<?php require_once('../template/pagination.php'); ?>
        </main>
    <script src="../js/error.js"></script>
    <script>
    	const editingCategoryTemplate = `<form class="category edit" method="POST" action="categories.php">
  			<div class="category__dot"></div>
  			<input type="hidden" name="id" value="" />
  			<input type="hidden" name="type" value="edit" />
  			<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
  			<input class="category__name" name="name" value="" />
  			<div class="category__count"></div>
    		<div class="tools">
    				<button class="btn-save">Save</button>
						<a class="btn-cancel">Cancel</a>
    		</div>
    	</form>`
    	const noEditCategoryTemplate = `<div class="category">
				<div class="category__content">
	  			<div class="category__dot"></div>
	  			<div class="category__name"></div>
	  			<div class="category__count"></div>
	  		</div>
	  		<div class="tools">
	  				<a class="btn-edit">Edit</a>
	  				<form method="POST" action="categories.php">
	      			<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
	      			<input type="hidden" name="type" value="delete" />
	      			<input type="hidden" name="id" value="" />
	            <button type="submit" class="btn-delete">Delete</button>
	          </form>
	  		</div>
			</div>`
    	const editCategoryTemplate = `<a class="btn-edit">Edit</a>`
      document.querySelector('.categories').addEventListener('click', (e) => {
      	if (e.target.classList.contains('btn-edit')) {
	      	e.preventDefault()
	      	const categoryEle = e.target.closest('.category')
	      	const categoryId = categoryEle.querySelector('input[name="id"]').value
	      	const categoryName = categoryEle.querySelector('.category__name').innerText
	      	const postsCount = categoryEle.querySelector('.category__count').innerText

	    		const temp = document.createElement('div')
	    		temp.innerHTML = editingCategoryTemplate
					const newEle = temp.firstElementChild
					categoryEle.replaceWith(newEle)  // replaceWith 會回傳舊元素，newEle 是新的
					newEle.querySelector('input[name="id"]').value = categoryId
					newEle.querySelector('.category__name').value = categoryName
					newEle.querySelector('.category__count').innerText = postsCount
					newEle.querySelector('.category__name').focus()
      	}
      	if (e.target.classList.contains('btn-cancel')) {
	      	e.preventDefault()
	      	const categoryEle = e.target.closest('.category')
	      	const categoryId = categoryEle.querySelector('input[name="id"]').value
	      	const categoryName = categoryEle.querySelector('.category__name').value
	      	const postsCount = categoryEle.querySelector('.category__count').innerText

	    		const temp = document.createElement('div')
	    		temp.innerHTML = noEditCategoryTemplate
					const newEle = temp.firstElementChild
					categoryEle.replaceWith(newEle)  // replaceWith 會回傳舊元素，newEle 是新的
					newEle.querySelector('input[name="id"]').value = categoryId
					newEle.querySelector('.category__name').innerText = categoryName
					newEle.querySelector('.category__count').innerText = postsCount	
      	}
      })
    </script>
  </body>
</html>