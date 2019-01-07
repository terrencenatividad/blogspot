<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->job_order        = new job_order_model();
		$this->parts_and_service= new parts_and_service_model();
		$this->inventory_model	= $this->checkoutModel('inventory_module/inventory_model');		
		$this->session			= new session();
		$this->fields 			= array(
			'job_order_no',
			'transactiondate',
            'customer',
			'reference',
            'service_quotation',
			'po_number',
			'notes',
			'stat'
		);
		$this->fields_header	= array(
			'header_fiscalyear'		=> 'fiscalyear',
			'header_period'			=> 'period',
			'header_discounttype'   => 'discounttype'
		);
		$this->fields2			= array(
			'job_order_no',
			'h_itemcode'		=> 'itemcode',
			'detailparticular',
			'linenum',
			'h_warehouse'		=> 'warehouse',
			'qty' 				=> 'quantity',
			'h_uom'				=> 'uom',
			'isbundle',
			'parentline',
			'parentcode'
		);
		$this->fields3			= array(
			'job_order_no',
			'jod.itemcode',
			'detailparticular',
			'linenum',
			'warehouse',
			'jod.qty' => 'jod.quantity',
			'jod.uom',
			'isbundle',
			'parentcode',
			'parentline',
			'item_ident_flag',
			'bom.quantity bomqty'
		);
		$this->fields4			= array(
			'job_release_no',
			'job_order_no',
			'h_transactiondate' =>'transactiondate',
			'h_itemcode' 		=> 'itemcode',
			'linenum',
			'h_detailparticular'=> 'detailparticulars',
			'h_warehouse'		=> 'warehouse',
			'quantity',
			'h_uom'  => 'unit',
			'serialnumbers',
			'stat'
		);
		$this->fieldsattachment	= array(
			//'attachment_id',
			'attachment_url',
			'attachment_name',
			'attachment_type',
			//'reference',
			//'stat'
		);
		$this->clean_number		= array(
			'issueqty'
		);
	}
	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}
	public function listing() {
		$this->view->title		= 'Job Order';
		$data['customer_list']	= $this->job_order->getCustomerList();
		$data['ui']				= $this->ui;
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
		$this->view->load('job_order/job_order_list', $data);
	}
	public function create() {
		$this->view->title			= 'Add Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array());
		$data['voucher_details']	= json_encode(array());
		$data['t_vatable_sales']	= 0;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 0;
		$data['t_vat']				= 0;
		$data['t_amount']			= 0;
		$data['t_discount']			= 0;
		$data['ajax_task']			= 'ajax_create';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;
		$this->view->load('job_order/job_order', $data);
	}
	public function edit($id) {
		$this->view->title			= 'Edit Job Order';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);

		$data						= (array) $this->job_order->getJOByID($this->fields, $id);

		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));

		$data['voucher_details']	= json_encode($this->job_order->getJobOrderDetails($this->fields2, $id));
		//var_dump($data['voucher_details']);

		$data['ajax_task']			= 'ajax_edit';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= true;
		
		$this->view->load('job_order/job_order', $data);
	}
	public function view($id) {
		$this->view->title			= 'View Job Order';
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
		$data						= $this->input->post($this->fields);
		$data						= (array) $this->job_order->getJOByID($this->fields, $id);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();

		$this->fields2['isbundle'] = " IF(isbundle = 'yes','1','0') isbundle";
		$voucherdetails 			= json_encode($this->job_order->getJobOrderDetails($this->fields2, $id));
		$data['voucher_details']	= $voucherdetails;
		// var_dump($voucherdetails); 	
		$data['header_values']		= json_encode(array(
			''
		));		
		$data['ajax_task']			= 'ajax_view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= true;

		$data['job_order_no']			= $id;
		if ($data['stat'] == 'completed') {
			$getData 				= $this->job_order->selectAttachment($id,$this->fieldsattachment);
			$data['filename'] 		= $getData->attachment_name;
			$data['filetype'] 		= $getData->attachment_type;
			$data['fileurl'] 		= $getData->attachment_url;
		}
		$this->view->load('job_order/job_order', $data);
	}
	public function payment($id) {
		$this->view->title			= 'Job Order - Issue Parts';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data						= (array) $this->job_order->getJOByID($this->fields, $id);
		$data['ui']					= $this->ui;
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->job_order->getOption('job_type','code');
		$data['customer_list']		= $this->job_order->getCustomerList();
		$data['discount_type_list']	= $this->job_order->getOption('discount_type','value');
		$data['item_list']			= $this->job_order->getItemList();
		$data['warehouse_list']		= $this->job_order->getWarehouseList();
		$data["taxrate_list"]		= $this->job_order->getTaxRateList();
		$data["taxrates"]			= $this->job_order->getTaxRates();
		$data['header_values']		= json_encode(array(
			''
		));
		$data['voucher_details']	= json_encode($this->job_order->getJobOrder($this->fields3, $id));
	
		$data['ajax_task']			= 'ajax_view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$this->view->load('job_order/job_order_payment', $data);
	}
	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];

		$pagination	= $this->job_order->getJOList($this->fields);
		$table		= '';
		// $pagination = new stdClass;
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $row) {
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'prepared')
									->addDelete($row->stat == 'prepared')
									->addPrint()
									->addOtherTask('Add Payment', 'bookmark')
									->addOtherTask('Tag as Complete', 'bookmark')
									->addCheckbox($row->stat == 'prepared')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->job_order_no)
									->draw();
			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $row->job_order_no . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . $row->service_quotation . '</td>';
			$table .= '<td>' . $row->po_number . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		// $len = 1;
		// for($x=1;$x<=$len;$x++){
		// 	$transactiondate = date("M d, Y");
		// 	$job_types 		= array('Inspection','Repair','Preventive Maintenance','Refurbish');
		// 	$job_type 		= $job_types[(array_rand($job_types))];
		// 	$statuses 		= array('Pending','Partial','With JO','Cancelled');
		// 	// $status 		= ($filter == 'all') ? $statuses[(array_rand($statuses))] : $filter;
		// 	$status 		= "Prepared";
		// 	$dropdown = $this->ui->loadElement('check_task')
		// 				->addView()
		// 				->addEdit()
		// 				->addDelete()
		// 				// ->addPrint()
		// 				->addOtherTask('Issue Parts', 'bookmark')
		// 				->addOtherTask('Tag as Complete', 'bookmark')
		// 				->addCheckbox()
		// 				->setLabels(array('delete' => 'Cancel'))
		// 				->setValue($x)
		// 				->draw();
		// 	// $table .= '<tr>';
		// 	// $table .= '<td align = "center">' . $dropdown . '</td>';
		// 	// $table .= '<td>' . $this->date->dateFormat($transactiondate) . '</td>';
		// 	// $table .= '<td>SQ00000'.$x.'</td>';
		// 	// $table .= '<td>Company C</td>';
		// 	// $table .= '<td>'.$job_type.'</td>';
		// 	// $table .= '<td>'.$this->generateRandomString().'</td>';
		// 	// $table .= '<td>'.$this->colorStat($status).'</td>';

		// 	$table .= '<tr>';
		// 	$table .= '<td align = "center">' . $dropdown . '</td>';
		// 	$table .= '<td>Dec 03, 2018</td>';
		// 	$table .= '<td>JO0000000001</td>';
		// 	$table .= '<td>Company C</td>';
		// 	$table .= '<td>SQ0000000001</td>';
		// 	$table .= '<td>001020123</td>';
		// 	$table .= '<td>'.$this->colorStat($status).'</td>';
		// }
		// $pagination->table = $table;
		// $pagination->pagination = '<div class="text-center">
		// 							<ul class="pagination">
		// 								<li class="disabled">
		// 									<a href="#" data-page="1">
		// 										<span aria-hidden="true">«</span>
		// 									</a>
		// 								</li>
		// 								<li class="active"><a href="#" data-page="1">1</a></li><li><a href="#" data-page="2">2</a></li><li><a href="#" data-page="3">3</a></li><li><a href="#" data-page="4">4</a></li><li><a href="#" data-page="5">5</a></li><li><a href="#" data-page="6">6</a></li><li><a href="#" data-page="7">7</a></li><li><a href="#" data-page="8">8</a></li>
		// 								<li><a href="#" data-page="9">9</a></li>
		// 								<li>
		// 									<a href="#" data-page="2">
		// 										<span aria-hidden="true">»</span>
		// 									</a>
		// 								</li>
		// 							</ul>
		// 							</div>';
		$pagination->table = $table;
		return $pagination;
	}

	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'prepared':
				$color = 'default';
				break;
			case 'partial':
				$color = 'warning';
				break;
			case 'cancelled':
				$color = 'danger';
				break;
			case 'completed':
				$color = 'info';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}

	public function generateRandomString($length = 10){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i = 0;$i < $length; $i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function ajax_serial_list() {
		$search	= $this->input->post('search');
		$itemcode = $this->input->post('itemcode');
		$allserials = $this->input->post('allserials');
		$itemselected = $this->input->post('itemselected');
		$linenum = $this->input->post('linenumber');
		$id = $this->input->post('id');
		$task = $this->input->post('task');
		$voucherno = '';
		// var_dump($linenum);
		if ($task=='ajax_edit') {
			$voucherno = $this->input->post('voucherno');
		}
		$curr = $this->job_order->getJOSerials($itemcode, $voucherno, $linenum);
		if ($curr) {
			$current_id = explode(",", $curr->serialnumbers);
		}
		else {
			$current_id = [];
		}
		$array_id = explode(',', $id);
		$all_id = explode(',', $allserials);
		
		$fields = array ('id', 'itemcode', 'serialno', 'engineno', 'chassisno', 'stat');
		$pagination	= $this->job_order->getSerialList($fields, $itemcode, $search);
		
		$table		= '';
		if (empty($pagination->result)) {
			$table = '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$checker = (in_array($row->id, $array_id) || in_array($row->id, $current_id)) ? 'checked' : '';
			$hide_tr = ((in_array($row->id, $all_id) && !in_array($row->id, $array_id)) || ($row->stat == 'Not Available') && (!in_array($row->id, $current_id))) ? 'hidden' : '';
			$table .= '<tr class = "'.$hide_tr.'">';
			$table .= '<td class = "text-center"><input type = "checkbox" name = "check_id[]" id = "check_id" class = "check_id" value = "'.$row->id.'" '.$checker.'></td>';
			$table .= '<td>' . $row->serialno . '</td>';
			$table .= '<td>' . $row->engineno . '</td>';
			$table .= '<td>' . $row->chassisno . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}
	
	private function ajax_load_sq_list() {
        $customer   = $this->input->post('customer');
		$pagination = $this->job_order->getSQPagination($customer);
		$table      = '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="5" class="text-center"><b>No Records Found</b></td></tr>';
		}
		foreach ($pagination->result as $key => $row) {
			$table .= '<tr data-id = "' . $row->voucherno . '">';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->notes . '</td>';
			$table .= '</tr>';
		}
		// $table .= '<script>checkExistingPR();</script>';
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_sq_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$details	= $this->job_order->getServiceQuotationDetails($voucherno, $warehouse);
		$header		= $this->job_order->getServiceQuotationHeader($this->fields_header, $voucherno);
		//var_dump($details,$header);
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

	private function ajax_create() {
		$seq 					= new seqcontrol();
		$job_order_no 			= $seq->getValue("JO");
		
		$data = $this->input->post($this->fields);
		$data['stat'] = 'prepared';
		$data['job_order_no'] = $job_order_no;
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		$data2 				= $this->input->post($this->fields2);
		foreach($data2 as $key => $content){
			if(is_array($content)){
				foreach($content as $ind => $val) {
					if($key == 'isbundle') {
						if($val == 1 || $val == 'Yes'){
							$data2[$key][$ind] = 'yes';
						} else {
							$data2[$key][$ind]  = 'no';
						}
					}
				}
			}
		}
		// var_dump($data2['isbundle']);
		// $result = 0;
		$result		= $this->job_order->saveJobOrder($data, $data2);
		//var_dump($data, $data2);
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_delete() {
		$delete_id = $this->input->post('delete_id');
		if ($delete_id) {
			$result		= $this->job_order->deleteJobOrder($delete_id);
			if(empty($result)) {
				$msg = "success";
			}else {
				$msg = $result;
			}
		}
		return array('success' => $msg);
	}

	private function ajax_load_bundle_details() {
		$itemcode	= $this->input->post('itemcode');
		
		$header		= $this->job_order->retrieveItemDetails($itemcode);
		$details 	= $this->job_order->retrieveBundleDetails($itemcode);
		$mainheader = $this->job_order->getItemListforBundle($itemcode);
		$table		= '';
		$success	= true;
		if (empty($header)) {
			$table		= '<tr><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
			$success	= false;
		}
		//var_dump($header, $details);
		return array(
			'table'		=> $header,
			'details'	=> $details,
			'header'	=> $header,
			'mainheader'=> $mainheader,
			'success'	=> $success
		);
	}

	private function ajax_create_issue() {
		$seq 						= new seqcontrol();
		$job_release_no 			= $seq->getValue("JR");
		$data						= $this->input->post($this->fields4);
		$customer					= $this->input->post('h_customer');
		$data['job_release_no'] 	= $job_release_no;
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		
		$result						= $this->job_order->saveJobRelease($data);
		
		$this->job_order->createClearingEntries($data['job_release_no']);

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Job Release', $data['job_release_no'])
									->setDetails($customer)
									->computeValues()
									->logChanges();

			// $this->inventory_model->setReference($data['voucherno'])
			// 						->setDetails($data['customer'])
			// 						->generateBalanceTable();
		}
		
		return array(	
			'result'=> $result
		);
	}

	private function ajax_edit_issue() {
		$job_release_no = $this->input->post('jobreleaseno');
		$data			= $this->input->post($this->fields4);
		$result = $this->job_order->getQty($job_release_no,$data);
		foreach ($result as $row) {
			$qty = $row->quantity;
		}
		return array(	
			'result' => $result
		);
	}

	private function ajax_update_issue() {
		$job_release_no = $this->input->post('jobreleaseno');
		$data			= $this->input->post($this->fields4);
		$customer		= $this->input->post('h_customer');
		$result 		= $this->job_order->updateIssueParts($job_release_no,$data);
		
		$this->job_order->createClearingEntries($job_release_no);

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Job Release', $job_release_no)
								->computeValues()
								->setDetails($customer)
								->logChanges();
		}
		return array(	
			'result' => $result
		);
	}

	private function ajax_delete_issue() {
		$delete_id = $this->input->post('id');
		$voucherno = $this->input->post('voucherno');
		
		if ($delete_id) {
			$result = $this->job_order->deleteJobRelease($delete_id,$voucherno);
		}
		if ($result && $this->inventory_model) {
			
				$this->inventory_model->prepareInventoryLog('Job Release', $delete_id)
										->computeValues()
										->logChanges('Cancelled');

				$this->job_order->createClearingEntries($delete_id);
			}
			// $this->inventory_model->generateBalanceTable();
		
		return array(
			'success' => $result
		);
	}

	private function ajax_load_issue() {
		$seq 						= new seqcontrol();
		$job_release_no 			= $seq->getValue("JR");
		$jobno						= $this->input->post('jobno');
		
		$asd	= $this->job_order->getIssuedPartsNo($jobno);
		
		$table = '';
		
		if (empty($asd)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		foreach ($asd as $row) {
			$table .= '<tr  data-jv = "' . $row->voucherno . '" data-id = "' . $row->asd . '">';
			$table .= '<td colspan="5">' . 'Part Issuance No.: '.$row->asd . '</td>';
			$table .= '<td>' . '<a class="btn-sm" pointer id="editip" title="Edit"><span class="glyphicon glyphicon-pencil editip pointer" style="border: 1px solid gainsboro;
			padding: 3px 4px 3px 4px;
			background-color: lavender;"></span></a>' . '</td>';
			$table .= '<td>' . '<a class="btn-sm" pointer title="Delete"><span class="glyphicon glyphicon-trash deleteip pointer" style="border: 1px solid gainsboro;
			padding: 3px 4px 3px 4px;
			background-color: lavender;"></span></a>' . '</td>';
			$table .= '</tr>';
			
			$list	= $this->job_order->getIssuedParts($row->asd);
		
			foreach ($list as $key => $row) {
			$table .= '<tr>';
			$table .= '<td>' . $row->itemcode . '</td>';
			$table .= '<td>' . $row->detailparticulars . '</td>';
			$table .= '<td>' . $row->description . '</td>';
			$table .= '<td>' . $row->quantity . '</td>';
			$table .= '<td>' . $row->unit . '</td>';
			$table .= '<td>' . '' . '</td>';
			$table .= '<td>' . '' . '</td>';
			$table .= '</tr>';
		}
	}		
		
		return array(	
			'issuedparts'=> $table
		);
	}

	private function ajax_checkbundle() {
		$itemcode	= $this->input->post('itemcode');
		$result		= $this->job_order->retrieveItemDetails($itemcode);
		$success	= true;
		if (empty($result)) {
			$success	= false;
		}
		//var_dump($result);
		return array(
			'result'  => $result,
			'success' => $success
		);
	 }
	 
	private function ajax_edit() {
		$job_order_no 			= $this->input->post('job_order_no');
		$data = $this->input->post($this->fields);
		$data['stat'] = 'prepared';
		$data['job_order_no'] = $job_order_no[0];
		$data['transactiondate'] 	= date('Y-m-d', strtotime($data['transactiondate']));
		$data2 = $this->input->post($this->fields2);
		
		// $itemcodewosq					= $this->input->post('detail_itemcode');
		// if($data['service_quotation'] == '') {
		// 	$data2['itemcode']	= $itemcodewosq;
		// }
		//var_dump($data, $data2, $data['job_order_no']);
		$result		= $this->job_order->updateJO($data, $data2);
		
		return array(
			'redirect' => MODULE_URL,
			'success' => $result
		);
	}

	private function ajax_upload_file()
	{
		$post_data 		= $this->input->post();
		$upload_handler	= new UploadHandler();
		$reference 		= $post_data['reference'];
		$upload_result 	= false;
		$task 			= $post_data['task'];
		unset($post_data['task']);

		if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
			if(!isset($upload_handler->response['files'][0]->error)){
				/**
				 * Generate Attachment Id
				 * @param table
				 * @param group fields
				 * @param custom condition
				 */

				if ($task=='view') 
				$attachment_id = $this->job_order->getCurrentId("job_order_attachments", $reference);
			
				else
				$attachment_id = $this->job_order->getNextId("job_order_attachments","attachment_id");
				foreach($upload_handler->response['files'] as $key => $row) {
					$post_data['attachment_id'] 	= $attachment_id;
					$post_data['attachment_name'] 	= $row->name;
					$post_data['attachment_type'] 	= $row->type;
					$post_data['attachment_url']	= $row->url;
				}
				if ($task == 'view')
					$upload_result 	= $this->job_order->replaceAttachment($post_data);
				
				else
					$upload_result 	= $this->job_order->uploadAttachment($post_data);
			}else{
				$upload_result 	= false;
			}
		}
		if($upload_result && $task == 'listing'){
			$this->job_order->updateData(array('stat' => 'completed'), 'job_order', " job_order_no = '$reference' ");
		}
	}
	
	private function ajax_retrieve_bom_qty(){
		$bundle_item = $this->input->post('bundle');

		$result 	 = $this->job_order->retrieve_bom_qty($bundle_item);

		// var_dump($result);
		return $dataArray = array('result' => $result);
	}
}