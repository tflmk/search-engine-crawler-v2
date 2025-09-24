<?php
require 'SimpleDom.php';

$host = 'localhost';
$db = 'websurfed_search';
$user = 'websurfed_search';
$pass = 'wjgt2d7a86h77MaN8y9P';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}


function isAllowedByRobots($url, $userAgent = 'yoursite.com bot 1.0') {
    $robotsUrl = rtrim(parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST), '/') . '/robots.txt';
    $robotsContent = @file_get_contents($robotsUrl);
    if ($robotsContent === false) {
        return true;
    }

    $lines = explode("\n", $robotsContent);
    $disallow = [];
    $currentAgent = '';

    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^User-agent:\s*(.+)$/i', $line, $matches)) {
            $currentAgent = trim($matches[1]);
        } elseif (preg_match('/^Disallow:\s*(.+)$/i', $line, $matches) && ($currentAgent === '*' || $currentAgent === $userAgent)) {
            $disallow[] = trim($matches[1]);
        }
    }

    $path = parse_url($url, PHP_URL_PATH) ?: '/';
    foreach ($disallow as $pattern) {
        if ($pattern && fnmatch($pattern, $path)) {
            return false;
        }
    }
    return true;
}

function normalizeUrl($url, $baseUrl) {
    $url = trim($url);
    if (empty($url) || $url === '#' || strpos($url, 'javascript:') === 0) {
        return null;
    }
    if (!preg_match('/^https?:\/\//i', $url)) {
        $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }
    return $url;
}

function crawl($startUrl, $pdo, $maxPages = 10) {
    $visited = [];
    $queue = [$startUrl];
    $pageCount = 0;

    while (!empty($queue) && $pageCount < $maxPages) {
        $url = array_shift($queue);
        if (in_array($url, $visited) || !isAllowedByRobots($url)) {
            echo "Skipped: $url\n";
            continue;
        }

        echo "Processing: $url\n";
        $visited[] = $url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Priserna.com bot 1.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($html === false || $httpCode !== 200) {
            echo "Loading error: $url (HTTP $httpCode)\n";
            continue;
        }

        $dom = str_get_html($html);
        if (!$dom) {
            echo "Parsing error: $url\n";
            continue;
        }

        $title = $dom->find('title', 0) ? $dom->find('title', 0)->text() : 'No title';
        $cachetext = $dom->find('body', 0) ? $dom->find('body', 0)->text() : 'No body';
        $description = '';
        $keywords = '';
        $meta = $dom->find('meta');
        foreach ($meta->getAll() as $m) {
            if ($m->getAttribute('name') === 'description') {
                $description = $m->getAttribute('content');
            }
            if ($m->getAttribute('name') === 'keywords') {
                $keywords = $m->getAttribute('content');
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO search (id, title, description, cachetext, url, date) VALUES (?, ?, ?, ?, ?, '".date('Y-m-d H:i:s')."')");
            $stmt->execute([$id, $title, $description, $cachetext, $url]);
        } catch (\PDOException $e) {
            echo "Saving error $url: " . $e->getMessage() . "\n";
        }

        $links = $dom->find('a');
        foreach ($links->getAll() as $link) {
            $href = $link->getAttribute('href');
            $normalizedUrl = normalizeUrl($href, $url);
            if ($normalizedUrl && !in_array($normalizedUrl, $visited) && !in_array($normalizedUrl, $queue)) {
                if (parse_url($normalizedUrl, PHP_URL_HOST) === parse_url($startUrl, PHP_URL_HOST)) {
                    $queue[] = $normalizedUrl;
                }
            }
        }

        $pageCount++;
        $dom->clear(); 
        sleep(1); 
    }

    echo "Pages processed: $pageCount\n";
}

$redirectPage = $_POST['startUrl'];
if($result == true) {
               header("location:crawler.php");
               exit();
               
          } else {
               echo 'data not inserted successfully'.mysqli_connect_error();
          }
$startUrl = $redirectPage;
header("Location: $startUrl");
crawl($startUrl, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>crawl url</title>
</head>
<body>
     <center>
         <h2>crawl url</h2>
     <form method="POST" action="crawler.php">
  <input type="text" value="" name="startUrl" />
  <input type="submit" value="Go" />
</form>

     <a href="search.php">Search Engine</a>
</body>
</center>
</html>