<?php
	require_once('conn.php');
	require_once('utils.php');

  // Hint: Posts Count = 0 不顯示文章
  // 選出精選文章
  $sql = "SELECT Count(id) as postsCount FROM `posts` WHERE selected = 1";
  $result = $conn->query($sql);
  if (!$result) {
    $postsCount = 0;
  } else {
    $postsCount = $result->fetch_assoc()['postsCount'];
  }
  if ($postsCount != 0) {
    // 分頁邏輯
    $page = getValue($_GET, 'page');
    if ($page === '') {
      $page = 1;
    } else {
      $page = (int)$page;
      $page = max(1, $page);
    }
    $perPage = 5;
    $totalPages = ceil($postsCount / $perPage);
    $page = min($totalPages, $page);
    $offset = ($page - 1) * $perPage;
    // 選出精選文章
    $sql = "SELECT id, title, subTitle, created_at FROM `posts` WHERE selected = 1 ORDER BY created_at ASC LIMIT ? OFFSET ?";
    $result = executeQuery($conn, $sql, 'ii', $perPage, $offset);
    if (!$result) {
      $postsCount = 0;
    }
    $prevDisabled = $page == 1 ? 'disabled' : '';
    $nextDisabled = $page == $totalPages ? 'disabled' : '';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <?php
    $title = '首頁';
    $localCSS = [
      'css/dist/about.css',
    ];
    $extraCSS = [
    	'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap',
      'https://fonts.googleapis.com/css2?family=Special+Elite&display=swap'
    ];
    require_once('template/head.php');
  ?>
  <body>
    <div class="content-wrapper">
        <?php
          $active = 'About';
          require_once('template/nav.php');
        ?>
        <div class="typewriter">
          <h1 id="tw-heading">Hello</h1>
          <p  id="tw-sub">I am Pocyun. Nice to meet you!</p>
        </div>
        <div class="profile-image">
          <img src="images/avatar.jpg" />
        </div>
        <p class="profile-image__illus">my best friend make for me</p>
        <div class="intro">Two years ago, I was a software developer. I'm not sure what I am now — and I think that's okay.

        I'm 39.757793101 years old and live in <span class="strong">Taipei</span> with my family, but previously lived in Bray and Dublin. I <span class="strong">studied Geography</span> at National Taiwan University, which is less a discipline than a permission slip to be curious about everything: landforms, human migration, spatial data, why cities look the way they do. I took courses in Math, Animal Science, and Civil Engineering too, mostly because I couldn't stop myself.

        I travel a lot.
        My relationship with travel has shifted over the years. The sweeping landscapes still get me — but what I really want now is to sit in someone's home for a few days, watch how they move through their lives, and ask them what they're frustrated about. Those conversations are where things get interesting.

        This blog is where I try to make sense of all of it. More of my travel writing lives on <a target="_blank" href="https://medium.com/wangboching" style="text-decoration: underline;">Medium</a>.
        </div>
        <div class="work">
          <h1>Work</h1>
          <p>My career doesn't follow a straight line — and I've stopped pretending it should.

          Before my year in Ireland, I was a full-stack developer at a startup that touched almost every industry, with a particular focus on healthcare systems and virtual exhibition platforms. Before that, I spent time at Academia Sinica's Biodiversity Research Center, assembling mitochondrial and chloroplast DNA and writing popular science pieces on evolution.

          The through-line, if there is one: I like building things, understanding how systems work, and explaining complicated ideas to people who didn't ask for a lecture.

          Ireland, for what it's worth, also taught me how to bake bread. Still counts.</p>
          <div class="work-image">
            <img src="images/work.jpg" />
          </div>
          <p class="work-image__illus">It is always good to put a flower on my working table!</p>
          <div class="jobs">
            <div class="job">
              <div class="company">The Larder (Bakery)</div>
              <div class="job__title">Baker</div>
              <div class="job__duration">2025–2025</div>
            </div>
            <div class="job">
              <div class="company">CacDi</div>
              <div class="job__title">Full-end Developer</div>
              <div class="job__duration">2022–2024</div>
            </div>
            <div class="job">
              <div class="company">Academic Sinica</div>
              <div class="job__title">Research Assistant</div>
              <div class="job__duration">2020–2022</div>
            </div>
          </div>
        </div>
        <div class="elsewhere">
          <h1>Elsewhere</h1>
          <p>I mainly write on Medium, where I share whatever's on my mind — travel, scuba diving, marathons, and whatever else pulls my attention.

          You'll find me as Wangboching, Pocyun, or Bocyun across most platforms. I use my Chinese name instead of an English one. Fair warning: it's not the easiest thing to pronounce. 😅
          </p>
          <div class="social-medias">
            <div class="social-media">Medium:<a target="_blank" href="https://medium.com/wangboching">@wangpoching</a></div>
            <div class="social-media">Instagram:<a target="_blank" href="https://www.instagram.com/wangpoching/">@wangpoching</a></div>
            <div class="social-media">Meta:<a target="_blank" href="https://www.facebook.com/profile.php?id=100004023007435">@Peter Wang</a></div>
          </div>
        </div>
        <div class="website">
          <h1>Website</h1>
          <p>I've been publishing here since 2021 when I hosted it on a Razer BLADE 2070 in my house. Everything is designed and coded myself — no themes or templates here. Although I redesign the whole style again in 2026 but I still use razer BLADE 2070 with the Sublime code editor, gitbash terminal and Arc browser. The only difference is my previous Razer BLADE 2070 is ran over by a bus.

          This website is built with php, and hosted on AWS. I use few nowday front-end tools because I intend to keep it old school fashion.</p>
        </div>
    </div>
    <?php require_once('template/footer.php'); ?>
  </body>
  <script>
    const twHeading = document.getElementById('tw-heading')
    const twSub = document.getElementById('tw-sub')
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // 進入畫面
          twHeading.classList.add('go')
        } else {
          // 離開畫面
          twHeading.classList.remove('go')
          twSub.classList.remove('go')
        }
      })
    }, {
      threshold: 1
    })
    observer.observe(twHeading)

    twHeading.addEventListener('animationend', function(e) {
      if (e.animationName === 'typing-h') {
        setTimeout(function() {
          twSub.classList.add('go')
        }, 200)
      }
    })
  </script>
</html>