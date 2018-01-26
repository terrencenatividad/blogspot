<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->accountclass		= new accountclass_model();
		$this->session			= new session();
		$this->fields 			= array(
			'accountclasscode',
			'accountclass'
		);
	}

	public function listing() {
		$this->view->title	= 'Account Class List';
		$data['ui']			= $this->ui;
		$this->view->load('accountclass/accountclass_list', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$pagination	= $this->accountclass->getAccountClassPagination($this->fields, $search, $sort);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="2" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $row->accountclasscode . '</td>';
			$table .= '<td>' . $row->accountclass . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

}