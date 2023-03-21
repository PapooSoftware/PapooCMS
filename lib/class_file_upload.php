<?php

if (stristr($_SERVER['PHP_SELF'], 'class_file_upload.php'))
{
	die('You are not allowed to see this page directly');
}

/**
 * Class file_upload
 */
#[AllowDynamicProperties]
class file_upload
{
	/** @var bool Add Praefix to Uploaded filename, Default: True */
	var $addPraefix = true;

	/** @var bool|string ase directory, where your main application is running */
	var $base_dir = '';

	/** @var string where the uploaded file should be copied to, relative to $base_dir */
	var $dest_dir = '';

	/** @var bool|int internal filesize limitation in bytes, 0 = off, php.ini limit always rules */
	var $max_upload_size = 0;

	/** @var int how to treat $extensions, 1 = block 2 = allow extensions in $extensions array */
	var $extensions_mode = 1;

	/** @var array list of file extensions */
	var $extensions = array();

	/** @var int how to handle uploads, 0 = don't overwrite existing file, 1 = rename existing file (see $copy_label), 2 = overwrite */
	var $copy_mode = 0;

	/** @var string if copy mode is 1, $copy_label + n will be appended to existing file */
	var $copy_label = '_copy';

	/** @var string error return message */
	var $error = '';

	/** @var int indicates if class was initialized correctly */
	var $init = 1;

	/** @var array all parameters of the file to be uploaded like in $_FILES, see $file_info also */
	var $file = array();

	/** @var array list of needed parameters for internal validation */
	var $file_info = array('name', 'type', 'size', 'tmp_name', 'error');

	/**
	 * file_upload constructor.
	 * @param bool $base_dir base directory
	 * @param bool $max_upload_size internal filesize limitation in bytes
	 * @return boolean|void
	 *
	 * @access public
	 */
	function __construct($base_dir = false, $max_upload_size = false)
	{
		if ($base_dir == true) {
			if ($dir = $this->check_dir($base_dir)) {
				$this->base_dir = $dir;
			}
			else {
				return false;
			}
		}
		if ($max_upload_size == true) {
			$this->max_upload_size = $max_upload_size;
		}
	}

	/**
	 * upload - main upload method
	 * TODO: parameter beschreibungen stimmen nicht mehr
	 *
	 * @param $file - array of file data like $_FILES
	 * @param bool $dest_dir - directory to copy the uploaded file to (relative to $base_dir)
	 * @param bool $copy_mode - how to handle uploads
	 * @param array|bool $extensions - list of file extensions
	 * @param bool $extensions_mode - how to treat $extensions
	 * @return bool - true if upload was successful
	 *
	 * @access public
	 */
	function upload($file, $dest_dir = false, $copy_mode = false, $extensions = false, $extensions_mode = false)
	{
		if ($this->init != 1) {
			$this->error_msg("error in class initialization");

			return false;
		}
		//böse extensions einfach ausblenden

		if ($extensions_mode == 1) {
			$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
			foreach ($extensions as $key => $dat) {
				if($dat === $ext) {
					//$file['name'] = strtolower($file['name']);
					//$file['name'] = str_replace($dat, "_", $file['name']);
					$file['name'] = substr_replace($file['name'], "_", (strlen($dat) * -1));
					break;
				}
			}
		}

		$this->error = '';

		// check if destination dir exists
		if ($dest_dir == true) {
			if ($dir = $this->check_dir($dest_dir, $this->base_dir)) {
				$this->dest_dir = $dir;
			}
			else {
				return false;
			}
		}

		// check if $file is array
		if (!is_array($file)) {
			$this->error_msg("error in submitting file parameters (not an array)");

			return false;
		}
		// check if all file parameters were submitted
		foreach ($this->file_info as $v) {
			if (!isset($file[$v])) {
				$this->error_msg("Missing parameter $v");

				return false;
			}
		}

		$this->file = $file;

		if ($copy_mode == true) {
			$this->copy_mode = $copy_mode;
		}

		if ($extensions == true) {
			if (!is_array($extensions)) {
				$extensions = array($extensions);
			}
			$this->extensions = $extensions;
		}
		if ($extensions_mode == true) {
			$this->extensions_mode = $extensions_mode;
		}

		// check $_FILES error segment
		if ($this->check_upload_error() != true) {
			return false;
		}

		// check file size
		if ($this->check_upload_size() != true) {
			return false;
		}

		// get file extension
		$this->file['ext'] = $this->get_ext();

		// check if file type is allowed for upload
		if ($this->check_extensions() != true) {
			return false;
		}

		// check if file comes via HTTP POST
		if (!is_uploaded_file($this->file['tmp_name'])) {
			$this->error_msg("No HTTP POST Upload");

			return false;
		}

		$this->create_new_filename();

		// copy the file considering copy mode
		switch ($this->copy_mode) {

		case 0: // don't move if file already exists
			if (file_exists($this->base_dir . $this->dest_dir . $this->file['name'])) {
				$this->error_msg("already exists");

				return false;
			}
			else {
				return $this->move_file();
			}
			break;

		case 1: // rename file if already exists
			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($add);

			$n = 1;
			$split = explode("." . $this->file['ext'], $this->file['name']);
			while (file_exists($this->base_dir . $this->dest_dir . $split[0] . $add . "." . $this->file['ext'])) {
				$add = $this->copy_label . $n;
				$n++;
			}
			$this->file['name'] = $split[0] . $add . "." . $this->file['ext'];

			return $this->move_file();
			break;

		case 2: // overwrite existing file
			return $this->move_file();
			break;

		default:
			$this->error_msg("invalid copy mode");

			return false;
			break;
		}
	}

