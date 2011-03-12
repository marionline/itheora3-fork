<?php
require_once('lib/ogg.class.php');
/**
 * itheora 
 * 
 * @package 
 * @copyright 2011
 * @author Mario Santagiuliana <mario@marionline.it>
 */
class itheora {

    protected $_videoName = 'example'; // Default value of videoName
    protected $_videoStoreDir;
    protected $_cacheDir;
    protected $_baseUrl;
    protected $_files = array();

    /**
     * __construct 
     * 
     * @access protected
     * @return void
     */
    function __construct() {
	// Local video store directory
	$this->_videoStoreDir = dirname(__FILE__) . '/../video';
	// Local cache directory
	$this->_cacheDir = dirname(__FILE__) . '/../cache';
	// The name of the server host
	$this->_baseUrl = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5)) == 'https://' ? 'https://' : 'http://' 
	    . $_SERVER['HTTP_HOST']
	    . pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);

	// Check if cache directory exist and is writable, if not writable change chmod
	if(is_dir($this->_cacheDir) && !is_writable($this->_cacheDir))
	    chmod($this->_cacheDir, 0755); // 755 should be ok

	$this->getFiles();
    }

    /**
     * video 
     * 
     * @access protected
     * @return string
     */
    protected function video() {
	return $this->_videoStoreDir . '/' . $this->_videoName;
    }

    /**
     * completeUrl 
     * 
     * @param string $file 
     * @access protected
     * @return string
     */
    protected function completeUrl($file) {
	return $this->_baseUrl . '/video/' . $this->_videoName . '/' . $file;
    }

    /**
     * getFiles 
     * 
     * @access protected
     * @return void | false
     */
    protected function getFiles() {
	// Get the files, video and picture to use
	if(is_dir($this->video())){
	    if ($handle = opendir($this->video()) ) {
		while (false !== ($file = readdir($handle))) {
		    $this->_files[pathinfo($file, PATHINFO_EXTENSION)] = $file;
		}
		closedir($handle);
	    }
	} else {
	    if($this->_videoName != 'error'){
		$this->setVideoName('error');
		$this->getFiles();
	    } else {
		return false;
	    }
	}
    }

    /**
     * setVideoName 
     * 
     * @param string $videoName 
     * @access public
     * @return void
     */
    public function setVideoName($videoName) {
	$this->_videoName = $videoName;
	$this->getFiles();
    }

    /**
     * getVideoName 
     * 
     * @access public
     * @return string
     */
    public function getVideoName() {
	return $this->_videoName;
    }

    /**
     * setVideoDir 
     * 
     * @param string $videoDir 
     * @access public
     * @return void
     */
    public function setVideoDir($videoDir) {
	$this->_videoStoreDir = $videoDir;
    }
    
    /**
     * getVideoDir 
     * 
     * @access public
     * @return string
     */
    public function getVideoDir() {
	return $this->_videoStoreDir;
    }
    
    /**
     * setBaseUrl 
     * 
     * @param string $baseUrl 
     * @access public
     * @return void
     */
    public function setBaseUrl($baseUrl) {
	$this->_baseUrl = $baseUrl;
    }

    /**
     * getBaseUrl 
     * 
     * @access public
     * @return string
     */
    public function getBaseUrl() {
	return $this->_baseUrl;
    }

    /**
     * getPoster 
     * 
     * @param array $filetypes default value ('png', 'jpg', 'gif')  
     * @access public
     * @return string | false
     */
    public function getPoster( $filetypes = array('png', 'jpg', 'gif') ) {
	if(is_array($filetypes)){
	    foreach( $filetypes as $filetype ) {
		if(isset($this->_files[$filetype]))
		    return $this->completeUrl($this->_files[$filetype]);
	    }
	}

	// If no pictures are found return false
	return false;
    }

    /**
     * getPosterSize 
     * return the getimagesize array, need GD Library
     * 
     * @param array $filetypes default value ('png', 'jpg', 'gif')  
     * @access public
     * @return array
     */
    public function getPosterSize( $filetypes = array('png', 'jpg', 'gif') ) {
	if(is_array($filetypes)){
	    foreach( $filetypes as $filetype ) {
		if(isset($this->_files[$filetype]))
		    return getimagesize($this->_videoStoreDir . '/' . $this->_videoName .  '/' .$this->_files[$filetype]);
	    }
	}

	// If no pictures are found return false
	return false;
    }

    /**
     * getVideo 
     * 
     * @param string $extension 
     * @access protected
     * @return string | false
     */
    protected function getVideo($extension) {
	if(isset($this->_files[$extension]))
	    return $this->completeUrl($this->_files[$extension]);
	else
	    return false;
    }

    /**
     * getOggVideo 
     * 
     * @access public
     * @return string | false
     */
    public function getOggVideo() {
	if($video = $this->getVideo('ogg'))
	    return $video;
	elseif($video = $this->getVideo('ogv'))
	    return $video;
	else
	    return false;
    }

    /**
     * getMP4Video 
     * 
     * @access public
     * @return string | false
     */
    public function getMP4Video() {
	return $this->getVideo('mp4');
    }

    /**
     * getWebMVideo 
     * 
     * @access public
     * @return string | false
     */
    public function getWebMVideo() {
	return $this->getVideo('webm');
    }
}
