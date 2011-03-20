<?php
// Include configuration
include_once('config/config.inc.php');
require_once('lib/itheora.class.php');
require_once('lib/aws-sdk/sdk.class.php');

// Get parameters from $_GET array
if(isset($_GET['v']) && $_GET['v'] != ''){
    $video = $_GET['v'];
}

if(isset($_GET['r'])){
    // If is a remote video instance Zend_Cloud
    if($_GET['r'] != '')
	$video  = $_GET['r'];

    // Inizialise AmazonS3 and itheora
    $s3 = new AmazonS3();
    if(isset($itheora_config['s3_vhost']) && $itheora_config['s3_vhost'] != '')
	$s3->set_vhost($itheora_config['s3_vhost']);
    $itheora = new itheora(60, null, $s3, $itheora_config);
} else {
    $itheora = new itheora();
}
if(isset($video))
    $itheora->setVideoName($video);

$posterSize = $itheora->getPosterSize();

$width  = isset($_GET['w']) ? ((int)$_GET['w']) : $posterSize[0];
$width  = $width.'px';

$height = isset($_GET['h']) ? ((int)$_GET['h']) : $posterSize[1];
$height = $height.'px';

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>HTML5 Video Player</title>

  <!-- Include the VideoJS Library -->
  <script src="<?php echo $itheora->getBaseUrl(); ?>/video-js/video.js" type="text/javascript" charset="utf-8"></script>

  <script type="text/javascript">
    // Must come after the video.js library
    // Add VideoJS to all video tags on the page when the DOM is ready
    VideoJS.setupAllWhenReady();
  </script>

  <!-- Include the VideoJS Stylesheet -->
  <link rel="stylesheet" href="<?php echo $itheora->getBaseUrl(); ?>/video-js/video-js.css" type="text/css" media="screen" title="Video JS">

      <style type="text/css">
	    html, body {
		margin: 0px;
	    }
      </style>
</head>
<body>
    <?php
    createVideoJS($itheora, $itheora_config, $width, $height);
    ?>
</body>
</html>
