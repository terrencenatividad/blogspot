<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->user_model		= new user_model();
		$this->session			= new session();
		$this->fields 			= array(
			'username',
			'password',
			'email',
			'stat',
			'is_login',
			'useragent',
			'groupname',
			'firstname',
			'lastname',
			'middleinitial',
			'phone',
			'mobile'
		);
		$this->data = array();
		$this->view->header_active = 'maintenance/user/';
	}

	public function listing() {
		$this->view->title = 'User List';
		$data['ui'] = $this->ui;
		$all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		$data['group_list'] = array_merge(array($all),  $this->user_model->getGroupList(''));
		$this->view->load('user/user_list', $data);
	}

	public function create() {
		$this->view->title = 'User Create';
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['ajax_task'] = 'ajax_create';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('user/user', $data);
	}

	public function edit($username) {
		$this->view->title = 'User Edit';
		$data = (array) $this->user_model->getUserById($this->fields, $username);
		$data['ui'] = $this->ui;
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&username_ref=$username";
		$data['show_input'] = true;
		$this->view->load('user/user', $data);
	}

	public function view($username) {
		$this->view->title = 'User View';
		$data = (array) $this->user_model->getUserById($this->fields, $username);
		$data['ui'] = $this->ui;
		$data['group_list'] = $this->user_model->getGroupList('');
		$data['show_input'] = false;
		$this->view->load('user/user', $data);
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
		$typeid = $data['typeid'];
		$classid = $data['classid'];

		$list = $this->user_model->getUserList($this->fields, $search, $typeid, $classid);
		$table = '';
		if (empty($list)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($list as $key => $row) {
			$table .= '<tr>';
			$table .= '<td align = "center">
							<input type="checkbox" class="checkbox item_checkbox" value="' . $row->username . '">
							<div class = "invi">
								<a class="btn btn-sm btn-link" href = "'. MODULE_URL .'edit/' . $row->username . '" title = "Edit"><span class="glyphicon glyphicon-pencil"></span></a>
								<a class="btn btn-sm btn-link delete" data-id = "' . $row->username . '" title = "Delete" ><i class="glyphicon glyphicon-trash"></i></a>
								<a class="btn btn-sm btn-link publish" href = "'. MODULE_URL .'view/' . $row->username . '" title = "View"><i class="glyphicon glyphicon-eye-open"></i></a>
							</div>
						</td>';
			$table .= '<td>' . $row->username . '</td>';
			$table .= '<td>' . $row->firstname . ' ' . $row->lastname . '</td>';
			$table .= '<td>' . $row->email . '</td>';
			$table .= '<td>' . $row->groupname . '</td>';
			$table .= '<td>' . $row->stat . '</td>';
			$table .= '</tr>';
		}
		return array(
			'table' => $table
		);
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$result = $this->user_model->saveUser($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$data['stat'] = 'active';
		$username = $this->input->post('username_ref');
		$result = $this->user_model->updateUser($data, $username);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$this->user_model->deleteUsers($delete_id);
		}
	}

}