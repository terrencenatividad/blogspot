<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->sr_model		= new salesreturn_model();
		$this->restrict 		= new sales_restriction_model();
		$this->financial_model  = $this->checkOutModel('financials_module/financial_model');
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'customer',
			'transactiondate',
			'remarks',
			'stat',
			'vat_sales',
			'vat_exempt',
			'vat_zerorated',
			'taxamount',
			'netamount',
			'discounttype',
			'discountamount'
		);
		$this->fields_header	= array(
			'header_warehouse'		=> 'warehouse',
			//'header_customer'		=> 'customer',
			'header_amount'			=> 'amount',
			//'header_fiscalyear'		=> 'fiscalyear',
			//'header_period'			=> 'period',
			'header_discounttype'	=> 'discounttype',
			'header_discountamount'	=> 'discountedamount',
			//'header_netamount'		=> 'netamount',
			'header_taxamount'		=> 'taxamount',
			//'header_wtaxcode'		=> 'wtaxcode',
			//'header_wtaxamount'		=> 'wtaxamount',
			//'header_wtaxrate'		=> 'wtaxrate',
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'warehouse',
			'linenum',
			'issueqty',
			'issueuom',
			'convuom',
			'convissueqty',
			'conversion',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount',
			'discounttype',
			'discountrate'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Sales Return List';
		$data['customer_list']	= $this->sr_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('salesreturn/salesreturn_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Sales Return';

		$fields = $this->fields;
		array_push($fields, 'source_no');

		$data						= $this->input->post($fields);

		$data['vat_sales'] 			= 0;
		$data['vat_exempt'] 		= 0;
		$data['vat_zerorated'] 		= 0;
		$data['total_sales'] 		= 0;
		$data['total_tax'] 			= 0;
		$data['total_amount'] 		= 0;
		$data['total_discount'] 	= 0;
		$data['total_netamount'] 	= 0;

		$data['reason'] 			= '';
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->sr_model->getCustomerList();
		$data['warehouse_list']		= $this->sr_model->getWarehouseList();
		$data["item_list"]			= $this->sr_model->getItemList();
		$data['taxrate_list'] 		= $this->sr_model->getTaxRateList();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_ri'] 		= false;
		$this->view->load('salesreturn/salesreturn', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Sales Return';
		$this->fields[]				= 'stat';
		$data['ui']					= $this->ui;
		$data						= (array) $this->sr_model->getSalesReturn($this->fields, $voucherno);
		$data['total_sales'] 		= $data['vat_sales'] + $data['vat_exempt'] + $data['vat_zerorated'];
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['customer_list']		= $this->sr_model->getCustomerList();
		$data['warehouse_list']		= $this->sr_model->getWarehouseList();
		$data["item_list"]			= $this->sr_model->getItemList();
		$data['taxrate_list'] 		= $this->sr_model->getTaxRateList();
		$data['header_values']		= json_encode($this->sr_model->getSalesReturn($this->fields, $voucherno));
		$data['voucher_details']	= json_encode($this->sr_model->getSalesReturnDetails($this->fields2, $voucherno, false));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_ri 			 	= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_ri'] 		= $restrict_ri;
		$this->view->load('salesreturn/salesreturn', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Return';
		$this->fields[]				= 'stat';
		$data						= (array) $this->sr_model->getReturnById($this->fields, $voucherno);
		$data['total_sales'] 		= $data['vat_sales'] + $data['vat_exempt'] + $data['vat_zerorated'];
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->sr_model->getCustomerList();
		$data['warehouse_list']		= $this->sr_model->getWarehouseList();
		$data["item_list"]			= $this->sr_model->getItemList();
		$data['taxrate_list'] 		= $this->sr_model->getTaxRateList();
		$data['header_values']		= json_encode($this->sr_model->getReturnById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->sr_model->getReturnDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_ri 			 	= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_ri'] 		= $restrict_ri;
		$this->view->load('salesreturn/salesreturn', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->sr_model->getDocumentInfo($voucherno);
		$customerdetails	= $this->sr_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'		=> $this->date->dateFormat($documentinfo->documentdate),
			'SRI #'	=> $voucherno,
			'SR #'		=> $documentinfo->referenceno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Sales Return Inventory')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->sr_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity	+= $row->Quantity;
			$row->Quantity	= number_format($row->Quantity);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Qty' => number_format($total_quantity)));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array('Total Qty' => number_format($total_quantity)));

		$print->drawPDF('Sales Return Inventory - ' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer		= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->sr_model->getSalesReturnPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;
			$restrict_ri 		=	$this->restrict->setButtonRestriction($transactiondate);
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Returned' && $restrict_ri)
									->addDelete($row->stat == 'Returned' && $restrict_ri)
									->addPrint($row->stat == 'Returned')
									->addCheckbox($row->stat == 'Returned' && $restrict_ri)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->source_no . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Returned':
				$color = 'success';
				break;
			case 'Scrapped':
				$color = 'danger';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
/*
	GET DATA FROM POST
*/
		$fields1 = array('source_no',
						'customer',
						'transactiondate', 
						'remarks', 
						'reason',
						'total_discount',
						'total_amount',
						'total_discount',
						'total_tax',
						'total_netamount'
					);
		$fields2 = $this->fields2;
		array_push($fields2, 'discountamount', 'defective', 'replacement', 'amount', 'netamount');


		$header				= $this->input->post($fields1);
		$details 			= $this->input->post($fields2);
/*
	END : GET DATA FROM POST
*/

/*
	PREPARE DATA FOR QUERY
*/
		$seq				= new seqcontrol();
		$voucherno			= $seq->getValue('SR');
		$transactiondate 	= $this->date->dateDbFormat($header['transactiondate']);
		$fiscalyear 		= date('Y', strtotime($header['transactiondate']));
		$period 			= date('n', strtotime($header['transactiondate']));

		$values = array(
						'voucherno' 		=> $voucherno,
						'source_no' 		=> $header['source_no'],
						'transactiondate' 	=> $transactiondate,
						'customer' 			=> $header['customer'],
						'warehouse' 		=> $details['warehouse'][0],
						'fiscalyear' 		=> $fiscalyear,
						'period' 			=> $period,
						'transtype' 		=> 'SR',
						'stat'     			=> 'Returned',
						'remarks'  			=> $header['remarks'],
						'reason' 			=> $header['reason'],
						'amount' 			=> $header['total_amount'],
						'discounttype' 		=> $details['discounttype'][0],
						'discountamount' 	=> $header['total_discount'],
						'netamount' 		=> $header['total_netamount'],
						'taxamount' 		=> $header['total_tax']
					);

		foreach ($details['itemcode'] as $key => $value) {
			$arr_voucherno[] 	= $voucherno; 
			$transtype[] 		= 'SR';
			$stat 	 			= 'Returned';
		}
		$values2 = array(
					'voucherno' 		=> $arr_voucherno,
					'transtype' 		=> $transtype,
					'linenum' 			=> $details['linenum'],
					'itemcode' 			=> $details['itemcode'],
					'warehouse' 		=> $details['warehouse'],
					'detailparticular' 	=> $details['detailparticular'],
					'defective' 		=> $details['defective'],
					'replacement' 		=> $details['replacement'],
					'issueuom' 			=> $details['issueuom'],
					'issueqty' 			=> $details['issueqty'],
					'convissueqty' 		=> $details['convissueqty'],
					'convuom' 			=> $details['convuom'],
					'conversion' 		=> $details['conversion'],
					'unitprice'     	=> $details['unitprice'],
					'discounttype' 		=> $details['discounttype'],
					'discountrate'		=> $details['discountrate'],
					'discountamount' 	=> $details['discountamount'],
					'taxcode'  			=> $details['taxcode'],
					'taxrate' 			=> $details['taxrate'],
					'taxamount' 		=> $details['taxamount'],
					'amount' 			=> $details['amount'],
					'netamount' 		=> $details['netamount'],
					'stat' 				=> $stat
				);

/*
	END : PREPARE DATA FOR QUERY
*/

/*
	RUN DATABASE OPERATIONS
*/
		$result		= $this->sr_model->saveSalesReturn($values, $values2);
		
		if ($result) {

			if (substr($header['source_no'], 0, 2) == 'SI') {
				$this->generate_receivable('yes',$voucherno);
			}

			if ($this->inventory_model) {
				$this->inventory_model->prepareInventoryLog('Sales Return', $voucherno)
										->setDetails($values['customer'])
										->computeValues()
										->logChanges();

				$this->inventory_model->setReference($voucherno)
										->setDetails($values['customer'])
										->generateBalanceTable();
			}
		}
/*
	END : RUN DATABASE OPERATIONS
*/

		$redirect_url = MODULE_URL;
		// if ($submit == 'save_new') {
		// 	$redirect_url = MODULE_URL . 'create';
		// } else if ($submit == 'save_preview') {
		// 	$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		// }
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);

		$this->inventory_model->prepareInventoryLog('Sales Return', $voucherno)
								->preparePreviousValues();

		$result						= $this->sr_model->updateReturn($data, $data2, $voucherno);
		
		if ($result && $this->inventory_model) {
			$this->inventory_model->computeValues()
									->setDetails($data['customer'])
									->logChanges();

			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result = $this->sr_model->deleteReturn($delete_id);
		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$this->inventory_model->prepareInventoryLog('Sales Return', $voucherno)
										->computeValues()
										->logChanges('Cancelled');
			}
			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_invoice_list() {
		$source 	= $this->input->post('source');
		$pagination	= $this->sr_model->getSourcePagination($source);
		
		$table		= '';
		
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->notes . '</td>';
			$table .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
			$table .= '</tr>';
		}
		
		$pagination->table = $table;
		
		return $pagination;
	}

	private function ajax_load_invoice_details() {
		$voucherno	= $this->input->post('voucherno');
		$result  	= $this->sr_model->getSource($voucherno);

		$table		= '';
		$success	= true;

		if (empty($result)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		
		return array(
			'table'		=> $table,
			'details'	=> $result['details'],
			'header'	=> $result['header'],
			'success'	=> $success
		);
	}

	private function getItemDetails() {
		$data = array();
 		$temp = $this->input->post($this->fields2);
		foreach ($temp['issueqty'] as $key => $quantity) {
			if ($quantity < 1) {
				foreach ($this->fields2 as $field) {
					if (is_array($temp[$field])) {
						unset($temp[$field][$key]);
					}
				}
			}
		}
		foreach ($this->fields2 as $field) {
			if (is_array($temp[$field])) {
				$data[$field] = array_values($temp[$field]);
			} else {
				$data[$field] = $temp[$field];
			}
		}
		return $data;
	}

}