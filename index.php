<?php
// CMS created by Renify – Илия Б. | www.sait4e.online

$baseDir = __DIR__;
$dataDir = "$baseDir/data";
$configFile = "$dataDir/config.json";
$langDir = "$baseDir/lang";
$pagesDir = "$baseDir/pages";

if (!file_exists($configFile)) die("❌ config.json not found at: $configFile");
$config = json_decode(file_get_contents($configFile), true);
if (!is_array($config)) die("❌ config.json could not be parsed.");

$defaultLang = $config['default_lang'] ?? 'bg';
$siteTitle = $config['site_title'] ?? 'Sait4e CMS';
$langCode = $defaultLang;

require_once "$langDir/{$langCode}.php";

$currentPage = $_GET['page'] ?? 'home';
$safePage = preg_replace('/[^a-z0-9\-_]/i', '', $currentPage);
$contentFile = "$pagesDir/{$safePage}.{$langCode}.html";
if (!file_exists($contentFile)) $contentFile = "$pagesDir/home.{$langCode}.html";
?><!DOCTYPE html>
<html lang="<?= $langCode ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $siteTitle ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-50 text-gray-900">

  <!-- NAVBAR -->
  
<nav class="sticky top-0 z-50 shadow-md" data-theme="light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Site name -->
            <div class="flex-shrink-0 font-bold text-xl">
            <?= $config['site_title'] ?? 'My CMS' ?>
        </div>
            <!-- Menu items for desktop -->
            <div class="hidden sm:flex sm:justify-center sm:flex-1">
                <div class="flex space-x-8">
                    <?php
                    $pages = json_decode(file_get_contents('data/pages.json'), true);
                    $menuItems = [];
                    $submenus = [];
                    foreach ($pages as $page) {
                        if (empty($page['parent'])) {
                            $menuItems[] = $page;
                        } else {
                            $submenus[$page['parent']][] = $page;
                        }
                    }
                    foreach ($menuItems as $page):
                        $hasSubmenu = isset($submenus[$page['name']]);
                    ?>
                    <div class="relative <?= $hasSubmenu ? 'group' : '' ?>">
                        <a href="?page=<?= $page['name'] ?>" class="px-4 py-2 text-base font-medium rounded-md transition duration-150 ease-in-out <?= ($currentPage === $page['name']) ? 'active' : '' ?>">
                            <?= $page['title'] ?>
                            <?php if ($hasSubmenu): ?>
                                <svg class="ml-2 h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                        </a>
                        <?php if ($hasSubmenu): ?>
                            <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                                <div class="py-1">
                                    <?php foreach ($submenus[$page['name']] as $subpage): ?>
                                        <a href="?page=<?= $subpage['name'] ?>" class="block px-4 py-2 text-sm transition duration-150 ease-in-out">
                                            <?= $subpage['title'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Right side: mobile menu toggle and theme toggle -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu toggle -->
                <button id="mobileMenuToggle" class="sm:hidden p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <!-- Theme toggle -->
                <button id="themeToggle" class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 transition text-sm font-medium">
                    o
                </button>
            </div>
        </div>
		
        <!-- Mobile menu -->
        <div id="mobileMenu" class="sm:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <?php foreach ($menuItems as $page): ?>
                    <?php $hasSubmenu = isset($submenus[$page['name']]); ?>
                    <div>
                        <a href="?page=<?= $page['name'] ?>" class="block px-3 py-2 rounded-md text-base font-medium <?= ($currentPage === $page['name']) ? 'active' : '' ?>">
                            <?= $page['title'] ?>
                        </a>
                        <?php if ($hasSubmenu): ?>
                            <div class="pl-4 space-y-1">
                                <?php foreach ($submenus[$page['name']] as $subpage): ?>
                                    <a href="?page=<?= $subpage['name'] ?>" class="block px-3 py-2 rounded-md text-sm font-medium">
                                        <?= $subpage['title'] ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobileMenuToggle').addEventListener('click', function() {
        var menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    });

    document.getElementById('themeToggle').addEventListener('click', function() {
        var nav = document.querySelector('nav');
        var currentTheme = nav.getAttribute('data-theme');
        var newTheme = currentTheme === 'light' ? 'dark' : 'light';
        nav.setAttribute('data-theme', newTheme);
    });
</script>

<style>
    nav[data-theme="light"] {
        background-color: white;
        color: #4a5568;
    }
    nav[data-theme="light"] a {
        color: #4a5568;
    }
    nav[data-theme="light"] a:hover {
        color: #1a202c;
        background-color: #f7fafc;
    }
    nav[data-theme="light"] .active {
        color: #1a202c;
        background-color: #edf2f7;
    }
    nav[data-theme="light"] .group:hover .opacity-0 {
        background-color: white;
    }
    nav[data-theme="light"] #mobileMenuToggle {
        color: #4a5568;
    }
    nav[data-theme="light"] #mobileMenuToggle:hover {
        color: #1a202c;
        background-color: #f7fafc;
    }

    nav[data-theme="dark"] {
        background-color: #1a202c;
        color: #e2e8f0;
    }
    nav[data-theme="dark"] a {
        color: #e2e8f0;
    }
    nav[data-theme="dark"] a:hover {
        color: white;
        background-color: #2d3748;
    }
    nav[data-theme="dark"] .active {
        color: white;
        background-color: #4a5568;
    }
    nav[data-theme="dark"] .group:hover .opacity-0 {
        background-color: #2d3748;
    }
    nav[data-theme="dark"] #mobileMenuToggle {
        color: #e2e8f0;
    }
    nav[data-theme="dark"] #mobileMenuToggle:hover {
        color: white;
        background-color: #2d3748;
    }
