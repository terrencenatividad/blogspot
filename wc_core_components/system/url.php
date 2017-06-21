<?php
class url {

	public function redirect($location) {
		header('Location: ' . $location);
		exit();
	}

}