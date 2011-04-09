<?php
require_once('../config/config.inc.php');
require_once('../lib/itheora.class.php');
require_once('../lib/aws-sdk/sdk.class.php');

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
	<?php if(isset($_GET['s3']) && $_GET['s3'] == true): ?>
	    <form action="http://<?php echo $itheora_config['s3_vhost']; ?>" method="post" enctype="multipart/form-data">

		    <?php
			$s3 = new AmazonS3($itheora_config['aws_key'], $itheora_config['aws_secret_key']);
			$s3->set_region($itheora_config['s3_region']);
			$s3->set_vhost($itheora_config['s3_vhost']);

			date_default_timezone_set('UTC');
			$policy = new CFPolicy($s3, array(
			    'expiration' => $s3->util->convert_date_to_iso8601(mktime(date('H')+1)),
			    'conditions' => array(
				array('acl' => 'public-read'),
				array('bucket' => $itheora_config['bucket_name']),
				array('starts-with', '$key', ''),
				array('starts-with', '$success_action_redirect', ''),
			    )
			));
		    ?>

		    <p>
		    Rename the file or don't change it: <input type="text" name="key" value="${filename}" />
		    <input type="hidden" name="acl" value="public-read" />
		    <input type="hidden" name="success_action_redirect" value="http://localhost/~mario/itheora3-fork/admin/index.php" />
		    <input type="hidden" name="AWSAccessKeyId" value="<?php echo $policy->get_key(); ?>" />
		    <input type="hidden" name="Policy" value="<?php echo $policy->get_policy(); ?>" />
		    <input type="hidden" name="Signature" value="<?php echo base64_encode(hash_hmac('sha1', $policy->get_policy(), $s3->secret_key, true))?>" />
		    </p>
		    <p>File: <input type="file" name="file" /></p>
		    <p><input type="submit" name="submit" value="Upload to Amazon S3" /></p>
	    </form>
	<?php else: ?>
	    <h1>Itheora3-fork :: Upload File Locally</h1>
	    <?php if(isset($error_message)) echo $error_message; ?>
	    <form action="addfile.php" method="post" enctype="multipart/form-data">
		<p>Upload file: <input type="file" name="file" /></p>
		<p><input type="submit" name="submit" value="Submit" /></p>
	    </form>
	<?php endif; ?>
    </body>
</html>
