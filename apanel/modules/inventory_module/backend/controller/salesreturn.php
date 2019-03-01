<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->sr_model		= new salesreturn_model();
		$this->restrict 		= new sales_restriction_model();
		$this->financial_model  = $this->checkoutModel('financials_module/financial_model');
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
			'serialnumbers',
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
		$this->view->title	= 'Edit Sales Return';

		$fields1 = array('source_no',
						'customer',
						'transactiondate', 
						'remarks', 
						'reason',
						'amount total_amount',
						'discountamount total_discount',
						'taxamount total_tax',
						'netamount total_netamount',
						'vat_sales',
						'vat_exempt',
						'vat_zerorated',
						'stat'
					);
		$fields2			= array(
			'main.itemcode',
			'main.detailparticular',
			'main.warehouse',
			'main.linenum',
			'main.issueqty',
			'tbl.issueqty srcqty',
			'main.issueuom',
			'main.convuom',
			'main.convissueqty',
			'main.conversion',
			'main.serialnumbers selectedserial',
			'tbl.serialnumbers',
			'main.unitprice',
			'main.taxcode',
			'main.taxrate',
			'main.taxamount',
			'main.discounttype',
			'tbl.discountrate',
			'tbl.discountamount',
			'main.defective', 
			'main.replacement', 
			'main.amount', 
			'main.netamount'
		);
		

		$data				= (array)$this->sr_model->getSRheader($voucherno, $fields1);
		$details 			= $this->sr_model->getSRdetails($voucherno, $fields2, $data['source_no']);
		
		$data['ui']					= $this->ui;
		
		$data['voucherno'] 			= $voucherno;
		$data['total_sales'] 		= $data['vat_sales'] + $data['vat_exempt'] + $data['vat_zerorated'];
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['customer_list']		= $this->sr_model->getCustomerList();
		$data['warehouse_list']		= $this->sr_model->getWarehouseList();
		$data["item_list"]			= $this->sr_model->getItemList();
		$data['taxrate_list'] 		= $this->sr_model->getTaxRateList();
		$data['header_values']		= json_encode($data);
		$data['voucher_details']	= json_encode($details);
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
		$this->view->title	= 'View Sales Return';

		$fields1 = array('source_no',
						'customer',
						'transactiondate', 
						'remarks', 
						'reason',
						'amount total_amount',
						'discountamount total_discount',
						'taxamount total_tax',
						'netamount total_netamount',
						'vat_sales',
						'vat_exempt',
						'vat_zerorated',
						'stat'
					);
		$fields2			= array(
			'main.itemcode',
			'main.detailparticular',
			'main.warehouse',
			'main.linenum',
			'main.issueqty',
			'tbl.issueqty srcqty',
			'main.issueuom',
			'main.convuom',
			'main.convissueqty',
			'main.conversion',
			'main.serialnumbers selectedserial',
			'tbl.serialnumbers',
			'main.unitprice',
			'main.taxcode',
			'main.taxrate',
			'main.taxamount',
			'main.discounttype',
			'main.discountamount',
			'main.defective', 
			'main.replacement', 
			'main.amount', 
			'main.netamount'
		);
		

		$data				= (array)$this->sr_model->getSRheader($voucherno, $fields1);
		$details 			= $this->sr_model->getSRdetails($voucherno, $fields2, $data['source_no']);
		
		$data['ui']					= $this->ui;
		
		$data['voucherno'] 			= $voucherno;
		$data['total_sales'] 		= $data['vat_sales'] + $data['vat_exempt'] + $data['vat_zerorated'];
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['customer_list']		= $this->sr_model->getCustomerList();
		$data['warehouse_list']		= $this->sr_model->getWarehouseList();
		$data["item_list"]			= $this->sr_model->getItemList();
		$data['taxrate_list'] 		= $this->sr_model->getTaxRateList();
		$data['header_values']		= json_encode($data);
		$data['voucher_details']	= json_encode($details);
		$data['ajax_task']			= 'view';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_ri 			 	= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_ri'] 		= $restrict_ri;
		$this->view->load('salesreturn/salesreturn', $data);
	}

	public function print_preview($voucherno) {
		$result				= $this->sr_model->getSRPrint($voucherno);
		$documentinfo 		= $result['header'];
		$documentcontent 	= $result['details'];


		$documentdetails	= array(
			'Date'			=> $this->date->dateFormat($documentinfo->transactiondate),
			'SR #'			=> $voucherno,
			'Reference#'	=> $documentinfo->source_no
		);
		$notes 				= $documentinfo->remarks;

		$print = new print_salesreturn_model();

		$print->setDocumentType('Sales Return')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($documentinfo)
				->setDocumentDetails($documentdetails)
				->setStatDetail($documentinfo->stat)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(25, 45, 20, 25, 10, 15, 20, 20, 20))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description','Defective', 'Replacement', 'Qty', 'UOM', 'Price', 'Discount', 'Tax'))
				->setRowAlign(array('L', 'L', 'L', 'L', 'R', 'L', 'R', 'R', 'R'))
				->setSummaryWidth(array('120', '50', '30'))
				->setSummaryAlign(array('J','R','R'));
		
		$detail_height = 37;

		$vat_sales		= 0;
		$vat_exempt		= 0;
		$vat_zerorated	= 0;
		$discount		= 0;
		$tax			= 0;
		$total_amount 	= 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			if($row->tax > 0.00 || $row->tax > 0 )	{
				$vat_sales += $row->amount;
			}
			else {
				if ($row->taxcode == '' || $row->taxcode == 'none' || $row->taxcode == 'ES') {
					$vat_exempt += $row->amount;
				}
				else {
					$vat_zerorated += $row->amount;
				}
			}
			unset($row->taxcode);
			$tax			+= $row->tax;
			$discount 	    += $row->discount;
			$row->qty		= number_format($row->qty);
			$row->price		= number_format($row->price, 2);
			$row->amount	= number_format($row->amount, 2);
			$row->tax		= number_format($row->tax, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$total_sales  = $vat_sales + $vat_exempt + $vat_zerorated;
				$total_amount = $total_sales + $tax;
				$summary = array(array('Notes:', 'VATable Sales', number_format($vat_sales, 2)),
					array($notes, 'VAT-Exempt Sales', number_format($vat_exempt, 2)),
					array('','VAT Zero Rated Sales'	, number_format($vat_zerorated, 2)),
					array('','Total Sales'		, number_format($total_sales, 2)),
					array('','Tax'				, number_format($tax, 2)),
					array('','Total Amount'		, number_format($total_amount, 2)),
					array('','', ''),
					array('','Discount'			, number_format($discount, 2))
				);
				$print->drawSummary($summary);
				// $print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));
				$vatable_sales	= 0;
				$vat_exempt		= 0;
				$vat_zerorated	= 0;
				$discount		= 0;
				$tax			= 0;
				$total_amount	= 0;
			}
		}
		$total_sales  = $vat_sales + $vat_exempt + $vat_zerorated;
		$total_amount = $total_sales + $tax;
		$summary = array(array('Notes:', 'VATable Sales', number_format($vat_sales, 2)),
					array($notes,'VAT-Exempt Sales', number_format($vat_exempt, 2)),
					array('','VAT Zero Rated Sales'	, number_format($vat_zerorated, 2)),
					array('','Total Sales'		, number_format($total_sales, 2)),
					array('','Tax'				, number_format($tax, 2)),
					array('','Total Amount'		, number_format($total_amount, 2)),
					array('','', ''),
					array('','Discount'			, number_format($discount, 2))
		);
		$print->drawSummary($summary);

		$print->drawPDF('Sales Return - ' . $voucherno);
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
		$customer	= $data['customer'];
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
									->addPrint()
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
				$color = 'warning';
				break;
			case 'Cancelled':
				$color = 'danger';
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
						'total_netamount',
						'vat_sales',
						'vat_exempt',
						'vat_zerorated'
					);
		$fields2 = array('itemcode',
						'detailparticular',
						'warehouse',
						'linenum',
						'issueqty',
						'issueuom',
						'convuom',
						'convissueqty',
						'conversion',
						'serialnumbers',
						'unitprice',
						'taxcode',
						'taxrate',
						'taxamount',
						'discounttype',
						'discountrate',
						'discountamount', 
						'defective', 
						'replacement', 
						'amount', 
						'netamount'
					);

		$header				= $this->input->post($fields1);
		$details 			= $this->input->post($fields2);
		$submit 			= $this->input->post('submit');
