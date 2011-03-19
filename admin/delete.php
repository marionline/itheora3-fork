<?php
require_once('../config/config.inc.php');
require_once('../lib/functions.php');
require_once('../lib/itheora.class.php');

session_start();
if(!isset($_SESSION['login'])){
    header('Location: login.php');
    exit;
}

if($_GET){
    $itheora = new itheora();
    if(isset($_GET['dir'])) {
	rrmdir($itheora->getVideoDir() . '/' . $_GET['dir']);
    } elseif(isset($_GET['file'])) {
	$file = $itheora->getVideoDir() . '/' . pathinfo($_GET['file'], PATHINFO_FILENAME) . '/' . $_GET['file'];
	if(is_file($file))
	    unlink($file);
    }
}

header('Location: index.php');
