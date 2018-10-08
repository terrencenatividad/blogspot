<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->purchase_model	= new purchase_receipt_model();
		$this->restrict 		= new purchase_restriction_model();
		$this->financial_model	= $this->checkOutModel('financials_module/financial_model');
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields			= array(
			'voucherno',
			'transactiondate',
			'vendor',
			'source_no',
			'invoiceno',
			'remarks',
			'warehouse',
			'amount',
			'discounttype',
			'discountrate',
			'discountamount',
			'netamount',
			'taxamount',
			'wtaxcode',
			'wtaxamount',
			'wtaxrate',
		);
		$this->fields_header	= array(
			// 'header_amount'			=> 'amount',
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			// 'header_discounttype'	=> 'discounttype',
			// 'header_discountamount'	=> 'discountamount',
			// 'header_netamount'		=> 'netamount',
			// 'header_taxamount'		=> 'taxamount',
			// 'header_wtaxcode'		=> 'wtaxcode',
			// 'header_wtaxamount'		=> 'wtaxamount',
			// 'header_wtaxrate'		=> 'wtaxrate',
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'receiptqty',
			'u.uomdesc receiptuom',
			'unitprice',
			'taxcode',
			'taxrate',
			'detail_taxamount'			=> 'taxamount',
			'detail_amount'				=> 'amount',
			'convreceiptqty',
			'convuom',
			'conversion',
			'discounttype',
			'detail_discountamount'		=> 'discountamount',
			'detail_withholdingamount'	=> 'withholdingamount',
			'detail_warehouse'			=> 'warehouse'
		);
		$this->clean_number		= array(
			'receiptqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Purchase Receipt List';
		$data['vendor_list']	= $this->purchase_model->getVendorList();
		$data['ui']				= $this->ui;
		$this->view->load('purchase_receipt/purchase_receipt_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Purchase Receipt';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data["taxrate_list"]		= $this->purchase_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->purchase_model->getWTaxCodeList();
		$data["taxrates"]			= $this->purchase_model->getTaxRates();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList($data);				
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['restrict_pr']		= false;
		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Purchase Receipt';
		$this->fields[]				= 'stat';
		$data						= (array) $this->purchase_model->getPurchaseReceiptById($this->fields, $voucherno);
		$transactiondate			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ui']					= $this->ui;
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data['header_values']		= json_encode($this->purchase_model->getPurchaseReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->purchase_model->getPurchaseReceiptDetails($this->fields2, $voucherno, false));
		$data["taxrate_list"]		= $this->purchase_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->purchase_model->getWTaxCodeList();
		$data["taxrates"]			= $this->purchase_model->getTaxRates();
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList($data);		
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['restrict_pr']		= $this->restrict->setButtonRestriction($transactiondate);
		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Purchase Receipt';
		$this->fields[]				= 'stat';
		$data						= (array) $this->purchase_model->getPurchaseReceiptById($this->fields, $voucherno);
		$transactiondate			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ui']					= $this->ui;
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data['header_values']		= json_encode($this->purchase_model->getPurchaseReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->purchase_model->getPurchaseReceiptDetails($this->fields2, $voucherno));
		$data["taxrate_list"]		= $this->purchase_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->purchase_model->getWTaxCodeList();
		$data["taxrates"]			= $this->purchase_model->getTaxRates();
		$data['ajax_task']			= '';
		$data['show_input']			= false;
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList($data);				
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['restrict_pr']		= $this->restrict->setButtonRestriction($transactiondate);
		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->purchase_model->getDocumentInfo($voucherno);
		$vendordetails		= $this->purchase_model->getVendorDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'PR #'	=> $voucherno,
			'PO #'	=> $documentinfo->referenceno,
			''		=> '',
			'TERMS'	=> $vendordetails->terms
		);

		$print = new purchase_print_model();
		$print->setDocumentType('Purchase Receipt')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setVendorDetails($vendordetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(30, 50, 20, 20, 30, 20, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Quantity', 'UOM', 'Price', 'Tax', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->purchase_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$vatable_sales	= 0;
		$vat_exempt		= 0;
		$discount		= 0;
		$tax			= 0;
		$wtax			= 0;
		$total_amount	= 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}
			
			if ($documentinfo->discountrate == 0 && $documentinfo->discount) {
				$documentinfo->discountrate = $documentinfo->discount / ($documentinfo->amount + $documentinfo->vat) * 100;
			}

			$vatable_sales	+= ($row->taxrate) ? $row->Amount : 0;
			$vat_exempt		+= ($row->taxrate) ? 0 : $row->Amount;
			$discount		+= number_format(($row->Amount + $row->Tax) * $documentinfo->discountrate / 100, 2, '.', '');
			$tax			+= $row->Tax;
			$wtax			+= number_format($row->Amount * $documentinfo->wtaxrate, 2, '.', '');
			$row->Quantity	= number_format($row->Quantity);
			$row->Price		= number_format($row->Price, 2);
			$row->Tax		= number_format($row->Tax, 2);
			$row->Amount	= number_format($row->Amount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$total_amount = $vatable_sales + $vat_exempt - $discount + $tax - $wtax;
				$summary = array(
					'VATable Sales'		=> number_format($vatable_sales, 2),
					'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
					'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
					'Discount'			=> number_format($discount, 2),
					'Tax'				=> number_format($tax, 2),
					'WTax'				=> number_format($wtax, 2),
					'Total Amount'		=> number_format($total_amount, 2)
				);
				$print->drawSummary($summary);
				$vatable_sales	= 0;
				$vat_exempt		= 0;
				$discount		= 0;
				$tax			= 0;
				$wtax			= 0;
				$total_amount	= 0;
			}
		}
		$total_amount = $vatable_sales + $vat_exempt - $discount + $tax - $wtax;
		$summary = array(
			'VATable Sales'		=> number_format($vatable_sales, 2),
			'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
			'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
			'Discount'			=> number_format($discount, 2),
			'Tax'				=> number_format($tax, 2),
			'WTax'				=> number_format($wtax, 2),
			'Total Amount'		=> number_format($total_amount, 2)
		);
		$print->drawSummary($summary);

		$print->drawPDF('Purchase Receipt - ' . $voucherno);
	}
	
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'vendor', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$vendor		= $data['vendor'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->purchase_model->getPurchaseReceiptPagination($search, $sort, $vendor, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;

			$restrict_pr 		=	$this->restrict->setButtonRestriction($transactiondate);

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Received' && $restrict_pr)
									// ->addDelete($row->stat == 'Received' && $restrict_pr)
									->addPrint()
									->addCheckbox($row->stat == 'Received' && $restrict_pr)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->invoiceno . '</td>';
			$table .= '<td>' . $row->vendor . '</td>';
			$table .= '<td>' . $row->source_no . '</td>';
			// $table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Received':
				$color = 'success';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		$submit						= $this->input->post('submit');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']		= $this->date->getYear($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('PR');
		$result						= $this->purchase_model->savePurchaseReceipt($data, $data2);
		if ($result && $this->financial_model) {
			$this->financial_model->generateAP($data['voucherno']);
		}
		if ($result && $this->inventory_model) {
			$this->inventory_model->setReference($data['voucherno'])
									->setDetails($data['vendor'])
									->generateBalanceTable();
		}
		$redirect_url = MODULE_URL;
		if ($submit == 'save_new') {
			$redirect_url = MODULE_URL . 'create';
		} else if ($submit == 'save_preview') {
			$redirect_url = MODULE_URL . 'view/' . $data['voucherno'];
		}
		return array(
			'redirect'	=> $redirect_url,
			'success'	=> $result
		);
	}

	private function ajax_edit() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']		= $this->date->getYear($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$result						= $this->purchase_model->updatePurchaseReceipt($data, $data2, $voucherno);
		if ($result && $this->financial_model) {
			$this->financial_model->generateAP($voucherno);
		}
		if ($result && $this->inventory_model) {
			$this->inventory_model->setReference($voucherno)
									->setDetails($data['vendor'])
									->generateBalanceTable();
		}
		return array(
			'redirect'	=> MODULE_URL,
			'success'	=> $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result = $this->purchase_model->deletePurchaseReceipt($delete_id);
		}
		if ($result && $this->financial_model) {
			foreach ($delete_id as $voucherno) {
				$this->financial_model->cancelAP($voucherno);
			}
		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$this->inventory_model->generateBalanceTable();
			}
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_purchase_list() {
		$vendor		= $this->input->post('vendor');
		$search		= $this->input->post('search');
		$pagination	= $this->purchase_model->getPurchaseOrderPagination($vendor, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_purchase_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$details	= $this->purchase_model->getPurchaseOrderDetails($voucherno, $warehouse);
		$header		= $this->purchase_model->getPurchaseOrderHeader($this->fields_header, $voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		return array(
			'table'		=> $table,
			'details'	=> $details,
			'header'	=> $header,
			'success'	=> $success
		);
	}

	private function getItemDetails() {
		$data = array();
 		$temp = $this->input->post($this->fields2);
		foreach ($temp['receiptqty'] as $key => $quantity) {
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