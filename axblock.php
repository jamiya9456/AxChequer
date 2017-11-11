<?php

define('AXBLOCK_VER','AXBLOCK v1.0.0');
define('AXBLOCK_INI','axblock.ini');

/*
   The AxChequer's AxBloquer

   Use PHP to block requests rather than the .htaccess file, using regular 
   expression/string matches on User-Agent, Referer, Remote-Addr and Query 
   String. Match "rules" are stored in text files.

   Usage is to customize the files:

      axblock.ini       configure
      ips.txt           substring matches on remote-addr
      agents.txt        regex matches on user-agent
      referers.txt      regex matches on referer

   And then include this file and call these functions:

      axblock()         configuration read, and data access (required)
      axcheckuser()     check server data for blocks

   The code will send status codes and/or text and/or terminate, or call a 
   user defined function.

   In addition, a (single) POST id for comment text can be searched for spam - 
   either by substring or regular expression:

      axcheckinput()    check POST data for blocks

   This will not terminate but either call a user defined function for set 
   constants to the matched spam string.

   See the INI file for more documentation.
*/

// running is test mode from index.php:
if (defined('AXTEST')) {
	register_shutdown_function('shut');
	$_SERVER['HTTP_USER_AGENT'] = $agent;
	if ($remote) $_SERVER['REMOTE_ADDR'] = $remote;
	$_SERVER['HTTP_REFERER'] = $referer;
	$url = parse_url($url);
	$_SERVER['REQUEST_URI'] = $url['path'];
	$_SERVER['REQUEST_SCHEME'] = $url['scheme'];
	if (isset($url['query']))
		$_SERVER['QUERY_STRING'] = $url['query'];
	$url = '';
	axblockrun();
	define('AXBLOCKDONE',1);
}

function axblockrun() {
	axblock();
	axcheckuser();
	axcheckinput();
	axblock(FALSE);
}

function axblock($var = NULL) {
static $block = array();
static $lists = array('ips','agents','referers','banwords','modwords','queries');
	if (!$block) {
		if (!($block = parse_ini_file(AXBLOCK_INI,TRUE)))
			exit(sprintf("%s error: %s\n",AXBLOCK_INI,gettype($block)));
		if (!isset($_SERVER['HTTP_USER_AGENT']))
			$_SERVER['HTTP_USER_AGENT'] = '';

	}
	if ($var === FALSE)
		return $block = array();
	if (!$var)
		return $block;
	if (!isset($block[$var]))
		return NULL;
	if (in_array($var,$lists)) {
		if (!($file = file($block[$var],FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)))
			return array();
		return array_filter($file,function($v){return $v[0] != ';';});
	}
	return $block[$var];
}


function axcheckuser() {
	if (!empty($_SERVER['HTTP_REFERER'])) {
		foreach (axblock('referers') as $re) {
			if (preg_match($re,$_SERVER['HTTP_REFERER']))
				axerror('bad_referer',$re,$_SERVER['HTTP_REFERER']);
		}
	}
	foreach (axblock('ips') as $ip) {
		if (strpos($_SERVER['REMOTE_ADDR'],$ip) !== FALSE)
			axerror('bad_remote_addr',$ip,$_SERVER['REMOTE_ADDR']);
	}
	foreach (axblock('agents') as $re) {
		if (preg_match($re,$_SERVER['HTTP_USER_AGENT'])) {
			if (empty($_SERVER['HTTP_USER_AGENT']))
				$_SERVER['HTTP_USER_AGENT'] = '""';
			axerror('bad_user_agent',$re,$_SERVER['HTTP_USER_AGENT']);
		}
	}
	if (!empty($_SERVER['QUERY_STRING'])) {
		foreach (axblock('queries') as $re) {
			if (preg_match($re,$_SERVER['QUERY_STRING']))
				axerror('bad_query',$re,$_SERVER['QUERY_STRING']);
		}
	}
}

function axcheckinput() {
	if (!($data = axblock('comment_post_id')) || empty($_POST[$data]))
		return;

	$data =& $_POST[$data];

	foreach (axblock('banwords') as $re) {
		if ($re[0] == '/')
			$m = preg_match($re,$data);
		else
			$m = stristr($data,$re);
		if ($m) {
			axerror('bad_comment_word',$re);
			break;
		}
	}

	if (!($count = (int)axblock('mod_words_count')))
		return;
	$n = 0; $ww = '';
	foreach (axblock('modwords') as $words) {
		foreach (explode(' ',$words) as $w) {
			$r = preg_match_all("/$w/is",$data);
			if ($r) $ww .= "$w ";
			$n += $r;
		}
	}
	if ($n >= $count)
		axerror('bad_mod_word',$ww);
}

function axerror($var, $re = NULL, $val = NULL) {
	// for comment string blocking this will return to the calling code, 
	// either by return value of custome function, or setting AXBLOCK 
	// constants indicating the kind of blocked strings 
	if ($var == 'bad_comment_word') {
		if (function_exists($var))
			return $var();
		define('AXBLOCK_COMMENT_BAD',$re);
		return;
	}
	if ($var == 'bad_mod_word') {
		if (function_exists($var))
			return $var();
		define('AXBLOCK_COMMENT_MOD',$re);
		return;
	}
	if (!($bad = axblock($var)))
		return;

if (defined('AXTEST')) {
	header("X-error: $var '$bad'");
	if ($re)
		header("X-match: $re");
	if ($val)
		header("X-value: $val");
}
	if (is_numeric($bad))
		http_header($bad,TRUE);
	if (function_exists($bad))
		$bad();
	if (preg_match('/^(\d+)\s*(.*)/',$bad,$m))
		http_error($m[1],$m[2]);
	exit();
}



function http_header($code, $xcode = NULL) {
static $header = array(
410 => '410 Gone',
404 => '404 Not Found',
403 => '403 Forbidden',
400 => '400 Bad Request',
);
	if (empty($header[$code]))
		return;
	header('HTTP/1.1 '.$h=$header[$code]);
if (defined('AXTEST')) {
	header("X-status: $code");
}
	if ($xcode !== NULL) {
		if ($xcode === TRUE) exit();
		exit(is_numeric($xcode)?(int)$xcode:$xcode);
	}
}

function http_error($err = 403, $file = '') {
	if ($err)
		http_header($err);
	if ($file) {
		if (!is_file($file))
			exit($file);
		if (strpos($file,'.htm') === FALSE) // not foolproof
			header('Content-type: text/plain');
		readfile($file);
	}
	exit();
}

function notfound() {
	http_error(404);
}
function forbidden() {
	http_error(403);
}
function badrequest() {
	http_error(400);
}

function shut() {
if (defined('AXBLOCKDONE'))
	return;
$hlist = headers_list();
if (!strpos(implode('',$hlist),'text/plain'))
	print("<pre>");
print("\nheaders sent");
foreach ($hlist as $h)
	print("\n$h");
print("\n\n--\n".AXBLOCK_VER);
}


function bad_user_agent() {
	http_header(400);
	if (!($ua=$_SERVER['HTTP_USER_AGENT']))
print <<< EOE
<p>Empty User-Agent fields not allowed.</p>
EOE;
	else
print <<< EOE
<p>Invalid User-Agent field. ($ua)</p>
EOE;
}
