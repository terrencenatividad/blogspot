<?php
class table {

	public function __construct() {
		$this->reset();
	}

	public function reset() {
		$this->header_attribute	= array();
		$this->thead			= array();
	}

	public function addHeader($value, $attribute = array(), $type = '', $field = '', $sort = '') {
		$this->thead[] = (object) array(
			'value'		=> $value,
			'type'		=> $type,
			'field'		=> $field,
			'sort'		=> $sort,
			'attribute' => $attribute
		);
		return $this;
	}

	public function setHeaderClass($class) {
		$this->header_attribute['class'] = $class;
		return $this;
	}

	public function draw() {
		$table = '';
		$thead_class = (isset($this->header_attribute['class'])) ? ' class="' . $this->header_attribute['class'] . '"' : '';
		$table .= ($this->thead) ? "<thead><tr{$thead_class}>" : '';
		foreach ($this->thead as $thead) {
			$table .= '<th' . $this->getAttributes($thead->attribute) . '>' . $this->getSort($thead) . '</th>';
		}
		$table .= ($this->thead) ? '</tr></thead>' : '';
		return $table;
	}

	private function getSort($thead) {
		$sort = '';
		$data_sort = ($thead->type == 'sort') ? ' data-sort="' . $thead->sort . '" data-field="' . $thead->field . '"' : '';
		$data_span = ($thead->type == 'sort') ? '<span></span>' : '';
		$sort .= '<a' . $data_sort . '>' . $thead->value . $data_span . '</a>';
		return $sort;
	}

	private function getAttributes($attribute) {
		$attr = array();
		foreach ($attribute as $key => $value) {
			$attr[] = $key . '="' . $value . '"';
		}
		return ' ' . implode(' ', $attr);
	}

}