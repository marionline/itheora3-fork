<?php
// Include configuration
include_once('config/config.inc.php');
require_once('lib/itheora.class.php');

// Get parameters from $_GET array
$width  = isset($_GET['w']) ? ((int)$_GET['w'] - 10) : '100%';
$width  = (substr($width, -1) == '%') ? $width : $width.'px';

$height = isset($_GET['h']) ? ((int)$_GET['h'] - 20) : '100%';
$height = (substr($height, -1) == '%') ? $height : $height.'px';

$video  = isset($_GET['v']) ? $_GET['v'] : 'example';
$itheora = new itheora();
$itheora->setVideoName($video);
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

      <!-- Begin VideoJS -->
      <div class="video-js-box">
	<!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
	<video id="example_video_1" class="video-js" width="<?php echo $width; ?>" height="<?php echo $height; ?>" controls="controls" preload="auto" poster="<?php echo $itheora->getPoster(); ?>">
	  <?php if(($MP4_source || $flash_fallback) && $video = $itheora->getMP4Video()): ?>
	  <source src="<?php echo $video; ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
	  <?php endif; ?>
	  <?php if($WEBM_source && $video = $itheora->getWebMVideo()): ?>
	  <source src="<?php echo $video; ?>" type='video/webm; codecs="vp8, vorbis"' />
	  <?php endif; ?>
	  <source src="<?php echo $itheora->getOggVideo(); ?>" type='video/ogg; codecs="theora, vorbis"' />
	  <?php if($flash_fallback && $video = $itheora->getMP4Video()): ?>
	  <!-- Flash Fallback. Use any flash video player here. Make sure to keep the vjs-flash-fallback class. -->
	  <object id="flash_fallback_1" class="vjs-flash-fallback" width="<?php echo $width; ?>" height="<?php echo $height; ?>" type="application/x-shockwave-flash"
	    data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
	    <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
	    <param name="allowfullscreen" value="true" />
	    <param name="flashvars" value='config={"playlist":["<?php echo $itheora->getPoster(); ?>", {"url": "<?php echo $video; ?>","autoPlay":false,"autoBuffering":true}]}' />
	    <!-- Image Fallback. Typically the same as the poster image. -->
	    <img src="<?php echo $itheora->getPoster(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="Poster Image"
	      title="No video playback capabilities." />
	  </object>
	  <?php endif; ?>
	</video>
	<!-- Download links provided for devices that can't play video in the browser. -->
	<p class="vjs-no-video"><strong>Download Video:</strong>
	  <?php if(($MP4_source) && $video = $itheora->getMP4Video()): ?>
	  <a href="<?php echo $video; ?>">MP4</a>,
	  <?php endif; ?>
	  <?php if($WEBM_source && $video = $itheora->getWebMVideo()): ?>
	  <a href="<?php echo $video; ?>">WebM</a>,
	  <?php endif; ?>
	  <a href="<?php echo $video; ?>">Ogg</a><br>
	  <!-- Support VideoJS by keeping this link. -->
	  <a href="http://videojs.com">HTML5 Video Player</a> by VideoJS
	</p>
      </div>
      <!-- End VideoJS -->
</body>
</html>
