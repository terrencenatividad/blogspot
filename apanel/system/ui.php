<?php
class ui {

	private $attribute = array();
	private $list = array();
	private $value = '';
	private $default = '';
	private $type = '';
	private $addon = '';
	private $label = '';
	private $draw = true;
	private $class = array();
	private $split = array();

	public function formField($type) {
		$this->attribute = array();
		$this->list = array();
		$this->value = '';
		$this->default = '';
		$this->type = $type;
		$this->addon = '';
		$this->label = '';
		$this->draw = true;
		$this->class = array();
		$this->split = array();
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

	public function setClass($class) {
		$this->class = array_merge(explode(' ', $class), $this->class);
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

	public function setList(array $list) {
		$this->list = $list;
		return $this;
	}

	public function setDefault($default) {
		$this->default = $default;
		return $this;
	}

	public function setPlaceholder($placeholder) {
		$index = ($this->type == 'dropdown') ? 'data-placeholder' : 'placeholder';
		$this->attribute[$index] = $placeholder;
		return $this;
	}

	public function setAttribute(array $attributes) {
		$this->attribute = array_merge($this->attribute, $attributes);
		return $this;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function setSplit($x, $y) {
		$this->split = array($x, $y);
		return $this;
	}

	public function draw($draw = true) {
		$this->draw = $draw;
		$label = $this->createLabel();
		$input = $this->drawInput();
		$this->draw = '<div class="form-group">' . $label . $input . '</div>';
		return $this->draw;
	}

	private function drawInput() {
		$addon = $this->createAddon();
		$input = $this->checkDraw();
		$x = (isset($this->split[1])) ? '<div class="' . $this->split[1] . '">' : '';
		$y = (isset($this->split[1])) ? '</div>' : '';
		if (empty($this->addon) || $this->draw) {
			return $x . $input . $y;
		} else {
			return $x . '<div class="input-group">' . $input . $addon . '</div>' . $y;
		}
	}

	private function checkDraw() {
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
				return $this->createInput('radio');
				break;
			case "checkbox":
				return $this->createInput('checkbox');
				break;
		}
	}

	private function createLabel() {
		$label = '';
		$for = ((isset($this->attribute['id']) && ! empty($this->attribute['id'])) ? ' for="' . $this->attribute['id'] . '"' : '');
		$class = (!empty($this->split)) ? ' class="control-label ' . $this->split[0] . '"' : '';
		if ( ! empty($this->label)) {
			$label = '<label' . $for . $class . '>' . $this->label . '</label>';
		}
		return $label;
	}

	private function createAddon() {
		$addon = '';
		if ( ! empty($this->addon) && $this->draw) {
			$addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-{this->addon}"></i></div>';
		}
		return $addon;
	}

	private function createInput($type = 'radio') {
		if ($this->draw) {
			$attributes = array();
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$checked = ($this->default == $this->value) ? ' checked' : '';
			$attributes = implode(' ', $attributes);
			$input = '<input type="' . $type . '" ' . $attributes . $checked . 'value="' . $this->default . '">';
		} else {
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createInputText($type = 'text') {
		if ($this->draw) {
			$attributes = array();
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$attributes = implode(' ', $attributes);
			$input = '<input type="' . $type . '" ' . $attributes . ' value="' . $this->value . '">';
		} else {
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createTextarea() {
		if ($this->draw) {
			$attributes = array();
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$attributes = implode(' ', $attributes);
			$input = '<textarea ' . $attributes . '>' . $this->value . '</textarea>';
		} else {
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createDropDown() {
		if ($this->draw) {
			$attributes = array();
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$attributes = implode(' ', $attributes);
			$placeholder = (isset($this->attributes['data-placeholder'])) ? '<option></option>' : '';
			$input = '<select ' . $attributes . '>' . $placeholder;
			foreach ($this->list as $key => $value) {
				$selected = ($key == $this->value) ? ' selected' : '';
				$input .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
			}
			$input .= '</select>';
		} else {
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function drawStaticInput() {
		return '<p class="form-control-static">' . $this->value . '</p>';
	}

}