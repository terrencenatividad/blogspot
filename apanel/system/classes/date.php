<?php
class date {

	public function dateDbFormat($date = '') {
		$date = $this->convertDate($date, 'Y-m-d');
		return $date;
	}

	public function datetimeDbFormat($date = '') {
		$date = $this->convertDate($date, 'Y-m-d H:i:s');
		return $date;
	}

	public function dateFormat($date = '') {
		$date = $this->convertDate($date, 'M d, Y');
		return $date;
	}

	public function datetimeFormat($date = '') {
		$date = $this->convertDate($date, 'M d, Y h:i:s A');
		return $date;
	}

	private function convertDate($date, $format) {
		if (empty($date)) {
			return date($format);
		} else {
			return date($format, strtotime($date));
		}
	}

}