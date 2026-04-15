<?php
	require_once('utils.php');
	require_once('conn.php');
	session_start();
	isAdmin();
	
	header('Content-Type: application/json');

	// 只接受 POST
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(405); // Method not allowed
		echo json_encode(['error' => '不允許的請求方式']);
		exit();
	}

	// 呼叫 Handle Function
	$imageName = null;
	try {
		$imageName = handleUpload('image', UPLOAD_CONTENT_PATH);
		if (!$imageName) {
			echo json_encode(['error' => 'noFileGiven']);
			exit();
		}

		echo json_encode([
			'data' => [
				'filePath' => UPLOAD_CONTENT_URL . $imageName
			]
		]);
	} catch (RuntimeException $e) {
		$msg = $e->getMessage();
		if ($msg === '檔案超過大小限制') {
			http_response_code(413);
			echo json_encode(['error' => 'fileTooLarge']);
		} elseif ($msg === '只接受 JPG、PNG、WebP') {
			http_response_code(415);
			echo json_encode(['error' => 'typeNotAllowed']);
		} else {
	        http_response_code(400);
	        echo json_encode(['error' => $msg]);
		}
	}
?>