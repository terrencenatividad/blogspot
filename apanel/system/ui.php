<?php
class ui {

	private $form_group = false;
	private $attribute = array();
	private $list = array();
	private $value = '';
	private $default = '';
	private $type = '';
	private $addon = '';
	private $addonbutton = '';
	private $label = '';
	private $draw = true;
	private $class = array();
	private $split = array();
	private $switch = false;
	private $validation = false;
	private $add_hidden = false;
	private $none = '';

	public function formField($type) {
		$this->reset();
		$this->form_group = true;
		$this->type = $type;
		return $this;
	}

	public function setElement($type) {
		$this->reset();
		$this->type = $type;
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

	public function setButtonAddon($addonbutton) {
		$this->addonbutton = $addonbutton;
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

	public function addHidden($draw = true) {
		$this->add_hidden = $draw;
		return $this;
	}

	public function draw($draw = true) {
		$this->draw = $draw;
		$label = $this->createLabel();
		$hidden = $this->createSubHidden();
		$input = $this->drawInput();
		$x = '<div class="form-group">';
		$y = '</div>';
		if ( ! $this->form_group) {
			$this->draw = $input;
		} else if ($this->switch) {
			$this->draw = $x . $input . $label . $y;
		} else {
			$this->draw = $x . $label . $hidden . $input . $y;
		}
		return $this->draw;
	}

	private function drawInput() {
		$addon = $this->createAddon();
		$input = $this->checkDraw();
		$x = (isset($this->split[1])) ? '<div class="' . $this->split[1] . '">' : '';
		$y = (isset($this->split[1])) ? '</div>' : '';
		$z = ($this->validation) ? '<p class="help-block m-none"></p>' : '';
		if ((empty($this->addon) && empty($this->addonbutton)) || ! $this->draw) {
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
			case "hidden":
				return $this->createInputText('hidden');
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

	private function createSubHidden() {
		$input = '';
		if ($this->add_hidden) {
			$this->attribute['class'] = implode(' ', $this->class);
			$attributes = $this->getAttributes();
			$input = '<input type="hidden" ' . $attributes . 'value="' . $this->value . '">';
		}
		return $input;
	}

	private function createAddon() {
		$addon = '';
		if ( ! empty($this->addon) && $this->draw) {
			$addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-' . $this->addon . '"></i></div>';
		}
		if ( ! empty($this->addonbutton) && $this->draw) {
			$addon = '<div class="input-group-btn"><button type="button" id="' . $this->attribute['id'] . '_button" class="btn btn-primary btn-flat"><i class="glyphicon glyphicon-' . $this->addonbutton . '"></i></button></div>';
		}
		return $addon;
	}

	private function createInput($type = 'radio') {
		if ($this->draw && ! $this->add_hidden) {
			$this->attribute['class'] = implode(' ', $this->class);
			$checked = ($this->default == $this->value) ? ' checked ' : '';
			$attributes = $this->getAttributes();
			$input = '<input type="' . $type . '" ' . $attributes . $checked . 'value="' . $this->default . '">';
		} else {
			$this->value = ($this->value) ? 'Yes' : 'No';
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createInputText($type = 'text') {
		if ($this->draw && ! $this->add_hidden) {
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			$attributes = $this->getAttributes();
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
		if ($this->draw && ! $this->add_hidden) {
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			$attributes = $this->getAttributes();
			$input = '<textarea ' . $attributes . '>' . $this->value . '</textarea>';
		} else {
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createDropDown() {
		if ($this->draw && ! $this->add_hidden) {
			$this->class[] = 'form-control';
			$this->attribute['class'] = implode(' ', $this->class);
			$attributes = $this->getAttributes();
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

	private function reset() {
		$this->form_group = false;
		$this->attribute = array();
		$this->list = array();
		$this->value = '';
		$this->default = '';
		$this->type = '';
		$this->addon = '';
		$this->addonbutton = '';
		$this->label = '';
		$this->draw = true;
		$this->class = array();
		$this->split = array();
		$this->switch = false;
		$this->validation = false;
		$this->none = '';
		$this->add_hidden = false;
	}

	private function getAttributes() {
		$attributes = array();
		foreach ($this->attribute as $key => $value) {
			if (is_int($key)) {
				$attributes[] = $value . '="' . $value . '"';
			} else {
				$attributes[] = $key . '="' . $value . '"';
			}
		}
		$attributes = implode(' ', $attributes);
		return $attributes;
	}

	private function checkType($type, array $checklist) {
		if (in_array($type, $checklist)) {
			return true;
		} else {
			if (DEBUGGING) {
				echo "Type: $type is not in " . json_encode($checklist);
			}
			return false;
		}
	}

	private function drawStaticInput() {
		$value = ($this->add_hidden !== true && $this->add_hidden !== false && ! $this->draw ) ? $this->add_hidden : $this->value;
		return '<p class="form-control-static">' . $value . '</p>';
	}

}