</style>


 <!-- HEADER -->
<section class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-10">
  <div class="max-w-4xl mx-auto px-4">
    <?php include "$dataDir/header.html"; ?>
  </div>
</section>

<!--Main Page loader-->

<main class="flex-grow max-w-5xl mx-auto px-4 py-10">
  <?php include $contentFile; ?>
</main>

<!-- NEWS SECTION 3 shown+all -->
<?php
// Define configurable path to news.json
define('NEWS_FILE_PATH', __DIR__ . '/admin/data/news.json');
?>
<section class="max-w-4xl mx-auto my-12 px-4 sm:px-6 lg:px-8" id="news">
  <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center animate-fade-in">Latest News</h2>
  <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" id="news-container">
    <?php
      // Load and parse news.json
      $news = [];
      if (file_exists(NEWS_FILE_PATH)) {
        $json_content = file_get_contents(NEWS_FILE_PATH);
        $decoded = json_decode($json_content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
          $news = $decoded;
        }
      }
      
      // Sort by date descending (newest first)
      if (!empty($news)) {
        usort($news, function($a, $b) {
          $date_a = strtotime($a['date'] ?? '1970-01-01');
          $date_b = strtotime($b['date'] ?? '1970-01-01');
          return $date_b - $date_a;
        });
        
        // Display initial 3 posts
        $initial_count = min(3, count($news));
        for ($i = 0; $i < $initial_count; $i++) {
          $post = $news[$i];
          $title = htmlspecialchars($post['title'] ?? '');
          $date = htmlspecialchars($post['date'] ?? date('Y-m-d'));
          $content = $post['content'] ?? '';
          $image = !empty($post['image']) ? htmlspecialchars($post['image']) : '';
          $plain_content = strip_tags($content);
          $is_long = strlen($plain_content) > 100;
          $preview = $is_long ? substr($plain_content, 0, 100) . '...' : $plain_content;
    ?>
          <article class="bg-white rounded-xl shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300 animate-fade-in-up" data-post-id="<?= htmlspecialchars($post['id'] ?? '') ?>">
            <?php if ($image): ?>
              <img src="<?= $image ?>" alt="<?= $title ?>" class="w-full h-48 object-cover" loading="lazy">
            <?php endif; ?>
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-2"><?= $title ?></h3>
              <p class="text-sm text-gray-500 mb-3"><?= date('F j, Y', strtotime($date)) ?></p>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?= $preview ?></p>
              <?php if ($is_long): ?>
                <button data-post-id="<?= htmlspecialchars($post['id'] ?? '') ?>" class="open-modal text-sky-600 font-medium text-sm hover:text-sky-800 transition-colors duration-200">Read More</button>
              <?php endif; ?>
            </div>
          </article>
          
          <!-- Modal for this post -->
          <div id="modal-<?= htmlspecialchars($post['id'] ?? '') ?>" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden animate-scale-in" role="dialog" aria-labelledby="modal-title-<?= htmlspecialchars($post['id'] ?? '') ?>">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 m-4 relative">
              <button class="close-modal absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-600 rounded-full p-2" aria-label="Close modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
              <?php if ($image): ?>
                <img src="<?= $image ?>" alt="<?= $title ?>" class="w-full h-64 object-cover rounded-lg mb-4">
              <?php endif; ?>
              <h3 id="modal-title-<?= htmlspecialchars($post['id'] ?? '') ?>" class="text-2xl font-semibold text-gray-800 mb-3"><?= $title ?></h3>
              <p class="text-sm text-gray-500 mb-4"><?= date('F j, Y', strtotime($date)) ?></p>
              <div class="text-gray-700 prose prose-sm max-w-none"><?= $content ?></div>
            </div>
          </div>
    <?php
        }
      } else {
    ?>
        <p class="col-span-full text-center text-gray-500 text-lg">No news available at the moment.</p>
    <?php
      }
    ?>
  </div>
  
  <?php if (!empty($news) && count($news) > 3): ?>
    <div class="text-center mt-8">
      <button id="load-more" class="bg-sky-600 text-white px-6 py-3 rounded-lg hover:bg-sky-700 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition duration-200" data-offset="3">Load More</button>
    </div>
  <?php endif; ?>
</section>

<style>
  @keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes fade-in-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes scale-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }
  .animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
  }
  .animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
  }
  .animate-scale-in {
    animation: scale-in 0.3s ease-out forwards;
  }
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .prose img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
  }
</style>

