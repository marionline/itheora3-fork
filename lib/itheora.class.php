<?php
//require_once('lib/ogg.class.php');
require_once(dirname(__FILE__) . '/../lib/functions.php');
require_once(dirname(__FILE__) . '/../lib/Zend/Cache.php');
/**
 * itheora 
 * 
 * @package 
 * @copyright 2011
 * @author Mario Santagiuliana <mario@marionline.it>
 */
class itheora {

    protected $_videoName = 'example'; // Default value of videoName
    protected $_videoErrorName = 'error';
    protected $_videoStoreDir;
    protected $_baseUrl;
    protected $_files = array();
    protected $_externalVideo = false; // Is set external video?
    protected $_externalUrl = ''; // External video URL if video is not locally
    protected $_supported_video = array('ogg', 'ogv', 'webm', 'mp4');
    protected $_supported_image = array('png', 'jpg', 'gif');
    protected $_mimetype_video = array();
    protected $_mimetype_image = array();
    protected $_cache;
    protected $_storage = null;

    /**
     * __construct 
     * 
     * @param float $cache_lifetime Pass cache lifetime in seconds default 60
     * @param mixed $cache_dir Provide another cache directory
     * @access protected
     * @return void
     */
    function __construct($cache_lifetime = 60 , $cache_dir = null, &$storage = null) {
	// Create supported mimetype image and video
	foreach($this->_supported_image as $extension){
	    if($extension == 'jpg')
		$extension = 'jpeg';
	    $this->_mimetype_image[] = 'image/' . $extension;
	}
	foreach($this->_supported_video as $extension){
	    $this->_mimetype_video[] = 'video/' . $extension;
	}
	// Local video store directory
	$this->_videoStoreDir = dirname(__FILE__) . '/../video';

	// The name of the server host
	$this->_baseUrl = getBaseUrl();

	// Start cache
	$frontendOptions = array(
	    'lifetime' => $cache_lifetime,
	    'automatic_serialization' => true
	);

	if($cache_dir === null) {
	    $backendOptions = array('cache_dir' => dirname(__FILE__) . '/../cache/');
	} else {
	    $backendOptions = array('cache_dir' => $cache_dir);
	}

	if($storage !== null){
	    $this->_storage = $storage;
	}

	$this->_cache = Zend_Cache::factory('Core',
	    'File',
	    $frontendOptions,
	    $backendOptions);

	// Get file, default is example
	$this->getFiles();
    }

    /**
     * completeUrl 
     * 
     * @param string $file 
     * @access protected
     * @return string
     */
    protected function completeUrl($file) {
	if($this->_externalVideo){
	    return $this->_externalUrl . $file;
	} else {
	    return $this->_baseUrl . '/video/' . $this->_videoName . '/' . $file;
	}
    }

    /**
     * check_founded_files 
     * Check if $this->_files store some files or not
     * 
     * @access protected
     * @return void | true if a file is founded
     */
    protected function check_founded_files() {
        if (count($this->_files) == 0)
	    return false;
	else
	    return true;
    }

    /**
     * is_supported_image 
     * 
     * @param mixed $file_headers Pass headers with get_headers($file, 1) 
     * @access protected
     * @return bool
     */
    protected function is_supported_image($file_headers) {
	// Use in_array function to check if mime-type is supported_video
	if(isset($file_headers['Content-Type'])){
	    return in_array($file_headers['Content-Type'], $this->_mimetype_image);
	} else {
	    throw new Exception('$file_headers[\'Content-Type\'] not set, please pass $file_headers with get_headers($file, 1).');
	}
    }
    /**
     * is_supported_video 
     * 
     * @param mixed $file_headers Pass headers with get_headers($file, 1)
     * @access protected
     * @return bool
     */
    protected function is_supported_video($file_headers) {
	// Use in_array function to check if mime-type is supported_video
	if(isset($file_headers['Content-Type'])){
	    return in_array($file_headers['Content-Type'], $this->_mimetype_video);
	} else {
	    throw new Exception('$file_headers[\'Content-Type\'] not set, please pass $file_headers with get_headers($file, 1).');
	}
    }

