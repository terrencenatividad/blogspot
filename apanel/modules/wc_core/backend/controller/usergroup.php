<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->usergroup_model	= new usergroup_model();
		$this->session			= new session();
		$this->fields 	= array(
			'groupname',
			'description'
		);
		$this->access_list = array(
			'mod_add'		=> 'Create',
			'mod_view'		=> 'View',
			'mod_edit'		=> 'Edit',
			'mod_delete'	=> 'Delete',
			'mod_list'		=> 'List',
			'mod_print'		=> 'Print'
		);
		$this->data = array();
		$this->view->header_active = 'maintenance/usergroup/';
	}

	public function listing() {
		$this->view->title = 'User Group List';
		$data['ui'] = $this->ui;
		$this->view->load('usergroup/usergroup_list', $data);
	}

	public function create() {
		$this->view->title = 'User Group Create';
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_create';
		$data['moduleaccess_list'] = $this->usergroup_model->getModuleAccessList();
		$data['access_list'] = $this->access_list;
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('usergroup/usergroup', $data);
	}

	public function edit($groupname) {
		$groupname = base64_decode($groupname);
		$this->view->title = 'User Group Edit';
		$data = (array) $this->usergroup_model->getGroupByName($this->fields, $groupname);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_edit';
		$data['moduleaccess_list'] = $this->usergroup_model->getModuleAccessList($groupname);
		$data['access_list'] = $this->access_list;
		$data['ajax_post'] = "&groupname_ref=$groupname";
		$data['show_input'] = true;
		$this->view->load('usergroup/usergroup', $data);
	}

	public function view($groupname) {
		$groupname = base64_decode($groupname);
		$this->view->title = 'User Group View';
		$data = (array) $this->usergroup_model->getGroupByName($this->fields, $groupname);
		$data['ui'] = $this->ui;
		$data['moduleaccess_list'] = $this->usergroup_model->getModuleAccessList($groupname);
		$data['access_list'] = $this->access_list;
		$data['show_input'] = false;
		$this->view->load('usergroup/usergroup', $data);
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

		$pagination = $this->usergroup_model->getGroupPagination($this->fields, $search);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$id = base64_encode($row->groupname);
			$table .= '<tr>';
			$table .= '<td align = "center">
							<input type="checkbox" class="checkbox item_checkbox" value="' . $id . '">
							<div class = "invi">
								<a class="btn btn-sm btn-link" href = "'. MODULE_URL .'edit/' . $id . '" title = "Edit"><span class="glyphicon glyphicon-pencil"></span></a>
								<a class="btn btn-sm btn-link delete" data-id = "' . $row->groupname . '" title = "Delete" ><i class="glyphicon glyphicon-trash"></i></a>
								<a class="btn btn-sm btn-link publish" href = "'. MODULE_URL .'view/' . $id . '" title = "View"><i class="glyphicon glyphicon-eye-open"></i></a>
							</div>
						</td>';
			$table .= '<td>' . $row->groupname . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_create() {
		$fields = $this->fields;
		$data = $this->input->post($fields);
		$module_access = $this->input->post('module_access');
		$result = $this->usergroup_model->saveGroup($data, $module_access);
		if ($result) {
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
			);
		}
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$groupname = $this->input->post('groupname_ref');
		$module_access = $this->input->post('module_access');
		$result = $this->usergroup_model->updateGroup($data, $groupname, $module_access);
		if ($result) {
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
			);
		}
	}

	private function ajax_delete() {
		$data = $this->input->post('delete_id');
		if ($data) {
			$this->usergroup_model->deleteGroup($data);
		}
	}

}