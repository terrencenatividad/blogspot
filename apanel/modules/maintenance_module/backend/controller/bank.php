<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->bank 		=	new bank();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			=  	new log();

			$this->view->header_active = 'maintenance/bank/';

			$this->fields = array(
				'gl_code',
				'shortname',
				'bankcode',
				'accountno',
				'address1',
				'currency',
				'checking_account'
			);

			$this->fields2 = array(
				'id',
				'booknumber',
				'firstchequeno',
				'lastchequeno'
			);
		}

		public function listing(){
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('bank/bank_list', $data);
		}

		public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			if ($this->input->isPost) 
			{
				$extracted_data = $this->input->post($data);
				
				extract($extracted_data);		
			}
			
			$data 					= $this->input->post($this->fields);
			$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
			$data['gllist']   		= $this->bank->retrieveGLDropdown();
			$data['ui'] 			= $this->ui;
			$data['task'] 			= 'add';
			$data['show_input'] 	= true;
			$data['ajax_post'] 		= '';
			$this->view->load('bank/bank', $data);
		}

		public function edit($id)
		{
			$this->view->title = $this->ui->EditLabel('');
			
			// $data 			 	= (array) $this->bank->retrieveExistingCurrency($this->fields, $code);
			$data 			 	= (array) $this->bank->retrieveExistingBank($this->fields, $id);
			$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
			$data['gllist']   		= $this->bank->retrieveGLDropdown();
			$data['ui'] 		= $this->ui;
			$data['task'] 		= 'update';
			$data['show_input'] = true;
			$data['id']			= $id;
			$data['ajax_post'] 	= "&id=$id";
			$this->view->load('bank/bank', $data);
		}

		public function view($code)
		{
			$this->view->title 	= $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->bank->retrieveExistingBank($this->fields, $code);
			$data['currencylist']   = $this->bank->retrieveExchangeRateDropdown();
			$data['gllist']   		= $this->bank->retrieveGLDropdown();
			
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= "";
			$data['ajax_post'] 	= "";

			$this->view->load('bank/bank', $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function bank_list() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->bank->retrieveListing($search, $sort, $limit);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$dropdown = $this->ui->loadElement('check_task')
										->addView()
										->addEdit()
										->addDelete()
										->addCheckbox()
										->setValue($row->id)
										->addOtherTask(
											'Manage Check',
											'new-window',
											$row->checking_account == 'yes'
										)
										->draw();

					if($row->stat == 'active'){
						$bank_status = '<span class="label label-success">'.strtoupper($row->stat).'</span>';
					}else if($row->stat == 'inactive'){
						$bank_status = '<span class="label label-danger">'.strtoupper($row->stat).'</span>';
					}

					$table .= '<tr>';
					$table .= ' <td align = "center">' .$dropdown. '</td>';
					$table .= '<td>' . $row->shortname . '</td>';
					$table .= '<td>' . $row->bankcode . '</td>';
					$table .= '<td>' . $row->accountno. '</td>';
					$table .= '<td>' . $bank_status. '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;

			return $list;
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);
			$result  		= $this->bank->insertBank($posted_data);
			$bankname 		= $posted_data["shortname"];
			$accountno 		= $posted_data["accountno"];

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Bank [$bankname] -  Accountno [$accountno]");
			}
			else
			{
				$msg = $result;
			}
			
			return $dataArray 		= array( "msg" => $msg, "bankname" => $posted_data["shortname"], "accountno" => $posted_data["accountno"] );
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$id 		 	= $this->input->post('id');
			$result 		= $this->bank->updateBank($posted_data, $id);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Bank [$id] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "id" => $id, "msg" => $msg );
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->bank->deleteBank($id);

			
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Bank [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg);
		}
		
		private function check_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;
			if( $current!='' && $current != $old )
			{
				$result = $this->bank->check_duplicate($current);
				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		public function manage_check($id){
			$data2 			 		= (array) $this->bank->retrieveExistingBank($this->fields, $id);
			$data2 					= $this->input->post($this->fields2);
			$data2['id']			= $id;
			$data2['currencylist']  = $this->bank->retrieveExchangeRateDropdown();
			$data2['gllist']   		= $this->bank->retrieveGLDropdown();
			$data2['ui'] 			= $this->ui;
			$data2['task'] 			= 'save_check';
			$data2['show_input'] 	= true;
			$data2['ajax_post'] 	= '';
			$this->view->load('bank/manage_check', $data2);
		}

		public function save_check(){
			$posted_data 	= $this->input->post($this->fields2);
			$result  		= $this->bank->insertCheck($posted_data);
			$firstchequeno 	= $posted_data['firstchequeno'];
			$lastchequeno	= $posted_data['lastchequeno'];
			$id				= $posted_data['id'];
			$accntname 		= $this->bank->getAccountname($id);
			$id = $accntname[0]->shortname;

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added Check Series On Bank ($id) $firstchequeno -  $lastchequeno");
			}
			
			else
			{
				$msg = $result;
			}
			
			return $dataArray 		= array( "msg" => $msg );

		}

		private function check_list() {

			$search = $this->input->post("search");
			$id 	= $this->input->post("id");
			$sort 	= $this->input->post('sort');
			$limit  = $this->input->post('limit');
			$list 	= $this->bank->checkListing($search, $sort, $limit, $id);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$dropdown = $this->ui->loadElement('check_task')
										->addView()
										->addEdit()
										->addDelete()
										->addCheckbox()
										->setValue($row->id)
										// ->addOtherTask(
										// 	'Manage Check',
										// 	'new-window',
										// 	$row->checking_account == 'yes'
										// )
										->draw();

					$table .= '<tr>';
					$table .= ' <td align = "center">' .$dropdown. '</td>';
					$table .= '<td>' . $row->accountno . '</td>';
					$table .= '<td>' . $row->booknumber . '</td>';
					$table .= '<td>' . $row->batch  . '</td>';
					$table .= '<td>' . $row->nextchequeno. '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;

			return $list;
		}


	}
?>