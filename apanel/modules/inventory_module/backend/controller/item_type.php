<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->item_type_model	= new item_type_model();
		$this->session			= new session();
		$this->fields			= array(
			'id',
			'label'
		);
		$this->csv_header		= array(
			'Item Type'
		);
	}

	public function listing() {
		$this->view->title = 'List Item Type';
		$data['ui'] = $this->ui;
		$this->view->load('item/item_type_list', $data);
	}

	public function create() {
		$this->view->title = 'Create Item Type';
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('item/item_type', $data);
	}

	public function edit($id) {
		$this->view->title = 'Edit Item Type';
		$data = (array) $this->item_type_model->getItemTypeById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('item/item_type', $data);
	}

	public function view($id) {
		$this->view->title = 'View Item Type';
		$data = (array) $this->item_type_model->getItemTypeById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['show_input'] = false;
		$this->view->load('item/item_type', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
		echo $csv;
	}

	public function get_export($search = '', $sort = '') {
		$search	= base64_decode($search);
		$sort	= base64_decode($sort);
		$csv	= $this->csv_header();
		$result = $this->item_type_model->getItemTypeList($this->fields, $search, $sort);
		foreach ($result as $row) {
			$csv .= "\n";
			$csv .= '"' . $row->label . '"';
		}
		echo $csv;
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$search	= $this->input->post('search');
		$sort	= $this->input->post('sort');

		$item = $this->item_type_model->getItemTypeListPagination($this->fields, $search, $sort);
		$table = '';
		if (empty($item->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($item->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addDelete()
									->addCheckbox()
									->setValue($row->id)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->label . '</td>';
			$table .= '</tr>';
		}
		$item->table = $table;
		return $item;
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$result = $this->item_type_model->saveItemType($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$code = $this->input->post('id');
		$result = $this->item_type_model->updateItemType($data, $code);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->item_type_model->deleteItemType($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function csv_header() {
		header('Content-type: application/csv');

		$csv = '';
		$csv .= '"' . implode('","', $this->csv_header) . '"';

		return $csv;
	}

	private function ajax_save_import() {
		$csv_array	= array_map('str_getcsv', file($_FILES['file']['tmp_name']));
		$result		= false;
		$duplicate	= array();
		$exist		= array();
		$error		= array();
		$values		= array();
		$validity	= array();
		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else {
				$check_field = array(
					'Item Type' => array()
				);
				foreach ($csv_array as $row) {
					$check_field['Item Type'][] = $this->getValueCSV('Item Type', $row);
					$values[] = array(
						'label' => $this->getValueCSV('Item Type', $row, 'required', $validity)
					);
				}
				foreach ($check_field as $key => $row) {
					$data_duplicate = $this->check_duplicate($row);
					if ($data_duplicate) {
						$duplicate[$key]	= array_values($data_duplicate);
					}
				}

				$exist_check = $this->item_type_model->checkExistingItemType($check_field['Item Type']);
				if ($exist_check) {
					foreach ($exist_check as $row) {
						$exist['Item Type'][] = $row->label;
					}
				}

				if ($duplicate) {
					$error[] = 'Duplicate Entry'; 
				}
					
				if ($exist) {
					$error[] = 'Entry Already Exist';
				}
						
				if ($validity) {
					$error[] = 'Invalid Entry';
				}

				$error = implode('. ', $error);

				if (empty($error)) {
					$result = $this->item_type_model->saveItemTypeCSV($values);
				}

			}
		} else {
			$error = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> $result,
			'error'		=> $error,
			'duplicate'	=> $duplicate,
			'exist'		=> $exist,
			'validity'	=> $validity
		);
		return $json;
	}

	private function getValueCSV($field, $array, $checker = '', &$error = array(), $checker_function = '', &$error_function = array()) {
		$key	= array_search($field, $this->csv_header);
		$value	= (isset($array[$key])) ? trim($array[$key]) : '';
		if ($checker != '') {
			$checker_array = explode(' ', $checker);
			if (in_array('integer', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9]*$/', $value)) {
					$error['Integer'][$field] = 'Integer';
				}
			}
			if (in_array('decimal', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9.]*$/', $value)) {
					$error['Decimal'][$field] = 'Decimal';
				}
			}
			if (in_array('required', $checker_array)) {
				if ($value == '') {
					$error['Required'][$field] = 'Required';
				}
			}
		}
		if ($checker_function && $value != '') {
			$result = $this->item_model->{$checker_function}($value);
			if ($result) {
				$value = $result[0]->ind;
			} else {
				$error_function[$field][] = $value;
				$value = '';
			}
		}
		return $value;
	}

	private function check_duplicate($array) {
		return array_unique(array_diff_assoc($array, array_unique($array)));
	}

}