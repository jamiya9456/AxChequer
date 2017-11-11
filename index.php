<?php

/*
   The AxChequer HTML/code to test Apache access.

   Now also can use the AxBlock PHP code to block/flag users and/or comment 
   spam.
*/

const AXCHECK_VER = 'v2.0.1';
const URL = 'http://localhost/ax/';
const DIR = 'check/';

$HTA = glob('htaccess*.txt');
$XRE = array('seo','semalt',);
$XUA = array('Synapse',
'Firefox/3.6',
'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:47.0) Gecko/20100101 Firefox/47.6',
);
// external lists
const UA_LIST = 'agents-ex.txt';
const RE_LIST = 'referers-ex.txt';
if (is_file(UA_LIST))
	$XUA = array_merge($XUA,file(UA_LIST,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
if (is_file(RE_LIST))
	$XRE = array_merge($XRE,file(RE_LIST,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
// post data to variables
const POST = ['referer','xreferer','agent','xagent','post','pout','pall','remote','comment','axblock', ];
if (empty($_POST['url']))
	$_POST['url'] = URL.DIR;
foreach (POST as $p)
	if (!isset($_POST[$p]))
		$_POST[$p] = '';
extract($_POST);
// example access files
if (isset($_GET['hta'])) {
	$t = copy($_GET['hta'],DIR.'.htaccess');
	var_dump($t);
	print "<a href='{$_SERVER['PHP_SELF']}'>reload</a>";
	exit;
}
// post feedback
if ($post) {
	print '<pre>';
	print_r($_POST);
	print '</pre>';
}
// process request
if ($axblock) {
	define('AXTEST',1);
	include 'axblock.php';
	$axbres = '';
	// have exited for server data blocked
	// for comment data blocking need to return to caller with defines
	if (defined('AXBLOCK_COMMENT_BAD'))
		$axbres .= 'comment spam word: "'.AXBLOCK_COMMENT_BAD.'"';
	if (defined('AXBLOCK_COMMENT_MOD'))
		$axbres .= ' comment moderate words: "'.AXBLOCK_COMMENT_MOD.'"';
	if ($axbres == '')
		$axbres = 'nothing to block';
	$axbres = 'axblock: '.$axbres;
}
?>
<head>
<title>The AxChequer</title>
<meta name="version" content="<?=AXCHECK_VER?>">
<style>
form{font-family:'Courier New';font-size:14px}
form{margin-bottom:-20px}
input[type=text]{width:400px;margin-bottom:1px;font-family:inherit}
select{min-width:400px;max-width:500px;margin-bottom:1px;padding:2px;font-family:inherit}
button{vertical-align:4px;font-family:inherit}
textarea{}
</style>
</head>
<body id="fff">
<?php
if (isset($axbres))
	print($axbres);
if ($url)
	submit();

if (strpos($url,URL) === 0) {
?>
<!-- examples list -->
<div style="position:absolute;top:2px;right:2px;width:300px;font-size:14px">
<form method="get">
<?php
foreach ($HTA as $hta) {
        print "<input type='radio' name='hta' value='$hta'> ";
        print "<a href='$hta'>$hta</a><br>\n";
}
?>
<span>use selected .htaccess file</span><br>
<button>select</button>
</form>
</div>
<?php } ?>
<!-- submit form -->
<form method="post">
<input type="text" name="url" value="<?=$_POST['url']?>"> url <br>
<input type="text" name="referer" value="<?=$_POST['referer']?>"> referer <br>
<input type="text" name="agent" value="<?=$_POST['agent']?>"> agent <br>
<select name="xreferer">
<option></option>
<?php
foreach ($XRE as $xre) {
	$s = ($xreferer == $xre) ?'selected' : '';
	print "<option $s>$xre</option>\n";
}
?>
</select> referer from list <br>
<select name="xagent">
<option></option>
<?php
foreach ($XUA as $xua) {
	$s = ($xagent == $xua) ?'selected' : '';
	print "<option $s>$xua</option>\n";
}
?>
</select> agent from list <br>
<input type="text" name="remote" title="for axblock" value="<?=$_POST['remote']?>"> IP <br>
<input type="text" name="comment" title="for axblock" value="<?=$_POST['comment']?>"> comment data <br>
output:
<span title="show POST data"><input type="checkbox" name="post" <?=c($post)?>> post </span>
<span title="show header to be sent"><input type="checkbox" name="pout" <?=c($pout)?>> out </span>
<span title="show all returned data"><input type="checkbox" name="pall" <?=c($pall)?>> all </span>
<span title="use axblock.php"><input type="checkbox" name="axblock" <?=c($axblock)?>> axblock </span>
<button name="button">send</button> <a href="<?=$_SERVER['PHP_SELF']?>">reset</a> <br>
</form>
<?php
// feedback
print("<pre>");
if (strpos($url,URL) === 0) {
	print("<br>--\n");
	if (is_file($_SERVER['DOCUMENT_ROOT'].'/.htaccess'))
		print("root .htaccess might interfere with testing\n\n");
	print(htmlentities(file_get_contents(DIR.'.htaccess')));
	print("</pre>");
}

// support functions

function c($d) {
	if ($d) print 'checked';
}

function submit() {
	extract($_POST);
	if ($xagent) $agent = $xagent;
	if ($xreferer) $referer = $xreferer;

	$url = parse_url($url);
	if (!isset($url['path']))
		$url['path'] = '/';
	if (isset($url['query']))
		$url['path'] .= '?'.$url['query'];
	if (!isset($url['scheme'])) {
		echo "not sure about that url, sorry<br>";
		return FALSE;
	}

	if (preg_match('/https/',$url['scheme']))
		$fp = fsockopen("ssl://".$url['host'],443,$errno,$errstr,30);
	else
		$fp = fsockopen($url['host'],80,$errno,$errstr,30);
	if (!$fp) {
		echo "$errstr<br>";
		return FALSE;
	}

	$out = "GET {$url['path']} HTTP/1.1\r\n";
	$out .= "Host: {$url['host']}\r\n";
	if ($agent)
		$out .= "User-Agent: $agent\r\n";
	if ($referer)
		$out .= "Referer: $referer\r\n";
	$out .= "Connection: Close\r\n\r\n";

	if ($pout)
		print "<pre>".htmlentities($out)."</pre>";

	fwrite($fp,$out);

	$i = 0;
	while (!feof($fp)) {
		$in[] = fgets($fp);
		if (++$i > 512)	// no need for too much
			break;
	}
	fclose($fp);

	print '<pre>';
	while (list(,$i) = each($in)) {
		if ($i == "\r\n")
			break;
		print $i;
	}
	if ($pall) { 
		print $i;
		while (list(,$i) = each($in))
			print htmlentities($i);
	}
	print '</pre>';
}
