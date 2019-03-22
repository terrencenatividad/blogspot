<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->content_model		= new content_model();
		$this->session			= new session();
		$this->fields 			= array(
			'id',
			'title',
			'content',
			'tags',
			'status'
		);
		$this->view->header_active = 'content';
	}

	public function listing() {
		$this->view->title = $this->ui->ListLabel('');
		$data['ui'] = $this->ui;
		$all = (object) array('ind' => 'null', 'val' => 'Filter: All');
		$this->view->load('content_list', $data);
	}

	public function create() {
		$this->view->title = $this->ui->AddLabel('');
		$data = $this->input->post($this->fields);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_create';
		$data['disabler'] = '';
		$data['ajax_post'] = '';
		$data['show_input'] = true;
		$this->view->load('content', $data);
	}

	public function edit($id) {
		$this->view->title = $this->ui->EditLabel('');
		$data = (array) $this->content_model->getContentById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_edit';
		$data['ajax_post'] = "&id=$id";
		$data['disabler'] = '';
		$data['show_input'] = true;
		$this->view->load('content', $data);
	}

	public function view($id) {
		$this->view->title = $this->ui->ViewLabel('');
		$data = (array) $this->content_model->getContentById($this->fields, $id);
		$data['ui'] = $this->ui;
		$data['ajax_task'] = 'ajax_view';
		$data['disabler'] = 'disabled';
		$data['show_input'] = false;
		$this->view->load('content', $data);
	}

	public function get_import() {
		$csv = $this->csv_header();
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
		$data	= $this->input->post(array('search', 'sort'));
		$search	= $data['search'];
		$sort	= $data['sort'];

		$pagination = $this->content_model->getContentList($this->fields, $search, $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {

			$stat = $row->status;

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
			->addView()
			->addEdit()
			->addPrint()
			->addOtherTask(
				'Unpublish',
				'arrow-down',
				($stat == 'published')
			)
			->addOtherTask(
				'Publish',
				'arrow-up',
				($stat == 'unpublished')
			)	
			->addDelete()
			->addCheckbox()
			->setValue($row->id)
			->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->title . '</td>';
			$table .= '<td>' . $row->content . '</td>';
			$table .= '<td>' . $this->colorStat($row->status) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'published':
			$color = 'success';
			break;
			case 'unpublished':
			$color = 'warning';
			break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data = $this->input->post($this->fields);
		$textarea = $_POST['mytextarea'];
		$data['content'] = $textarea;
		unset($data['status']);
		$result = $this->content_model->saveContent($data);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data = $this->input->post($this->fields);
		$id = $this->input->post('id');
		$textarea = $_POST['mytextarea'];
		$data['content'] = $textarea;
		unset($data['status']);
		$result = $this->content_model->updateContent($data, $id);
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		$error_id = array();
		if ($delete_id) {
			$error_id = $this->content_model->deleteContent($delete_id);
		}
		return array(
			'success'	=> (empty($error_id)),
			'error_id'	=> $error_id
		);
	}

	private function ajax_create_upload() {
		$accepted_origins = array("http://localhost", "http://10.2.1.125");

		$imageFolder = "../uploads/tinymce_uploads/";

		reset ($_FILES);
		$temp = current($_FILES);
		if (is_uploaded_file($temp['tmp_name'])){
			if (isset($_SERVER['HTTP_ORIGIN'])) {
				if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
					header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
				} else {
					header("HTTP/1.1 403 Origin Denied");
					return;
				}
			}
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
				header("HTTP/1.1 400 Invalid file name.");
				return;
			}

			if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
				header("HTTP/1.1 400 Invalid extension.");
				return;
			}
			$type = explode('/', $temp['type']);
			$random = rand();
			$id = uniqid();
			$filename = $random . '-' . $id . '.' .$type[1];
			$filetowrite = $imageFolder . $filename;
			move_uploaded_file($temp['tmp_name'], $filetowrite);
			$img = str_replace('/apanel', '', BASE_URL) . "uploads/tinymce_uploads/" .$filename;
			return $img;
		} else {
			header("HTTP/1.1 500 Server Error");
		}
	}

	private function ajax_update_stat() {
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		$data = $this->input->post(array('status'));
		$data['status'] = $status;
		$result = $this->content_model->updateStatus($data, $id);
		return $result;
	}
}