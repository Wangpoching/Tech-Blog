<?php
  require('../conn.php');
  require('../utils.php');
  session_start();
  // 檢查是否是 Admin
  isAdmin();

  function handleEditPost($conn, $postId, $post) {
      verifyCsrfToken();
      $title      = getValue($_POST, 'title');
      $subTitle   = getValue($_POST, 'subTitle');
      $content    = getValue($_POST, 'content');
      $categoryId = (int)getValue($_POST, 'category_id'); // 沒選擇的話就是未分類: 0
      $status     = getValue($_POST, 'status');
      $tags       = $_POST['tags'] ?? [];

      if ($title === '')   return '標題不可為空';
      if ($content === '') return '內文不可為空';
      if ($status !== DRAFT_POST_STATUS && $status !== PUBLISHED_POST_STATUS) return '發布狀態不合法';

      // 儲存封面圖片
      $coverImage = (int)$_POST['remove_cover'] === 1 ? null : $post['cover_image'];
      try {
          $newCoverImage = handleUpload('coverImage', UPLOAD_COVERS_PATH);
          // 有上傳新圖，順便刪舊圖
          if ($newCoverImage != null) {
            if ($post['cover_image']) {
                unlink(UPLOAD_COVERS_PATH . $post['cover_image']);
            }
            $coverImage = $newCoverImage;
          }
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
          $sql = "
            UPDATE `posts` SET 
            title = ?, subTitle = ?, content = ?,
            category_id = ?, status = ?, cover_image = ?
            WHERE id = ?
          ";
          $result = executeUpdate($conn, $sql, 'sssissi',
            $title,
            $subTitle,
            $content,
            $categoryId,
            $status,
            $coverImage,
            $postId
          );
          if (!$result['success']) throw new Exception($result['errno']);

          // 儲存 tags
          $sql = "DELETE FROM `post_tags` WHERE post_id = ?";
          $deleteResult = executeUpdate($conn, $sql, 'i', $postId);
          if (!$deleteResult['success']) throw new Exception($result['errno']);
          foreach ($tags as $tag) {
            savePostTag($conn, $postId, $tag);
          }

          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          return $e->getMessage();
      }

      header('Location: index.php');
      exit();
  }

  // 沒有給文章 Id 返回
  $postId = getValue($_GET, 'id');
  if ($postId === '') {
    header('Location: index.php');
    exit();
  }
  $postId = (int)$postId;

  // 取得所有類別以及文章內容
  $categories = getAllCategories();
  $sql = "SELECT posts.title, posts.subTitle, posts.content, posts.status, posts.cover_image, posts.category_id, GROUP_CONCAT(tags.name) as tags FROM `posts`
    LEFT JOIN `categories` as c on posts.category_id = c.id
    LEFT JOIN `post_tags` as pt on posts.id = pt.post_id
    LEFT JOIN `tags` on pt.tag_id = tags.id
    WHERE posts.id = ?
    GROUP BY posts.id
  ";
  $result = executeQuery($conn, $sql, 'i', $postId);
  // 找不到文章
  if (!$result || $result->num_rows === 0) {
      header('Location: index.php');
      exit();    
  }
  $post = $result->fetch_assoc();
  $tags = $post['tags'] ? explode(',', $post['tags']) : [];
  $error = '';
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = handleEditPost($conn, $postId, $post);
  }
