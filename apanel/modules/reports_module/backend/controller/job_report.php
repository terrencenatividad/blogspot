<?php
    class controller extends wc_controller {
        public function __construct() {
            parent::__construct();
            $this->ui				= new ui();
            $this->input			= new input();
            $this->job_report   	= new job_report();
            $this->session			= new session();
            $this->report_model 	= new report_model;
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
            
            $this->report_model->generateBalanceTable();

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
            $amountview = 0;
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
                    $valuee = 0;
                    $amountview = $amountview + $row->amount;
                    if($row->amount > 0) {
                        $valuee = number_format($row->amount,2);
                    }else {
                        $valuee = "(" .number_format((abs($row->amount)),2) . ")";
                    }

                    $table .= '<tr>';
                    $table .= '<td>' . $row->job_no . '</td>';
                    $table .= '<td>' . $row->segment5 . '</td>';
                    $table .= '<td>' . $row->accountname . '</td>';
                    $table .= '<td class = "finalsum text-right" > <a href="javascript:void(0);" class = "amount" data-id="'.$row->id.'" data-job="'.$row->job_no.'" >' . $valuee . '</a> </td>';
                    $table .= '<td class = "text-right">' . $jstatus . '</td>';
                    $table .= '</tr>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            // if($amountview = 0 ) {
            //     $pagination->amountview = $amountview;
            // }
            $amountnotif = '';
            if($amountview > 0) {
                $amountview = number_format($amountview,2);
                $amountnotif = "positive";
            }else {
                $amountview = "(" .number_format((abs($amountview)),2) . ")";
                $amountnotif = "negative";
            }
            $pagination->amountnotif = $amountnotif;
            $pagination->amountview = $amountview;
            $pagination->table 	= $table;
            $pagination->csv 	= $this->export_main();
            //var_dump($amountview);
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
                    $valuee = 0;
                    if($row->amount > 0) {
                        $valuee = number_format($row->amount,2);
                    }else {
                        $valuee = "(" .number_format((abs($row->amount)),2) . ")";
                    }
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
                    $csv .= '"' . $valuee . '",';
                    $csv .= '"' . $jstatus . '",';
                    $csv .= "\n";
                }
            }
            if($totalamount > 0) {
                $totalamount = number_format($totalamount,2);
            }else {
                $totalamount = "(" .number_format((abs($totalamount)),2) . ")";
            }
            
            $csv .= '"","","Total ","'. $totalamount .'"';
            return $csv;
        }

        private function processing_fee_listing()
        {
            $sort2          = $this->input->post("sort2");
            $pjobno         = $this->input->post('process_jobno');
            $account_code   = $this->input->post('process_accid');
            //var_dump($account_code);
            //$posted_data 	= array($account_code,$sort2,$pjob_no);
            //$code 	= $this->input->post('account_code');
            //var_dump($sort2,$pjobno,$account_code);
            $pagination 	= $this->job_report->retrieveprocessListing($sort2,$pjobno,$account_code);

            $table 	= '';

            if( !empty($pagination) ) :
                foreach ($pagination as $key => $row) {
                    $link = '';
                    if($row->transtype == "AP") {
                    $link .= BASE_URL . 'financials/accounts_payable/view/'.$row->referenceList;
                    }else if ($row->transtype == "CM") {
                        $link .= BASE_URL . 'financials/credit_memo/view/'.$row->referenceList;
                    }else if ($row->transtype == "DM") {
                        $link .= BASE_URL . 'financials/debit_memo/view/'.$row->referenceList;
                    }else {
                        $link ='';
                    }

                    $table .= '<tr>';
                    $table .= '<td> <a href='.$link.'>' . $row->referenceList . '</a> </td>';
                    $table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
                    $table .= '<td class="tobesum text-right">' . number_format($row->converted_debit,2) . '</td>';
                    $table .= '<td class="tobesum2 text-right">' . number_format($row->converted_credit,2) . '</td>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            
            //$pagination->table 	= $table;
            return array('table' => $table);
            //return $table;
        }

        private function close_job_listing()
        {
            $posted_data 	= $this->input->post(array("close_job_number","sort3"));
            //var_dump($this->input->post('close_job_number'));
            $pagination 	= $this->job_report->retrieveclosedjobListing($posted_data);
            
            $table 	= '';

            if( !empty($pagination) ) :
                foreach ($pagination as $key => $row) {



                    $table .= '<tr>';
                    $table .= '<td>' . $row->voucherno . '</td>';
                    $table .= '<td>' . $row->accountname . '</td>';
                    $table .= '<td class="closed_debit text-right">' . number_format($row->converted_debit,2) . '</td>';
                    $table .= '<td class="closed_credit text-right">' . number_format($row->converted_credit,2) . '</td>';
                }
            else:
                $table .= "<tr>
                <td colspan = '12' class = 'text-center'>No Records Found</td>
                </tr>";
            endif;
            
            //$pagination->table 	= $table;
            //return $pagination;
            return array('table' => $table);
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

        private function jobreport_listingTotal()
        {
            $posted_data 	= $this->input->post(array("daterangefilter", "job_number", "account_search","sort"));
            //var_dump($posted_data);
            //var_dump($this->input->post());

            $result 	= $this->job_report->retrieveListingTotal($posted_data);
            $total = 0;
            if( !empty($result) ) {
                foreach($result as $row) {
                    $valuee = number_format($row->amount,2);
                    $total =+ $valuee;
                }
                var_dump($total);
            }
            return $total;
        }
        
    }


?>