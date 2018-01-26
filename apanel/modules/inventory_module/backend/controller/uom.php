<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->uom_model		= new uom_model();
		$this->session			= new session();
		$this->fields 			= array(
			'uomcode',
			'uomdesc',
			'uomtype'
		);
		$this->data = array();
		$this->view->header_active = 'maintenance/uom/';
	}

	public function listing() {
		$this->view->title = 'Unit of Measure List';
		$data['ui'] = $this->ui;
		// $all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		// $data['itemclass_list'] = array_merge(array($all),  $this->item_class_model->getParentClass(''));
		// $data['itemtype_list'] = array_merge(array($all), $this->item_model->getItemtypeList());
		$this->view->load('uom/uom_list', $data);
	}

	public function create() {
		$this->view->title = 'Unit of Measure Create';
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['type_list'] = $this->uom_model->getOption('uom_type');
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('uom/uom', $data);
	}

	public function edit($dataid) {
		$this->view->title = 'Unit of Measure Edit';
		$data = (array) $this->uom_model->getItemById($this->fields, $dataid);
		$data['ui'] = $this->ui;
		$data['type_list'] = $this->uom_model->getOption('uom_type');
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&dataid=$dataid";
		$data['show_input'] = true;
		$this->view->load('uom/uom', $data);
	}

	public function view($dataid) {
		$this->view->title = 'Unit of Measure View';
		$data = (array) $this->uom_model->getItemById($this->fields, $dataid);
		$data['ui'] = $this->ui;
		$data['type_list'] = $this->uom_model->getOption('uom_type');
		$data['show_input'] = false;
		$this->view->load('uom/uom', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		// $data = $this->input->post(array('search', 'typeid', 'classid'));
		$data = $this->input->post(array('search','limit'));
		$search = $data['search'];
		$limit = $data['limit'];
		// $typeid = $data['typeid'];
		// $classid = $data['classid'];

		$list = $this->uom_model->getItemList($this->fields, $search, $limit);
		$table = '';
		if (empty($list)) {
			$table = '<tr><td colspan="3" class="text-center"><strong>No Records Found</strong></td></tr>';
		}
		foreach ($list as $key => $row) {

			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addDelete()
									->addCheckbox()
									->setValue($row->uomcode)
									->draw();
			$table .= '<tr>';
			$table .= '<td align = "center">
							'.$dropdown.'
						</td>';
			$table .= '<td>' . $row->uomcode . '</td>';
			$table .= '<td>' . $row->uomdesc . '</td>';
			$table .= '<td>' . ucwords($row->uomtype) . '</td>';
			$table .= '</tr>';
		}
		return array(
			'table' => $table
		);
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$result = $this->uom_model->saveItem($data);
		return array(
			'redirect'	=> BASE_URL . 'maintenance/uom',
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data 	= $this->input->post($this->fields);
		$dataid = $this->input->post('dataid');
		$result = $this->uom_model->updateItem($data, $dataid);
		return array(
			'redirect'	=> BASE_URL . 'maintenance/uom',
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->uom_model->deleteItems($delete_id);
		}
	}

}