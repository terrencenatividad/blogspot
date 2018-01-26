<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui			= new ui();
		$this->url			= new url();
		$this->input		= new input();
		$this->bankrecon	= new bankrecon_model();
		$this->report_model	= $this->checkoutModel('reports_module/report_model');
		$this->csv_header	= array(
			'Transaction Date',
			'Check Number',
			'Description',
			'Income',
			'Expense'
		);
	}

	public function create() {
		$this->view->title	= 'Bank Recon';
		$data['ui']			= $this->ui;
		$data['show_input']	= true;
		$data['date_today']	= $this->date->dateFormat();
		$data['bank_list']	= $this->bankrecon->getBankList();
		$data['has_recon']	= $this->bankrecon->getLastRecon();
		$data['ajax_task']	= 'ajax_save_import';
		$data['ajax_post']	= '';
		$this->view->load('bankrecon/bankrecon_upload', $data);
	}

	public function listing($id = '') {
		if (empty($id)) {
			$this->url->redirect(MODULE_URL);
		}
		$this->view->title	= 'Bank Recon (Transaction Tagging)';
		$data['ui']			= $this->ui;
		$data['recon_id']	= $id;
		$this->bankrecon->cleanData(base64_decode($id));
		$this->view->load('bankrecon/bankrecon_tagging', $data);
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

	private function ajax_get_headers() {
		$recon_id = base64_decode($this->input->post('recon_id'));

		$ajax = $this->bankrecon->getHeaderValues($recon_id);

		return $ajax;
	}

	private function ajax_get_bank() {
		$recon_id = base64_decode($this->input->post('recon_id'));
		$pagination	= $this->bankrecon->getBankListPagination($recon_id);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->nature . '</td>';
			$table .= '<td>' . $row->checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td><button type="button" class="btn btn-success btn-xs tag-match" data-id="' . $row->id . '" data-nature="' . $row->nature . '">Find Match</button></td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_formatching() {
		$recdet_id	= $this->input->post('recdet_id');

		$match = $this->bankrecon->getForMatchingList($recdet_id);
		$table		= '';
		if (empty($match->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($match->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->chequenumber . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td><button type="button" class="btn btn-success btn-xs tag-match" data-id="' . $row->voucherno . '" data-match="' . $recdet_id . '">Tag as Match</button></td>';
			$table .= '</tr>';
		}
		$ajax = array(
			'table'				=> $table,
			'transaction_type'	=> $match->transaction_type
		);
		return $ajax;
	}

	private function ajax_get_formatching2() {
		$voucherno	= $this->input->post('voucherno');
		$recon_id	= base64_decode($this->input->post('recon_id'));

		$match = $this->bankrecon->getForMatchingList2($voucherno, $recon_id);
		$table		= '';
		if (empty($match->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($match->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td></td>';
			$table .= '<td>' . $row->checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td><button type="button" class="btn btn-success btn-xs tag-match" data-id="' . $row->id . '" data-match="' . $voucherno . '">Tag as Match</button></td>';
			$table .= '</tr>';
		}
		$ajax = array(
			'table'				=> $table,
			'transaction_type'	=> $match->transaction_type
		);
		return $ajax;
	}

	private function ajax_set_match() {
		$data = $this->input->post(array('recdet_id', 'voucherno'));
		extract($data);
		
		$result = $this->bankrecon->setMatch($recdet_id, $voucherno);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_get_system() {
		$recon_id = base64_decode($this->input->post('recon_id'));
		$pagination	= $this->bankrecon->getSystemListPagination($recon_id);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->nature . '</td>';
			$table .= '<td>' . $row->chequenumber . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td colspan="2"><button type="button" class="btn btn-success btn-xs tag-match" data-id="' . $row->voucherno . '" data-nature="' . $row->nature . '">Find Match</button></td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_get_matched() {
		$recon_id = base64_decode($this->input->post('recon_id'));
		$pagination	= $this->bankrecon->getMatchedListPagination($recon_id);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->r_transactiondate) . '</td>';
			$table .= '<td>' . $row->r_checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->r_amount, 2) . '</td>';
			$table .= '<td colspan="2" style="white-space: nowrap"><button type="button" class="btn btn-success btn-xs tag-match" data-id="' . $row->id . '">Confirm Match</button> <button type="button" class="btn btn-danger btn-xs remove-match" data-id="' . $row->id . '">Remove</button></td>';
			$table .= '<td>' . $this->date->dateFormat($row->v_transactiondate) . '</td>';
			$table .= '<td>' . $row->v_checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->v_amount, 2) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_set_confirm() {
		$tagged_id = $this->input->post('tagged_id');

		$result = $this->bankrecon->confirmMatched($tagged_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_get_confirmed() {
		$recon_id = base64_decode($this->input->post('recon_id'));
		$pagination	= $this->bankrecon->getConfirmedListPagination($recon_id);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->r_transactiondate) . '</td>';
			$table .= '<td>' . $row->r_checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->r_amount, 2) . '</td>';
			$table .= '<td colspan="2"><button type="button" class="btn btn-danger btn-xs remove-match" data-id="' . $row->id . '">Remove</button></td>';
			$table .= '<td>' . $this->date->dateFormat($row->v_transactiondate) . '</td>';
			$table .= '<td>' . $row->v_checkno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->v_amount, 2) . '</td>';
			$table .= '</tr>';
		}

		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_remove_match() {
		$tagged_id = $this->input->post('tagged_id');

		$result = $this->bankrecon->removeMatched($tagged_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_set_bankdeposit() {
		$recdet_id = $this->input->post('recdet_id');

		$result = $this->bankrecon->tagUnrecordedDeposit($recdet_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_set_bankwithdrawal() {
		$recdet_id = $this->input->post('recdet_id');
		
		$result = $this->bankrecon->tagUnrecordedWithdrawal($recdet_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_set_systemdeposit() {
		$voucherno = $this->input->post('voucherno');
		$recon_id = base64_decode($this->input->post('recon_id'));

		$result = $this->bankrecon->tagDepositTransit($voucherno, $recon_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_set_systemwithdrawal() {
		$voucherno = $this->input->post('voucherno');
		$recon_id = base64_decode($this->input->post('recon_id'));
		
		$result = $this->bankrecon->tagOutstandingCheque($voucherno, $recon_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function ajax_get_tagged() {
		$type		= $this->input->post('type');
		$recon_id	= base64_decode($this->input->post('recon_id'));

		$result = $this->bankrecon->getTagged($type, $recon_id);
		$table		= '';
		if (empty($result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($result as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->checkno . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '<td><button type="button" class="btn btn-danger btn-xs  remove-match" data-id="' . $row->id . '">Remove Tag</button></td>';
			$table .= '</tr>';
		}
		$ajax = array(
			'table' => $table
		);
		return $ajax;
	}

	private function ajax_set_finalized() {
		$recon_id	= base64_decode($this->input->post('recon_id'));

		$result = $this->bankrecon->finalizeBankRecon($recon_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function csv_header() {
		header('Content-type: application/csv');

		$csv = '';
		$csv .= '"' . implode('","', $this->csv_header) . '"';

		return $csv;
	}

	private function ajax_save_import() {
		$csv_array	= array_map('str_getcsv', file($_FILES['file']['tmp_name']));
		$result		= false;
		$error		= array();
		$values		= array();
		$invalid	= array();
		$validity	= array();
		$recon_id	= '';

		if ($csv_array[0] == $this->csv_header) {
			unset($csv_array[0]);

			$csv_array = array_reverse($csv_array);
			foreach ($csv_array as $key => $row) {
				if (empty(implode('', $row))) {
					unset($csv_array[$key]);
				} else {
					break;
				}
			}
			$csv_array = array_reverse($csv_array);

			if (empty($csv_array)) {
				$error = 'No Data Given';
			} else {
				$fields = array(
					'accountcode',
					'endbalance'
				);
				$data		= $this->input->post($fields);
				$datefilter	= $this->input->post('daterangefilter');
				$datefilter	= explode('-', $datefilter);
				foreach ($datefilter as $date) {
					$dates[] = date('Y-m-d', strtotime($date));
				}
				$lastrecon = $this->bankrecon->getLastReconClosed($data['accountcode']);
				if ($lastrecon) {
					if ($lastrecon->periodto >= $dates[0]) {
						$error[] = "Invalid Date. Last Recon for '{$lastrecon->accountname}' is {$this->date->dateFormat($lastrecon->periodfrom)} - {$this->date->dateFormat($lastrecon->periodto)}";
					}
				}
				$check_field = array(
					'Item Code' => array()
				);
				$linenum = 0;
				foreach ($csv_array as $row) {
					$linenum++;
					$transactiondate = $this->date->dateDbFormat($this->getValueCSV('Transaction Date', $row, 'required', $validity));
					if ($transactiondate < $dates[0] || $transactiondate > $dates[1]) {
						$error[0] = 'Data dates is not within the Date Ranged picked.';
					}
					$values[] = array(
						'linenum'			=> $linenum,
						'transactiondate'	=> $transactiondate,
						'checkno'			=> $this->getValueCSV('Check Number', $row, '', $validity),
						'description'		=> $this->getValueCSV('Description', $row, '', $validity),
						'debit'				=> $this->getValueCSV('Income', $row, 'decimal', $validity),
						'credit'			=> $this->getValueCSV('Expense', $row, 'decimal', $validity)
					);
				}
				
				if ($invalid) {
					$error[] = 'Invalid Entry';
				}
						
				if ($validity) {
					$error[] = 'Invalid Entry';
				}

				$error = implode('. ', $error);

				if (empty($error)) {
					$data['periodfrom']	= $this->date->dateDbFormat($dates[0]);
					$data['periodto']	= $this->date->dateDbFormat($dates[1]);
					$data['endbalance']	= str_replace(',', '', $data['endbalance']);
					$recon_id = $this->bankrecon->saveBankReconCSV($values, $data);
				}
			}
		} else {
			$error = 'Invalid Import File. Please Use our Template for Uploading CSV';
		}

		$json = array(
			'success'	=> ($recon_id) ? true : false,
			'error'		=> $error,
			'invalid'	=> $invalid,
			'validity'	=> $validity,
			'redirect'	=> MODULE_URL . 'listing/' . base64_encode($recon_id)
		);
		return $json;
	}

	private function ajax_delete_current() {
		$recon_id	= base64_decode($this->input->post('recon_id'));

		$result = $this->bankrecon->cancelBankRecon($recon_id);

		$ajax = array(
			'success' => $result
		);
		return $ajax;
	}

	private function getValueCSV($field, $array, $checker = '', &$error = array(), $checker_function = '', &$error_function = array()) {
		$key	= array_search($field, $this->csv_header);
		$value	= (isset($array[$key])) ? trim($array[$key]) : '';
		if ($checker != '') {
			$checker_array = explode(' ', $checker);
			if (in_array('integer', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9]*$/', $value)) {
					$error['Integer'][$field] = 'Integer';
				}
			}
			if (in_array('decimal', $checker_array)) {
				$value = str_replace(',', '', $value);
				if ( ! preg_match('/^[0-9.]*$/', $value)) {
					$error['Decimal'][$field] = 'Decimal';
				}
			}
			if (in_array('required', $checker_array)) {
				if ($value == '') {
					$error['Required'][$field] = 'Required';
				}
			}
		}
		if ($checker_function && $value != '') {
			$result = $this->item_model->{$checker_function}($value);
			if ($result) {
				$value = $result[0]->ind;
			} else {
				$error_function[$field][] = $value;
				$value = '';
			}
		}
		return $value;
	}

}