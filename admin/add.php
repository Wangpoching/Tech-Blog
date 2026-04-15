<?php
  require('../conn.php');
  require('../utils.php');
  session_start();
  // 檢查是否是 Admin
  isAdmin();

  function handleAddPost($conn) {
      verifyCsrfToken();
      $title      = getValue($_POST, 'title');
      $subTitle   = getValue($_POST, 'subTitle');
      $content    = getValue($_POST, 'content');
      $categoryId = (int)getValue($_POST, 'category_id');
      $status     = getValue($_POST, 'status');
      $tags       = $_POST['tags'] ?? [];

      if ($title === '')  return '標題不可為空';
      if ($content === '') return '內文不可為空';
      if ($status !== DRAFT_POST_STATUS && $status !== PUBLISHED_POST_STATUS) {
          return '發布狀態不合法';
      }

      $coverImage = null;
      try {
          $coverImage = handleUpload('coverImage', UPLOAD_COVERS_PATH);
      } catch (RuntimeException $e) {
          return $e->getMessage();
      }

      $conn->begin_transaction();
      try {
          // 檢查 category 存不存在
          $sql = "SELECT id FROM `categories` WHERE id = ?";
          $result = executeQuery($conn, $sql, 'i', $categoryId);
          if (!$result || $result->num_rows === 0) throw new Exception('分類不存在');

          // 儲存文章
          $sql = "INSERT INTO `posts` 
                  (title, subTitle, content, user_id, category_id, status, cover_image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
          $result = executeUpdate($conn, $sql, 'sssiiss',
              $title,
              $subTitle,
              $content,
              $_SESSION['admin_id'],
              $categoryId,
              $status,
              $coverImage
          );
          if (!$result['success']) throw new Exception($result['errno']);
          $postId = (int)$result['insert_id'];

          // 儲存 tags
          foreach ($tags as $tag) {
            savePostTag($conn, $postId, $tag);
          }

          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          return $e->getMessage();
      }

      header('Location: ../index.php');
      exit();
  }

  $error = '';
  $categories = getAllCategories();
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = handleAddPost($conn);
  }
?>
<!DOCTYPE html>
<html lang="en">
<?php
  $title = '後台新增文章';
  $localCSS = [
    'css/dist/admin-add.css',
  ];
  $extraCSS = [
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Noto+Sans+TC:wght@400;500&display=swap',
    'https://unpkg.com/easymde/dist/easymde.min.css'

  ];
  require_once('../template/head.php');
?>
  <body>
    <form method="POST" action="add.php" class="add-post-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>" />
        <header>
          <div class="site-name">Pocyun Wang<span>admin</span></div>
          <div class="btns">
            <a href="index.php" class="btn btn-cancel">Cancel</a>
            <button type="submit" name="status" value="<?= DRAFT_POST_STATUS ?>" class="btn btn-save-draft">Save draft</button>
            <button type="submit" name="status" value="<?= PUBLISHED_POST_STATUS ?>" class="btn btn-publish">Publish</button>
          </div>
        </header>
        <main>
          <?php if ($error): ?>
            <div class="error-box">⚠ <span><?= escape($error) ?></span></div>
          <?php endif; ?>
          <div class="field">
            <label for="title" class="field-label">TITLE</label>
            <input id="title" type="text" name="title" class="input-title" placeholder="Post Title..." />
          </div>
          <div class="field">
            <label for="subTitle" class="field-label">SUBTITLE</label>
            <input id="subTitle" type="text" name="subTitle" placeholder="A short description..." />
          </div>
          <div class="field">
            <label class="field-label">Cover Image</label>
            <label for="coverImage">  <!-- 用 label 包住，點任何地方都會觸發 -->
              <div class="cover-upload">
                <div class="cover-upload-wrapper">
                  <div class="cover-icon">🖼️</div>
                  <div class="cover-text">Drop image here or click to upload</div>
                  <div class="cover-hint">JPG, PNG, WebP · Max 2MB</div>
                </div>
                <div class="cover-remove" id="cover-remove">✕</div>
              </div>
            </label>
            <input type="file" id="coverImage" name="coverImage" accept="image/jpeg,image/png,image/webp" style="display:none">
          </div>
          <div class="field">
            <label class="field-label content-label">Content</label>
            <textarea id="content" name="content"></textarea>
          </div>
        </main>
        <side>
          <div class="field">
            <label class="field-label content-label">Category</label>
            <select name="category_id" >
              <option value="" disabled selected>Select category</option>
              <?php if ($categories): ?>
                <?php while ($row = $categories->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="field">
            <label class="field-label content-label">Tags</label>
            <div class="tags-wrap" id="tags-wrap">
              <!-- chip 跟 hidden input 都動態新增進來 -->
              <input type="text" id="tag-input" placeholder="Add a tag...">
            </div>
            <div class="tags-hint">Press enter to add a tag</div>
          </div>
        </side>
    </form>
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="../js/easyMde.js"></script>
    <script src="../js/error.js"></script>
    <script>
      // ③ 新增 Tag Chip
      const tagInput = document.getElementById('tag-input')
      const tagsWrap = document.getElementById('tags-wrap')
      const tags = new Set()

      tagInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return
        e.preventDefault()
        const tag = e.target.value.trim()
        // 如果是空的或已經打過的不新增
        if (!tag || tags.has(tag)) {
          tagInput.value = ''
          return
        }
        tags.add(tag);
        const chip = document.createElement('div')
        chip.className = 'tag-chip'
        chip.textContent = tag
        const chipX = document.createElement('span')
        chipX.className = 'tag-chip-x'
        chipX.dataset.tag = tag
        chipX.textContent = '✕'
        chip.appendChild(chipX)
        const hiddenInput = document.createElement('input')
        hiddenInput.type = 'hidden'
        hiddenInput.name = 'tags[]'
        hiddenInput.value = tag
        hiddenInput.dataset.tagHidden = tag
        // tag-chip 放在輸入框之前
        tagsWrap.insertBefore(chip, tagInput)
        // 隱藏的 input 放在最後面
        tagsWrap.appendChild(hiddenInput)
        tagInput.value = ''
      })

      // 刪除 tag
      tagsWrap.addEventListener('click', function(e) {
        if (!e.target.classList.contains('tag-chip-x')) return
        const tagName = e.target.dataset.tag
        e.target.closest('.tag-chip').remove()
        document.querySelector(`input[data-tag-hidden="${CSS.escape(tagName)}"]`).remove()
        tags.delete(tagName)
      })

      //  處理封面圖
      const removeCoverBtn = document.querySelector('.cover-remove')
      function removePreview(e) {
        if (!removeCoverBtn) return
        // 清掉 file input 的值
        document.getElementById('coverImage').value = ''
        e.target.closest('.cover-upload').classList.remove('has-image')
        e.target.closest('.cover-upload').style.backgroundImage = 'none'
      }

      // 刪除封面圖
      removeCoverBtn.addEventListener('click', (e) => {
        e.preventDefault()
        removePreview(e)
      })

      // 及時預覽 + 上傳新圖
      const coverInput = document.getElementById('coverImage')

      coverInput.addEventListener('change', function(e) {
        const file = e.target.files[0]
        if (!file) return

        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = function(e) {
          // 把圖片 url 設成背景
          const coverUpload = document.querySelector('.cover-upload')
          coverUpload.classList.add('has-image')
          // 把 base64 塞到背景裡面
          coverUpload.style.backgroundImage = `url(${e.target.result})`
          coverUpload.style.backgroundSize = 'cover'
          coverUpload.style.backgroundPosition = 'center'
        }
      })
    </script>
  </body>
</html>
