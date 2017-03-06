<?php
class ui {

	private $attribute = array();
	private $list = array();
	private $value = '';
	private $type = '';
	private $addon = '';
	private $label = '';
	private $draw = '';

	public function formField($type) {
		$this->attribute = array();
		$this->list = array();
		$this->value = '';
		$this->type = $type;
		$this->addon = '';
		$this->draw = '';
		return $this;
	}

	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	public function setAddon($addon) {
		$this->addon = $addon;
		return $this;
	}

	public function setName($name) {
		$this->attribute['name'] = $name;
		return $this;
	}

	public function setId($id) {
		$this->attribute['id'] = $id;
		return $this;
	}

	public function setPlaceholder($placeholder) {
		$this->attribute['placeholder'] = $placeholder;
		return $this;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function draw($draw = true) {
		if ($draw) {
			$label = $this->createLabel();
			$input = $this->checkDraw();
			$addon = $this->createAddon();
			if (empty($this->addon)) {
				$this->draw = '<div class="form-group">' . $label . $input . '</div>';
			} else {
				$this->draw = '<div class="form-group"><div class="input-group">' . $label . $input . $addon . '</div></div>';
			}
		}
		return $this->draw;
	}

	public function checkDraw() {
		switch ($this->type) {
			case "text":
				return $this->createInputText();
				break;
			case "password":
				return $this->createInputText('password');
				break;
			case "dropdown":
				return $this->createDropDown();
				break;
			case "textarea":
				return $this->createTextarea();
				break;
			case "radio":
				return $this->createRadio();
				break;
			case "checkbox":
				return $this->createCheckbox();
				break;
		}
	}

	public function createLabel() {
		$label = '';
		$for = ((isset($this->attribute['id']) && ! empty($this->attribute['id'])) ? ' for="' . $this->attribute['id'] . '"' : '');
		if ( ! empty($this->label)) {
			$label = '<label' . $for . '>' . $this->label . '</label>';
		}
		return $label;
	}

	public function createAddon() {
		$addon = '';
		if ( ! empty($this->addon)) {
			$addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-{this->addon}"></i></div>';
		}
		return $addon;
	}

	public function createInputText($type = 'text') {
		$attributes = array();
		foreach ($this->attributes as $key => $value) {
			$attributes[] = '{$key}="{$value}"';
		}
		$attributes = implode(' ', $attributes);
		$input = '<input type="{$type}" {$attributes} value="{$this->value}">';
		return $input;
	}

	public function createTextarea() {
		$attributes = array();
		foreach ($this->attributes as $key => $value) {
			$attributes[] = '{$key}="{$value}"';
		}
		$attributes = implode(' ', $attributes);
		$input = '<textarea {$attributes}>{$this->value}</textarea>';
		return $input;
	}

	public function createDropDown() {
		$attributes = array();
		foreach ($this->attributes as $key => $value) {
			$attributes[] = '{$key}="{$value}"';
		}
		$attributes = implode(' ', $attributes);
		$placeholder = (isset($this->attributes['placeholder'])) ? '<option></option>' : '';
		$input = '<select {$attributes}>{$placeholder}';
		foreach ($this->list as $key => $value) {
			$selected = ($key == $this->value) ? ' selected' : '';
			$input .= '<option value="{$key}"{$selected}>{$value}</option>';
		}
		$input .= '</select>';
		return $input;
	}

	public function createRadio() {

	}

	public function createCheckbox() {

	}

}