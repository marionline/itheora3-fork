<?php
/**
 * Usefull helper function to use with itheora3-fork 
 */
require_once(dirname(__FILE__) . '/../lib/itheora.class.php');

/**
 * createObjectTag 
 * 
 * @param string $video 
 * @param int $width 
 * @param int $height 
 * @param bool $useFilesInCloud 
 * @access public
 * @return html code
 */
function createObjectTag($video = 'example', $width = null, $height = null, $useFilesInCloud = false){
    // If no width or height are passed I use the image width and height
    if(is_null($width) || is_null($height)){
	$itheora = new itheora();
	$itheora->setVideoName($video);
	$posterSize = $itheora->getPosterSize();
    }

    if(!is_null($width)) {
	$width_style = 'width: ' . $width . 'px;';
	$width_url = '&amp;w=' . $width;
    } else {
	$width_style = 'width: ' . $posterSize[0] . 'px;';
	$width_url = '&amp;w=' . $posterSize[0];
    }

    if(!is_null($height)) {
	$height_style = 'height: ' . $height . 'px;';
	$height_url = '&amp;h=' . $height;
    } else {
	$height_style = 'height: ' . $posterSize[1] . 'px;';
	$height_url = '&amp;h=' . $posterSize[1];
    }

    if($useFilesInCloud) {
	$key = 'r';
    } else {
	$key = 'v';
    }

    return '<object id="' . $video . '" name="' . $video . '" class="itheora3-fork" type="application/xhtml+xml" data="itheora.php?' . $key . '=' . $video . $width_url . $height_url . '" style="' . $width_style . ' ' . $height_style . '"> 
	</object>';
}

/**
 * createVideoJS 
 * 
 * @param itheora $itheora      Pass itheora object
 * @param array $itheora_config Pass itheora configuration
 * @param mixed $width          Force width 
 * @param mixed $height         Force height 
 * @access public
 * @return void
 */
function createVideoJS(itheora &$itheora, array &$itheora_config, $width = null, $height = null) {
    if($width === null || $height === null)
	$posterSize = $itheora->getPosterSize();
    if($width === null)
	$width = $posterSize[0].'px';
    if($height === null)
	$height = $posterSize[1].'px';

    if($itheora->useFilesInCloud()) {
	$key = 'r';
    } else {
	$key = 'v';
    }

?>
      <!-- Begin VideoJS -->
      <div class="video-js-box">
	<!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
	<video id="<?php echo $itheora->getVideoName(); ?>" class="video-js" width="<?php echo $width; ?>" height="<?php echo $height; ?>" controls="controls" preload="auto" poster="<?php echo $itheora->getPoster(); ?>">
	  <?php if(($itheora_config['MP4_source'] || $itheora_config['flash_fallback']) && $video = $itheora->getMP4Video()): ?>
	  <source src="<?php echo $video; ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
	  <?php endif; ?>
	  <?php if($itheora_config['WEBM_source'] && $video = $itheora->getWebMVideo()): ?>
	  <source src="<?php echo $video; ?>" type='video/webm; codecs="vp8, vorbis"' />
	  <?php endif; ?>
	  <source src="<?php echo $itheora->getOggVideo(); ?>" type='video/ogg; codecs="theora, vorbis"' />
	  <?php if($itheora_config['flash_fallback'] && $video = $itheora->getMP4Video()): ?>
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
	  <?php if(($itheora_config['MP4_source']) && $video = $itheora->getMP4Video()): ?>
	  <a href="<?php echo $video; ?>" target="_parent">MP4</a>,
	  <?php endif; ?>
	  <?php if($itheora_config['WEBM_source'] && $video = $itheora->getWebMVideo()): ?>
	  <a href="<?php echo $video; ?>" target="_parent">WebM</a>,
	  <?php endif; ?>
	  <a href="<?php echo $itheora->getOggVideo(); ?>" target="_parent">Ogg</a><br>
	  <!-- Share script -->
	  <strong>Share this video:</strong>
	  <br />
	  <span><?php echo htmlspecialchars('<object id="' . $itheora->getVideoName() . '" name="' . $itheora->getVideoName() . '" type="application/xhtml+xml" data="' . $itheora->getBaseUrl() . '/itheora.php?' . $key. '=' . $itheora->getVideoName() . '&amp;w=' . $width . '&amp;h=' . $height . '" style="width:' . $width . '; height:' . $height . '"></object>'); ?></span>
	  <br />
	  <!-- Support VideoJS by keeping this link. -->
	    <small>Powered by <a href="http://videojs.com" target="_parent">VideoJS</a> and <a href="https://github.com/marionline/itheora3-fork" target="_parent">itheora3-fork</a></small>
	</p>
      </div>
      <!-- End VideoJS -->
<?php

}

/**
 * getBaseUrl 
 * 
 * @access public
 * @return string
 */
function getBaseUrl() {
    return strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https://' ? 'https://' : 'http://' 
	. $_SERVER['HTTP_HOST']
	. pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);
}

/**
 * rrmdir 
 * Remove directory recursivley
 * http://www.php.net/manual/en/function.rmdir.php#98622
 * 
 * @param mixed $dir 
 * @access public
 * @return void
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
	$objects = scandir($dir);
	foreach ($objects as $object) {
	    if ($object != "." && $object != "..") {
		if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
	    }
	}
	reset($objects);
	rmdir($dir);
    }
} 