/*
	END : GET DATA FROM POST
*/

/*
	PREPARE DATA FOR QUERY
*/
		$seq 				= new seqcontrol();
		$voucherno 			= $seq->getValue('SR');
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
						'taxamount' 		=> $header['total_tax'],
						'vat_sales' 		=> str_replace(',', '', $header['vat_sales']),
						'vat_exempt' 		=> str_replace(',', '', $header['vat_exempt']),
						'vat_zerorated' 	=> str_replace(',', '', $header['vat_zerorated'])
					);

		foreach ($details['itemcode'] as $key => $value) {
			$arr_voucherno[] 	= $voucherno; 
			$arr_transtype[] 		= 'SR';
			$arr_stat[] 	 		= 'Returned';
		}

		$values2 = array(
					'voucherno' 		=> $arr_voucherno,
					'transtype' 		=> $arr_transtype,
					'linenum' 			=> $details['linenum'],
					'itemcode' 			=> $details['itemcode'],
					'warehouse' 		=> $details['warehouse'],
					'detailparticular' 	=> $details['detailparticular'],
					'defective' 		=> $details['defective'],
					'replacement' 		=> $details['replacement'],
					'serialnumbers' 	=> $details['serialnumbers'],
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
					'stat' 				=> $arr_stat
				);

		$sourcetype = 'DR';

		if (substr($header['source_no'], 0, 2) == 'SI') {
			$sourcetype = 'SI';
		}
