<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->delivery_model	= new delivery_receipt_model();
		$this->restrict 		= new sales_restriction_model();
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');
		$this->session			= new session();
		$this->fields 			= array(
			'voucherno',
			'transactiondate',
			'customer',
			's_address',
			'deliverydate',
			'source_no',
			'remarks',
			'warehouse',
			'amount',
			'netamount'
		);
		$this->fields_header	= array(
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_taxcode' 		=> 'taxcode', 
			'header_taxamount' 		=> 'taxamount',
			'header_discounttype'   => 'discounttype',
			'header_discountrate'   => 'discountrate',
			'header_discountamount' => 'discountamount'
		);
		$this->fields2			= array(
			'itemcode',			
			'detailparticular',
			'linenum',
			'issueqty',
			'issueuom',
			'unitprice',
			'discountrate',
			'discounttype',
			'discountamount',
			'detail_amount'			=> 'amount',
			'convissueqty',
			'convuom',
			'conversion',
			'detail_warehouse'		=> 'warehouse',
			'taxcode',
			'taxamount',
			'taxrate',
			'serialnumbers',
			'parentcode'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}

	public function listing() {
		$this->view->title		= 'Delivery Receipt';
		$this->view->addCSS(array(
				'jquery.fileupload.css'
			)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$data['customer_list']	= $this->delivery_model->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('delivery_receipt/delivery_receipt_list', $data);
	}

	public function create() {
		$this->view->title			= 'Create Delivery Receipt';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data["taxrate_list"]		= $this->delivery_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->delivery_model->getWTaxCodeList();
		$data["taxrates"]			= $this->delivery_model->getTaxRates();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList($data);		
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Delivery Receipt';
		$this->fields[]				= 'stat';

		$data						= (array) $this->delivery_model->getDeliveryReceiptById($this->fields, $voucherno);
		$transactiondate 			= $data['transactiondate'];
		$data['deliverydate']		= $this->date->dateFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList($data);
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data['header_values']		= json_encode($this->delivery_model->getDeliveryReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->delivery_model->getDeliveryReceiptDetails($this->fields2, $voucherno, false));
		$data["taxrate_list"]		= $this->delivery_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->delivery_model->getWTaxCodeList();
		$data["taxrates"]			= $this->delivery_model->getTaxRates();
		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= "&voucherno_ref=$voucherno";
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->restrict->getClosedDate();
		$data['close_date']			= $close_date;
		$restrict_dr 			 	= $this->restrict->setButtonRestriction($transactiondate);
		$data['restrict_dr'] 		= $restrict_dr;
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Delivery Receipt';
		$this->view->addCSS(array(
				'jquery.fileupload.css'
			)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$this->fields[]				= 'stat';
		$this->fields2[] 			= 'returnedqty';
		$data						= (array) $this->delivery_model->getDeliveryReceiptById($this->fields, $voucherno);
		if ($data['stat'] == 'Delivered' || $data['stat'] == 'With Invoice') {
			$getData = $this->delivery_model->getFile($voucherno);
			$data['filename'] 		= $getData->attachment_name;
			$data['filetype'] 		= $getData->attachment_type;
			$data['fileurl'] 		= $getData->attachment_url;
		}
		$transactiondate 			= $data['transactiondate'];
		$data['transactiondate']	= $this->date->dateFormat($transactiondate);
		$data['deliverydate']		= $this->date->dateFormat($data['deliverydate']);
		$data['ajax_task']			= '';
		$data['ui']					= $this->ui;
		$data['customer_list']		= $this->delivery_model->getCustomerList();
		$data["item_list"]			= $this->delivery_model->getItemList();
		$data['header_values']		= json_encode($this->delivery_model->getDeliveryReceiptById($this->fields_header, $voucherno));
		$data['voucher_details']	= json_encode($this->delivery_model->getDeliveryReceiptDetails($this->fields2, $voucherno));
		$data["taxrate_list"]		= $this->delivery_model->getTaxRateList();
		$data["wtaxcode_list"]		= $this->delivery_model->getWTaxCodeList();
		$data["taxrates"]			= $this->delivery_model->getTaxRates();
		$data['show_input']			= false;
		$data['warehouse_list']		= $this->delivery_model->getWarehouseList($data);		
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();;
		$data['restrict_dr'] 		= $this->restrict->setButtonRestriction($transactiondate);
		$this->view->load('delivery_receipt/delivery_receipt', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->delivery_model->getDocumentInfo($voucherno);
		$customerdetails	= $this->delivery_model->getCustomerDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'DR #'	=> $voucherno,
			'SO #'	=> $documentinfo->referenceno
		);

		$print = new sales_print_model();
		$print->setDocumentType('Delivery Receipt')
				->setFooterDetails(array('Approved By', 'Checked By'))
				->setCustomerDetails($customerdetails)
				->setShippingDetail($documentinfo->s_address)
				->setStatDetail($documentinfo->stat)
				->setDocumentDetails($documentdetails)
				// ->addTermsAndCondition()
				->addReceived();

		$print->setHeaderWidth(array(40, 100, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM'))
				->setRowAlign(array('L', 'L', 'R', 'L'))
				->setSummaryWidth(array('120', '50', '30'))
				->setSummaryAlign(array('J','R','R'));
		
		$documentcontent	= $this->delivery_model->getDocumentContent($voucherno);
		$detail_height = 37;

		$hasSerial = false;
		foreach($documentcontent as $key => $row) {
			if ($row->serialnumbers != ''){
				$hasSerial = true;
			}
		}

		if ($hasSerial) {
			$print->setHeaderWidth(array(30, 55, 20, 20, 25, 25, 25))
					->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
					->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', 'S/N', 'E/N', 'C/N',))
					->setRowAlign(array('L', 'L', 'R', 'L', 'L', 'L', 'L'))
					->setSummaryWidth(array('120', '50', '30'))
					->setSummaryAlign(array('J','R','R'));		
		}

		$notes = preg_replace('!\s+!', ' ', $documentinfo->notes);
		$total_quantity = 0;
		foreach ($documentcontent as $key => $row) {
			if ($key % $detail_height == 0) {
				$print->drawHeader();
			}

			$total_quantity	+= $row->Quantity;
			$row->Quantity	= number_format($row->Quantity, 0);
			if($hasSerial){
				$print->addRow(array($row->ItemCode, $row->Description, $row->Quantity, $row->UOM, '', '', ''));
				if ($row->serialnumbers != '') {
					$serials = explode(',', $row->serialnumbers);
					foreach($serials as $id) {
						$serial = $this->delivery_model->getSerialById($id);
						$sndisplay = $serial->serialno;
						$endisplay = $serial->engineno;
						$cndisplay = $serial->chassisno;
						$print->addRow(array('', '', '', '', $sndisplay, $endisplay, $cndisplay));
					}
				}
			} 
			else {
				$print->addRow($row);
			}
			if (($key + 1) % $detail_height == 0) {
				$print->drawSummary(array(array('Notes:', 'Total Qty', $total_quantity),
											array($notes, '', ''),
											array('', '', ''),
											array('', '', ''),
											array('', '', '')
				));
				$total_quantity = 0;
			}
		}
		$print->drawSummary(array(array('Notes:', 'Total Qty', $total_quantity),
											array($notes, '', ''),
											array('', '', ''),
											array('', '', ''),
											array('', '', '')
		));

		$print->drawPDF('Delivery Receipt - ' . $voucherno);
	}

	public function print_view($voucherno) {
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

		$pagination	= $this->delivery_model->getDeliveryReceiptPagination($search, $sort, $customer, $filter, $datefilter);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;
			$restrict_dr 		=	$this->restrict->setButtonRestriction($transactiondate);
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Prepared' && $restrict_dr)
									->addDelete($row->stat == 'Prepared' && $restrict_dr)
									->addPrint()
									->addOtherTask('Tag as Delivered', 'bookmark',($row->stat == 'Prepared' && $restrict_dr))
									->addCheckbox($row->stat == 'Prepared' && $restrict_dr)
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->source_no . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->deliverydate) . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Delivered':
				$color = 'success';
				break;
			case 'Cancelled':
				$color = 'warning';
				break;
			case 'With Invoice':
				$color = 'info';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	private function ajax_create() {
		$data						= array_merge($this->input->post($this->fields), $this->input->post($this->fields_header));
		$submit						= $this->input->post('submit');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$data['deliverydate']		= $this->date->dateDbFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('DR');
		$result						= $this->delivery_model->saveDeliveryReceipt($data, $data2);
		
		$this->delivery_model->createClearingEntries($data['voucherno']);

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Delivery Receipt', $data['voucherno'])
									->setDetails($data['customer'])
									->computeValues()
									->logChanges();

			$this->inventory_model->setReference($data['voucherno'])
									->setDetails($data['customer'])
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
		$data['deliverydate']		= $this->date->dateDbFormat($data['deliverydate']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);

		$this->inventory_model->prepareInventoryLog('Delivery Receipt', $voucherno)
								->preparePreviousValues();

		$result						= $this->delivery_model->updateDeliveryReceipt($data, $data2, $voucherno);

		$this->delivery_model->createClearingEntries($voucherno);

		$this->delivery_model->UpdateItemsSerialized($voucherno);

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
			$result = $this->delivery_model->deleteDeliveryReceipt($delete_id);
		}
		if ($result && $this->inventory_model) {
			foreach ($delete_id as $voucherno) {
				$this->inventory_model->prepareInventoryLog('Delivery Receipt', $voucherno)
										->computeValues()
										->logChanges('Cancelled');
				$this->delivery_model->createClearingEntries($voucherno);
			}
			$this->inventory_model->generateBalanceTable();
		}
		return array(
			'success' => $result
		);
	}

	private function ajax_upload_file()
	{
		$post_data 		= $this->input->post();
		$upload_handler	= new UploadHandler();
		$dr_voucherno 	= $post_data['dr_voucherno'];
		$upload_result 	= false;

		if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
			if(!isset($upload_handler->response['files'][0]->error)){
				$attachment_id = $this->delivery_model->getNextId("dr_attachment","attachment_id");
				foreach($upload_handler->response['files'] as $key => $row) {
					$post_data['attachment_id'] = $attachment_id;
					$post_data['attachment_name'] = $row->name;
					$post_data['attachment_type'] = $row->type;
					$post_data['attachment_url'] = $row->url;
				}
				$upload_result 	= $this->delivery_model->uploadAttachment($post_data);
			}else{
				$upload_result 	= false;
			}
		}
		if($upload_result){
			$result = $this->delivery_model->tagAsDelivered($dr_voucherno);
				$retrieve_details 	=	$this->delivery_model->getDeliveryReceiptById(array('voucherno','customer'), $dr_voucherno);
				$customer 			= 	isset($retrieve_details->customer) 	?	$retrieve_details->customer 	:	"";

				if ($result && $this->inventory_model) {
					$this->inventory_model->setReference($dr_voucherno)
										->setDetails($customer)
										->generateBalanceTable();
				}
		}
	}

	private function ajax_update_untagdelivered() {
		$id = $this->input->post('id');
		if ($id) {
			$result = $this->delivery_model->untagAsDelivered($id);
			if ($result && $this->inventory_model) {
				$this->inventory_model->generateBalanceTable();
			}
			
		}
	}

	private function ajax_load_ordered_list() {
		$customer		= $this->input->post('customer');
		$search			= $this->input->post('search');
		$pagination		= $this->delivery_model->getSalesOrderPagination($customer, $search);
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id="' . $row->voucherno . '" data-address="'.$row->s_address.'">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->remarks . '</td>';
			$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
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
		if ($task=='ajax_edit') {
			$voucherno = $this->input->post('voucherno');
		}
		$curr = $this->delivery_model->getDRSerials($itemcode, $voucherno, $linenum);
		if ($curr) {
			$current_id = explode(",", $curr->serialnumbers);
			$curr_serialnumbers = $curr->serialnumbers;
		}
		else {
			$current_id = [];
			$curr_serialnumbers = '';
		}
		$array_id = explode(',', $id);
		$all_id = explode(',', $allserials);
		$checked_id = explode(',', $checked_serials);

		$pagination	= $this->delivery_model->getSerialList($itemcode, $search, $voucherno, $linenum);
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
			$hide_chassis 	=	($has_chassis == 0)? "hidden" 	:	"";
			
			$table .= '<td class = "'.$hide_serial.'">' . $row->serialno . '</td>';
			$table .= '<td class = "'.$hide_engine.'">' . $row->engineno . '</td>';
			$table .= '<td class = "'.$hide_chassis.'">' . $row->chassisno . '</td>';
			$table .= '</tr>';
			$counter++;
		}
		if ($counter == 0) {
			$table.= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_ordered_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$details	= $this->delivery_model->getSalesOrderDetails($voucherno, $warehouse);
		$header		= $this->delivery_model->getSalesOrderHeader($this->fields_header, $voucherno);
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