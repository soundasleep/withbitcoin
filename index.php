<?php

require 'vendor/autoload.php';
require 'config.php';

$parsedown = new Parsedown();

// clean up the request URI
$request = isset($_GET['q']) ? $_GET['q'] : "";
$request = preg_replace("#[^a-zA-Z0-9\_\-]+#im", "", $request);
if (!$request) $request = "index";

if (!file_exists(__DIR__ . "/" . $request . ".md")) {
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
	$content = fix_relative_links(file_get_contents(__DIR__ . "/" . $request . ".md"));
	return $parsedown->parse($content);
}

$content = fix_relative_links(file_get_contents(__DIR__ . "/" . $request . ".md"));
// strip out the title from the first line
$title = explode("\n", $content, 2);
if (count($title) == 2) {
	$title = $title[0];
	// strip out any # heading characters
	$title = trim(str_replace("#", "", $title));
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

?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo htmlspecialchars($title); ?></title>
	<link rel="stylesheet" type="text/css" href="default.css" />
	<script type="text/javascript" src="js/common.js"></script>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="img/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="76x76" href="img/apple-touch-icon-76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="img/apple-touch-icon-120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="img/apple-touch-icon-152.png">
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
</script>
</body>
</html>
