<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Color Palette — Ppcyun Blog</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      /* Accent */
      --accent:        #d97706;
      --accent-light:  #fffbeb;
      --accent-border: #fcd34d;
      --accent-text:   #b45309;
      --accent-dark:   #92400e;
      --accent-blue:   #5b8dee;
      /* Neutral */
      --bg:            #f8f7f4;
      --bg-sidebar:    #f0ede8;
      --bg-hover:      #eee6d8;
      --bg-input:      #fafaf8;
      --border:        #e8e6e0;
      --text-hint:     #c0bdb8;
      --text-muted:    #8a8880;
      --text-body:     #3a3830;
      --text-primary:  #1a1916;
      /* Semantic */
      --success-bg:    #e4f0e4;
      --success:       #3a7a3a;
      --danger-bg:     #fdf0f0;
      --danger:        #e05050;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text-primary);
      padding: 48px 40px;
      min-height: 100vh;
    }

    .page-title {
      font-size: 11px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.12em;
      margin-bottom: 8px;
    }

    .page-heading {
      font-size: 28px;
      font-weight: 500;
      color: var(--text-primary);
      letter-spacing: -0.03em;
      margin-bottom: 48px;
    }

    .section { margin-bottom: 48px; }

    .section-label {
      font-size: 10px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.12em;
      margin-bottom: 16px;
      padding-bottom: 10px;
      border-bottom: 0.5px solid var(--border);
    }

    .swatches {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .swatch {
      width: 140px;
      border-radius: 10px;
      overflow: hidden;
      border: 0.5px solid rgba(0,0,0,0.06);
    }

    .swatch-color { height: 72px; }

    .swatch-info {
      padding: 10px 12px;
      background: white;
    }

    .swatch-name {
      font-size: 12px;
      font-weight: 500;
      color: var(--text-primary);
      margin-bottom: 2px;
    }

    .swatch-var {
      font-size: 10px;
      color: var(--text-muted);
      font-family: monospace;
      margin-bottom: 2px;
    }

    .swatch-hex {
      font-size: 10px;
      color: var(--text-hint);
      font-family: monospace;
    }

    /* Typography section */
    .type-section { margin-bottom: 48px; }

    .type-row {
      display: flex;
      align-items: baseline;
      gap: 24px;
      padding: 14px 0;
      border-bottom: 0.5px solid var(--border);
    }

    .type-meta {
      width: 200px;
      flex-shrink: 0;
    }

    .type-label {
      font-size: 10px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 3px;
    }

    .type-spec {
      font-size: 11px;
      color: var(--text-hint);
      font-family: monospace;
    }

    .type-sample { color: var(--text-primary); }
  </style>
</head>
<body>

  <div class="page-title">Design System</div>
  <div class="page-heading">Color Palette</div>

  <!-- ACCENT -->
  <div class="section">
    <div class="section-label">Accent — 暖橘</div>
    <div class="swatches">
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent-light)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent Light</div>
          <div class="swatch-var">--accent-light</div>
          <div class="swatch-hex">#fffbeb</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent-border)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent Border</div>
          <div class="swatch-var">--accent-border</div>
          <div class="swatch-hex">#fcd34d</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent</div>
          <div class="swatch-var">--accent</div>
          <div class="swatch-hex">#d97706</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent-text)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent Text</div>
          <div class="swatch-var">--accent-text</div>
          <div class="swatch-hex">#b45309</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent-dark)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent Dark</div>
          <div class="swatch-var">--accent-dark</div>
          <div class="swatch-hex">#92400e</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--accent-blue)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Accent Blue</div>
          <div class="swatch-var">--accent-blue</div>
          <div class="swatch-hex">#5b8dee</div>
        </div>
      </div>
    </div>
  </div>

  <!-- NEUTRAL -->
  <div class="section">
    <div class="section-label">Neutral — 黑白灰</div>
    <div class="swatches">
      <div class="swatch">
        <div class="swatch-color" style="background: var(--bg); border-bottom: 0.5px solid var(--border);"></div>
        <div class="swatch-info">
          <div class="swatch-name">BG</div>
          <div class="swatch-var">--bg</div>
          <div class="swatch-hex">#f8f7f4</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--bg-sidebar)"></div>
        <div class="swatch-info">
          <div class="swatch-name">BG Sidebar</div>
          <div class="swatch-var">--bg-sidebar</div>
          <div class="swatch-hex">#f0ede8</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--bg-hover)"></div>
        <div class="swatch-info">
          <div class="swatch-name">BG Hover</div>
          <div class="swatch-var">--bg-hover</div>
          <div class="swatch-hex">#eee6d8</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--bg-input); border-bottom: 0.5px solid var(--border);"></div>
        <div class="swatch-info">
          <div class="swatch-name">BG Input</div>
          <div class="swatch-var">--bg-input</div>
          <div class="swatch-hex">#fafaf8</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--border)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Border</div>
          <div class="swatch-var">--border</div>
          <div class="swatch-hex">#e8e6e0</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--text-hint)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Text Hint</div>
          <div class="swatch-var">--text-hint</div>
          <div class="swatch-hex">#c0bdb8</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--text-muted)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Text Muted</div>
          <div class="swatch-var">--text-muted</div>
          <div class="swatch-hex">#8a8880</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--text-body)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Text Body</div>
          <div class="swatch-var">--text-body</div>
          <div class="swatch-hex">#3a3830</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--text-primary)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Text Primary</div>
          <div class="swatch-var">--text-primary</div>
          <div class="swatch-hex">#1a1916</div>
        </div>
      </div>
    </div>
  </div>

  <!-- SEMANTIC -->
  <div class="section">
    <div class="section-label">Semantic — 狀態色</div>
    <div class="swatches">
      <div class="swatch">
        <div class="swatch-color" style="background: var(--success-bg)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Success BG</div>
          <div class="swatch-var">--success-bg</div>
          <div class="swatch-hex">#e4f0e4</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--success)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Success</div>
          <div class="swatch-var">--success</div>
          <div class="swatch-hex">#3a7a3a</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--danger-bg)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Danger BG</div>
          <div class="swatch-var">--danger-bg</div>
          <div class="swatch-hex">#fdf0f0</div>
        </div>
      </div>
      <div class="swatch">
        <div class="swatch-color" style="background: var(--danger)"></div>
        <div class="swatch-info">
          <div class="swatch-name">Danger</div>
          <div class="swatch-var">--danger</div>
          <div class="swatch-hex">#e05050</div>
        </div>
      </div>
    </div>
  </div>

  <!-- TYPOGRAPHY -->
  <div class="section">
    <div class="section-label">Typography — 字體大小</div>
    <div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Page Heading</div>
          <div class="type-spec">28px / 500 / -0.03em</div>
        </div>
        <div class="type-sample" style="font-size:28px;font-weight:500;letter-spacing:-0.03em">Welcome Back</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Section Title</div>
          <div class="type-spec">22px / 500 / -0.03em</div>
        </div>
        <div class="type-sample" style="font-size:22px;font-weight:500;letter-spacing:-0.03em">All Posts</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Post Title (editor)</div>
          <div class="type-spec">24px / 500 / -0.03em</div>
        </div>
        <div class="type-sample" style="font-size:24px;font-weight:500;letter-spacing:-0.03em">Post title…</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Important Article Title</div>
          <div class="type-spec">20px / 500</div>
        </div>
        <div class="type-sample" style="font-size:20px;font-weight:500">You are the best</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Article Title</div>
          <div class="type-spec">15px / 500</div>
        </div>
        <div class="type-sample" style="font-size:15px;font-weight:500">Understanding the JavaScript Event Loop</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Body</div>
          <div class="type-spec">15px / 400 / line-height 1.85</div>
        </div>
        <div class="type-sample" style="font-size:15px;color:var(--text-body);line-height:1.85">From call stacks to web APIs — finally making sense of async JavaScript.</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Label / Meta</div>
          <div class="type-spec">13px / 400</div>
        </div>
        <div class="type-sample" style="font-size:13px;color:var(--text-muted)">Sign in to manage your blog.</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Small / Date</div>
          <div class="type-spec">12px / 400</div>
        </div>
        <div class="type-sample" style="font-size:12px;color:var(--text-hint)">2024/12/01</div>
      </div>
      <div class="type-row">
        <div class="type-meta">
          <div class="type-label">Eyebrow</div>
          <div class="type-spec">10px / 400 / uppercase / 0.12em</div>
        </div>
        <div class="type-sample" style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.12em">Admin Area</div>
      </div>
    </div>
  </div>

</body>
</html>
