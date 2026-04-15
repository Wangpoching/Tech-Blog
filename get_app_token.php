<?php
		require_once('conn.php');
		require_once('utils.php');
		require_once('configs/config.php');
		session_start();

		$appToken = getValue($_SESSION, 'appToken');
		if ($appToken === '' || isTokenExpired($appToken)) {
				$ch = curl_init(BOARD_APP_TOKEN_URL);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
						'appKey'    => BOARD_APP_KEY,
						'appSecret' => BOARD_APP_SECRET
				]));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close($ch);

				$data = json_decode($response, true);
				if ($data['ok']) {
						$appToken = $data['token'];
						$_SESSION['appToken'] = $appToken;
				}
		}

		header('Content-Type: application/json');
		echo json_encode(['token' => $appToken]);
?>