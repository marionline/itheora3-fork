<?php
require_once('../config/config.inc.php');
//require_once('../lib/functions.php');
require_once('../lib/itheora.class.php');
require_once('../lib/aws-sdk/sdk.class.php');

session_start();
if(!isset($_SESSION['login'])){
    header('Location: login.php');
    exit;
}

if($_GET){
    if(isset($_GET['s3']) && $_GET['s3'] == true) {
	$s3 = new AmazonS3($itheora_config['aws_key'], $itheora_config['aws_secret_key']);
	$s3->set_region($itheora_config['s3_region']);
	$s3->set_vhost($itheora_config['s3_vhost']);
	if(isset($_GET['file']))
	    $response = $s3->delete_object($itheora_config['bucket_name'], $_GET['file']);
    } else {
	$itheora = new itheora();
	if(isset($_GET['dir'])) {
	    rrmdir($itheora->getVideoDir() . '/' . $_GET['dir']);
	} elseif(isset($_GET['file'])) {
	    $file = $itheora->getVideoDir() . '/' . pathinfo($_GET['file'], PATHINFO_FILENAME) . '/' . $_GET['file'];
	    if(is_file($file))
		unlink($file);
	}
    }
}

header('Location: index.php');
