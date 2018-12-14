<?php
    class controller extends wc_controller {
        public function __construct() {
            parent::__construct();
            $this->ui				= new ui();
            $this->input			= new input();
            $this->job_report   	= new job_report();
            $this->session			= new session();
            $this->data = array();
            $this->view->header_active = 'report/';
        }

        public function listing() {
            //$this->report_model->generatePurchaseReportsTable();
    
            $this->view->title = 'Job Report';
            
            $data['ui'] 				= $this->ui;
            $data['datefilter'] 		= $this->date->datefilterMonth();

            $data['job_list'] 		= $this->job_report->get_allJob();
            $data['job_list2']      = $this->job_report->get_job_without_closed();
            $this->view->load('job_report', $data);
        }

        public function ajax($task) {
            $ajax = $this->{$task}();
            if ($ajax) {
                header('Content-type: application/json');
                echo json_encode($ajax);
            }
        }

        private function jobreport_listing()
        {
            $posted_data 	= $this->input->post(array("daterangefilter", "job_number", "account_search","sort"));
            //var_dump($posted_data);
            //var_dump($this->input->post());

            $pagination 	= $this->job_report->retrieveListing($posted_data);

            $table 	= '';

            if( !empty($pagination->result) ) :
                foreach ($pagination->result as $key => $row) {

                    if($row->stat == 'on-going')
                    {
                        $jstatus = '<span class="label label-warning">'.strtoupper($row->stat).'</span>';
                    }
                    else if($row->stat == 'closed')
                    {
                        $jstatus = '<span class="label label-success">'.strtoupper($row->stat).'</span>';
                    }
                    else if($row->stat == 'cancelled')
                    {
                        $jstatus = '<span class="label label-danger">'.strtoupper($row->stat).'</span>';
                    }

                    $table .= '<tr>';
                    $table .= '<td>' . $row->job_no . '</td>';
                    $table .= '<td>' . $row->segment5 . '</td>';
                    $table .= '<td>' . $row->accountname . '</td>';
                    $table .= '<td class = "finalsum text-right" > <a href="#" class = "amount" data-id="'.$row->id.'" >' . number_format($row->amount,2) . '</a> </td>';
                    $table .= '<td class = "text-right">' . $jstatus . '</td>';
                    $table .= '</tr>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            
            $pagination->table 	= $table;
            $pagination->csv 	= $this->export_main();
            return $pagination;
        }

        private function export_main(){
            $posted_data 	= $this->input->post(array("daterangefilter", "job_number", "account_search","sort"));
            
            $header = array("Job Number","Account Code","Account Name","Amount","Status");
    
            $csv = '';
            $csv .= '"' . implode('","', $header) . '"';
            $csv .= "\n";
            
            $result = $this->job_report->export_main($posted_data);
    
            $totalamount 	=	0;
    
            if (!empty($result)){
                foreach ($result->result as $key => $row){
    
                    $totalamount += $row->amount;
                    $jstatus = '';
                    if($row->stat == 'on-going')
                    {
                        $jstatus = strtoupper($row->stat);
                    }
                    else if($row->stat == 'closed')
                    {
                        $jstatus = strtoupper($row->stat);
                    }
                    else if($row->stat == 'cancelled')
                    {
                        $jstatus = strtoupper($row->stat);
                    }
    
                    $csv .= '"' . $row->job_no . '",';
                    $csv .= '"' . $row->segment5 . '",';
                    $csv .= '"' . $row->accountname . '",';
                    $csv .= '"' . number_format($row->amount,2) . '",';
                    $csv .= '"' . $jstatus . '",';
                    $csv .= "\n";
                }
            }
            
            $csv .= '"","","Total ","'. number_format($totalamount,2) .'"';
            return $csv;
        }

        private function processing_fee_listing()
        {
            $posted_data 	= $this->input->post(array("account_code","sort"));

            //$code 	= $this->input->post('account_code');
            $pagination 	= $this->job_report->retrieveprocessListing($posted_data);

            $table 	= '';

            if( !empty($pagination->result) ) :
                foreach ($pagination->result as $key => $row) {



                    $table .= '<tr>';
                    $table .= '<td>' . $row->referenceList . '</td>';
                    $table .= '<td>' . $this->date->dateFormat($row->entereddate) . '</td>';
                    $table .= '<td class="tobesum text-right">' . number_format($row->debit,2) . '</td>';
                    $table .= '<td class="tobesum2 text-right">' . number_format($row->credit,2) . '</td>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            
            $pagination->table 	= $table;
            return $pagination;
        }

        private function close_job_listing()
        {
            $posted_data 	= $this->input->post(array("close_job_number","sort"));
            //var_dump($this->input->post('close_job_number'));
            $pagination 	= $this->job_report->retrieveclosedjobListing($posted_data);
            
            $table 	= '';

            if( !empty($pagination->result) ) :
                foreach ($pagination->result as $key => $row) {



                    $table .= '<tr>';
                    $table .= '<td>' . $row->voucherno . '</td>';
                    $table .= '<td>' . $row->accountname . '</td>';
                    $table .= '<td class="closed_debit text-right">' . number_format($row->debit,2) . '</td>';
                    $table .= '<td class="closed_credit text-right">' . number_format($row->credit,2) . '</td>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            
            $pagination->table 	= $table;
            return $pagination;
        }

        private function ajax_get_closing() {
			$job_no 	= $this->input->post('closedjob');
			$data['stat'] 			=	'closed';
			$result 			= 	$this->job_report->updateStat($data, $job_no);
			if($result)
			{
				$msg = "success";
			} else {
				$msg = "Failed to Update.";
			}
	
			return $dataArray = array( "msg" => $msg );
        }
        
    }


?>