    /**
     * getExternalFiles 
     * 
     * @access protected
     * @return bool
     */
    protected function getExternalFiles() {
	$id = str_replace(array('/', '\\', ':', '.', '-'), '_', $this->_externalUrl . $this->_videoName);
	if(!($files = $this->_cache->load($id))) {
	    $extensions = array_merge($this->_supported_video, $this->_supported_image);
	    foreach($extensions as $extension) {
		// Basename file
		$file = $this->_videoName . '.' . $extension;
		// Get headers of $file
		$headers = get_headers($this->completeUrl($file), 1);
		// Check if HTTP respons is not a 404 error
		if(substr($headers[0], 9, 3) != '404') {
		    // Check if file is supported
		    if($this->is_supported_image($headers) || $this->is_supported_video($headers)) {
			$this->_files[$extension] = $file;
		    }
		}
	    }
	    $this->_cache->save($this->_files, $id, array('external'));
	} else {
	    $this->_files = $files;
	}

	if(!$this->check_founded_files()) {
	    return $this->setVideoName($this->_videoErrorName);
	} else {
	    return true;
	}
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
     * getLocalFiles 
     * 
     * @access protected
     * @return bool False if no file are found
     */
    protected function getLocalFiles() {
	$id = str_replace(array('/', '\\', ':', '.', '-'), '_', $this->_videoStoreDir . $this->_videoName);
	if(!($files = $this->_cache->load($id))) {
	    if(is_dir($this->video())){
		if ( $handle = opendir($this->video()) ) {
		    while (false !== ($file = readdir($handle))) {
			$this->_files[pathinfo($file, PATHINFO_EXTENSION)] = $file;
		    }
		    closedir($handle);
		}
		$this->_cache->save($this->_files, $id, array('internal'));
	    } else {
		if($this->_videoName != $this->_videoErrorName) {
		    $this->setVideoName($this->_videoErrorName);
		    return $this->getLocalFiles();
		}
	    }
	} else {
	    $this->_files = $files;
	}

	return $this->check_founded_files();
    }

    /**
     * getFilesFromCloud 
     * 
     * @access protected
     * @return void
     */
    protected function getFilesFromCloud() {
	// TODO
    }

    /**
     * getFiles 
     * 
     * @access protected
     * @return bool
     */
    protected function getFiles() {
	// Get the files, video and picture to use
	if($this->_externalVideo){
	    // Videos are store remotely
	    return $this->getExternalFiles();
	} else {
	    if($this->_storage === null){
		// Videos are store locally
		return $this->getLocalFiles();
	    } else {
		// Videos are store in the Cloud access with Zend_Cloud
		return $this->getFilesFromCloud();
	    }
	}
    }

    /**
     * Check if an url is existed
     * Take from here:
     * http://www.php.net/manual/en/function.file-exists.php#76420
     *
     * @param  string    $url
     * @return bool      True if the url is accessible and false if the url is unaccessible or does not exist
     * @throws Exception An exception will be thrown when Curl session fails to start
     */
    protected function url_exists($url)
    {
        if (null === $url || '' === trim($url))
        {
            throw new Exception('The url to check must be a not empty string');
        }
       
        $handle   = curl_init($url);

        if (false === $handle)
        {
            throw new Exception('Fail to start Curl session');
        }

        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);

        // grab Url
        $connectable = curl_exec($handle);

        // close Curl resource, and free up system resources
        curl_close($handle);   
        return $connectable;
    }

    /**
     * setVideoName 
     * 
     * @param string $videoName 
     * @access public
     * @return bool             True if videoName is set, false if there are some errors
     */
    public function setVideoName($videoName) {
	$this->_files = array();
	if($this->url_exists($videoName)){
	    // Check if is supported video
	    if($this->is_supported_video(get_headers($videoName, 1))) {
		// Ok url is valid, the video is supported and store remotely
		$this->_externalVideo = true;
		$url = parse_url($videoName);
		$this->_videoName = pathinfo($url['path'], PATHINFO_FILENAME);
		$this->_externalUrl = str_replace(pathinfo($url['path'], PATHINFO_BASENAME), '', $url['scheme'] . '://' . $url['host'] . $url['path']);
	    } else {
		$this->_videoName = $this->_videoErrorName;
	    }
	    // Now get and search other files
	    return $this->getFiles();
	} else {
	    // It is not a valid url, check if it is a valid name and get file
	    if(preg_match('/^[a-z0-9._-]+$/i', $videoName)){
		$this->_videoName = $videoName;
	    } else {
		$this->_videoName = $this->_videoErrorName;
	    }
	    return $this->getFiles();
	}

	return false;
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
    public function getPoster( $filetypes = null ) {
	if($filetypes === null){
	    $filetypes = $this->_supported_image;
	}
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
    public function getPosterSize( $filetypes = null ) {
	if($filetypes == null){
	    $filetypes = $this->_supported_image;
	}
	if($this->_externalVideo){
	    $string = implode('_', $filetypes) . $this->_externalUrl . $this->_videoName . 'external';
	} else {
	    $string = implode('_', $filetypes) . $this->_videoStoreDir . $this->_videoName . 'internal';
	}
	$id = str_replace(array('/', '\\', ':', '.', '-'), '_', $string);
	if(!($return = $this->_cache->load($id))) {
	    if(is_array($filetypes)){
		foreach( $filetypes as $filetype ) {
		    if(isset($this->_files[$filetype]))
			if($this->_externalVideo){
			    $image_size = getimagesize($this->completeUrl($this->_files[$filetype]));
			    $this->_cache->save($image_size, $id, array('imagesize'));
			    return $image_size;
			} else {
			    $image_size = getimagesize($this->_videoStoreDir . '/' . $this->_videoName .  '/' .$this->_files[$filetype]);
			    $this->_cache->save($image_size, $id, array('imagesize'));
			    return $image_size;
			}
		}
	    }
	} else {
	    return $return;
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

    /**
     * getSupportedMimetype 
     * 
     * @access public
     * @return array of supported mimetype
     */
    public function getSupportedMimetype() {
	return array_merge($this->_mimetype_video, $this->_mimetype_image);
    }
}
