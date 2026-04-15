<?php
	require_once('../conn.php');
	require_once('../utils.php');
	// admin 檢查
	session_start();
	isAdmin();

	// CSRF Token 檢查
	verifyCsrfToken();

	// 訪問方法限制
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		header('Location: index.php');
		exit();    
	}

	// 搜尋文章是否存在
	if (getValue($_GET, 'id') === '') {
		header('Location: index.php');
		exit();
	}
	$postId = (int)getValue($_GET, 'id');
	$sql = "SELECT * FROM `posts` WHERE id = ?";
	$result = executeQuery($conn, $sql, 'i', $postId);
	if (!$result) {
		header('Location: index.php');
		exit();
	}
	$row = $result->fetch_assoc();
	$image_cover = $row['cover_image'] ?? null;

	// 刪除文章
	$conn->begin_transaction();
		try {
			// Step1: 刪除 post_tags
			$sql = 'DELETE FROM `post_tags` WHERE post_id = ?';
			$result = executeUpdate($conn, $sql, 'i', $postId);
			if (!$result['success']) throw new Exception($result['errno']);

			// Step2: 刪除 post
			$sql = 'DELETE FROM `posts` WHERE id = ?';
			$result = executeUpdate($conn, $sql, 'i', $postId);
			if (!$result['success']) throw new Exception($result['errno']);

			$conn->commit();
		} catch (Exception $e) {
			$conn->rollback();
			header('Location: index.php');
			exit();			
		}
	// Step3: 刪除封面照片
	if ($image_cover) unlink(UPLOAD_COVERS_PATH . $image_cover);
	header('Location: index.php');
	exit();
?>