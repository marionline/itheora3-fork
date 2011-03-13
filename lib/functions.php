<?php
/**
 * Usefull helper function to use with itheora3-fork 
 */
require_once('lib/itheora.class.php');

/**
 * createObjectTag 
 * 
 * @param string $video 
 * @param mixed $width 
 * @param mixed $height 
 * @access public
 * @return html code
 */
function createObjectTag($video = 'example', $width = null, $height = null){
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

    return '<object id="' . $video . '" name="' . $video . '" class="itheora3-fork" type="application/xhtml+xml" data="itheora.php?v=' . $video . $width_url . $height_url . '" style="' . $width_style . ' ' . $height_style . '"> 
	</object>';
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
