<?php
	mysqli_report(MYSQLI_REPORT_OFF); // mysql 的錯誤不會拋出, 要自己檢查

	$servername = 'localhost';
	$username = 'root';
	$password = '';
	$dbname = 'phpbased_blog';
	$conn = new mysqli($servername, $username, $password, $dbname);

	if (!empty($conn->connect_error)) {
		die('資料庫連線錯誤:' . $conn->connect_error);
	}

	$conn->query('SET NAMES UTF8');
	$conn->query('SET time_zone = "+8:00"');	
?>