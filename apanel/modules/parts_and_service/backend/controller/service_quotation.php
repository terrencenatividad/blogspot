<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->service_quotation= new service_quotation_model();
		$this->parts_and_service= new parts_and_service_model();
		$this->session			= new session();
		$this->fields 			= array(
			'reference',
			'customer',
			'notes',
			'jobtype',
			'reference',
			'discounttype',
			'transactiondate',
			'targetdate',
			'vat_sales',
			'exempt_sales',
			't_sales',
			't_vat',
			't_amount',
			't_discount'
		);
		$this->fields_header	= array(
			'taxcode' 			=> 'taxcode', 
			'taxamount' 		=> 'taxamount',
			'discounttype'   	=> 'discounttype',
			'discountrate'   	=> 'discountrate',
			'discountamount' 	=> 'discountamount'
		);
		$this->fields_details			= array(
			'itemcode',
			'linenum',
			'parentcode',
			'parentline',
			'childqty',
			'detailparticular',
			'isbundle',
			'haswarranty',
			'warehouse',
			'quantity',
			'uom',
			'unitprice',
			'discount',
			'amount',
			'taxcode',
			'taxamount',
			'taxrate',
			'discountamount',
			'discounttype_details'
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
		$this->view->title		= 'Service Quotation';
		$this->view->addCSS(array(
				'jquery.fileupload.css'
			)
		);  
		$this->view->addJS(
			array(
				'jquery.dirrty.js',
				'jquery.ui.widget.js',
				'tmpl.min.js',
				'load-image.all.min.js',
				'jquery.iframe-transport.js',
				'jquery.fileupload.js',
				'jquery.fileupload-process.js',
				'jquery.fileupload-validate.js',
				'jquery.fileupload-ui.js'
			)
		);
		$data['customer_list']	= $this->service_quotation->getCustomerList();
		$data['ui']				= $this->ui;
		$this->view->load('service_quotation/service_quotation_list', $data);
	}
	public function create() {
		$this->view->title			= 'Add Service Quotation';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		$data['voucherno'] 			= '- Auto Generated -';
		$data['transactiondate']	= $this->date->dateFormat();
		$data['targetdate']			= $this->date->dateFormat();
		$data['job_list']			= $this->service_quotation->getOption('job_type','code');
		$data['customer_list']		= $this->service_quotation->getCustomerList();
		
		$data['discount_type_list']	= $this->service_quotation->getOption('discount_type','value');
		$data['discount_type'] 		= "none";
		$data['item_list']			= $this->service_quotation->getItemList();
		$data['warehouse_list']		= $this->service_quotation->getWarehouseList();
		$data["taxrate_list"]		= $this->service_quotation->getTaxRateList();
		$data["taxrates"]			= $this->service_quotation->getTaxRates();
		$data['header_values']		= array();
		$data['voucher_details']	= array();
		$data['t_vatable_sales']	= 0;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 0;
		$data['t_vat']				= 0;
		$data['t_amount']			= 0;
		$data['t_discount']			= 0;
		$data['ajax_task']			= 'ajax_create';
		
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;
		$data['restrict_dr'] 		= false;

		$this->view->load('service_quotation/service_quotation', $data);
	}
	public function edit($id) {
		$this->view->title			= 'Edit Service Quotation';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		

		$servicequotation			= $this->service_quotation->retrieveServiceQuotation($id);
		$servicequotation_details	= $this->service_quotation->retrieveServiceQuotationDetails($id);
		
		$data['voucherno'] 			= $id;
		$data['jobtype'] 			= $servicequotation[0]->jobtype;
		$data['customer'] 			= $servicequotation[0]->customer;
		$data['transactiondate']	= $this->date->dateFormat($servicequotation[0]->transactiondate);
		$data['targetdate']			= $this->date->dateFormat($servicequotation[0]->targetdate);
		$data['reference'] 			= $servicequotation[0]->reference;
		$data['discount_type'] 		= $servicequotation[0]->discounttype;
		$data['notes']				= $servicequotation[0]->notes;

		$data['job_list']			= $this->service_quotation->getOption('job_type','code');
		$data['customer_list']		= $this->service_quotation->getCustomerList();
		$data['discount_type_list']	= $this->service_quotation->getOption('discount_type','value');
		$data['item_list']			= $this->service_quotation->getItemList();
		$data['warehouse_list']		= $this->service_quotation->getWarehouseList();
		$data["taxrate_list"]		= $this->service_quotation->getTaxRateList();
		$data["taxrates"]			= $this->service_quotation->getTaxRates();
		
		$data['voucher_details']	= $servicequotation_details;

		$data['t_vatable_sales']	= 0;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 0;
		$data['t_vat']				= 0;
		$data['t_amount']			= 0;
		$data['t_discount']			= 0;

		$data['ajax_task']			= 'ajax_update';
		$data['ajax_post']			= '';
		$data['show_input']			= true;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;

		$this->view->load('service_quotation/service_quotation', $data);
	}
	public function view($id) {
		$this->view->title			= 'View Service Quotation';
		$this->fields[]				= 'stat';
		$data						= $this->input->post($this->fields);
		$data['ui']					= $this->ui;
		

		$servicequotation			= $this->service_quotation->retrieveServiceQuotation($id);
		$servicequotation_details	= $this->service_quotation->retrieveServiceQuotationDetails($id);
		
		$data['voucherno'] 			= $id;
		$data['jobtype'] 			= $servicequotation[0]->jobtype;
		$data['customer'] 			= $servicequotation[0]->customer;
		$data['transactiondate']	= $this->date->dateFormat($servicequotation[0]->transactiondate);
		$data['targetdate']			= $this->date->dateFormat($servicequotation[0]->targetdate);
		$data['reference'] 			= $servicequotation[0]->reference;
		$data['discount_type'] 		= $servicequotation[0]->discounttype;
		$data['notes']				= $servicequotation[0]->notes;
		$data['filename'] 			= $servicequotation[0]->filename;
		$data['filetype'] 			= $servicequotation[0]->filetype;

		$data['job_list']			= $this->service_quotation->getOption('job_type','code');
		$data['customer_list']		= $this->service_quotation->getCustomerList();
		$data['discount_type_list']	= $this->service_quotation->getOption('discount_type','value');
		$data['item_list']			= $this->service_quotation->getItemList();
		$data['warehouse_list']		= $this->service_quotation->getWarehouseList();
		$data["taxrate_list"]		= $this->service_quotation->getTaxRateList();
		$data["taxrates"]			= $this->service_quotation->getTaxRates();
		
		$data['voucher_details']	= $servicequotation_details;

		$data['t_vatable_sales']	= 0;
		$data['t_vat_exempt_sales']	= 0;
		$data['t_vatsales']			= 0;
		$data['t_vat']				= 0;
		$data['t_amount']			= 0;
		$data['t_discount']			= 0;

		$data['ajax_task']			= 'view';
		$data['ajax_post']			= '';
		$data['show_input']			= false;
		// Closed Date
		$close_date 				= $this->parts_and_service->getClosedDate();
		$data['close_date']			= $close_date;

		$this->view->load('service_quotation/service_quotation', $data);
	}
	private function ajax_list() {
		$data		= $this->input->post(array('search', 'sort', 'customer', 'filter', 'daterangefilter', 'limit'));
		$sort		= $data['sort'];
		$search		= $data['search'];
		$customer	= $data['customer'];
		$filter		= $data['filter'];
		$datefilter	= $data['daterangefilter'];
		$limit 		= $data['limit'];

		$pagination = new stdClass;
		$pagination	= $this->service_quotation->getServiceQuotationPagination($search, $sort, $customer, $filter, $datefilter, $limit);

		$table		= '';

		if (empty($pagination->result)) {
			$table = '<tr><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>';
		}

		foreach ($pagination->result as $key => $row) {
			$transactiondate 	=	$row->transactiondate;
			$table .= '<tr>';
			$dropdown = $this->ui->loadElement('check_task')
									->addView()
									->addEdit($row->stat == 'Pending')
									->addDelete($row->stat == 'Pending')
									->addOtherTask('Tag as Accepted', 'bookmark', $row->stat == 'Pending')
									->addCheckbox($row->stat == 'Pending')
									->setLabels(array('delete' => 'Cancel'))
									->setValue($row->voucherno)
									->draw();

			$table .= '<td align = "center">' . $dropdown . '</td>';
			$table .= '<td>' . $this->date->dateFormat($row->transactiondate) . '</td>';
			$table .= '<td>' . $row->voucherno . '</td>';
			$table .= '<td>' . $row->customer . '</td>';
			$table .= '<td>' . ucfirst($row->jobtype) . '</td>';
			$table .= '<td>' . $row->reference . '</td>';
			$table .= '<td>' . $this->colorStat($row->stat) . '</td>';
			$table .= '</tr>';
		}
		
		$pagination->table = $table;
		return $pagination;
	}
	private function ajax_delete(){
		$vouchers 		=	$this->input->post('delete_id');
		$seq_vouchers 	= 	"'" . implode("','", $vouchers) . "'";
		
		$data['stat'] 	=	"Cancelled";
		
		$cond 			=	" voucherno IN ($seq_vouchers) ";

		$result 		=	$this->service_quotation->updateData($data, "servicequotation", $cond);

		if( $result )
		{
			$result 	=	$this->service_quotation->updateData($data, "servicequotation_details", $cond);
		}

		if($result == '1')
		{
			$msg = "success";
		}
		else
		{
			$msg = "Failed to Cancel.";
		}

		return $dataArray = array( "msg" => $msg );
	}
	private function ajax_create(){
		$seq 				= new seqcontrol();
		$voucherno 			= $seq->getValue("SEQ");
		$quote 				= $this->input->post($this->fields);
		$quote_details 		= $this->input->post($this->fields_details);
		$submit_data 		= $this->input->post('submit_data');

		$transactiondate 	= date('Y-m-d', strtotime($quote['transactiondate']));
		$targetdate 		= date('Y-m-d', strtotime($quote['targetdate']));
		$fiscalyear 		= date('Y', strtotime($transactiondate));
		$period 			= date('n', strtotime($transactiondate));
		$values = array(
					'voucherno' 	=> $voucherno,
					'transtype' 	=> 'SEQ',
					'transactiondate' => $transactiondate,
					'targetdate' 	=> $targetdate,
					'customer' 		=> $quote['customer'],
					'jobtype' 		=> $quote['jobtype'],
					'reference' 	=> $quote['reference'],
					'discounttype' 	=> $quote['discounttype'],
					'notes' 		=> $quote['notes'],
					'fiscalyear' 	=> $fiscalyear,
					'period' 		=> $period,
					't_amount' 		=> str_replace(',', '', $quote['t_amount']),
					'vat_sales' 	=> str_replace(',', '', $quote['vat_sales']),
					'exempt_sales' 	=> str_replace(',', '', $quote['exempt_sales']),
					't_sales' 		=> str_replace(',', '', $quote['t_sales']),
					't_vat' 		=> str_replace(',', '', $quote['t_vat']),
					't_amount' 		=> str_replace(',', '', $quote['t_amount']),
					't_discount' 	=> str_replace(',', '', $quote['t_discount']),
					'stat' 			=> 'Pending'
		);
		
		$result1 = $this->service_quotation->saveValues('servicequotation',$values);
		
		$values = array(
					'voucherno' 		=> $voucherno,
					'transtype' 		=> 'SEQ',
					'itemcode' 			=> $quote_details['itemcode'],
					'linenum' 			=> $quote_details['linenum'],
					'haswarranty' 		=> $quote_details['haswarranty'],
					'isbundle' 			=> $quote_details['isbundle'],
					'parentcode' 		=> $quote_details['parentcode'],
					'parentline' 		=> $quote_details['parentline'],
					'childqty' 			=> $quote_details['childqty'],
					'detailparticular' 	=> $quote_details['detailparticular'],
					'warehouse' 		=> $quote_details['warehouse'],
					'qty' 				=> str_replace(',', '', $quote_details['quantity']),
					'uom' 				=> $quote_details['uom'],
					'unitprice' 		=> str_replace(',', '', $quote_details['unitprice']),
					'taxcode' 			=> $quote_details['taxcode'],
					'taxrate' 			=> $quote_details['taxrate'],
					'taxamount' 		=> str_replace(',', '', $quote_details['taxamount']),
					'amount' 			=> str_replace(',', '', $quote_details['amount']),
					'discounttype' 		=> $quote_details['discounttype_details'],
					'discountrate' 		=> str_replace(',', '', $quote_details['discount']),
					'discountamount' 	=> str_replace(',', '', $quote_details['discountamount']),
					'stat' 				=> 'Pending'
		);
		
		$result2 = $this->service_quotation->saveFromPost('servicequotation_details', $values);

		$result = array('query1' => $result1, 'query2' => $result2);
		return $result;
	}
	private function ajax_update(){
		$voucherno 			= $this->input->post('voucherno');
		$quote 				= $this->input->post($this->fields);
		$quote_details 		= $this->input->post($this->fields_details);
		$submit_data 		= $this->input->post('submit_data');

		$transactiondate 	= date('Y-m-d', strtotime($quote['transactiondate']));
		$targetdate 		= date('Y-m-d', strtotime($quote['targetdate']));
		$fiscalyear 		= date('Y', strtotime($transactiondate));
		$period 			= date('n', strtotime($transactiondate));

		$cond 		= 'voucherno = "'. $voucherno .'"';
		
		$values = array(
					'voucherno' 	=> $voucherno,
					'transtype' 	=> 'SEQ',
					'transactiondate' => $transactiondate,
					'targetdate' 	=> $targetdate,
					'customer' 		=> $quote['customer'],
					'jobtype' 		=> $quote['jobtype'],
					'reference' 	=> $quote['reference'],
					'discounttype' 	=> $quote['discounttype'],
					'notes' 		=> $quote['notes'],
					'fiscalyear' 	=> $fiscalyear,
					'period' 		=> $period,
					't_amount' 		=> str_replace(',', '', $quote['t_amount']),
					'vat_sales' 	=> str_replace(',', '', $quote['vat_sales']),
					'exempt_sales' 	=> str_replace(',', '', $quote['exempt_sales']),
					't_sales' 		=> str_replace(',', '', $quote['t_sales']),
					't_vat' 		=> str_replace(',', '', $quote['t_vat']),
					't_amount' 		=> str_replace(',', '', $quote['t_amount']),
					't_discount' 	=> str_replace(',', '', $quote['t_discount']),
					'stat' 			=> 'Pending'
		);
		
		$result1 	= $this->service_quotation->updateData($values, 'servicequotation', $cond);

		$delete1 	= $this->service_quotation->deleteData('servicequotation_details', $cond);

		$values = array(
					'voucherno' 		=> $voucherno,
					'transtype' 		=> 'SEQ',
					'itemcode' 			=> $quote_details['itemcode'],
					'linenum' 			=> $quote_details['linenum'],
					'haswarranty' 		=> $quote_details['haswarranty'],
					'isbundle' 			=> $quote_details['isbundle'],
					'parentcode' 		=> $quote_details['parentcode'],
					'parentline' 		=> $quote_details['parentline'],
					'childqty' 			=> $quote_details['childqty'],
					'detailparticular' 	=> $quote_details['detailparticular'],
					'warehouse' 		=> $quote_details['warehouse'],
					'qty' 				=> str_replace(',', '', $quote_details['quantity']),
					'uom' 				=> $quote_details['uom'],
					'unitprice' 		=> str_replace(',', '', $quote_details['unitprice']),
					'taxcode' 			=> $quote_details['taxcode'],
					'taxrate' 			=> $quote_details['taxrate'],
					'taxamount' 		=> str_replace(',', '', $quote_details['taxamount']),
					'amount' 			=> str_replace(',', '', $quote_details['amount']),
					'discounttype' 		=> $quote_details['discounttype_details'],
					'discountrate' 		=> str_replace(',', '', $quote_details['discount']),
					'discountamount' 	=> str_replace(',', '', $quote_details['discountamount']),
					'stat' 				=> 'Pending'
		);
		$result2 = $this->service_quotation->saveFromPost('servicequotation_details', $values);
		
		$result = array(
			'delete1' => $delete1,
			'query1' => $result1, 
			'query2' => $result2
		);

		return $result;
	}
	private function colorStat($stat) {
		$color = 'default';
		switch ($stat) {
			case 'Pending':
				$color = 'default';
				break;
			case 'Approved':
				$color = 'success';
				break;
			case 'Partial':
				$color = 'warning';
				break;
			case 'Cancelled':
				$color = 'danger';
				break;
			case 'With JO':
				$color = 'info';
				break;
		}
		return '<span class="label label-' . $color . '">' . strtoupper($stat) . '</span>';
	}
	private function get_item_details()
	{
		$itemcode 	= $this->input->post('itemcode');

		$result 	= $this->service_quotation->retrieveItemDetails($itemcode);
		
		return $result;
	}
	private function get_bundle_items()
	{
		$parentitemcode 	= $this->input->post('itemcode');
		$parentline 	= $this->input->post('linenum');
		$ui 			= $this->ui;

		$result 		= $this->service_quotation->retrieveBundleDetails($parentitemcode);
		
		$cc_entry_data  = array("itemcode ind","CONCAT(itemcode,' - ',itemname) val");
		$itemcodes 		= $this->service_quotation->getItemList();

		$w_entry_data           = array("warehousecode ind","description val");
		$warehouse_list 	= $this->service_quotation->getWarehouseList();

		$itemcode = [];
		$itemdesc = [];
		$itemname = [];
		$itemqty = [];
		$itemuom = [];
		$table = '';

				
		foreach ($result as $key => $row) {
			$table .= '<tr class="subitem'.$parentline.'">';
			$table .= '<td>' . $ui->formField('dropdown')
								->setPlaceholder('Select Item')
								->setSplit('	', 'col-md-12')
								->setName("displayitemcode[]")
								->setList($itemcodes)
								->setClass('itemcode')
								->setAttribute(array('disabled',true))
								->setValue($row->item_code)
								->draw(true);
			$table .= "
				<input type='hidden' name='itemcode[]' 		class='itemcode' 	value='". $row->item_code ."'>
				<input type='hidden' name='warehouse[]' 	class='warehouse' 	value=''>
				<input type='hidden' name='taxcode[]' 		class='taxcode' 	value=''>

				<input type='hidden' name='linenum[]' 		class='linenum' 	value=''>
				<input type='hidden' name='parentcode[]' 	class='parentcode' 	value='". $parentitemcode ."'>
				<input type='hidden' name='parentline[]' 	class='parentline' 	value='". $parentline ."'>
				<input type='hidden' name='childqty[]' 		class='childqty' 	value='". $row->quantity ."'>
				<input type='hidden' name='isbundle[]' 		class='isbundle' 	value='No'>
				<input type='hidden' name='haswarranty[]' 	class='haswarranty' value=''>
				
				<input type='hidden' name='discounttype_details[]' class='discounttype_details' value=''>
				<input type='hidden' name='discountamount[]' class='discountamount' value='0.00'>
				<input type='hidden' name='taxrate[]' 		class='taxrate' 	value=''>
				<input type='hidden' name='taxamount[]' 	class='taxamount' 	value='0.00'>";
						'</td>';
			
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setName("detailparticular[]")
								->setAttribute(array('readonly', 'readonly'))
								->setClass('detailparticular')
								->setValue($row->detailsdesc) 
								->draw(true);
						'</td>';
			$table .= "<td class='text-center'>" . $ui->formField('checkbox')
								->setName('warranty[]')
								->setClass('warranty')
								->setAttribute(array('disabled' ,true)) 
								->setValue('0')
								->draw(true);
					"</td>";
			$table .= '<td>' . $ui->formField('dropdown')
								->setPlaceholder('Select Warehouse')
								->setSplit('', 'col-md-12')
								->setName('warehouse[]')
								->setClass('warehouse')
								->setList($warehouse_list)
								->setValue('')
								->setAttribute(array('disabled', true))
								->setValidation('required')
								->draw(true);
						'</td>';
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setName("quantity[]")
								->setAttribute(array('readonly' => 'readonly'))
								->setClass('quantity text-right ')
								->setValue('0')
								->draw(true); 
						'</td>';
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setName("uom[]")
								->setAttribute(array('readonly', 'readonly'))
								->setClass('uom text-right')
								->setValue($row->uom)
								->draw(true); 
						'</td>';
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setAttribute(array('readonly', 'readonly'))
								->setName("unitprice[]")
								->setClass('text-right')
								->setValue("0.00")
								->draw(true); 
						'</td>';
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setName("discount[]")
								->setAttribute(array('readonly', 'readonly'))
								->setClass('discount text-right')
								->setValue("0.00")
								->draw(true); 
						'</td>';
			$table .= '<td>' . $ui->formField('dropdown')
								->setSplit('	', 'col-md-12')
								->setName("displaytaxcode[]")
								->setAttribute(array('disabled', true))
								->setValue('0')
								->setList(array('0'=>'None'))
								->setClass('taxcode')
								->draw(true); 
						'</td>';
			$table .= '<td>' . $ui->formField('text')
								->setSplit('	', 'col-md-12')
								->setName("amount[]")
								->setAttribute(array('readonly', 'readonly'))
								->setClass('amount text-right')
								->setValue("0.00")
								->draw(true); 
						'</td>';
			$table .= '<td class="text-center">
			<button type="button" class="btn btn-danger btn-flat confirm-delete" disabled><span class="glyphicon glyphicon-trash"></span></button>
			</td>';
			$table .= '</tr>';
		}
		return array('table' => $table);
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
}