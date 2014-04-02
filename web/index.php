<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';

$parsedown = new Parsedown();

// clean up the request URI
$request = isset($_GET['q']) ? $_GET['q'] : "";
$request = preg_replace("#[^a-zA-Z0-9\_\-/]+#im", "", $request);
$original_request = $request;
if (!$request) $request = "index";

if (!file_exists(__DIR__ . "/../" . $request . ".md")) {
	header("HTTP/1.0 404 Not Found");
	$request = "404";
}

/**
 * Replace [link](/foo) with [link](/withbitcoin/foo) if necessary.
 */
function fix_relative_links($markdown) {
	if (get_site_config('relative_path')) {
		return str_replace("](/", "](/" . get_site_config('relative_path') . "/", $markdown);
	}
	return $markdown;
}

function output_content($request) {
	global $parsedown;
	$content = fix_relative_links(file_get_contents(__DIR__ . "/../" . $request . ".md"));
	return $parsedown->parse($content);
}

$content = fix_relative_links(file_get_contents(__DIR__ . "/../" . $request . ".md"));
// strip out the title from the first line
$title = explode("\n", $content, 2);
if (count($title) == 2) {
	$title = $title[0];
	// strip out any # heading characters
	$title = trim(str_replace("#", "", $title));
  // strip out any links
  $title = trim(preg_replace("/\[([^\)]+)\)/i", "", $title));
	if ($request != "index") {
		if ($request != "contact") {
			$title .= " in New Zealand - withbitcoin.co.nz";
		} else {
			$title .= " - withbitcoin.co.nz";
		}
	}
} else {
	$title = "withbitcoin.co.nz";
}


/**
 * Can be cached.
 */
$global_calculate_relative_path = null;
function calculate_relative_path() {
  global $global_calculate_relative_path;
  if ($global_calculate_relative_path === null) {
    // construct a relative path for this request based on the request URI, but only if it is set
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] && !defined('FORCE_NO_RELATIVE')) {
      $uri = $_SERVER['REQUEST_URI'];
      // strip out the hostname from the absolute_url
      $intended = substr(get_site_config('absolute_url'), strpos(get_site_config('absolute_url'), '://') + 4);
      $intended = substr($intended, strpos($intended, '/'));
      // if we're in this path, remove it
      // now generate ../s as necessary
      if (strtolower(substr($uri, 0, strlen($intended))) == strtolower($intended)) {
        $uri = substr($uri, strlen($intended));
      }
      // but strip out any parameters, which might have /s in them, which will completely mess this up
      // (see issue #13)
      if (strpos($uri, "?") !== false) {
        $uri = substr($uri, 0, strpos($uri, "?"));
      }
      $global_calculate_relative_path = str_repeat('../', substr_count($uri, '/'));
    } else {
      $global_calculate_relative_path = "";
    }
  }
  return $global_calculate_relative_path;
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo htmlspecialchars($title); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo calculate_relative_path(); ?>default.css" />
	<link rel="shortcut icon" href="<?php echo calculate_relative_path(); ?>favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="<?php echo calculate_relative_path(); ?>img/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo calculate_relative_path(); ?>img/apple-touch-icon-76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo calculate_relative_path(); ?>img/apple-touch-icon-120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo calculate_relative_path(); ?>img/apple-touch-icon-152.png">
	<meta name="viewport" content="width=660">
</head>
<body id="page_<?php echo htmlspecialchars($request); ?>">

<div id="navigation" class="content">
<?php echo str_replace("@IMAGE@", "<span class=\"navigation_image\"></span>", output_content("navigation")); ?>
</div>

<div id="content" class="content">
<?php echo $parsedown->parse($content); ?>
</div>

<div id="footer" class="content">
<?php echo output_content("footer"); ?>
</div>

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', <?php echo json_encode(get_site_config('google_analytics_account')); ?>]);
  _gaq.push(['_setDomainName', <?php echo json_encode(get_site_config('google_analytics_domain')); ?>]);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>