/*
	END : PREPARE DATA FOR QUERY
*/

/*
	RUN DATABASE OPERATIONS
*/
		$result		= $this->sr_model->saveSalesReturn($values, $values2);

		if ($result['details']) {
			

			$jvresult = $this->sr_model->createClearingEntries($voucherno, $sourcetype);
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
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result,
			'jvgenerate' => $jvresult
		);
	}

	private function ajax_edit() {
		/*
	GET DATA FROM POST
*/
		$fields1 = array('voucherno',
						'source_no',
						'customer',
						'transactiondate', 
						'remarks', 
						'reason',
						'total_discount',
						'total_amount',
						'total_discount',
						'total_tax',
						'total_netamount',
						'vat_sales',
						'vat_exempt',
						'vat_zerorated'
					);
		$fields2 = array('itemcode',
						'detailparticular',
						'warehouse',
						'linenum',
						'issueqty',
						'issueuom',
						'convuom',
						'convissueqty',
						'conversion',
						'serialnumbers',
						'allserial',
						'unitprice',
						'taxcode',
						'taxrate',
						'taxamount',
						'discounttype',
						'discountrate',
						'discountamount', 
						'defective', 
						'replacement', 
						'amount', 
						'netamount'
					);

		$header				= $this->input->post($fields1);
		$details 			= $this->input->post($fields2);
		$voucherno			= $this->input->post('voucherno');
		$submit 			= $this->input->post('submit');
/*
	END : GET DATA FROM POST
*/

/*
	PREPARE DATA FOR QUERY
*/
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
						'taxamount' 		=> $header['total_tax'],
						'vat_sales' 		=> str_replace(',', '', $header['vat_sales']),
						'vat_exempt' 		=> str_replace(',', '', $header['vat_exempt']),
						'vat_zerorated' 	=> str_replace(',', '', $header['vat_zerorated'])
					);
		
		foreach ($details['itemcode'] as $key => $value) {
			$arr_voucherno[] 	= $voucherno; 
			$arr_transtype[] 		= 'SR';
			$arr_stat[] 	 		= 'Returned';
		}

		$values2 = array(
					'voucherno' 		=> $arr_voucherno,
					'transtype' 		=> $arr_transtype,
					'linenum' 			=> $details['linenum'],
					'itemcode' 			=> $details['itemcode'],
					'warehouse' 		=> $details['warehouse'],
					'detailparticular' 	=> $details['detailparticular'],
					'defective' 		=> $details['defective'],
					'replacement' 		=> $details['replacement'],
					'serialnumbers' 	=> $details['serialnumbers'],
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
					'stat' 				=> $arr_stat
				);
		$sourcetype = 'DR';

		if (substr($header['source_no'], 0, 2) == 'SI') {
			$sourcetype = 'SI';
		}

