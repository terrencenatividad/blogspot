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
	
	public function loadElement($type) {
		$element = '';
		if (file_exists(PRE_PATH . CORE_COMPONENTS . "system/ui_classes/$type.php")) {
			require_once PRE_PATH . CORE_COMPONENTS . "system/ui_classes/$type.php";
			$element = new $type();
		} else if (DEBUGGING) {
			echo "Invalid Element Type";
		}
		return $element;
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

	public function setMaxLength($maxlength) {
		$this->attribute['maxlength'] = $maxlength;
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
		if ((empty($this->addon) && empty($this->addonbutton)) || ! $this->draw || $this->add_hidden) {
			return $x . $input . $z . $y;
		} else if ($this->switch) {
			return $x . '<div class="input-group">' . $addon . $input . '</div>' . $z . $y;
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
				return $this->createButton('submit');
				break;
			case "button":
				return $this->createButton('button');
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
		if ($this->add_hidden != true && ! empty($this->addon) && $this->draw) {
			if (is_array($this->addon)) {
				$addon = '<div class="input-group-addon ' . $this->addon['class'] . '">' . $this->addon['value'] . '</div>';
			} else {
				$addon = '<div class="input-group-addon"><i class="glyphicon glyphicon-' . $this->addon . '"></i></div>';
			}
		}
		if ($this->add_hidden != true && ! empty($this->addonbutton) && $this->draw) {
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
			$placeholder = (isset($this->attribute['data-placeholder']) && ! in_array('multiple', $this->attribute)) ? '<option></option>' : '';
			if ($this->none) {
				$this->list = array_merge(array((object) array('ind' => 'none', 'val' => $this->none)), $this->list);
			}
			$parent = '';
			$input = '<select ' . $attributes . '>' . $placeholder;
			foreach ($this->list as $key => $value) {
				$optvalue = (is_object($value)) ? $value->ind : $key;
				$optlabel = (is_object($value)) ? $value->val : $value;
				$selected = ($optvalue == $this->value) ? ' selected' : '';
				if (isset($value->parent) && $parent != $value->parent) {
					$input .= '<optgroup label="' . $value->parent . '">';
					$parent = $value->parent;
				}
				$input .= '<option value="' . $optvalue . '"' . $selected . '>' . $optlabel . '</option>';
				$n = $key + 1;
				if ( ! isset($this->list[$n]) || ! isset($this->list[$n]->parent) || (isset($this->list[$n]->parent) && $this->list[$n]->parent != $parent)) {
					$input .= '</optgroup>';
				}
			}
			$input .= '</select>';
		} else {
			if ($this->value === 0 || $this->value === '0') {
				$this->value = '';
			}
			if (isset($this->list[0]) && is_object($this->list[0])) {
				foreach ($this->list as $key => $value) {
					if ($value->ind == $this->value) {
						$this->value = $value->val;
					}
				}
			} else if (isset($this->list[$this->value])) {
				$this->value = $this->list[$this->value];
			} else if (isset($this->list->{$this->value})) {
				$this->value = $this->list->{$this->value};
			}
			
			$input = $this->drawStaticInput();
		}
		return $input;
	}

	private function createButton($type) {
		$input = '';
		if ($this->draw) {
			$btn_class = array('btn');
			$btn = array('btn-primary', 'btn-success', 'btn-default', 'btn-warning', 'btn-info');
			$add_btn = true;
			foreach ($btn as $btn_type) {
				if (in_array($btn_type, $this->class)) {
					$add_btn = false;
				}
			}
			if ($add_btn) {
				$btn_class[] = 'btn-primary';
			}
			$this->class = array_merge($this->class, $btn_class);
			$this->attribute['class'] = implode(' ', $this->class);
			$placeholder = $this->attribute['placeholder'];
			unset($this->attribute['placeholder']);
			$attributes = $this->getAttributes();
			$input = '<button type="' . $type . '" ' . $attributes . 'value="' . $this->value . '">' . $placeholder . '</button>';
		}
		return $input;
	}

	public function createUploadFile() {
		$attributes = $this->getAttributes();
		return '<div class="input-group">
					<span class="input-group-btn">
						<label class="btn btn-info">
							Browse...
							<input type="file" ' . $attributes . '" class="hidden" data-uploader="file">
						</label>
					</span>
					<label for="' . $this->attribute['id'] . '" class="form-control">' . $this->label . '</label>
				</div>';
	}

	public function drawSubmit($draw) {
		if ($draw) {
			return '<button type="submit" class="btn btn-primary">Save</button>';
		} else {
			$url = MODULE_URL . 'edit';
			if (FULL_URL != MODULE_URL) {
				$url = str_replace('view', 'edit', FULL_URL);
			}
			return '<a href="' . $url  . '" class="btn btn-primary">Edit</a>';
		}
	}
	
	public function drawSubmitDropdown($draw, $ajax_task = 'ajax_create') {
		if ($draw) {
			if ($ajax_task == 'ajax_create') {
				return '<div class="btn-group" id="save_group">
							<button type="submit" name="submit" class="btn btn-primary" value="save">Save</button>
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a class="clickable" data-link="input">Save & New <input type="submit" name="submit" class="hidden" value="save_new"></a></li>
								<li><a class="clickable" data-link="input">Save & Preview <input type="submit" name="submit" class="hidden" value="save_preview"></a></li>
							</ul>
						</div>';
			} else {
				return '<button type="submit" name="submit" class="btn btn-primary" value="save">Save</button>';
			}
		} else {
			$url = MODULE_URL . 'edit';
			if (FULL_URL != MODULE_URL) {
				$url = str_replace('view', 'edit', FULL_URL);
			}
			return '<a href="' . $url  . '" class="btn btn-primary">Edit</a>';
		}
	}

	public function drawCancel() {
		return ' <a href="' . MODULE_URL . '" class="btn btn-default" data-toggle="back_page">Cancel</a>';
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

	public function CreateNewButton($type) {
		$url = MODULE_URL . 'create';
		$mod_name = "Add New " . MODULE_NAME;
		return ' <a href="' . $url . '" class="btn btn-primary btn-flat" role="button">'.$mod_name.'</a>';
	}

	public function OptionButton($type){
		$mod_name = MODULE_NAME;
		return '
				<div class="btn-group">
					<button type="button" 
					class="btn btn-info btn-flat dropdown-toggle" 
						data-toggle="dropdown">
						Options <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a class="export" id="export_id"><span class="glyphicon glyphicon-export"></span>Export '.$mod_name.'</a></li>
						<li><a class="import" id="import_id"><span class="glyphicon glyphicon-save"></span> Import '.$mod_name.'</a></li>
					</ul>
				</div>
			';
	}


	public function ListLabel($type){
		$mod_name = MODULE_NAME;
		return $mod_name ;
	}

	public function AddLabel($type){
		$mod_name = MODULE_NAME;
		return 'Add New ' . $mod_name;
	}

	public function EditLabel($type){
		$mod_name = MODULE_NAME;
		return 'Edit ' . $mod_name;
	}

	public function ViewLabel($type){
		$mod_name = MODULE_NAME;
		return 'View ' . $mod_name;
	}

	private function drawStaticInput() {
		$value = ($this->add_hidden !== true && $this->add_hidden !== false && ! $this->draw ) ? $this->add_hidden : $this->value;
		$id = '';
		$id = ($this->add_hidden && $this->draw && isset($this->attribute['id'])) ? ' id="' . $this->attribute['id'] . '_static"' : '';
		return '<p class="form-control-static"' . $id . '>' . $value . '</p>';
	}

}