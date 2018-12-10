<?php
    class controller extends wc_controller {
        public function __construct() {
            parent::__construct();
            $this->ui				= new ui();
            $this->input			= new input();
            $this->job   	        = new job();
            $this->session			= new session();
<<<<<<< HEAD
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

=======

            $this->data = array();
            $this->view->header_active = 'purchase/job/';
            $this->log              = new log();
            $this->fields           = array(
                'job_no',
                'notes',
                'ipo_no',
                'stat'
            );
            $this->jobdetails       = array(
                'job_no',
                'ipo_no',
                'itemcode',
                'serial_number',
                'notes',
                'qty',
                'uom'
            );
            

        }

        public function listing() {
            $this->view->title = 'Job';
            
            $data['ui'] 			= $this->ui;
            $data['datefilter']		= date("M d, Y");

            $this->view->load('job/job_list', $data);
        }

        public function create()
        {
            $this->view->title = $this->ui->AddLabel('');

            $data['ui']         = $this->ui;
            $data['task']       = 'save';
            $data['show_input'] = true;
            

            $data['ajax_post']  = '';

            $data['job_no']     = $this->job->autoGenerate("JOB",'job');  
            $data['transactiondate'] = $this->date->dateFormat();
            $data['notes']      = "";

            $editresult = "";

            $this->view->load('job/job', $data);
        }

        public function edit($job)
        {
            $this->view->title = $this->ui->EditLabel('');
            
            $data['ui']                 = $this->ui;
            $data['task']               = 'update';
            $data['show_input']         = true;
            

            $data["job_no"]             = $job;

            $retrievedjob               = $this->job->getJob($job);
            $job_date                   = $this->date->dateFormat($retrievedjob[0]->transactiondate);

            $data['transactiondate']    = $job_date;
            $data['notes']              = $retrievedjob[0]->notes;

            $result                   = (array) $this->job->retrieveExistingJob($job);
            $data['result'] = $result;

            $pr_item                    = array();
            $item                       = array();
            $qty                        = array();
            foreach ($result as $key => $row) {
                
                $pr_item[]      = $row->ipo_no;
                $item[]         = $row->itemcode;
                $qty[]          = $row->qty;

            }
            
            $data['pr_selected']        = $pr_item;
            $data['item_selected']      = $item;
            $data['qty']                = $qty;
            $this->view->load('job/job',  $data);
        }

        public function view($job){
            $this->view->title = $this->ui->ViewLabel('');

            $data['ui']             = $this->ui;
            $data['task']           = 'view';
            $data['show_input']     = false;
            
            $data["job_no"]         = $job;

            $retrievedjob               = $this->job->getJob($job);
            $job_date                   = $this->date->dateFormat($retrievedjob[0]->transactiondate);

            $data['transactiondate']    = $job_date;
            $data['notes']              = $retrievedjob[0]->notes;

            $result                   = (array) $this->job->retrieveExistingJob($job);
            $data['result'] = $result;
            foreach ($result as $key => $row) {
                $pr[]       = $row->ipo_no;
                $item[]     = $row->itemcode;
                $qty[]      = $row->qty;
            }

            $data['pr_selected']        = $pr;
            $data['item_selected']      = $item;
            $data['qty']                = $qty;
            $this->view->load('job/job',  $data);
        }

        public function ajax($task) {
            $ajax = $this->{$task}();
            if ($ajax) {
                header('Content-type: application/json');
                echo json_encode($ajax);
            }
        }

        private function ajax_list() {
            $data   = $this->input->post(array('search', 'sort', 'filter'));
            extract($data);
            $pagination = $this->job->getJobListing($this->fields, $sort, $search, $filter);
            $table = '';
            if (empty($pagination->result)) {
                $table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
            }
            foreach($pagination->result as $row) {
                $show_activate      = ($row->stat != 'inactive');
                $show_deactivate    = ($row->stat != 'active');
                $dropdown = $this->ui->loadElement('check_task')
                ->addView()
                ->addEdit()
                ->addDelete()
                ->addCheckbox()
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
                'id'
            );
            $data = $this->input->post($data_var);
            extract($data);
    
            $data_var = array('id');
            $id       = $this->input->post($data_var);

            $result = $this->job->deleteJob($id);
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

        public function ajax_processingFee() {

            $data_var = array('id');
            $id       = $this->input->post($data_var);
            $pagination = $this->job->check_importationCost($id);
            $table = '';
            if (empty($pagination->result)) {
                $table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
            }
            foreach($pagination->result as $row) {
                $table .= '<td>' . $row->reference . '</td>';
                $table .= '<td>' . $row->date . '</td>';
                $table .= '<td>' . $row->debit . '</td>';
                $table .= '<td>' . $row->credit . '</td>';
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
                $stat = 'JOB CLOSED';
                break;
                case 'inactive':
                $color = 'warning';
                $stat = 'JOB ON-GOING';
                break; 
            }
            return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
        }

        private function ajax_load_ipo_list() {
            $selected_ipo = (array)$this->input->post("preselect");
            $pagination = $this->job->getIPOPagination();
            $table      = '';

            if (empty($pagination->result)) {
                $table = '<tr><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>';
            }
            foreach ($pagination->result as $key => $row) {
                $table .= '<tr>';
                $table .= '<td class="ipo_checkbox"><input type="checkbox" data-prno = "' . $row->voucherno . '"></td>';
                $table .= '<td class="ipo_no">' . $row->voucherno . '</td>';
                $table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
                $table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
                $table .= '</tr>';
            }
            $table .= '<script>checkExistingIPO();</script>';
            $pagination->table = $table;
            return $pagination;
        }

        private function ajax_load_ipo_items(){
            $job_no     = $this->input->post("job");
            $ipo        = (array)$this->input->post("ipo");
            $task       = $this->input->post("task");
            $table      = '';
            $checkid    = 0;
            
            
            foreach ($ipo as $key => $value) {
                $pagination = $this->job->getItemPagination($value);
                
                if (empty($pagination->result)) {
                    $table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
                }
                

                foreach ($pagination->result as $key => $row) {
                    
                    $taggedqty = $this->job->getTaggedItemQty($row->voucherno, $row->itemcode, $job_no);
                    
                    if (empty($taggedqty)) {
                        $maxval = $row->receiptqty;
                    }
                    else
                        $maxval = $row->receiptqty - $taggedqty[0]->count;
                    
                    if ($maxval) {
                        $table .= '<tr>';
                        
                            $table .= '<td class = "item_checkbox">';
                            $table .= '
                                <input type="checkbox" id = "'.$checkid.'" data-itemcode="'.$row->itemcode.'" data-pr="'.$row->voucherno.'">
                                <input type="hidden" name="txtipo[]" value="'.$row->voucherno.'">
                                <input type="hidden" name="txtitem[]" value="'.$row->itemcode.'">
                                <input type="hidden" name="txtuom[]" value="'.$row->receiptuom.'">
                                <input type="hidden" name="txtserial[]" value="">
                                <input type="hidden" name="txtdesc[]" value="'.$row->detailparticular.'">
                            </td>';
                        
                        
                        $table .= '<td class = "td_ipono">' . $row->voucherno . '</td>';
                        $table .= '<td>' . $row->itemcode . " - " . $row->detailparticular . '</td>';
                        $table .= '<td></td>';
                        $table .= '<td>';
                        $table .= $this->ui->formField('text')
                                            ->setName('txtquantity[]')
                                            ->setCLass('quantity text-right')
                                            ->setMaxLength(12)
                                            ->setValue(1)
                                            ->setAttribute(array("data-maxval"=>$maxval))
                                            ->setValidation('integer') //required code
                                            ->draw(true);
                        $table .= '</td>';
                        $table .= '<td class = "text-right">' . strtoupper($row->receiptuom) . '</td>';
                        $table .= '</tr>';
                        $checkid++;
                    }
                }
            }
            if ($table=="") {
                $table = '<tr><td colspan="7" class="text-center"><b>No Available Items To Tag</b></td></tr>';
            }
            $table .= '<script>checkSelectedItems();</script>';
            if ($task=="view") {
                $table .= '<script>disabledfields();</script>';
            }
            $pagination->table = $table;
            return $pagination;
        }
        

        private function save(){
            $job_voucher    = $this->job->autoGenerate("JOBIPO","job_ipo");
            $job_notarray   = $this->input->post("txtjob");
            $notes          = $this->input->post("remarks");
            $date           = $this->input->post("transaction_date");
            $date           = $this->date->dateDbFormat($date);
            $status         = "inactive";
            $ipo            = $this->input->post("txtipo");
            $itemcode       = $this->input->post("txtitem");
            $qty            = $this->input->post("txtquantity");
            $uom            = $this->input->post("txtuom");
            $serial         = $this->input->post("txtserial");
            $desc           = $this->input->post("txtdesc");
            $is_multiple    = (count($itemcode)>1) ? true : false;

            for ($i=0 ; $i<count($itemcode) ; $i++) {
                $job_static[$i]            = $this->input->post("txtjob");
                $job_increment[$i]  = $job_voucher++;
            }


            $values = array(
                'job_no'            => $job_notarray,
                'ipo_no'            => "",
                'notes'             => $notes,
                'transactiondate'   => $date,
                'stat'              => $status
            );
            $result = $this->job->saveValues("job", $values);


            $values = array(
                'job_no'        =>$job_static,
                'ipo_no'        =>$ipo,
                'itemcode'      =>$itemcode,
                'qty'           =>$qty,
                'uom'           =>$uom,
                'serial_number' =>$serial,
                'description'   =>$desc,
            );
            
            $result1 = $this->job->saveFromPost("job_details", $values);

            $values = array(
                'job_voucher_no'    => $job_increment,
                'job_no'            => $job_static, 
                'voucher_no'        => $ipo,
            );
            
            $result2 = $this->job->saveFromPost("job_ipo", $values);

            $query_result = array(
                'query1' => $result,
                'query2' => $result1,
                'query3' => $result2
            );
            return $query_result;
        }

        private function update(){
            $job_voucher    = $this->job->autoGenerate("JOBIPO","job_ipo");
            $job_notarray   = $this->input->post("txtjob");
            $notes          = $this->input->post("remarks");
            $date           = $this->input->post("transaction_date");
            $date           = $this->date->dateDbFormat($date);
            $status         = "inactive";
            $ipo            = $this->input->post("txtipo");
            $itemcode       = $this->input->post("txtitem");
            $qty            = $this->input->post("txtquantity");
            $uom            = $this->input->post("txtuom");
            $serial         = $this->input->post("txtserial");
            $desc           = $this->input->post("txtdesc");
            $is_multiple    = (count($itemcode)>1) ? true : false;

            for ($i=0 ; $i<count($itemcode) ; $i++) {
                $job_static[$i]            = $this->input->post("txtjob");
                $job_increment[$i]  = $job_voucher++;
            }
            
            $delete_result  = $this->job->deleteJobValues("job_details", $job_static[0]);
            $delete_result2 = $this->job->deleteJobValues("job_ipo", $job_static[0]);

            $values = array(
                'job_no'            => $job_notarray,
                'ipo_no'            => "",
                'notes'             => $notes,
                'transactiondate'   => $date,
                'stat'              => $status
            );

            $result = $this->job->updateJobValues($values, $job_static[0]);


            $values = array(
                'job_no'        =>$job_static,
                'ipo_no'        =>$ipo,
                'itemcode'      =>$itemcode,
                'qty'           =>$qty,
                'uom'           =>$uom,
                'serial_number' =>$serial,
                'description'   =>$desc,
            );
            if ($delete_result===true) {
                $result1 = $this->job->saveFromPost("job_details", $values);
            }
            

            $values = array(
                'job_voucher_no'    => $job_increment,
                'job_no'            => $job_static, 
                'voucher_no'        => $ipo,
            );
            if ($delete_result2===true) {
                $result2 = $this->job->saveFromPost("job_ipo", $values);
            }
            

            $query_result = array(
                'delquery1' => $delete_result,
                'delquery2' => $delete_result2,
                'query1' => $result,
                'query2' => $result1,
                'query3' => $result2
            );
            return $query_result;
        }
        
    } 
>>>>>>> 873296fbff198c31140d832fae21195ca6ead96e

?>