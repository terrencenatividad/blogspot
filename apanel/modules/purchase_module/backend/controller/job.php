<?php
    class controller extends wc_controller {
        public function __construct() {
            parent::__construct();
            $this->ui				= new ui();
            $this->input			= new input();
            $this->job   	        = new job();
            $this->session			= new session();
            $this->log 			    = new log();
            $this->seq              = new seqcontrol();
            
            $this->view->header_active = 'purchase/job/';

            $this->fields 			= array(
                'job_no',
                'notes',
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

        public function view($job){
            $this->view->title = $this->ui->ViewLabel('');

            $data['ui']                 = $this->ui;
            $data['task']               = 'view';
            $data['show_input']         = false;
            
            $data["job_no"]             = $job;

            $retrievedjob               = $this->job->getJob($job);
            $job_date                   = $this->date->dateFormat($retrievedjob[0]->transactiondate);

            $data['transactiondate']    = $job_date;
            $data['notes']              = $retrievedjob[0]->notes;
            $data['stat']               = $retrievedjob[0]->stat;
            $result                     = (array) $this->job->retrieveExistingJob($job);
            $data['result']             = $result;

            $ipo_item                   = array();
            $item                       = array();
            $qty                        = array();
            $linenum                    = array();
            $qty_left                   = array();
            foreach ($result as $key => $row) {
                $getQty     = $this->job->getTaggedItemQty($row->ipo_no, $row->linenum, $job, 'view');
                $getThisQty = $this->job->getThisQty($row->ipo_no, $row->linenum, $job);
                $ipo_item[]         = $row->ipo_no;
                $item[]             = $row->itemcode;
                $linenum[]          = $row->linenum;
                $qty_left[]         = $row->receiptqty - $getQty->count;
                $qty[]              = $row->qty;
                $detailparticular[] = $row->description;
                $uom[]              = $row->uom;
            }
            
            $data['ipo']        = $ipo_item;
            $data['item']       = $item;
            $data['linenum']    = $linenum;
            $data['qty_left']   = $qty_left;
            $data['qty']        = $qty;
            $data['detailparticular'] = $detailparticular;
            $data['uom']        = $uom;
            $this->view->load('job/job',  $data);
        }
        
        public function create()
        {
            $this->view->title = $this->ui->AddLabel('');

            $data['ui']         = $this->ui;
            $data['task']       = 'save';
            $data['show_input'] = true;
            

            $data['ajax_post']  = '';

            $data['job_no']     = '';  
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

            $result                     = (array) $this->job->retrieveExistingJob($job);
            $data['result']             = $result;

            $ipo_item                   = array();
            $item                       = array();
            $qty                        = array();
            $linenum                    = array();
            foreach ($result as $key => $row) {
                
                $ipo_item[]      = $row->ipo_no;
                $item[]         = $row->itemcode;
                $linenum[]      = $row->linenum;
                $qty[]          = $row->qty;

            }
            
            $data['ipo']         = $ipo_item;
            $data['item']       = $item;
            $data['linenum']    = $linenum;
            $data['qty']        = $qty;
            $this->view->load('job/job',  $data);
        }

        private function save(){
            $job_voucher    = $this->seq->getValue("JOBIPO");
            $job_notarray   = $this->seq->getValue("JOB");
            $notes          = $this->input->post("remarks");
            $date           = $this->input->post("transaction_date");
            $date           = $this->date->dateDbFormat($date);
            $status         = "on-going";
            $ipo            = $this->input->post("txtipo");
            $itemcode       = $this->input->post("txtitem");
            $linenum        = $this->input->post("txtlinenum");
            $qty            = $this->input->post("txtquantity");
            $uom            = $this->input->post("txtuom");
            $serial         = $this->input->post("txtserial");
            $desc           = $this->input->post("txtdesc");
            $submit         = $this->input->post("submit");
            $is_multiple    = (count($itemcode)>1) ? true : false;

            $jobcost = 0;
            for ($i=0 ; $i<count($itemcode) ; $i++) {
                $job_static[$i]     = $job_notarray;
                $job_increment[$i]  = $job_voucher++;
                
                $itemcost[$i]           = $this->job->getItemCost($ipo[$i],$itemcode[$i],$linenum[$i]);
                $jobcost            += $itemcost[$i] * $qty[$i];
            }

            $values = array(
                'job_no'            => $job_notarray,
                'notes'             => $notes,
                'transactiondate'   => $date,
                'stat'              => $status,
                'job_cost'          => $jobcost
            );
            $result = $this->job->saveValues("job", $values);


            $values = array(
                'job_no'        =>$job_static,
                'ipo_no'        =>$ipo,
                'linenum'       =>$linenum,
                'itemcode'      =>$itemcode,
                'serialno'      =>$serial,
                'engineno'      =>'',
                'chassisno'     =>'',
                'qty'           =>$qty,
                'uom'           =>$uom,
                'description'   =>$desc
            );
            
            $result1 = $this->job->saveFromPost("job_details", $values);

            $values = array(
                'job_voucher_no'    => $job_increment,
                'job_no'            => $job_static, 
                'voucher_no'        => $ipo,
            );
            
            $result2 = $this->job->saveFromPost("job_ipo", $values);

            $redirect_url = MODULE_URL;
            if ($submit == 'save_new') {
             $redirect_url = MODULE_URL . 'create';
            } else if ($submit == 'save_preview') {
             $redirect_url = MODULE_URL . 'view/' . $job_notarray;
            }
            return array(
                'redirect'  => $redirect_url,
                'query1' => $result,
                'query2' => $result1,
                'query3' => $result2
            );
        }

        private function update(){
            $job_voucher    = $this->seq->getValue("JOBIPO");
            $job_notarray   = $this->input->post("txtjob");
            $notes          = $this->input->post("remarks");
            $date           = $this->input->post("transaction_date");
            $date           = $this->date->dateDbFormat($date);
            $status         = "on-going";
            $ipo            = $this->input->post("txtipo");
            $itemcode       = $this->input->post("txtitem");
            $linenum        = $this->input->post("txtlinenum");
            $qty            = $this->input->post("txtquantity");
            $uom            = $this->input->post("txtuom");
            $serial         = $this->input->post("txtserial");
            $desc           = $this->input->post("txtdesc");
            $is_multiple    = (count($itemcode)>1) ? true : false;
            $submit         = $this->input->post("submit");

            $jobcost = 0;
            for ($i=0 ; $i<count($itemcode) ; $i++) {
                $job_static[$i]            = $this->input->post("txtjob");
                $job_increment[$i]  = $job_voucher++;
                
                $itemcost[$i]           = $this->job->getItemCost($ipo[$i],$itemcode[$i],$linenum[$i]);
                $jobcost            += $itemcost[$i] * $qty[$i];
            }
            // var_dump($jobcost);
            
            $delete_result  = $this->job->deleteJobValues("job_details", $job_static[0]);
            $delete_result2 = $this->job->deleteJobValues("job_ipo", $job_static[0]);

            $values = array(
                'job_no'            => $job_notarray,
                'notes'             => $notes,
                'transactiondate'   => $date,
                'stat'              => $status,
                'job_cost'          => $jobcost
            );

            $result = $this->job->updateJobValues($values, $job_static[0]);


            $values = array(
                'job_no'        =>$job_static,
                'ipo_no'        =>$ipo,
                'linenum'       =>$linenum,
                'itemcode'      =>$itemcode,
                'serialno'      =>$serial,
                'engineno'      =>'',
                'chassisno'     =>'',
                'qty'           =>$qty,
                'uom'           =>$uom,
                'description'   =>$desc
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
            
            $redirect_url = MODULE_URL;
            if ($submit == 'save_new') {
             $redirect_url = MODULE_URL . 'create';
            } else if ($submit == 'save_preview') {
             $redirect_url = MODULE_URL . 'view/' . $job_notarray;
            }
            $query_result = array(
                'redirect'  => $redirect_url,
                'delquery1' => $delete_result,
                'delquery2' => $delete_result2,
                'query1' => $result,
                'query2' => $result1,
                'query3' => $result2
            );

            return $query_result;
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
          $data  = $this->input->post(array('search', 'sort', 'filter', 'daterangefilter'));
          extract($data);
          //var_dump($data);
          $pagination = $this->job->getJobListing($this->fields, $sort, $search, $filter, $daterangefilter);
          $table = '';
          if (empty($pagination->result)) {
            $table = '<tr><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>';
          }
          foreach($pagination->result as $row) {
            $show_activate     = ($row->stat == 'on-going');
            
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
        
        private function ajax_load_ipo_list() {
            
            $pagination = $this->job->getIPOPagination();
            $table      = '';

            if (empty($pagination->result)) {
                $table = '<tr><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>';
            }
            foreach ($pagination->result as $key => $row) {
                $table .= '<tr>';
                $table .= '<td><input type="checkbox" data-ipono = "' . $row->voucherno . '"></td>';
                $table .= '<td>' . $row->voucherno . '</td>';
                $table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
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
                    
                    $taggedqty = $this->job->getTaggedItemQty($row->voucherno, $row->linenum, $job_no, $task);
                    $maxval = $row->receiptqty - $taggedqty->count;
                    
                    if ($maxval) {
                        $readonly = '';
                        $disable = '';
                    }
                    else{
                        $readonly = 'readonly';
                        $disable = 'disabled';
                    }

                        $table .= '<tr>';
                        
                            $table .= '<td>';
                            $table .= '
                                <input type="checkbox" id = "'.$checkid.'" data-itemcode="'.$row->itemcode.'" data-ipo="'.$row->voucherno.'" data-linenum="'.$row->linenum.'" '.$disable.'>
                                <input type="hidden" name="txtipo[]" value="'.$row->voucherno.'">
                                <input type="hidden" name="txtitem[]" value="'.$row->itemcode.'">
                                <input type="hidden" name="txtlinenum[]" value="'.$row->linenum.'">
                                <input type="hidden" name="txtserialno[]" value="">
                                <input type="hidden" name="txtengineno[]" value="">
                                <input type="hidden" name="txtchassisno[]" value="">
                                <input type="hidden" name="txtuom[]" value="'.$row->receiptuom.'">
                                <input type="hidden" name="txtdesc[]" value="'.$row->detailparticular.'">
                            </td>';
                        
                        
                        $table .= '<td class = "td_ipono">' . $row->voucherno . '</td>';
                        $table .= '<td>' . $row->itemcode . " - " . $row->detailparticular . '</td>';
                        $table .= '<td></td>';
                        $table .= '<td style = "padding-right:20px">';
                        $table .= $this->ui->formField('text')
											->setAttribute(array("readOnly"=>"readOnly"))
											->setClass("qty_left input_label text-right")
											->setValue($maxval)
											->draw(true);
                        $table .= '</td>';
                        $table .= '<td>';
                        $table .= $this->ui->formField('text')
                                            ->setName('txtquantity[]')
                                            ->setCLass('quantity text-right')
                                            ->setAttribute(array($readonly))
                                            ->setMaxLength(12)
                                            ->setValue($maxval)
                                            ->setAttribute(array("data-maxval"=>$maxval))
                                            ->setValidation('integer') //required code
                                            ->draw(true);
                        $table .= '</td>';
                        $table .= '<td class = "text-right">' . strtoupper($row->receiptuom) . '</td>';
                        $table .= '</tr>';
                        $checkid++;
                    
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
    }


?>