	/**
	 * move_file - moves the file from upload temp dir to destination dir
	 *
	 * @return bool
	 *
	 * @access private
	 */
	function move_file()
	{

		# Rechte setzen
		#umask(044);#
		#$leer=utf8_encode(" ");


		#$this->file['name']=eregi_replace($leer,"_",$this->file['name']);
		#$this->file['md5name']=md5($this->file['name'].$this->file['size']);
		#$this->file['md5name']=$this->file['name'];
		#$this->dest_dir=$this->dest_dir."".$this->file['md5name']."/";

		#$this->file['name']=substr($this->file['md5name'],0,5)."_".$this->file['name'];
		$this->file['name'] = preg_replace("/[^a-zA-Z0-9_-]_-/", "", $this->file['name']);
		#mkdir(PAPOO_ABS_PFAD."/dokumente/upload/".$upload_do->file['md5name']);
		// move file to destination
		if (!@move_uploaded_file($this->file['tmp_name'], $this->base_dir .
			$this->dest_dir . $this->file['name'])
		) {

			$this->error_msg("file could not be copied to " . $this->dest_dir);

			return false;
		}
		else {
			chmod($this->base_dir . $this->dest_dir . $this->file['name'], 0644);

			return true;
		}
	}

	/**
	 * delete_file
	 * TODO: parameter beschreibung für $best_dir stimmt nicht mehr
	 *
	 * @param string $del_file - file to delete
	 * @param bool $dest_dir - directory which contains file to delete
	 *
	 * @return bool
	 *
	 * @access public
	 */
	function delete_file($del_file, $dest_dir = false)
	{
		// check if dest dir exists
		if ($dest_dir == true) {
			if ($dir = $this->check_dir($dest_dir, $this->base_dir)) {
				$this->dest_dir = $dir;
			}
			else {
				return false;
			}
		}

		if (!@unlink($this->base_dir . $this->dest_dir . $del_file)) {
			$this->error_msg("file $del_file could not be deleted");

			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * check_dir - check if a directory does exist
	 * TODO: parameter beschreibung für $path stimmt nicht mehr
	 *
	 * @param string $dir - dir to check
	 * @param bool $path - path to dir
	 *
	 * @return bool|string
	 *
	 * @access private
	 */
	function check_dir($dir, $path = false)
	{
		if (is_dir($path . $dir) or is_dir($path . $dir . "/")) {
			return (substr($dir, -1) == "/") ? $dir : $dir . "/";
		}
		else {
			$this->error_msg("dir $path$dir does not exist");
			$this->init = 0;

			return false;
		}
	}

	/**
	 * check_upload_error - checks $_FILES error segment
	 *
	 * @return boolean|void
	 *
	 * @access private
	 */
	function check_upload_error()
	{

		switch ($this->file['error']) {
		case 0:
			return true;
		case 1:
			$this->error_msg("php.ini filesize exceeded");

			return false;
			break;
		case 2:
			$this->error_msg("html filesize exceeded");

			return false;
			break;
		case 3:
			$this->error_msg("file uploaded partly");

			return false;
			break;
		case 4:
			$this->error_msg("no file delivered");

			return false;
			break;
		}
	}

	/**
	 * check_upload_size
	 *
	 * @return bool
	 *
	 * @access private
	 */
	function check_upload_size()
	{

		if ($this->max_upload_size == true && $this->file['size'] > $this->max_upload_size) {
			$this->error_msg("upload exceeded " . $this->max_upload_size . " Bytes");

			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * check_extensions - checks if file extension is allowed for upload
	 *
	 * @return bool
	 *
	 * @access private
	 */
	function check_extensions()
	{


		if ((in_array($this->file['ext'], $this->extensions) && $this->extensions_mode == 1)
			|| (!in_array($this->file['ext'], $this->extensions) && $this->extensions_mode == 2)) {

			$this->error_msg("upload of " . $this->file['ext'] . "-files not allowed");

			return false;
		}
		elseif ($this->extensions_mode != 1 && $this->extensions_mode != 2) {
			$this->error_msg("invalid extension mode");

			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * get_ext - get the file extension
	 *
	 * @return string extension
	 *
	 * @access private
	 */
	function get_ext()
	{
		return strtolower(substr(strrchr($this->file['name'], '.'), 1));
	}


	/**
	 * error_msg - collects the error messages
	 *
	 * @param string $error_msg - error message
	 *
	 * @return void
	 *
	 * @access private
	 */
	function error_msg($error_msg)
	{

		if (!empty($this->error)) {
			$this->error .= " & " . $error_msg;
		}
		else {
			$this->error = $error_msg;
		}
	}

	/**
	 * @param bool $status
	 */
	function set_addPraefix($status = true)
	{
		$this->addPraefix = $status;
	}

	/**
	 * creates a filename to be stored. Adds a prefix to the filename.
	 *
	 * @return void
	 *
	 * @access private
	 */
	function create_new_filename()
	{
		if ($this->addPraefix) {
			$leer = utf8_encode(" ");
			$this->file['name'] = str_replace($leer, "_", $this->file['name']);
			$this->file['md5name'] = md5($this->file['name'] . $this->file['size']);
			$this->file['name'] = substr($this->file['md5name'], 0, 5) . "_" . $this->file['name'];
		}
	}
}