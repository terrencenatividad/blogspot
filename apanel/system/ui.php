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
	private $switch = false;
	private $validation = false;
	private $add_hidden = false;
	private $none = '';

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
		$this->switch = false;
		$this->validation = false;
		$this->none = '';
		$this->add_hidden = false;
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

	public function setValidation($value) {
		$this->attribute['data-validation'] = $value;
		$this->validation = true;
		return $this;
	}

	public function setSplit($x, $y) {
		$this->split = array($x, $y);
		return $this;
	}

	public function setSwitch() {
		$this->switch = true;
		return $this;
	}

	public function setNone($caption) {
		$this->none = $caption;
		return $this;
	}

	public function addHidden($draw = false) {
		$this->add_hidden = $draw;
		return $this;
	}

	public function draw($draw = true) {
		$this->draw = $draw;
		$label = $this->createLabel();
		$hidden = $this->createHidden();
		$input = $this->drawInput();
		if ($this->switch) {
			$this->draw = '<div class="form-group">' . $input . $label . '</div>';
		} else {

			$this->draw = '<div class="form-group">' . $label . $hidden . $input . '</div>';
		}
		return $this->draw;
	}

	private function drawInput() {
		$addon = $this->createAddon();
		$input = $this->checkDraw();
		$x = (isset($this->split[1])) ? '<div class="' . $this->split[1] . '">' : '';
		$y = (isset($this->split[1])) ? '</div>' : '';
		$z = ($this->validation) ? '<p class="help-block m-none"></p>' : '';
		if (empty($this->addon) || ! $this->draw) {
			return $x . $input . $z . $y;
		} else {
			return $x . '<div class="input-group">' . $input . $addon . '</div>' . $z . $y;
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
			case "file":
				return $this->createUploadFile();
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
			case "submit":
				return $this->createSubmit();
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

	private function createHidden() {
		$input = '';
		if ($this->add_hidden) {
			$attributes = array();
			$this->attribute['class'] = implode(' ', $this->class);
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$attributes = implode(' ', $attributes);
			$input = '<input type="hidden" ' . $attributes . 'value="' . $this->value . '">';
		}
		return $input;
	}

	private function createAddon() {
		$addon = '';
		if ( ! empty($this->addon) && $this->draw) {
			$addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-' . $this->addon . '"></i></div>';
		}
		return $addon;
	}

	private function createInput($type = 'radio') {
		if ($this->draw) {
			$attributes = array();
			$this->attribute['class'] = implode(' ', $this->class);
			foreach ($this->attribute as $key => $value) {
				$attributes[] = $key . '="' . $value . '"';
			}
			$checked = ($this->default == $this->value) ? ' checked ' : '';
			$attributes = implode(' ', $attributes);
			$input = '<input type="' . $type . '" ' . $attributes . $checked . 'value="' . $this->default . '">';
		} else {
			$this->value = ($this->value) ? 'Yes' : 'No';
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
			if ($type == 'password') {
				$this->value = '*********';
			}
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
			$placeholder = (isset($this->attribute['data-placeholder'])) ? '<option></option>' : '';
			if ($this->none) {
				$this->list = array_merge(array((object) array('ind' => 'none', 'val' => $this->none)), $this->list);
			}
			$input = '<select ' . $attributes . '>' . $placeholder;
			foreach ($this->list as $key => $value) {
				if (is_object($value)) {
					$selected = ($value->ind == $this->value) ? ' selected' : '';
					$input .= '<option value="' . $value->ind . '"' . $selected . '>' . $value->val . '</option>';
				} else {
					$selected = ($key == $this->value) ? ' selected' : '';
					$input .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
				}
			}
			$input .= '</select>';
		} else {
			foreach ($this->list as $key => $value) {
				if (is_object($value)) {
					$this->value = ($value->ind == $this->value) ? $value->val : $this->value;
				} else {
					$this->value = ($key == $this->value) ? $value : $this->value;
				}
			}
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	public function createUploadFile() {
		if ($draw) {
			return '<div class="input-group">
						<span class="input-group-btn">
							<label class="btn btn-info">
								Upload Resume
								<input id="' . $this->attribute['id'] . '" name="' . $this->attribute['name'] . '" class="hidden" type="file" data-uploader accept=".docx, .doc, .pdf">
							</label>
						</span>
						<label for="' . $this->attribute['id'] . '" class="form-control"></label>
					</div>';
		} else {
			return '<a href=""></a>';
		}
	}

	public function drawSubmit($draw) {
		if ($draw) {
			return '<button type="submit" class="btn btn-primary">Save</button>';
		} else {
			$url = str_replace('view', 'edit', FULL_URL);
			return '<a href="' . $url  . '" class="btn btn-primary">Edit</a>';
		}
	}

	private function drawStaticInput() {
		return '<p class="form-control-static">' . $this->value . '</p>';
	}

}