<script>
  // Staggered animation delay for initial articles
  document.querySelectorAll('#news-container article').forEach((article, index) => {
    article.style.animationDelay = `${index * 0.2}s`;
  });

  // Modal functionality
  function initializeModals() {
    const modals = document.querySelectorAll('[id^="modal-"]');
    const openButtons = document.querySelectorAll('.open-modal');
    const closeButtons = document.querySelectorAll('.close-modal');

    openButtons.forEach(button => {
      button.removeEventListener('click', openModalHandler);
      button.addEventListener('click', openModalHandler);
    });

    closeButtons.forEach(button => {
      button.removeEventListener('click', closeModalHandler);
      button.addEventListener('click', closeModalHandler);
    });

    modals.forEach(modal => {
      modal.removeEventListener('click', overlayClickHandler);
      modal.addEventListener('click', overlayClickHandler);
    });

    function openModalHandler() {
      const postId = this.getAttribute('data-post-id');
      const modal = document.getElementById(`modal-${postId}`);
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        modal.querySelector('.close-modal').focus();
      }
    }

    function closeModalHandler() {
      const modal = this.closest('[id^="modal-"]');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      }
    }

    function overlayClickHandler(e) {
      if (e.target === this) {
        this.classList.add('hidden');
        document.body.style.overflow = '';
      }
    }
  }

  // Escape key handler
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('[id^="modal-"]').forEach(modal => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      });
    }
  });

  // Load More functionality
  const loadMoreButton = document.getElementById('load-more');
  if (loadMoreButton) {
    let newsData = <?php echo json_encode($news, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>;
    const container = document.getElementById('news-container');

    // Ensure newsData is sorted by date descending
    newsData.sort((a, b) => {
      const dateA = new Date(a.date || '1970-01-01').getTime();
      const dateB = new Date(b.date || '1970-01-01').getTime();
      return dateB - dateA;
    });

    loadMoreButton.addEventListener('click', () => {
      try {
        const offset = parseInt(loadMoreButton.getAttribute('data-offset'));
        const nextPosts = newsData.slice(offset, offset + 3);

        nextPosts.forEach((post, index) => {
          const isLong = (post.content.replace(/<[^>]+>/g, '') || '').length > 100;
          const preview = isLong ? (post.content.replace(/<[^>]+>/g, '') || '').substring(0, 100) + '...' : (post.content.replace(/<[^>]+>/g, '') || '');
          const safeTitle = (post.title || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
          const safeImage = post.image ? (post.image.replace(/</g, '&lt;').replace(/>/g, '&gt;')) : '';
          const safeDate = post.date ? new Date(post.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '';
          const safeContent = post.content || '';

          const article = document.createElement('article');
          article.className = 'bg-white rounded-xl shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300 animate-fade-in-up';
          article.setAttribute('data-post-id', post.id || '');
          article.style.animationDelay = `${index * 0.2}s`;
          
          article.innerHTML = `
            ${safeImage ? `<img src="${safeImage}" alt="${safeTitle}" class="w-full h-48 object-cover" loading="lazy">` : ''}
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-800 mb-2 line-clamp-2">${safeTitle}</h3>
              <p class="text-sm text-gray-500 mb-3">${safeDate}</p>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">${preview}</p>
              ${isLong ? `<button data-post-id="${post.id}" class="open-modal text-sky-600 font-medium text-sm hover:text-sky-800 transition-colors duration-200">Read More</button>` : ''}
            </div>
          `;
          
          const modal = document.createElement('div');
          modal.id = `modal-${post.id}`;
          modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden animate-scale-in';
          modal.setAttribute('role', 'dialog');
          modal.setAttribute('aria-labelledby', `modal-title-${post.id}`);
          modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 m-4 relative">
              <button class="close-modal absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-600 rounded-full p-2" aria-label="Close modal">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
              ${safeImage ? `<img src="${safeImage}" alt="${safeTitle}" class="w-full h-64 object-cover rounded-lg mb-4">` : ''}
              <h3 id="modal-title-${post.id}" class="text-2xl font-semibold text-gray-800 mb-3">${safeTitle}</h3>
              <p class="text-sm text-gray-500 mb-4">${safeDate}</p>
              <div class="text-gray-700 prose prose-sm max-w-none">${safeContent}</div>
            </div>
          `;
          
          container.appendChild(article);
          document.body.appendChild(modal);
        });

        loadMoreButton.setAttribute('data-offset', offset + 3);
        if (offset + 3 >= newsData.length) {
          loadMoreButton.classList.add('hidden');
        }

        initializeModals();
      } catch (error) {
        console.error('Error loading more posts:', error);
      }
    });
  }

  initializeModals();
</script>
<br>

<footer class="bg-white border-t mt-auto">
  <div class="text-center py-6 text-sm text-gray-500">
    <p>© <?= date('Y') ?> <?= $siteTitle ?> – Renify</p>
    <p>Made with 🩷 <a href="https://www.sait4e.online" target="_blank" class="underline hover:text-pink-400">sait4e.online</a></p>
  </div>
</footer>

</body>
</html>
