<?php
    define('DRAFT_POST_STATUS', 'draft');
    define('PUBLISHED_POST_STATUS', 'published');
    $isProd = ($_SERVER['HTTP_HOST'] ?? '') === 'tech-blog.bocyun.tw';

    define('BASE_URL', $isProd ? '/' : '/phpBased_blog/');
    // 實體路徑（給 PHP 存檔用）
    define('UPLOAD_COVERS_PATH', $_SERVER['DOCUMENT_ROOT'] . ($isProd ? '' : '/phpBased_blog') . '/uploads/covers/');
    define('UPLOAD_CONTENT_PATH', $_SERVER['DOCUMENT_ROOT'] . ($isProd ? '' : '/phpBased_blog') . '/uploads/content/');
    // 網頁路徑（給瀏覽器存取用）
    define('UPLOAD_COVERS_URL', BASE_URL . 'uploads/covers/');
    define('UPLOAD_CONTENT_URL', BASE_URL . 'uploads/content/');
    define('DOMAIN', $isProd ? 'https://tech-blog.bocyun.tw' : 'http://localhost');

    function executeQuery($conn, $sql, $types, ...$params) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) return null;
        return $stmt->get_result();
    }

    function executeUpdate($conn, $sql, $types, ...$params) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return [
            'success' => false,
            'errno' => 0,
            'affected_rows' => 0,
            'insert_id' => null
        ];
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'errno' => $conn->errno,
                'affected_rows' => 0,
                'insert_id' => null
            ];
        }
        return [
            'success' => true,
            'errno' => 0,
            'affected_rows' => $stmt->affected_rows,
            'insert_id' => $conn->insert_id // 回傳第一筆新增的 id, 更新或刪除就沒有
        ];
    }

    // 欄位沒設置或是沒給值都回傳 ''
    function getValue($arr, $key) {
        return trim($arr[$key] ?? '');
    }

    function getUserFromSession($adminId) {
        global $conn;
        $sql = "SELECT *
            FROM `users` 
            WHERE id = ?
        ";
        $result = executeQuery($conn, $sql, 'i', $adminId);
        if (!$result) return null;
        if ($result->num_rows === 0) return null;
        $row = $result->fetch_assoc();
        return $row;
    }

    function isAdmin() {
        // 沒登入回登入頁面
        $adminId = getValue($_SESSION, 'admin_id');
        if ($adminId === '') {
            header('Location:' . BASE_URL . 'admin/login.php');
            exit();
        }
        // session 與資料庫的資料不一致, 需要清除 cookie
        $row = getUserFromSession($adminId);
        if (!$row) {
            header('Location:' . BASE_URL . 'admin/logout.php');
            exit();
        }
        return true;
    }

    function handleUpload($fileKey, $uploadDir) {
        // 檢查上傳是否有錯誤
        if (!isset($_FILES[$fileKey]['error']) || 
            is_array($_FILES[$fileKey]['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }

        // 沒上傳檔案直接回傳 null（封面圖是選填的）
        if ($_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        switch ($_FILES[$fileKey]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('檔案超過大小限制');
            default:
                throw new RuntimeException('上傳失敗');
        }

        // 檢查大小
        if ($_FILES[$fileKey]['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('檔案不能超過 2MB');
        }

        // 檢查 MIME type
        // 建立一個 finfo 物件，專門用來讀 MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $allowedTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif'
        ];
        if (false === $ext = array_search(
            // 呼叫 file() 方法，傳入檔案路徑, 他會利用檔案的內容決定真實的副檔名
            // tmp_name 是站存檔的位置
            $finfo->file($_FILES[$fileKey]['tmp_name']),
            $allowedTypes,
            true
        )) {
            throw new RuntimeException('只接受 JPG、PNG、WebP、GIF');
        }

        // 移動檔案
        $fileName = sha1_file($_FILES[$fileKey]['tmp_name']) . '.' . $ext;
        $destination = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destination)) {
            throw new RuntimeException('檔案儲存失敗');
        }

        return $fileName; // 回傳檔名，存進資料庫
    }

    function getAllCategories() {
        global $conn;
        $sql = "SELECT id, name FROM `categories` ORDER BY id ASC";
        $result = $conn->query($sql);
        if (!$result) return null;
        return $result;
    }

    function generateCsrfToken() {
        // 同一位使用者的 csrf token 使用同一組
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    function verifyCsrfToken() {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('Location:' . BASE_URL . 'admin/login.php');
            exit();
        }
    }

    /* 單雙引號都轉譯 */
    function escape($str) {
        return htmlspecialchars($str, ENT_QUOTES);
    }

    function savePostTag($conn, $postId, $tag) {
      $tag = trim($tag);
      if ($tag === '') return;

      $sql = "SELECT id FROM `tags` WHERE name = ?";
      $result = executeQuery($conn, $sql, 's', $tag);
      if (!$result) throw new Exception('儲存 tag 失敗');

      // 如果 tag 還沒有被創建過要先新增 tag
      if ($result->num_rows === 0) {
          $sql = "INSERT INTO `tags` (name) VALUES (?)";
          $result = executeUpdate($conn, $sql, 's', $tag);
          if (!$result['success'] || $result['affected_rows'] === 0) throw new Exception('儲存 tag 失敗');
          $tagId = $result['insert_id'];
      } else {
          $tagId = $result->fetch_assoc()['id'];
      }

      $sql = "INSERT INTO `post_tags` (post_id, tag_id) VALUES (?, ?)";
      $result = executeUpdate($conn, $sql, 'ii', $postId, $tagId);
      if (!$result['success'] || $result['affected_rows'] === 0) throw new Exception('儲存 tag 失敗');        
    }

    function isTokenExpired($token) {
      // JWT 分三段：header.payload.signature
      $parts = explode('.', $token);
      if (count($parts) !== 3) return true;
      
      // 解碼 payload
      $payload = json_decode(base64_decode($parts[1]), true);
      if (!$payload || !isset($payload['exp'])) return true;
      
      // 比較過期時間
      return $payload['exp'] < time();
    }
?>