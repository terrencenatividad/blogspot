<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->item_class_model	= new item_class_model();
		$this->session			= new session();
		$this->fields 			= array(
			'id',
			'label',
			'parentid'
		);
		$this->data = array();
		$this->view->header_active = 'maintenance/item_class/';
	}

	public function listing() {
		$this->view->title = 'Item Class List';
		$data['ui'] = $this->ui;
		$this->view->load('item/item_class_list', $data);
	}

	public function create() {
		$this->view->title = 'Item Class Create';
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass('', true);
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('item/item_class', $data);
	}

	public function edit($id) {
		$this->view->title = 'Item Class Edit';
		$data = (array) $this->item_class_model->getItemClassById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass($id, true);
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['show_input'] = true;
		$this->view->load('item/item_class', $data);
	}

	public function view($id) {
		$this->view->title = 'Item Class View';
		$data = (array) $this->item_class_model->getItemClassById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['parents'] = $this->item_class_model->getParentClass($id, true);
		$data['show_input'] = false;
		$this->view->load('item/item_class', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data = $this->input->post(array('search', 'typeid', 'classid'));
		$search = $data['search'];
		$list = $this->item_class_model->getItemClassList($this->fields, $search);
		$table = $this->createList($list);
		return array(
			'table' => $table
		);
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$result = $this->item_class_model->saveItemClass($data);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$itemcode = $this->input->post('id');
		$result = $this->item_class_model->updateItemClass($data, $itemcode);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->item_class_model->deleteItemClass($delete_id);
		}
	}

	private function createList($data, $parent = '') {
		$table = '';
		foreach ($data as $id => $row) {
			$table .= '<tr>';
			$table .= '<td align = "center">
							<input type="checkbox" class="checkbox item_checkbox" value="' . $id . '">
							<div class = "invi">
								<a class="btn btn-sm btn-link" href = "'. MODULE_URL .'edit/' . $id . '" title = "Edit"><span class="glyphicon glyphicon-pencil"></span></a>
								<a class="btn btn-sm btn-link delete" data-id = "' . $id . '" title = "Delete" ><i class="glyphicon glyphicon-trash"></i></a>
								<a class="btn btn-sm btn-link publish" href = "'. MODULE_URL .'view/' . $id . '" title = "View"><i class="glyphicon glyphicon-eye-open"></i></a>
							</div>
						</td>';
			$table .= '<td>' . $row['label'] . '</td>';
			$table .= '<td>' . $parent . '</td>';
			$table .= '</tr>';
			if (isset($row['children'])) {
				$table .= $this->createList($row['children'], $row['label']);
			}
		}
		return $table;
	}
	

}