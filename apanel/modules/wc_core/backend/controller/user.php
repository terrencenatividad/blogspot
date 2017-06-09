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

		$pagination = $this->user_model->getUserPagination($this->fields, $search, $typeid, $classid);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addPrint()
									->addDelete()
									->addCheckbox()
									->setValue($row->username)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->username . '</td>';
			$table .= '<td>' . $row->firstname . ' ' . $row->lastname . '</td>';
			$table .= '<td>' . $row->email . '</td>';
			$table .= '<td>' . $row->groupname . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'active':
				$color = 'success';
				break;
			case 'inactive':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
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
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->user_model->deleteUsers($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_check_username() {
		$username	= $this->input->post('username');
		$reference	= $this->input->post('username_ref');
		$result = $this->user_model->checkUsername($username, $reference);
		return array(
			'available'	=> $result
		);
	}

}