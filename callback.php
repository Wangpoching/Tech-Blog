<?php
	require_once('configs/config.php');
	require_once('utils.php');
	session_start();

	$code = getValue($_GET, 'code');
	if ($code === '') {
		header('Location: index.php');
		exit();
	}
	$state = getValue($_GET, 'state');
	if ($state === '') {
		$state = 'index.php';
	}

	// 換 userToken
	$ch = curl_init(BOARD_TOKEN_URL);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
	    'code'      => $code,
	    'appKey'    => BOARD_APP_KEY,
	    'appSecret' => BOARD_APP_SECRET
	]));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 把結果存到變數，不直接輸出

	$response = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($response, true); // 將 response 轉成陣列
	if (!$data['ok']) {
		$_SESSION['flash'] = $data['message'];
		header('Location: ' . $state);
		exit();
	} else {
		$_SESSION['userToken'] = $data['token'];
		header('Location: ' . $state);
		exit();
	}
?>