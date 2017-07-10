<?php
class image {

	private $files;
	private $error;

	public function __construct() {
		$this->files	= $_FILES;
		$this->error	= array();
		$this->folder	= '';
		$this->destination = '/';
		$this->size		= array();
		$this->sizes	= array('large', 'thumb');
	}

	public function setSize($size = '') {
		$this->size = (is_array($size)) ? $size : array_map('trim', explode(',', $size));
		return $this;
	}

	public function setFolderName($folder = '') {
		$this->folder = $folder;
		return $this;
	}

	public function getImage($file) {
		$result = $this->checkImage($file);
		if ($result) {
			$this->destination = '/' . (($this->folder) ? $this->folder . '/' : '');
			$this->sizes = array('large', 'thumb');
			$filename = $this->getFileName();
			foreach ($this->size as $size) {
				if (in_array($size, $this->sizes)) {

				}
			}
		}

		return $result;
	}

	public function getErrors() {
		return $this->error;
	}

	private function getFileName() {
		$name = substr(md5(rand()), 0, 7);
		foreach ($this->sizes as $size) {
			if (file_exists($this->destination . "$size/$name")) {
				$name = $this->getFileName();
			}
		}
		return $name;
	}

	private function convertImage() {

	}

	private function checkImage($file) {
		if (isset($this->files[$file])) {
			$check_files = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
			var_dump(exif_imagetype($this->files[$file]['tmp_name']));
			if (in_array(exif_imagetype($this->files[$file]['tmp_name']), $check_files)) {
				echo 'success';
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