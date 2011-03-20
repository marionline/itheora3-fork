<?php
$start_time = microtime(true);
require_once('../config/config.inc.php');
require_once('../lib/itheora.class.php');
require_once('../lib/aws-sdk/sdk.class.php');

session_start();
if(!isset($_SESSION['login'])){
    if(isset($_POST['admin_username']) && isset($_POST['admin_pass'])) { 
	if($_POST['admin_username'] == $itheora_config['admin_username'] && $_POST['admin_pass'] == $itheora_config['admin_pass']){
	    $_SESSION['login'] = $_POST['admin_username'];
	}
    } else {
	header('Location: login.php');
	exit;
    }
}
if(isset($_POST['logout']) && $_POST['logout'] == 'Logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

$itheora = new itheora();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
	<title>Itheora3-fork control panel</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
	<form action="index.php" method="post">
	    <p><input class="submit" type="submit" name="logout" value="Logout" /></p>
	</form>
	<h1>Itheora3-fork Admin Control Panel</h1>
	<h2>List of files saved locally</h2>
	<ul class="itheora-video-local">
	    <?php
	    $content = scandir($itheora->getVideoDir());
            $html = '';
	    if($content) {
		foreach($content as $id => $item) {
		    if( $id > 1 ) {
			$html .= '<li class="itheora-video-name"><a href="delete.php?dir=' . $item . '" class="delete">Delete</a> <strong>' . $item . ':</strong>';
			$subcontent = scandir($itheora->getVideoDir() . '/' . $item);
			if($subcontent) {
			    $html .= '<ul class="itheora-local-files">';
			    foreach($subcontent as $sub_id => $sub_item) {
				if( $sub_id > 1 ) {
				    $html .= '<li><a href="delete.php?file=' . $sub_item . '" class="delete">Delete</a> ' . $sub_item . '</li>';
				}
			    }
			    $html .= '</ul>';
			}
			$html .= '</li>';
		    }
		}
	    }
	    echo $html;
	    ?>
	</ul>
	<p><a href="addfile.php">Add File Locally</a></p>
	<h2>List of remote files:</h2>
	<p>Coming soon...</p>
	<?php
	    $s3 = new AmazonS3();
	    $s3->set_region($itheora_config['s3_region']);
	    $s3->set_vhost('media.marionline.it');
	    var_dump($s3->get_object_list($itheora_config['bucket_name']));
	    var_dump($s3->get_object_url($itheora_config['bucket_name'], 'example.ogv'));
	?>
	<?php
	    $time = microtime(true) - $start_time;
	    echo '<p>' . $time . '</p>';
	    ?>
    </body>
</html>
