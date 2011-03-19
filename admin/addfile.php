<?php
require_once('../config/config.inc.php');
require_once('../lib/itheora.class.php');

session_start();
if(!isset($_SESSION['login'])){
    header('Location: login.php');
    exit;
}

$itheora = new itheora();

if($_FILES){
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK){
	// Check if file type is supported
	if(in_array($_FILES['file']['type'], $itheora->getSupportedMimetype())) {
	    $tmp_name = $_FILES['file']['tmp_name'];
	    $name = $_FILES['file']['name'];
	    $dest_dir = $itheora->getVideoDir().'/'.pathinfo($name, PATHINFO_FILENAME);
	    // If not exist a directory for new file create it
	    if(!is_dir($dest_dir))
		mkdir($dest_dir);
	    move_uploaded_file($tmp_name, $dest_dir . '/' . $name);
	    header('Location: index.php');
	    exit;
	} else {
	    $error_message = 'File type not supported';
	}
    } else{
	$error_message = 'Cannot upload file for some reasons';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
	<title>Itheora3-fork control panel :: Upload Form</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
	<p><a href="index.php">Admin home</a></p>
	<h1>Itheora3-fork :: Upload File Locally</h1>
	<?php if(isset($error_message)) echo $error_message; ?>
	<form action="addfile.php" method="post" enctype="multipart/form-data">
	    <p>Upload file: <input type="file" name="file" /></p>
	    <p><input type="submit" name="submit" value="Submit" /></p>
	</form>
    </body>
</html>
