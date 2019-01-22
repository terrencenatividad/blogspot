<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->purchase_model	= new purchase_return_model();
		$this->restrict 		= new purchase_restriction_model();
		// $this->financial_model  = $this->checkOutModel('financials_module/financial_model');
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'vendor',
			'source_no',
			'remarks',
			'warehouse',
			'amount',
			'taxamount',
			'reason'
		);
		$this->fields_header	= array(
			'header_warehouse'		=> 'warehouse',
			'header_amount'			=> 'amount',
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_discounttype'	=> 'discounttype',
			'header_discountamount'	=> 'discountamount',
			'header_netamount'		=> 'netamount',
			'header_taxamount'		=> 'taxamount',
			'header_wtaxcode'		=> 'wtaxcode',
			'header_wtaxamount'		=> 'wtaxamount',
			'header_wtaxrate'		=> 'wtaxrate',
		);
		$this->fields2			= array(
			'itemcode',
			'detailparticular',
			'linenum',
			'receiptqty',
			'receiptuom',
			'convuom',
			'convreceiptqty',
			'conversion',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount',
			'detail_amount' => 'amount',
			'convreceiptqty',
			'discounttype',
			'discountamount',
			'detail_warehouse' => 'warehouse',
			'po_qty',
			'item_ident_flag'
		);
		$this->clean_number		= array(
			'receiptqty'
		);
		$this->serial_fields	= array(
			// 'detail_warehouse'			=> 'warehouse',
			'voucherno',
			// 'source_no',
			'h_itemcode',
			'linenumber',
			'serialnumbers',
			'enginenumbers',
			'chassisnumbers',
			'receiptqty',
			'item_ident_flag'
		);
	}

	public function listing() {
		$this->view->title		= 'Purchase Return';
		$data['vendor_list']	= $this->purchase_model->getVendorList();
		$data['ui']				= $this->ui;
		$this->view->load('purchase_return/purchase_return_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Purchase Return';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_ret'] 	 	= false;
		$this->view->load('purchase_return/purchase_return', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Purchase Return';
		$this->fields[]				= 'stat';
		$data						= (array) $this->purchase_model->getPurchaseReturnById($this->fields, $voucherno);
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ui']					= $this->ui;
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data['header_values']		= json_encode($this->purchase_model->getPurchaseReturnById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->purchase_model->getPurchaseReturnDetails($this->fields2, $voucherno, false));
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_ret 				= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_ret'] 	 	= $restrict_ret;
		$this->view->load('purchase_return/purchase_return', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Purchase Return';
		$this->fields[]				= 'stat';
		$data						= (array) $this->purchase_model->getPurchaseReturnById($this->fields, $voucherno);
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['vendor_list']		= $this->purchase_model->getVendorList();
		$data['warehouse_list']		= $this->purchase_model->getWarehouseList();
		$data["item_list"]			= $this->purchase_model->getItemList();
		$data['header_values']		= json_encode($this->purchase_model->getPurchaseReturnById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->purchase_model->getPurchaseReturnDetails($this->fields2, $voucherno));
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_ret 				= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_ret'] 	 	= $restrict_ret;
		$this->view->load('purchase_return/purchase_return', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->purchase_model->getDocumentInfo($voucherno);
		$vendordetails		= $this->purchase_model->getVendorDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'			=> $this->date->dateFormat($documentinfo->documentdate),
			'PRTN #'		=> $voucherno,
			'PR #'			=> $documentinfo->referenceno,
			'SUP INV #'		=> $documentinfo->invoiceno
		);

		$print = new purchase_print_model();
		$print->setDocumentType('Purchase Return')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setVendorDetails($vendordetails)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 60, 20, 20, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', 'Price', 'Amount'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'R', 'R'))
				->setSummaryWidth(array('170', '30'));
		
		$documentcontent	= $this->purchase_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$total_amount = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_amount	+= $row->Amount;
			$row->Quantity	= number_format($row->Quantity);
			$row->Price		= number_format($row->Price, 2);
			$row->Amount	= number_format($row->Amount, 2);
			$print->addRow($row);
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));
				$total_amount = 0;
			}
		}
		$print->drawSummary(array('Total Amount' => number_format($total_amount, 2)));

		$print->drawPDF('Purchase Return - ' . $voucherno);
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

		$pagination	= $this->purchase_model->getPurchaseReturnPagination($search, $sort, $vendor, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;

			$restrict_ret 		=	$this->restrict->setButtonRestriction($transactiondate);

			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Returned' && $restrict_ret)
									->addDelete($row->stat == 'Returned' && $restrict_ret)
									->addPrint($row->stat == 'Returned')
									->addCheckbox($row->stat == 'Returned' && $restrict_ret)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->vendor . '</td>';
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
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('PRTN');
		$serials					= $this->input->post($this->serial_fields);
		$serials['voucherno']		= $data['source_no'];
		$results2					= $this->purchase_model->updateSerialData($serials);
		$result						= $this->purchase_model->savePurchaseReturn($data, $data2);
	
		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Purchase Return', $data['voucherno'])
									->setDetails($data['vendor'])
									->computeValues()
									->logChanges();

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
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);

		$this->inventory_model->prepareInventoryLog('Purchase Return', $voucherno)
								->preparePreviousValues();

		$result						= $this->purchase_model->updatePurchaseReturn($data, $data2, $voucherno);
		
		if ($result && $this->inventory_model) {
			$this->inventory_model->computeValues()
									->setDetails($data['vendor'])
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
			$result = $this->purchase_model->deletePurchaseReturn($delete_id);
		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$this->inventory_model->prepareInventoryLog('Purchase Return', $voucherno)
										->computeValues()
										->logChanges('Cancelled');
			}
			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_load_receipt_list() {
		$vendor		= $this->input->post('vendor');
		$search		= $this->input->post('search');
		$pagination	= $this->purchase_model->getPurchaseReceiptPagination($vendor, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '" data-si="' . $row->invoiceno . '">';
			$table .= '<td>' . $row->po_no . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_receipt_details() {
		$voucherno	= $this->input->post('voucherno');
		$details	= $this->purchase_model->getPurchaseReceiptDetails($voucherno);
		$header		= $this->purchase_model->getPurchaseReceiptHeader($this->fields_header, $voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		$total_amount	= $header->amount;
		$total_discount	= 0;
		$discountrate	= $header->discountrate;

		if ($header->discounttype == 'perc') {
			$discountrate	= $header->discountrate / 100;
		} else {
			$total_discount	= $header->discountamount;
			$discountrate	= $total_discount / $total_amount;
		}
		
		foreach ($details as $key => $row) {
			$discount = $row->unitprice * $discountrate;
			$details[$key]->unitprice = $row->unitprice - $discount;
			// $details[$key]->taxrate = 0;
			// $details[$key]->taxamount = 0;
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

	private function ajax_serial_list() {
		$search	= $this->input->post('search');
		$itemcode = $this->input->post('itemcode');
		$allserials = $this->input->post('allserials');
		$itemselected = $this->input->post('itemselected');
		$linenum = $this->input->post('linenumber');
		$id = $this->input->post('id');
		$task = $this->input->post('task');
		$item_ident = $this->input->post('item_ident');
		$checked_serials = $this->input->post('checked_serials');
		$voucherno = '';
		// if ($task=='ajax_edit') {
		$voucherno = $this->input->post('voucherno');
		// }
		// $curr = $this->delivery_model->getDRSerials($itemcode, $voucherno, $linenum);
		// if ($curr) {
		// 	$current_id = explode(",", $curr->serialnumbers);
		// 	$curr_serialnumbers = $curr->serialnumbers;
		// }
		// else {
			$current_id = [];
			$curr_serialnumbers = '';
		// }
		$array_id = explode(',', $id);
		$all_id = explode(',', $allserials);
		$checked_id = explode(',', $checked_serials);

		$pagination	= $this->purchase_model->getSerialList($itemcode, $search, $voucherno, $linenum);
		$table		= '';
		$counter = 0;
		foreach ($pagination->result as $key => $row) {
			if ($curr_serialnumbers == $id) {
				$checker = (in_array($row->id, $array_id) || in_array($row->id, $checked_id) || in_array($row->id, $current_id)) ? 'checked' : '';
			}
			else {
				$checker = (in_array($row->id, $array_id) || in_array($row->id, $checked_id)) ? 'checked' : '';
			}
			$hide_tr = ((in_array($row->id, $all_id) && !in_array($row->id, $array_id))) ? 'hidden' : '';
			$table .= '<tr class = "'.$hide_tr.'">';
			$table .= '<td class = "text-center"><input type = "checkbox" name = "check_id[]" id = "check_id" class = "check_id" value = "'.$row->id.'" '.$checker.'></td>';
			
			$has_serial 	=	substr($item_ident,0,1);
			$has_engine 	=	substr($item_ident,1,1);
			$has_chassis 	=	substr($item_ident,2,1);

			$hide_serial 	=	($has_serial == 0) 	? "hidden" 	:	"";
			$hide_engine 	=	($has_engine == 0) ? "hidden" 	:	"";
			$hide_chassis 	=	($has_chassis == 0) ? "hidden" 	:	"";
			
			$table .= '<td class = "'.$hide_serial.' serialno">' . $row->serialno . '</td>';
			$table .= '<td class = "'.$hide_engine.' engineno">' . $row->engineno . '</td>';
			$table .= '<td class = "'.$hide_chassis.' chassisno">' . $row->chassisno . '</td>';
			$table .= '</tr>';
			$counter++;
		}
		if ($counter == 0) {
			$table.= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

}