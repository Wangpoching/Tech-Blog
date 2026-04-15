<?php
	require('../conn.php');
	require('../utils.php');
	session_start();
	isAdmin();

	// ---- 清空資料 ----
	$conn->query("SET FOREIGN_KEY_CHECKS = 0");
	$conn->query("TRUNCATE TABLE post_tags");
	$conn->query("TRUNCATE TABLE posts");
	$conn->query("TRUNCATE TABLE tags");
	$conn->query("TRUNCATE TABLE categories");
	$conn->query("SET FOREIGN_KEY_CHECKS = 1");

	echo "✅ 清空完成<br>";

	// ---- Categories ----
	$categories = [
	    'Frontend', 'Backend', 'Database', 'DevOps', 'Security',
	    'Performance', 'JavaScript', 'PHP', 'CSS', 'Linux',
	    'Git', 'API', 'Testing', 'Architecture', 'Tools'
	];

	foreach ($categories as $name) {
	    $conn->query("INSERT INTO `categories` (name) VALUES ('$name')");
	}

	echo "✅ 分類塞入完成<br>";

	// ---- Tags ----
	$tags = [
	    'async', 'promise', 'closure', 'event-loop', 'prototype',
	    'REST', 'GraphQL', 'JWT', 'OAuth', 'webhook',
	    'index', 'query', 'transaction', 'migration', 'ORM',
	    'docker', 'nginx', 'CI/CD', 'kubernetes', 'linux',
	    'XSS', 'CSRF', 'SQL injection', 'HTTPS', 'firewall',
	    'caching', 'lazy-loading', 'CDN', 'compression', 'profiling',
	    'grid', 'flexbox', 'animation', 'responsive', 'variable',
	    'composer', 'PDO', 'session', 'middleware', 'MVC',
	    'branch', 'merge', 'rebase', 'commit', 'pull-request',
	    'unit-test', 'TDD', 'mock', 'e2e', 'coverage'
	];

	foreach ($tags as $name) {
	    $conn->query("INSERT INTO `tags` (name) VALUES ('$name')");
	}

	echo "✅ 標籤塞入完成<br>";

	// ---- Posts ----
	$posts = [
	    // Frontend
	    [
	        'title'      => 'Understanding the JavaScript Event Loop',
	        'subTitle'   => 'How the browser handles async code under the hood',
	        'content'    => "## What is the Event Loop?\n\nThe event loop is the mechanism that allows JavaScript to perform non-blocking operations.\n\n## Call Stack\n\nThe call stack keeps track of where we are in the code.\n\n## Task Queue\n\nCallbacks from async operations wait here before being pushed to the stack.\n\n## Microtask Queue\n\nPromise callbacks live here and are processed before the task queue.",
	        'category'   => 'Frontend',
	        'status'     => 'published',
	        'tags'       => ['async', 'event-loop', 'promise'],
	        'created_at' => '2024-01-05 10:00:00',
	    ],
	    [
	        'title'      => 'CSS Grid vs Flexbox: When to Use Which',
	        'subTitle'   => 'A practical guide to choosing the right layout tool',
	        'content'    => "## Flexbox\n\nFlexbox is one-dimensional. It controls layout in one direction at a time.\n\n## CSS Grid\n\nCSS Grid is two-dimensional. It controls rows AND columns simultaneously.\n\n## When to Use Flexbox\n\nUse flexbox for navigation bars, button groups, and card rows.\n\n## When to Use Grid\n\nUse grid for full-page layouts and complex component structures.",
	        'category'   => 'CSS',
	        'status'     => 'published',
	        'tags'       => ['grid', 'flexbox', 'responsive'],
	        'created_at' => '2024-01-10 09:00:00',
	    ],
	    [
	        'title'      => 'JavaScript Closures Explained',
	        'subTitle'   => 'Finally understanding one of JS\'s most powerful features',
	        'content'    => "## What is a Closure?\n\nA closure is a function that remembers the variables from its outer scope even after the outer function has returned.\n\n## Practical Example\n\n```js\nfunction counter() {\n  let count = 0;\n  return () => ++count;\n}\n```\n\n## Common Use Cases\n\nClosures are used for data privacy, factory functions, and event handlers.",
	        'category'   => 'JavaScript',
	        'status'     => 'published',
	        'tags'       => ['closure', 'prototype', 'async'],
	        'created_at' => '2024-01-15 08:30:00',
	    ],
	    [
	        'title'      => 'CSS Custom Properties (Variables) Deep Dive',
	        'subTitle'   => 'How to build a scalable design system with CSS variables',
	        'content'    => "## Defining Variables\n\n```css\n:root {\n  --color-primary: #d97706;\n  --spacing-md: 16px;\n}\n```\n\n## Using Variables\n\n```css\n.button {\n  background: var(--color-primary);\n  padding: var(--spacing-md);\n}\n```\n\n## Dynamic Updates with JS\n\nCSS variables can be updated at runtime using JavaScript, making them perfect for theming.",
	        'category'   => 'CSS',
	        'status'     => 'published',
	        'tags'       => ['variable', 'responsive', 'animation'],
	        'created_at' => '2024-01-20 11:00:00',
	    ],
	    [
	        'title'      => 'Async/Await vs Promises: Which Should You Use?',
	        'subTitle'   => 'A practical comparison with real-world examples',
	        'content'    => "## Promises\n\nPromises represent a value that may be available now, in the future, or never.\n\n## Async/Await\n\nAsync/await is syntactic sugar built on top of Promises, making asynchronous code look synchronous.\n\n## When to Use Each\n\nUse Promises when chaining multiple async operations. Use async/await for cleaner, more readable code.",
	        'category'   => 'JavaScript',
	        'status'     => 'published',
	        'tags'       => ['async', 'promise', 'event-loop'],
	        'created_at' => '2024-01-25 14:00:00',
	    ],

	    // Backend
	    [
	        'title'      => 'RESTful API Design Best Practices',
	        'subTitle'   => 'How to design APIs that developers will love',
	        'content'    => "## Use Nouns for Resources\n\nEndpoints should represent resources, not actions.\n\nGood: `GET /posts`\nBad: `GET /getPosts`\n\n## Use HTTP Methods Correctly\n\n- GET: Read\n- POST: Create\n- PUT/PATCH: Update\n- DELETE: Remove\n\n## Return Meaningful Status Codes\n\nAlways return appropriate HTTP status codes with your responses.",
	        'category'   => 'API',
	        'status'     => 'published',
	        'tags'       => ['REST', 'GraphQL', 'JWT'],
	        'created_at' => '2024-02-01 09:00:00',
	    ],
	    [
	        'title'      => 'PHP Sessions and Cookies Explained',
	        'subTitle'   => 'Managing user state in PHP web applications',
	        'content'    => "## Cookies\n\nCookies are stored on the client side and sent with every request.\n\n## Sessions\n\nSessions store data on the server side. Only a session ID is stored in the cookie.\n\n## Security Considerations\n\nAlways use `HttpOnly` and `Secure` flags for session cookies. Regenerate session IDs after login.",
	        'category'   => 'PHP',
	        'status'     => 'published',
	        'tags'       => ['session', 'middleware', 'MVC'],
	        'created_at' => '2024-02-05 10:30:00',
	    ],
	    [
	        'title'      => 'Building Middleware in PHP',
	        'subTitle'   => 'How to add reusable request handling layers to your app',
	        'content'    => "## What is Middleware?\n\nMiddleware sits between the request and response, processing or filtering the request.\n\n## Common Use Cases\n\n- Authentication checks\n- Logging\n- CORS headers\n- Rate limiting\n\n## Implementation\n\nMiddleware can be implemented as a chain of responsibility pattern in PHP.",
	        'category'   => 'PHP',
	        'status'     => 'published',
	        'tags'       => ['middleware', 'MVC', 'composer'],
	        'created_at' => '2024-02-10 13:00:00',
	    ],
	    [
	        'title'      => 'JWT Authentication Explained',
	        'subTitle'   => 'Stateless authentication with JSON Web Tokens',
	        'content'    => "## What is a JWT?\n\nA JSON Web Token consists of three parts: Header, Payload, and Signature.\n\n## How it Works\n\n1. User logs in\n2. Server creates a JWT and returns it\n3. Client stores the JWT\n4. Client sends JWT with every request\n5. Server verifies the JWT\n\n## Pros and Cons\n\nJWT is stateless and scalable, but tokens can't be invalidated before expiry.",
	        'category'   => 'Security',
	        'status'     => 'published',
	        'tags'       => ['JWT', 'OAuth', 'HTTPS'],
	        'created_at' => '2024-02-15 09:00:00',
	    ],
	    [
	        'title'      => 'PHP Composer: Managing Dependencies Like a Pro',
	        'subTitle'   => 'Everything you need to know about PHP\'s package manager',
	        'content'    => "## What is Composer?\n\nComposer is a dependency manager for PHP. It allows you to declare the libraries your project depends on.\n\n## Installing a Package\n\n```bash\ncomposer require vendor/package\n```\n\n## Autoloading\n\nComposer generates an autoloader so you don't need to manually require files.\n\n## composer.lock\n\nAlways commit your `composer.lock` file to ensure everyone uses the same versions.",
	        'category'   => 'PHP',
	        'status'     => 'draft',
	        'tags'       => ['composer', 'PDO', 'MVC'],
	        'created_at' => '2024-02-20 11:00:00',
	    ],

	    // Database
	    [
	        'title'      => 'MySQL Index Optimization in Practice',
	        'subTitle'   => 'Speed up your queries with the right indexing strategy',
	        'content'    => "## What is an Index?\n\nAn index is a data structure that improves query speed at the cost of storage space.\n\n## When to Add an Index\n\nAdd indexes on columns frequently used in WHERE, ORDER BY, and JOIN conditions.\n\n## Reading EXPLAIN Output\n\nUse `EXPLAIN SELECT ...` to understand how MySQL executes your query and whether it uses indexes.\n\n## Composite Indexes\n\nA composite index on `(a, b)` can serve queries on `a` alone, but not `b` alone.",
	        'category'   => 'Database',
	        'status'     => 'published',
	        'tags'       => ['index', 'query', 'transaction'],
	        'created_at' => '2024-03-01 10:00:00',
	    ],
	    [
	        'title'      => 'Database Transactions and ACID Properties',
	        'subTitle'   => 'Understanding the foundation of reliable database operations',
	        'content'    => "## What is a Transaction?\n\nA transaction is a sequence of operations treated as a single unit of work.\n\n## ACID Properties\n\n- **Atomicity**: All or nothing\n- **Consistency**: Data remains valid\n- **Isolation**: Transactions don't interfere\n- **Durability**: Committed data persists\n\n## Using Transactions in PHP\n\n",
	        'category'   => 'Database',
	        'status'     => 'published',
	        'tags'       => ['transaction', 'ORM', 'migration'],
	        'created_at' => '2024-03-05 09:30:00',
	    ],
	    [
	        'title'      => 'Understanding SQL JOINs',
	        'subTitle'   => 'INNER, LEFT, RIGHT, and FULL JOIN explained with examples',
	        'content'    => "## INNER JOIN\n\nReturns rows that have matching values in both tables.\n\n## LEFT JOIN\n\nReturns all rows from the left table, and matched rows from the right.\n\n## RIGHT JOIN\n\nReturns all rows from the right table, and matched rows from the left.\n\n## When to Use Which\n\nUse LEFT JOIN when you want all records from the primary table, regardless of whether there's a match.",
	        'category'   => 'Database',
	        'status'     => 'published',
	        'tags'       => ['query', 'index', 'ORM'],
	        'created_at' => '2024-03-10 14:00:00',
	    ],

	    // DevOps
	    [
	        'title'      => 'Setting Up Docker for Local Development',
	        'subTitle'   => 'One command to spin up your entire dev environment',
	        'content'    => "## Why Docker?\n\nDocker eliminates the 'works on my machine' problem by containerising your application.\n\n## docker-compose.yml\n\n```yaml\nservices:\n  web:\n    image: php:8.2-apache\n  db:\n    image: mysql:8.0\n```\n\n## Common Commands\n\n```bash\ndocker-compose up -d\ndocker-compose down\ndocker-compose logs\n```",
	        'category'   => 'DevOps',
	        'status'     => 'published',
	        'tags'       => ['docker', 'nginx', 'CI/CD'],
	        'created_at' => '2024-03-15 10:00:00',
	    ],
	    [
	        'title'      => 'Git Branching Strategies Explained',
	        'subTitle'   => 'GitFlow, trunk-based development, and when to use each',
	        'content'    => "## GitFlow\n\nGitFlow uses multiple long-lived branches: main, develop, feature, release, and hotfix.\n\n## Trunk-Based Development\n\nAll developers commit to a single trunk (main) branch frequently.\n\n## GitHub Flow\n\nA simpler alternative: create a branch, open a PR, merge to main.\n\n## Which to Choose?\n\nSmall teams benefit from trunk-based development. Larger teams with release cycles may prefer GitFlow.",
	        'category'   => 'Git',
	        'status'     => 'published',
	        'tags'       => ['branch', 'merge', 'pull-request'],
	        'created_at' => '2024-03-20 11:00:00',
	    ],
	    [
	        'title'      => 'Setting Up a CI/CD Pipeline with GitHub Actions',
	        'subTitle'   => 'Automate your testing and deployment workflow',
	        'content'    => "## What is CI/CD?\n\nContinuous Integration and Continuous Deployment automate the process of testing and deploying code.\n\n## GitHub Actions Basics\n\nWorkflows are defined in `.github/workflows/*.yml` files.\n\n## Example Workflow\n\n```yaml\non: [push]\njobs:\n  test:\n    runs-on: ubuntu-latest\n    steps:\n      - uses: actions/checkout@v3\n      - run: composer install\n      - run: php artisan test\n```",
	        'category'   => 'DevOps',
	        'status'     => 'published',
	        'tags'       => ['CI/CD', 'docker', 'linux'],
	        'created_at' => '2024-03-25 09:00:00',
	    ],
	    [
	        'title'      => 'Nginx Configuration for PHP Applications',
	        'subTitle'   => 'A practical guide to setting up Nginx as a web server',
	        'content'    => "## Basic Server Block\n\n```nginx\nserver {\n    listen 80;\n    root /var/www/html;\n    index index.php;\n\n    location ~ \\.php$ {\n        fastcgi_pass php:9000;\n        include fastcgi_params;\n    }\n}\n```\n\n## PHP-FPM\n\nNginx doesn't process PHP itself — it passes requests to PHP-FPM.\n\n## Common Pitfalls\n\nAlways deny access to `.env` and other sensitive files.",
	        'category'   => 'DevOps',
	        'status'     => 'draft',
	        'tags'       => ['nginx', 'linux', 'docker'],
	        'created_at' => '2024-04-01 10:00:00',
	    ],

	    // Security
	    [
	        'title'      => 'Preventing XSS Attacks in PHP',
	        'subTitle'   => 'How to sanitise output and protect your users',
	        'content'    => "## What is XSS?\n\nCross-Site Scripting (XSS) allows attackers to inject malicious scripts into web pages.\n\n## Prevention\n\nAlways escape output with `htmlspecialchars()` before rendering user data.\n\n```php\necho htmlspecialchars(\$input, ENT_QUOTES);\n```\n\n## Content Security Policy\n\nA CSP header tells the browser which sources of content are allowed.",
	        'category'   => 'Security',
	        'status'     => 'published',
	        'tags'       => ['XSS', 'CSRF', 'SQL injection'],
	        'created_at' => '2024-04-05 09:00:00',
	    ],
	    [
	        'title'      => 'SQL Injection Prevention with Prepared Statements',
	        'subTitle'   => 'Why you should never concatenate user input into SQL',
	        'content'    => "## What is SQL Injection?\n\nSQL injection occurs when user input is directly concatenated into a SQL query.\n\n## The Wrong Way\n\n```php\n\$sql = \"SELECT * FROM users WHERE id = \" . \$_GET['id'];\n```\n\n## The Right Way\n\n```php\n\$stmt = \$conn->prepare('SELECT * FROM users WHERE id = ?');\n\$stmt->bind_param('i', \$id);\n\$stmt->execute();\n```\n\nPrepared statements separate code from data, making injection impossible.",
	        'category'   => 'Security',
	        'status'     => 'published',
	        'tags'       => ['SQL injection', 'XSS', 'HTTPS'],
	        'created_at' => '2024-04-10 11:00:00',
	    ],
	    [
	        'title'      => 'CSRF Protection in Web Applications',
	        'subTitle'   => 'Understanding and preventing Cross-Site Request Forgery',
	        'content'    => "## What is CSRF?\n\nCSRF tricks a user's browser into making an unwanted request to a site they're authenticated on.\n\n## Prevention: CSRF Tokens\n\nGenerate a unique token per session and include it in every form.\n\n```php\n\$_SESSION['csrf_token'] = bin2hex(random_bytes(32));\n```\n\n## SameSite Cookie Attribute\n\nSetting `SameSite=Strict` on cookies prevents them from being sent in cross-site requests.",
	        'category'   => 'Security',
	        'status'     => 'published',
	        'tags'       => ['CSRF', 'XSS', 'HTTPS'],
	        'created_at' => '2024-04-15 10:00:00',
	    ],

	    // Performance
	    [
	        'title'      => 'Web Performance Optimisation: The Basics',
	        'subTitle'   => 'Quick wins to make your website load faster',
	        'content'    => "## Compress Assets\n\nEnable gzip/brotli compression on your server to reduce file sizes.\n\n## Use a CDN\n\nServe static assets from a Content Delivery Network close to your users.\n\n## Lazy Load Images\n\n```html\n<img src='photo.jpg' loading='lazy' />\n```\n\n## Minimise HTTP Requests\n\nBundle your JS and CSS files to reduce the number of network requests.",
	        'category'   => 'Performance',
	        'status'     => 'published',
	        'tags'       => ['caching', 'CDN', 'compression'],
	        'created_at' => '2024-04-20 09:00:00',
	    ],
	    [
	        'title'      => 'MySQL Query Optimisation Techniques',
	        'subTitle'   => 'Making slow queries fast with practical techniques',
	        'content'    => "## Use EXPLAIN\n\nAlways start with `EXPLAIN` to understand your query execution plan.\n\n## Avoid SELECT *\n\nOnly select the columns you need.\n\n## Use Indexes Wisely\n\nToo many indexes slow down writes. Only index what you query frequently.\n\n## Avoid N+1 Queries\n\nLoad related data with JOINs instead of querying in a loop.",
	        'category'   => 'Performance',
	        'status'     => 'published',
	        'tags'       => ['profiling', 'caching', 'lazy-loading'],
	        'created_at' => '2024-04-25 11:00:00',
	    ],

	    // Linux
	    [
	        'title'      => 'Essential Linux Commands for Web Developers',
	        'subTitle'   => 'The commands you\'ll use every day as a developer',
	        'content'    => "## File Navigation\n\n```bash\nls -la        # list files with details\ncd /path      # change directory\npwd           # print working directory\n```\n\n## File Operations\n\n```bash\ncp source dest   # copy\nmv source dest   # move/rename\nrm -rf dir       # remove\n```\n\n## Process Management\n\n```bash\nps aux           # list processes\nkill -9 PID      # force kill\ntop              # real-time process viewer\n```",
	        'category'   => 'Linux',
	        'status'     => 'published',
	        'tags'       => ['linux', 'CI/CD', 'docker'],
	        'created_at' => '2024-05-01 10:00:00',
	    ],
	    [
	        'title'      => 'File Permissions in Linux Explained',
	        'subTitle'   => 'Understanding chmod, chown, and file permission bits',
	        'content'    => "## Permission Bits\n\nEach file has three sets of permissions: owner, group, and others.\n\n```\n-rwxr-xr--\n owner group others\n```\n\n## chmod\n\n```bash\nchmod 755 file.php   # rwxr-xr-x\nchmod 644 file.txt   # rw-r--r--\n```\n\n## chown\n\n```bash\nchown www-data:www-data uploads/\n```\n\nAlways set `uploads/` to be writable by the web server user.",
	        'category'   => 'Linux',
	        'status'     => 'published',
	        'tags'       => ['linux', 'nginx', 'docker'],
	        'created_at' => '2024-05-05 09:00:00',
	    ],

	    // Git
	    [
	        'title'      => 'Git Rebase vs Merge: Which to Use',
	        'subTitle'   => 'Understanding the difference and when each is appropriate',
	        'content'    => "## Git Merge\n\nMerge creates a merge commit that ties together the histories of both branches.\n\n## Git Rebase\n\nRebase rewrites history by moving your commits on top of another branch.\n\n## Golden Rule of Rebasing\n\nNever rebase commits that have been pushed to a shared repository.\n\n## When to Use Each\n\nUse merge for shared branches. Use rebase to clean up your local feature branch before merging.",
	        'category'   => 'Git',
	        'status'     => 'published',
	        'tags'       => ['rebase', 'merge', 'branch'],
	        'created_at' => '2024-05-10 10:00:00',
	    ],
	    [
	        'title'      => 'Writing Better Git Commit Messages',
	        'subTitle'   => 'A guide to clear, useful, and professional commit history',
	        'content'    => "## The Anatomy of a Good Commit\n\n```\nfeat: add user authentication\n\nImplemented JWT-based login with session management.\nAdded password hashing with bcrypt.\n\nCloses #42\n```\n\n## Conventional Commits\n\nUse prefixes like `feat:`, `fix:`, `docs:`, `refactor:` to categorise commits.\n\n## Keep it Short\n\nThe subject line should be under 72 characters.",
	        'category'   => 'Git',
	        'status'     => 'draft',
	        'tags'       => ['commit', 'pull-request', 'branch'],
	        'created_at' => '2024-05-15 11:00:00',
	    ],

	    // Testing
	    [
	        'title'      => 'Introduction to Unit Testing in PHP',
	        'subTitle'   => 'Writing your first tests with PHPUnit',
	        'content'    => "## What is Unit Testing?\n\nUnit tests verify that individual units of code (functions, methods) work as expected.\n\n## Installing PHPUnit\n\n```bash\ncomposer require --dev phpunit/phpunit\n```\n\n## Writing a Test\n\n```php\nclass CalculatorTest extends TestCase {\n    public function testAdd() {\n        \$calc = new Calculator();\n        \$this->assertEquals(4, \$calc->add(2, 2));\n    }\n}\n```",
	        'category'   => 'Testing',
	        'status'     => 'published',
	        'tags'       => ['unit-test', 'TDD', 'mock'],
	        'created_at' => '2024-05-20 10:00:00',
	    ],
	    [
	        'title'      => 'Test-Driven Development in Practice',
	        'subTitle'   => 'Writing tests first to improve your code design',
	        'content'    => "## The TDD Cycle\n\n1. Write a failing test (Red)\n2. Write just enough code to pass (Green)\n3. Refactor (Refactor)\n\n## Benefits of TDD\n\n- Forces you to think about the interface before implementation\n- Creates a safety net for refactoring\n- Produces naturally testable code\n\n## When NOT to Use TDD\n\nExploratory prototyping and UI work are often better without strict TDD.",
	        'category'   => 'Testing',
	        'status'     => 'published',
	        'tags'       => ['TDD', 'unit-test', 'coverage'],
	        'created_at' => '2024-05-25 09:00:00',
	    ],

	    // Architecture
	    [
	        'title'      => 'MVC Architecture Explained',
	        'subTitle'   => 'Understanding Model, View, and Controller in web apps',
	        'content'    => "## Model\n\nThe Model handles data logic and database interaction.\n\n## View\n\nThe View renders the UI. It receives data from the Controller.\n\n## Controller\n\nThe Controller handles requests, calls the Model, and passes data to the View.\n\n## Benefits\n\nMVC separates concerns, making code easier to maintain and test.",
	        'category'   => 'Architecture',
	        'status'     => 'published',
	        'tags'       => ['MVC', 'middleware', 'ORM'],
	        'created_at' => '2024-06-01 10:00:00',
	    ],
	    [
	        'title'      => 'RESTful vs GraphQL APIs: A Practical Comparison',
	        'subTitle'   => 'Choosing the right API design for your project',
	        'content'    => "## RESTful APIs\n\nREST uses multiple endpoints, each representing a resource. Simple and widely understood.\n\n## GraphQL\n\nGraphQL uses a single endpoint. Clients request exactly the data they need.\n\n## When to Use REST\n\nSimple CRUD applications, public APIs, and when caching is important.\n\n## When to Use GraphQL\n\nComplex data relationships, mobile clients that need to minimise data transfer, and rapid iteration.",
	        'category'   => 'API',
	        'status'     => 'published',
	        'tags'       => ['GraphQL', 'REST', 'webhook'],
	        'created_at' => '2024-06-05 11:00:00',
	    ],

	    // Tools
	    [
	        'title'      => 'VS Code Extensions Every Web Developer Needs',
	        'subTitle'   => 'The tools that will make you more productive immediately',
	        'content'    => "## Must-Have Extensions\n\n- **Prettier**: Auto-format your code\n- **ESLint**: Catch JavaScript errors\n- **PHP Intelephense**: PHP intelligence\n- **GitLens**: Supercharge Git\n- **Thunder Client**: Test APIs without leaving VS Code\n\n## Productivity Tips\n\nLearn keyboard shortcuts. `Ctrl+P` for file search and `Ctrl+Shift+P` for the command palette will save you hours.",
	        'category'   => 'Tools',
	        'status'     => 'published',
	        'tags'       => ['CI/CD', 'pull-request', 'coverage'],
	        'created_at' => '2024-06-10 09:00:00',
	    ],
	    [
	        'title'      => 'Postman vs Thunder Client: API Testing Tools Compared',
	        'subTitle'   => 'Which API testing tool should you use in 2024?',
	        'content'    => "## Postman\n\nPostman is the industry standard for API testing. It has a rich feature set including automated tests, environments, and team collaboration.\n\n## Thunder Client\n\nThunder Client is a lightweight VS Code extension for quick API testing without leaving the editor.\n\n## Which to Choose?\n\nUse Thunder Client for quick checks during development. Use Postman for comprehensive API documentation and team workflows.",
	        'category'   => 'Tools',
	        'status'     => 'draft',
	        'tags'       => ['REST', 'webhook', 'e2e'],
	        'created_at' => '2024-06-15 10:00:00',
	    ],
	];

	// ---- Insert Posts ----
	$coverImage = 'file_example_PNG_500kB.png';

	foreach ($posts as $post) {
	    // Get category id
	    $catResult = $conn->query("SELECT id FROM `categories` WHERE name = '{$post['category']}'");
	    $catRow = $catResult->fetch_assoc();
	    $categoryId = $catRow['id'];

	    $stmt = $conn->prepare("
	        INSERT INTO `posts` (title, subTitle, content, user_id, category_id, status, cover_image, created_at, updated_at)
	        VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?)
	    ");
	    $updatedAt = $post['created_at'];
	    $stmt->bind_param('sssissss',
	        $post['title'],
	        $post['subTitle'],
	        $post['content'],
	        $categoryId,
	        $post['status'],
	        $coverImage,
	        $post['created_at'],
	        $updatedAt
	    );
	    $stmt->execute();
	    $postId = $conn->insert_id;

	    // Insert tags
	    foreach ($post['tags'] as $tagName) {
	        $tagResult = $conn->query("SELECT id FROM `tags` WHERE name = '$tagName'");
	        $tagRow = $tagResult->fetch_assoc();
	        if ($tagRow) {
	            $conn->query("INSERT INTO `post_tags` (post_id, tag_id) VALUES ($postId, {$tagRow['id']})");
	        }
	    }
	}

	echo "✅ 文章與標籤塞入完成！共 " . count($posts) . " 篇<br>";
	echo "<br>🎉 Seed 全部完成！";