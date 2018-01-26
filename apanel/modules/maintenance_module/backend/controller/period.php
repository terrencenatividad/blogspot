<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->period 		=	new period();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();

			$this->fields = array(
			'id',
			'period',
			'fiscalyear',
			'description',
			'startdate',
			'enddate'
			);
		}

		public function listing()
		{	
			$data['ui'] = $this->ui;
			$this->view->title = 'Period Listing';
			$this->view->load('period/period_list', $data);
		}

		public function create()
		{
			$this->view->title = 'Add Period';
			
			if ($this->input->isPost) 
			{
				$extracted_data = $this->input->post($data);
				extract($extracted_data);		
			}

			$data 				= $this->input->post($this->fields);
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'add';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';
			$this->view->load('period/period_create',  $data);
		}

		public function edit($id)
		{
			$this->view->title = 'Edit Period';
			
			$data 			 	= (array) $this->period->retrieveExistingPeriod($this->fields,$id);
			$data['startdate']  = date('M j, Y', strtotime($data['startdate']));
			$data['enddate']    = date('M j, Y', strtotime($data['enddate']));
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'update';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&id=$id";
			$this->view->load('period/period_create',  $data);
		}

		public function view($id)
		{
			$this->view->title = 'View Period';
			$data 			 	= (array) $this->period->retrieveExistingPeriod($this->fields,$id);
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'view';
			$data['show_input'] = false;
			$data['ajax_post'] 	= "&id=$id";
			$this->view->load('period/period_create',  $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function ajax_list() {
		$data = $this->input->post(array('search','sort', 'limit'));
		$search = $data['search'];
		$sort	= $data['sort'];
		$limit	= $data['limit'];

		$pagination = $this->period->retrieveListing($this->fields, $search, $sort, $limit);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit()
									->addDelete()
									->addCheckbox()
									->setValue($row->id)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->period . '</td>';
			$table .= '<td>' . $row->fiscalyear . '</td>';
			$table .= '<td>' .  $this->date->dateFormat($row->startdate) . '</td>';
			$table .= '<td>' .  $this->date->dateFormat($row->enddate) . '</td>';
			$table .= '<td class="text-center">' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'open':
				$color = 'success';
				break;
			case 'inactive':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$validate		= $this->period->validInsert("*",$posted_data['startdate'],$posted_data['enddate']);
			$result = (empty($validate)) ? true : false;
			if($result){
			$result  		= $this->period->insertPeriod($posted_data);
			}
			return array(
				'success' => $result,
				'redirect' => BASE_URL . 'maintenance/period'
			);
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$id 	= $this->input->post('id');
			$validate		= $this->period->validInsert($posted_data ,$posted_data['startdate'],$posted_data['enddate']);
			$result = (empty($validate)) ? true : false;
			if($result){
			$result 		= $this->period->updatePeriod($posted_data, $id);
			}
			return array(
				'success' => $result,
				'redirect' => BASE_URL . 'maintenance/period'
			);
		}

		private function ajax_delete() {
		$posted_data 	= $this->input->post($this->fields);
		$delete_id = $this->input->post('delete_id');
		$data 			 	= (array) $this->period->retrieveExistingPeriod($this->fields,$delete_id);
			if ($delete_id) {
				$this->period->deletePeriod($delete_id,$data);
			}
		}
	
	}
?>