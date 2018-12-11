<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->general_journal	= new general_journal();
		$this->item_model		= new item_model();
		$this->session			= new session();
		$this->show_input 	    = true;
		$this->data = array();
		$this->view->header_active  = 'report/';
	}

	public function listing() {
		$this->view->title = 'General Journal';
		$data['ui'] = $this->ui;
		$data['datefilter'] 	= $this->date->datefilterMonth();
		$data['show_input']  = $this->show_input;
		$this->view->load('general_journal', $data);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data = $this->input->post(array('daterangefilter','customer','sort'));
		$datefilter	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $datefilter);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$pagination = $this->general_journal->general_journalList($dates[0], $dates[1], $sort);
		$table = '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
        }

        $totaldebit = 0.00;
        $totalcredit = 0.00;
		foreach ($pagination->result as $key => $row) {
            $totaldebit     +=   $row->debit;
            $totalcredit    +=   $row->credit;
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td><a href="'.BASE_URL."financials/journal_voucher/view/" . $row->voucherno . '" target="_blank"> '.$row->voucherno.'</a></td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td>' . $row->accountname . '</td>';
			$table .= '<td>' . number_format($row->debit,2) . '</td>';
			$table .= '<td>' . number_format($row->credit,2) . '</td>';
			$table .= '</tr>';
        }
        
        $table .= '<tr>';
        $table .= '<td colspan = "3"></td>';
        $table .= '<td><b>Total</b></td>';
        $table .= '<td>'.number_format($totaldebit,2).'</td>';
        $table .= '<td>'.number_format($totalcredit,2).'</td>';
        $table .= '</tr>';

		$pagination->table  = $table;
		$pagination->csv 	= $this->export();
		return $pagination;
	}

	private function export(){
		$data = $this->input->post(array('daterangefilter','customer','sort'));
		$strdate	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$sort      = $data['sort'];
		$datefilter = explode('-', $data['daterangefilter']);
		$dates		= array();
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$result = $this->general_journal->fileExport($dates[0], $dates[1], $sort);
		$header = array("Date","Reference No.","Remarks","Account Title","Debit","Credit");

		$csv = '';
		$csv .= 'General Journal';
		$csv .= "\n\n";
		$csv .= '"Date:","'.$strdate.'"';
		$csv .= "\n\n";
		$csv .= '"' . implode('","', $header) . '"';
		$csv .= "\n";
        
        $totaldebit = 0.00;
        $totalcredit = 0.00;
        $retrieved  =   array_filter($result);

		if (!empty($retrieved)){
			foreach ($retrieved as $key => $row){
                $totaldebit     +=   $row->debit;
                $totalcredit    +=   $row->credit;
				$csv .= '"' . $this->date->dateFormat($row->transactiondate) . '",';
				$csv .= '"' . $row->voucherno . '",';
				$csv .= '"' . $row->remarks . '",';
				$csv .= '"' . $row->accountname . '",';
				$csv .= '"' . number_format($row->debit,2) . '",';
				$csv .= '"' . number_format($row->credit,2) . '"';
				$csv .= "\n";
            }

            $csv .= '"","","",';
            $csv .= '"Total",';
            $csv .= '"' . number_format($totaldebit,2) . '",';
            $csv .= '"' . number_format($totalcredit,2). '"';
            $csv .= "\n";
		}
		return $csv;
	}
}