?>
<!DOCTYPE html>
<html lang="en">
<?php
  $title = '後台編輯文章';
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
    <form method="POST" action="edit.php?id=<?= escape($postId) ?>" class="add-post-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
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
            <input id="title" type="text" name="title" class="input-title" value="<?= escape($post['title']) ?>" />
          </div>
          <div class="field">
            <label for="subTitle" class="field-label">SUBTITLE</label>
            <input id="subTitle" type="text" name="subTitle" value="<?= escape($post['subTitle']) ?>" />
          </div>
          <div class="field">
            <label class="field-label">Cover Image</label>
            <label for="coverImage" >  <!-- 用 label 包住，點任何地方都會觸發 -->
              <?php
                $imageStorageRoute = UPLOAD_COVERS_URL . escape($post['cover_image']);
                $bgStyle = $post['cover_image'] 
                  ? "background: url('" . $imageStorageRoute . "') center/cover no-repeat;" 
                  : '';
                $className = $post['cover_image'] ? 'has-image' : '';
              ?>
              <div class="cover-upload <?= $className ?>" style="<?= $bgStyle ?>">
                <div class="cover-upload-wrapper">
                  <div class="cover-icon">🖼️</div>
                  <div class="cover-text">Drop image here or click to upload</div>
                  <div class="cover-hint">JPG, PNG, WebP · Max 2MB</div>
                </div>
                <div class="cover-remove <?= $post['cover_image'] ? '' : 'hidden' ?>" id="cover-remove">✕</div>
                <input type="hidden" name="remove_cover" id="remove-cover" value="0">
              </div>
            </label>
            <input type="file" id="coverImage" name="coverImage" accept="image/jpeg,image/png,image/webp" style="display:none">
          </div>
          <div class="field">
            <label class="field-label content-label">Content</label>
            <textarea id="content" name="content"><?= escape($post['content']) ?></textarea>
          </div>
        </main>
        <side>
          <div class="field">
            <label class="field-label">STATUS</label>
            <div class="post-status"><?= $post['status'] === 'published' ? 'Published' : 'Draft' ?></div>
          </div>
          <div class="side-divider"></div>
          <div class="field">
            <label class="field-label content-label">Category</label>
            <select name="category_id" >
              <?php if ($categories): ?>
                <?php while ($row = $categories->fetch_assoc()): ?>
                  <option value="<?= $row['id']; ?>" <?= $row['id'] == $post['category_id'] ? 'selected' : '' ?>><?= escape($row['name']) ?></option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="field">
            <label class="field-label content-label">Tags</label>
            <div class="tags-wrap" id="tags-wrap">
              <!-- chip 跟 hidden input 都動態新增進來 -->
              <?php foreach($tags as $tag): ?>
                <div class="tag-chip"><?= escape($tag) ?><span class="tag-chip-x" data-tag="<?= escape($tag) ?>">✕</span></div>
              <?php endforeach; ?>
              <input type="text" id="tag-input" placeholder="Add a tag...">
              <?php foreach($tags as $tag): ?>
                <input type="hidden" name="tags[]" value="<?= escape($tag) ?>" data-tag-hidden="<?= escape($tag) ?>" />
              <?php endforeach; ?>
            </div>
            <div class="tags-hint">Press enter to add a tag</div>
          </div>
          <div class="side-divider"></div>
          <div class="field">
            <label class="field-label">DANGER ZONE</label>
            <button class="danger-btn" formaction="delete.php?id=<?= escape($postId) ?>">Delete this post</button>
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>" />
          </div>
        </side>
    </form>
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
    <script src="../js/easyMDE.js"></script>
    <script src="../js/error.js"></script>
    <script>
      // ③ Tags
      const existingTags = <?= json_encode($tags) ?>;
      const tagInput = document.getElementById('tag-input');
      const tagsWrap = document.getElementById('tags-wrap');
      const tags = new Set(existingTags);

      tagInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const tag = e.target.value.trim();
        if (!tag || tags.has(tag)) {
          tagInput.value = '';
          return;
        }
        tags.add(tag);
        const chip = document.createElement('div');
        chip.className = 'tag-chip';
        chip.textContent = tag;
        const chipX = document.createElement('span');
        chipX.className = 'tag-chip-x';
        chipX.dataset.tag = tag;
        chipX.textContent = '✕';
        chip.appendChild(chipX);
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tags[]';
        hiddenInput.value = tag;
        hiddenInput.dataset.tagHidden = tag;
        tagsWrap.insertBefore(chip, tagInput);
        tagsWrap.appendChild(hiddenInput);
        tagInput.value = '';
      });

      tagsWrap.addEventListener('click', function(e) {
        if (!e.target.classList.contains('tag-chip-x')) return;
        const tagName = e.target.dataset.tag;
        e.target.closest('.tag-chip').remove();
        document.querySelector(`input[data-tag-hidden="${CSS.escape(tagName)}"]`).remove();
        tags.delete(tagName);
      });

      //  處理封面圖
      let removeBg = false
      const removeCoverBtn = document.querySelector('.cover-remove')
      function removePreview(e) {
        if (!removeCoverBtn) return
        if (removeBg === false) {
          // 標記為刪除原始封面圖
          document.querySelector('#remove-cover').value = 1
          removeBg = true
        }
        // 清掉 file input 的值
        document.getElementById('coverImage').value = ''
        e.target.classList.add('hidden')
        e.target.closest('.cover-upload').classList.remove('has-image')
        e.target.closest('.cover-upload').style.backgroundImage = 'none'        
      }

      // 刪除封面圖
      removeCoverBtn.addEventListener('click', (e) => {
        e.preventDefault()
        removePreview(e)
      })

      // 及時預覽 + 上傳新圖
      const coverInput = document.getElementById('coverImage');

      coverInput.addEventListener('change', function(e) {
        const file = e.target.files[0]
        if (!file) return

        const reader = new FileReader()
        reader.onload = function(e) {
          // 把圖片 url 設成背景
          const coverUpload = document.querySelector('.cover-upload')
          coverUpload.classList.add('has-image')
          // 把 base64 塞到背景裡面
          coverUpload.style.backgroundImage = `url(${e.target.result})`
          coverUpload.style.backgroundSize = 'cover'
          coverUpload.style.backgroundPosition = 'center'

          // 顯示 remove 按鈕
          const removeBtn = document.getElementById('cover-remove')
          if (removeBtn) removeBtn.classList.remove('hidden')
        }

        reader.readAsDataURL(file)
      });
    </script>
  </body>
</html>
