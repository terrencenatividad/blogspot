<?php
    class controller extends wc_controller {
        public function __construct() {
            parent::__construct();
            $this->ui				= new ui();
            $this->input			= new input();
            $this->job   	        = new job();
            $this->session			= new session();
            $this->log 			    = new log();
            $this->view->header_active = 'purchase/job/';

            $this->fields 			= array(
                'job_no',
                'notes',
				'ipo_no',
				'stat'
			);
			
			$this->jobdetails		= array(
				'job_no',
				'ipo_no',
				'itemcode',
				'serial_number',
				'notes',
				'qty',
				'uom'
			);

            $data = array();
        }

        public function listing() {
    
            $this->view->title = 'Job';
            
            $data['ui'] 				= $this->ui;
            $data['datefilter']		    = date("M d, Y");
            $this->view->load('job/job_list', $data);
        }

        public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}

        public function view()
		{
			$this->view->title 	= $this->ui->ViewLabel('');
			$session  			= new session();
			$login    			= $session->get('login');
			$groupname  		= isset($login['groupname']) ? $login['groupname'] : '';
			//$access 			= $this->job->checkAccess($groupname);

			//$data 			 	= (array) $this->job->retrieveExistingBrand($this->fields, $code);
			
			//$data['access']		= $access->mod_edit;
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= "";
			$data['ajax_post'] 	= "";

			$this->view->load('job/job', $data);
		}
		
		private function colorStat($stat) {
			$color = 'default';
			switch ($stat) {
				case 'closed':
				$color = 'success';
				$stat = 'CLOSED';
				break;
				case 'on-going':
				$color = 'warning';
				$stat = 'ON-GOING';
				break; 
				case 'cancelled':
				$color = 'danger';
				$stat = 'CANCELLED';
				break; 
			}
			return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
		}

		private function ajax_list() {
			$data	= $this->input->post(array('search', 'sort', 'filter'));
			extract($data);
			//var_dump($data);
			$pagination = $this->job->getJobListing($this->fields, $sort, $search, $filter);
			$table = '';
			if (empty($pagination->result)) {
				$table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
			}
			foreach($pagination->result as $row) {
				$show_activate 		= ($row->stat == 'on-going');
				
				$edit_check = $this->job->check_importationCost($row->job_no);
				//var_dump($edit_check);

				$dropdown = $this->ui->loadElement('check_task')
				->addView()
				->addEdit( $show_activate && $edit_check[0]->count == 0 )
				->addOtherTask(
					'Cancel',
					'remove',
					$show_activate
				)
				->addCheckbox($row->stat == 'on-going')
				->setValue($row->job_no)
				->draw();
				
				$table .= '<tr>';
				$table .= '<td align = "center">' . $dropdown . '</td>';
				$table .= '<td>' . $row->job_no . '</td>';
				$table .= '<td>' . $row->notes . '</td>';
				$table .= '<td class="text-center">' . $this->colorStat($row->stat) . '</td>';
				$table .= '</tr>';
			}
	
			$pagination->table = $table;
	
			return $pagination;
		}

		public function ajax_delete() {
			$data_var = array(
				'job_no'
			);
			$data = $this->input->post($data_var);
			extract($data);
	
			$data_var 					= array('job_no');
			$id      		 			= $this->input->post($data_var);
			$datas['stat'] 			=	'cancelled';
			$result = $this->job->deleteJob($datas, $id);
			var_dump($result);
			if( empty($result) )
			{
				$result = "success";
			}
			else
			{
				$result = $result;
			}
			$dataArray = array( "msg" => $result );
			return $dataArray;
		}

		public function canceljob()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->job->cancel_job($id);
			if( empty($result) )
			{
				$msg = "success";
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg);
		}

    }


?>