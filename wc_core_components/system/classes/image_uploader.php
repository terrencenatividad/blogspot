<?php
class image_uploader {

	private $files;
	private $error;
	private $max_size;

	public function __construct() {
		$this->files		= $_FILES;
		$this->error		= array();
		$this->folder		= '';
		$this->destination = '/';
		$this->size			= array();
		$this->sizes		= array('large', 'thumb');
		$this->max_size		= (object) array('large' => 800, 'thumb' => 250);
	}

	public function setSize($size = '') {
		$this->size = (is_array($size)) ? $size : array_map('trim', explode(',', $size));
		return $this;
	}

	public function setFolderName($folder = '') {
		$this->folder = $folder;
		return $this;
	}

	public function setImage($imagename, $path) {
		$filename = '';
		$filename = $this->convertImage($imagename, $path . '/' . $imagename);
		return $filename;
	}

	public function getImage($file) {
		$result = $this->checkImage($file);
		$filename = '';
		if ($result) {
			$filename = $this->convertImage($result['name'], $result['tmp_name']);
		}
		return $filename;
	}
	
	public function convertImage($imagename, $imagepath) {
		$filename = '';
		$this->destination	= (($this->folder) ? $this->folder . '/' : '');
		$this->sizes		= array('large', 'thumb');
		$name_array			= explode('.', $imagename);
		$ext				= $name_array[count($name_array) - 1];
		$filename			= $this->getFileName($ext) . ".$ext";
		$image_function		= '';
		$image_source		= $this->getImageFromFile($imagepath, $ext, $image_function);
		foreach ($this->size as $size) {
			if (in_array($size, $this->sizes)) {
				$directory = $this->destination . "$size";
				if ( ! is_dir($this->destination)) {
					mkdir($this->destination, 0755);
					chmod($this->destination, 0755);
				}
				if ( ! is_dir($directory)) {
					mkdir($directory, 0755);
					chmod($directory, 0755);
				}
				list($width, $height)			= getimagesize($imagepath);
				list($new_width, $new_height)	= $this->getNewImageSize($width, $height, $size);
				$converted_image				= imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($converted_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$image_function($converted_image, $directory . "/$filename");
				chmod($directory . "/$filename", 0644);
				imagedestroy($converted_image);
			}
		}
		return $filename;
	}

	public function getErrors() {
		return $this->error;
	}

	private function getNewImageSize($width, $height, $size) {
		$ratio_width	= 1;
		$ratio_height	= 1;
		$max_size = $this->max_size->{$size};
		if ($width > $max_size) {
			$ratio_width = $max_size / $width;
		}
		if ($height > $max_size) {
			$ratio_height = $max_size / $height;
		}

		$ratio = ($ratio_width < $ratio_height) ? $ratio_width : $ratio_height;
		$new_width	= round($width * $ratio);
		$new_height	= round($height * $ratio);
		return array($new_width, $new_height);
	}

	private function getImageFromFile($source, &$ext, &$image_function) {
		$image	= false;
		$ext	= strtolower($ext);
		
		if ($ext == 'jpg' || $ext == 'jpeg') {
			$image			= imagecreatefromjpeg($source);
			$image_function	= 'imagejpeg';
			$ext			= 'jpg';
		} else if ($ext == 'png') {
			$image = imagecreatefrompng($source);
			$image_function	= 'imagepng';
		} else if ($ext == 'gif') {
			$image = imagecreatefromgif($source);
			$image_function	= 'imagegif';
		}

		return $image;
	}

	private function getFileName($ext) {
		$name = substr(md5(rand()), 0, 7);
		foreach ($this->sizes as $size) {
			if (file_exists($this->destination . "$size/$name.$ext")) {
				$name = $this->getFileName($ext);
			}
		}
		return $name;
	}

	private function checkImage($file) {
		if (isset($this->files[$file]) && $this->files[$file]['tmp_name']) {
			$check_files = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
			if (in_array(exif_imagetype($this->files[$file]['tmp_name']), $check_files)) {
				return $this->files[$file];
			} else {
				$this->error[] = "Upload is not an Image";
				return false;
			}
		} else {
			$this->error[] = "Can't find Image";
			return false;
		}
	}

}