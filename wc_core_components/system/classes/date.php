<?php
class date {

	public function datefilterMonth($date = '') {
		$date = $this->convertDate($date, 'M 01, Y', '+0 day') . ' - ' . $this->convertDate($date, 'M t, Y', '+0 day');
		return $date;
	}

	public function dateDbFormat($date = '', $offset = '+0 day') {
		$date = $this->convertDate($date, 'Y-m-d', $offset);
		return $date;
	}

	public function datetimeDbFormat($date = '', $offset = '+0 day') {
		$date = $this->convertDate($date, 'Y-m-d H:i:s', $offset);
		return $date;
	}

	public function dateFormat($date = '', $offset = '+0 day') {
		$date = $this->convertDate($date, 'M d, Y', $offset);
		return $date;
	}

	public function datetimeFormat($date = '', $offset = '+0 day') {
		$date = $this->convertDate($date, 'M d, Y h:i:s A', $offset);
		return $date;
	}

	private function convertDate($date, $format, $offset) {
		if (empty($date)) {
			return date($format, strtotime($offset));
		} else if (strtotime($date) > 0) {
			return date($format, strtotime($date));
		} else {
			return '';
		}
	}

}