/*
	END : PREPARE DATA FOR QUERY
*/

/*
	RUN DATABASE OPERATIONS
*/
		if ($this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Sales Return', $voucherno)
								->preparePreviousValues();
		}

		$result	= $this->sr_model->saveSalesReturn($values, $values2);


		if ($result['details']) {
			$jvresult = $this->sr_model->createClearingEntries($voucherno, $sourcetype);
			if ($this->inventory_model) {
				
				$this->inventory_model->computeValues()
									->setDetails($values['customer'])
									->logChanges();

				$this->inventory_model->generateBalanceTable();
			}
		}
/*
	END : RUN DATABASE OPERATIONS
*/

		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result,
			'jvgenerate' => $jvresult
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result 			= $this->sr_model->deleteSalesReturn($delete_id);
			$result_serial 		= $this->sr_model->revertItemSerialized($delete_id);
			$result_dr 			= $this->sr_model->revertDRchanges($delete_id);

		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$result_jv[] = $this->sr_model->createClearingEntries($voucherno);

				$this->inventory_model->prepareInventoryLog('Sales Return', $voucherno)
										->computeValues()
										->logChanges('Cancelled');
			}
			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'success' 			=> $result,
			'revert serialized' => $result_serial,
			'revert drtable' 	=> $result_dr
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
		foreach ($result['details'] as $key => $row) {
			$result['details'][$key]->issueqty = $row->maxqty;
		}
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

	private function getSerialItemList() {
		$serialids 	= explode(',', $this->input->post('serials'));
		$linenum 	= $this->input->post('linenum');
		$show_input = $this->input->post('showinput');
		$sourceno 	= $this->input->post('sourceno');
		$task 		= $this->input->post('task');
		$voucherno 	= $this->input->post('voucherno');
		$serials 	= $this->sr_model->getSerialItemList($serialids);
		$taggedserials = $this->sr_model->getTaggedSerial($voucherno, $sourceno, $linenum);
		$table 		= '';

		foreach ($serials as $key => $row) {
			$state = '';
			if (!empty($taggedserials)) {
				foreach ($taggedserials as $key => $value) {
					if ($row->id == $value) {
						$state = 'disabled';
					}
				}
			}
			$table .= '<tr>';
			if ($show_input) {
				$table .= '<td><input type="checkbox" class="chkserial" data-serialid="' . $row->id . '"' . $state . '></td>';
			}
			$table .= '<td>' . $row->item . '</td>';
			$table .= '<td>' . $row->serialno . '</td>';
			$table .= '<td>' . $row->chassisno . '</td>';
			$table .= '<td>' . $row->engineno . '</td>';
			$table .= '</tr>';
		}
		$table .= '<script>checkSelectedSerial($(".chkserial"));</script>';
		return $result = array('linenum' => $linenum, 'table' => $table);;
	}

}