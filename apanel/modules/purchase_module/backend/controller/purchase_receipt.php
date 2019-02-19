<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui				= new ui();
		$this->input			= new input();
		$this->logs				= new log();
		$this->purchase_model	= new purchase_receipt_model();
		$this->restrict 		= new purchase_restriction_model();
		$this->report_model		= $this->checkOutModel('reports_module/report_model');
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
			'assetid',
			'months',
			'amount',
			'discounttype',
			'discountrate',
			'discountamount',
			'netamount',
			'total_tax',
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
			'itemcode'					=>'pr.itemcode',
			'detailparticular',
			'linenum',
			'receiptqty',
			'receiptuom',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount'	=> 'taxamount',
			'amount'	=> 'amount',
			'convreceiptqty',
			'convuom',
			'conversion',
			'discounttype',
			'detail_discountamount'		=> 'discountamount',
			'detail_withholdingamount'	=> 'withholdingamount',
			'detail_warehouse'			=> 'warehouse',
			'po_qty',
			'exchangerate',
			'item_ident_flag'
		);
		$this->fields3			= array(
			'itemcode',
			'budgetcode',
			'detailparticular',
			'linenum',
			'receiptqty',
			'receiptuom',
			'unitprice',
			'taxcode',
			'taxrate',
			'taxamount'	=> 'taxamount',
			'detail_amount'	=> 'amount',
			'convreceiptqty',
			'convuom',
			'conversion',
			'discounttype',
			'detail_discountamount'		=> 'discountamount',
			'detail_withholdingamount'	=> 'withholdingamount',
			'detail_warehouse'			=> 'warehouse',
			'po_qty',
			'exchangerate'
		);
		$this->clean_number		= array(
			'receiptqty'
		);

		$this->serial_fields	= array(
			'detail_warehouse'			=> 'warehouse',
			'voucherno',
			'source_no',
			'itemcode',
			'linenum',
			'serial_no_list',
			'engine_no_list',
			'chassis_no_list',
			'receiptqty',
			'item_ident_flag'
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
		$data['asset_list']			= $this->purchase_model->retrieveAssetList();
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['restrict_pr']		= false;
		$data['serial_db']			= $this->purchase_model->getSerialNoFromDbValidation();
		$data['serial_db_array']	= array();
		foreach ($data['serial_db'] as $serial_db) {
			array_push($data['serial_db_array'], $serial_db->serialno);
		}
		$data['engine_db_array']	= array();
		foreach ($data['serial_db'] as $engine_db) {
			array_push($data['engine_db_array'], $engine_db->engineno);
		}
		$data['chassis_db_array']	= array();
		foreach ($data['serial_db'] as $chassis_db) {
			array_push($data['chassis_db_array'], $chassis_db->chassisno);
		}
		$data['attachment_url']			= '';
		$data['attachment_filename']	= '';
		$data['attachment_filetype']	= '';

		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function edit($voucherno) {
		$this->view->title			= 'Edit Purchase Receipt';
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
		$data['serial_db']			= $this->purchase_model->getSerialNoFromDbView($voucherno);
		$data['asset_list']			= $this->purchase_model->retrieveAssetList();
		$data['serial_db_array']	= array();
		foreach ($data['serial_db'] as $serial_db) {
			array_push($data['serial_db_array'], $serial_db->serialno);
		}
		$data['engine_db_array']	= array();
		foreach ($data['serial_db'] as $engine_db) {
			array_push($data['engine_db_array'], $engine_db->engineno);
		}
		$data['chassis_db_array']	= array();
		foreach ($data['serial_db'] as $chassis_db) {
			array_push($data['chassis_db_array'], $chassis_db->chassisno);
		}
		$attachment						= $this->purchase_model->getAttachmentFile($voucherno);
		$data['attachment_url']			= '';
		$data['attachment_filename']	= '';
		$data['attachment_filetype']	= '';
		
		if (isset($attachment->attachment_url)) {
			$data['attachment_url'] 		= $attachment->attachment_url;
			$data['attachment_filename']	= $attachment->attachment_name;
			$data['attachment_filetype']	= $attachment->attachment_type;
		} 
		
		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function view($voucherno) {
		$this->view->title			= 'View Purchase Receipt';
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
		$data['asset_list']			= $this->purchase_model->retrieveAssetList();
		// Closed Date
		$data['close_date']			= $this->restrict->getClosedDate();
		$data['restrict_pr']		= $this->restrict->setButtonRestriction($transactiondate);
		$data['serial_db']			= $this->purchase_model->getSerialNoFromDbView($voucherno);
		$data['serial_db_array']	= array();
		foreach ($data['serial_db'] as $serial_db) {
			array_push($data['serial_db_array'], $serial_db->serialno);
		}
		$data['engine_db_array']	= array();
		foreach ($data['serial_db'] as $engine_db) {
			array_push($data['engine_db_array'], $engine_db->engineno);
		}
		$data['chassis_db_array']	= array();
		foreach ($data['serial_db'] as $chassis_db) {
			array_push($data['chassis_db_array'], $chassis_db->chassisno);
		}
		$attachment						= $this->purchase_model->getAttachmentFile($voucherno);
		$data['attachment_url']			= '';
		$data['attachment_filename']	= '';
		$data['attachment_filetype']	= '';
		
		if (isset($attachment->attachment_url)) {
			$data['attachment_url'] 		= $attachment->attachment_url;
			$data['attachment_filename']	= $attachment->attachment_name;
			$data['attachment_filetype']	= $attachment->attachment_type;
		} 
		
		$this->view->load('purchase_receipt/purchase_receipt', $data);
	}

	public function print_preview($voucherno) {
		$documentinfo		= $this->purchase_model->getDocumentInfo($voucherno);
		$vendordetails		= $this->purchase_model->getVendorDetails($documentinfo->partnercode);
		$documentdetails	= array(
			'Date'	=> $this->date->dateFormat($documentinfo->documentdate),
			'PR #'	=> $voucherno,
			'PO #'	=> $documentinfo->referenceno,
			'INVOICE NO.'		=> $documentinfo->invoiceno,
			'TERMS'	=> $vendordetails->terms
		);

		$print = new purchase_print_model();
		$print->setDocumentType('Purchase Receipt')
		->setFooterDetails(array('Approved By', 'Checked By', 'Received By'))
		->setVendorDetails($vendordetails)
		->setDocumentDetails($documentdetails)
				// ->addTermsAndConditon()
		->addReceived();

		$print->setHeaderWidth(array(50, 110, 20, 20))
		->setHeaderAlign(array('C', 'C', 'C', 'C'))
		->setHeader(array('Item Code', 'Description', 'Qty', 'UOM'))
		->setRowAlign(array('L', 'L', 'R', 'L'))
		->setSummaryAlign(array('J'))	
		->setSummaryWidth(array('200'));
		
		$documentcontent	= $this->purchase_model->getDocumentContent($voucherno);		
		
		$hasSerial = false;
		$serial = '0';
		$engine = '0';
		$chassis = '0';
		$serial_total = 0;
		foreach($documentcontent as $key => $row) {
			if ($row->Ident != 0){
				$hasSerial = true;
				if (substr($row->Ident,0,1) == '1')
					$serial = '1';
				if (substr($row->Ident,1,1) == '1')
					$engine = '1';
				if (substr($row->Ident,2,1) == '1')
					$chassis = '1';
			}
		}

		$serial_total = (int)$serial + (int)$engine + (int)$chassis;
		
		if ($hasSerial) {
			if ($serial_total == 1) {
			// 1 SERIAL FIELD ONLY
				$first = ($serial == '1') ? 'S/N' : (($engine == '1') ? 'E/N' : 'C/N');
				$print->setHeaderWidth(array(30, 90, 20, 20, 40))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', $first))
				->setRowAlign(array('L', 'L', 'R', 'L', 'L'))
				->setSummaryAlign(array('J'))	
				->setSummaryWidth(array('200'));		
			} else if ($serial_total == 2) {
			// 2 SERIAL FIELDS
				if ($serial == '1' && $engine == '1' && $chassis == '0') {
					$first = 'S/N';
					$second = 'E/N';
				} else if ($serial == '1' && $engine == '0' && $chassis == '1') {
					$first = 'S/N';
					$second = 'C/N';
				} else if ($serial == '0' && $engine == '1' && $chassis == '1') {
					$first = 'E/N';
					$second = 'C/N';
				}

				$print->setHeaderWidth(array(30, 40, 20, 20, 45, 45))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', $first, $second))
				->setRowAlign(array('L', 'L', 'R', 'L', 'L', 'L', 'L'))
				->setSummaryAlign(array('J'))	
				->setSummaryWidth(array('200'));
			} else if ($serial_total == 3) {
			// 3 SERIAL FIELDS
				$print->setHeaderWidth(array(30, 40, 20, 20, 30, 30, 30))
				->setHeaderAlign(array('C', 'C', 'C', 'C', 'C', 'C', 'C'))
				->setHeader(array('Item Code', 'Description', 'Qty', 'UOM', 'S/N', 'E/N', 'C/N'))
				->setRowAlign(array('L', 'L', 'R', 'L', 'L', 'L', 'L'))
				->setSummaryAlign(array('J'))	
				->setSummaryWidth(array('200'));
			}
		}

		$detail_height = 37;
		//$notes = preg_replace('!\s+!', ' ', $documentinfo->remarks);
		$notes = htmlentities($documentinfo->remarks);
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
			
			if($hasSerial){
				if ($serial_total == 1) {
					$print->addRow(array($row->ItemCode, $row->Description, $row->Quantity, $row->UOM, '', $row->Price, $row->Tax,$row->Amount));
				} else if ($serial_total == 2) {
					$print->addRow(array($row->ItemCode, $row->Description, $row->Quantity, $row->UOM, '', '', $row->Price, $row->Tax,$row->Amount));
				} else if ($serial_total == 3) {
					$print->addRow(array($row->ItemCode, $row->Description, $row->Quantity, $row->UOM, '', '', '', $row->Price, $row->Tax,$row->Amount));
				}
			// $print->addRow(array($row->ItemCode, $row->Description, $row->Quantity, $row->UOM, '', '', $row->Price, $row->Tax,$row->Amount));
				if ($row->Ident != 0) {
					$documentserials	= $this->purchase_model->getDocumentSerials($voucherno,$row->ItemCode);
					foreach($documentserials as $key => $rowserials) {
						
						$sndisplay = $rowserials->Serial;
						$endisplay = $rowserials->Engine;
						$cndisplay = $rowserials->Chassis;
						if ($serial_total == 1) {
							if ($serial == '1' && $engine == '0' && $chassis == '0')
								$print->addRow(array('', '', '', '', $sndisplay, '', '',''));
							if ($serial == '0' && $engine == '1' && $chassis == '0')
								$print->addRow(array('', '', '', '', $endisplay, '', '',''));
							if ($serial == '0' && $engine == '0' && $chassis == '1')
								$print->addRow(array('', '', '', '', $cndisplay, '', '',''));
						} else if ($serial_total == 2) {
							if ($serial == '1' && $engine == '1' && $chassis == '0')
								$print->addRow(array('', '', '', '', $sndisplay, $endisplay, '', '',''));
							if ($serial == '1' && $engine == '0' && $chassis == '1')
								$print->addRow(array('', '', '', '', $sndisplay, $cndisplay, '', '',''));
							if ($serial == '0' && $engine == '1' && $chassis == '1')
								$print->addRow(array('', '', '', '', $endisplay, $cndisplay, '', '',''));
						} else if ($serial_total == 3) {
							$print->addRow(array('', '', '', '', $sndisplay, $endisplay, $cndisplay, '', '',''));
						}						
					}
				}
			} else {
				$print->addRow($row);
			}
			if (($key + 1) % $detail_height == 0) {
				$total_amount = $vatable_sales + $vat_exempt - $discount + $tax - $wtax;
				// $summary = array(
				// 	'VATable Sales'		=> number_format($vatable_sales, 2),
				// 	'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
				// 	'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
				// 	'Discount'			=> number_format($discount, 2),
				// 	'Tax'				=> number_format($tax, 2),
				// 	'WTax'				=> number_format($wtax, 2),
				// 	'Total Amount'		=> number_format($total_amount, 2)
				// );
				// $print->drawSummary($summary);
				// $vatable_sales	= 0;
				// $vat_exempt		= 0;
				// $discount		= 0;
				// $tax			= 0;
				// $wtax			= 0;
				// $total_amount	= 0;
				$print->drawSummary(array(array('Notes:'),
					array($notes),
					array(''),
					array(''),
					array('')
				));
			}
		}
		$total_amount = $vatable_sales + $vat_exempt - $discount + $tax - $wtax;
		// $summary = array(
		// 	'VATable Sales'		=> number_format($vatable_sales, 2),
		// 	'VAT-Exempt Sales'	=> number_format($vat_exempt, 2),
		// 	'Total Sales'		=> number_format($vatable_sales + $vat_exempt, 2),
		// 	'Discount'			=> number_format($discount, 2),getDocumentContent
		// 	'Tax'				=> number_format($tax, 2),
		// 	'WTax'				=> number_format($wtax, 2),
		// 	'Total Amount'		=> number_format($total_amount, 2)
		// );
		// $print->drawSummary($summary);
		$print->drawSummary(array(array('Notes:'),
			array($notes),
			array(''),
			array(''),
			array('')
		));
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
									//->addEdit($row->stat == 'Received')
									// ->addDelete($row->stat == 'Received' && $restrict_pr)
			->addPrint()
									//->addCheckbox($row->stat == 'Received' && $restrict_pr)
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
		// for($i=0;$i<count($data2['budgetcode']);$i++) {
		// 	if(!empty($data2['budgetcode'][$i])) {
		// 		$getid = $this->purchase_model->getItemAccount($data2['itemcode'][$i]);
		// 		$getallocatedamount = $this->purchase_model->getAllocatedAmount($data2['budgetcode'][$i], $getid->expense_account);
		// 		var_dump($getid->expense_account);
		// 	}
		// }
		// var_dump($data);
		unset($data2['budgetcode']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']			= $this->date->getYear($data['transactiondate']);
		$seq						= new seqcontrol();
		$data['voucherno']			= $seq->getValue('PR');
		$data['transtype']			= $this->purchase_model->getTransactionType($data['source_no']);
		$data['assetid']			= str_replace("none","",$data['assetid']);
		$result						= $this->purchase_model->savePurchaseReceipt($data, $data2);
		$serials					= $this->input->post($this->serial_fields);		
		$result2					= $this->purchase_model->saveSerialNumbers($serials,$data['voucherno']);
		$attachment_update['reference'] = $data['voucherno'];
		$attachment					= $this->purchase_model->updateAttachmentReference($attachment_update,$data['source_no']);
		// retrieve  freight, insurance, packaging 
		// $ret_misc 					= $this->purchase_model->retrieve_misc_fees($data['source_no']);
		// $total_misc_fee 			= isset($ret_misc->total_miscfee) ? $ret_misc->total_miscfee 	:	0;	
			
		if ($result && $this->financial_model) {
			$this->financial_model->generateAP($data['voucherno']);
		}

		if ($result && $this->inventory_model) {
			$this->inventory_model->prepareInventoryLog('Purchase Receipt', $data['voucherno'])
			->setDetails($data['vendor'])
			->computeValues()
			->logChanges();

			$this->inventory_model->setReference($data['voucherno'])
			->setDetails($data['vendor'])
			->generateBalanceTable();
		}

		if($data['assetid'] != ''){
			$getAsset = $this->purchase_model->getAsset($data['assetid']);
			$capitalized_cost 	= $getAsset->capitalized_cost;
			$balance_value    	= $getAsset->balance_value;
			$salvage_value	  	= $getAsset->salvage_value;
			$useful_life	  	= $getAsset->useful_life;
			$depreciation_month = $getAsset->depreciation_month;
			$time  				= strtotime($depreciation_month);
			
			$getAP 				= $this->purchase_model->getAP($data['voucherno']);
			$debit 				= $getAP->converteddebit;
			// $exchangerate = $getAP->exchangerate;
			$convdebit 			= str_replace(',', '',$debit);

			$bv = $balance_value + $convdebit;

			$this->purchase_model->updateAsset($data['assetid'],$convdebit,$capitalized_cost,$balance_value,$useful_life,$data['months']);
		}
		
		$columns['transactiontype'] = 'Received Asset';
		if ($result && $this->report_model){ 
			$this->report_model->generateAssetActivity();
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
		$temp_voucherno				= $data['voucherno'];
		unset($data['voucherno']);
		$data['transactiondate']	= $this->date->dateDbFormat($data['transactiondate']);
		$data['period']				= $this->date->getMonthNumber($data['transactiondate']);
		$data['fiscalyear']			= $this->date->getYear($data['transactiondate']);
		$data['assetid']			= str_replace("none","",$data['assetid']);
		$voucherno					= $this->input->post('voucherno_ref');
		$data2						= $this->getItemDetails();
		$data2						= $this->cleanData($data2);
		$serials					= $this->input->post($this->serial_fields);
		$delete						= $this->purchase_model->deleteSerialNumbers($temp_voucherno);
		$result2					= $this->purchase_model->saveSerialNumbers($serials,$temp_voucherno);
		$get_attachment				= $this->purchase_model->getAttachmentFile($voucherno);
		// $attachment_update['reference'] = $data['voucherno'];
		// if (isset($get_attachment->attachment_url)) {
		// 	$attachment				= $this->purchase_model->updateAttachmentReference($temp_voucherno,$data['source_no']);
		// } else {
		// 	$attachment				= $this->purchase_model->updateAttachmentReference($temp_voucherno,$data['source_no']);			
		// }
		
		$this->inventory_model->prepareInventoryLog('Purchase Receipt', $voucherno)
		->preparePreviousValues();

		$result						= $this->purchase_model->updatePurchaseReceipt($data, $data2, $voucherno);
		if ($result && $this->financial_model) {
			$this->financial_model->generateAP($voucherno);
		}

		if ($result && $this->inventory_model) {
			$this->inventory_model->computeValues()
			->setDetails($data['vendor'])
			->logChanges();

			$this->inventory_model->generateBalanceTable();
		}
		if($this->report_model){
			$this->report_model->generateAssetActivity();
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
				$this->inventory_model->prepareInventoryLog('Purchase Receipt', $voucherno)
				->computeValues()
				->logChanges('Cancelled');
			}
			$this->inventory_model->generateBalanceTable();
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
			//$table .= '<td class="text-right">' . number_format($row->netamount, 2) . '</td>';
			$table .= '</tr>';
		}
		$pagination->table = $table;
		return $pagination;
	}

	private function ajax_load_purchase_details() {
		$voucherno	= $this->input->post('voucherno');
		$warehouse	= $this->input->post('warehouse');
		$transtype	= $this->purchase_model->getTransactionType($voucherno);
		// if ($transtype == 'PO') {
		$details	= $this->purchase_model->getPurchaseOrderDetails($voucherno, $warehouse);
		// // } else {
		// 	$details	= $this->purchase_model->getImportPurchaseOrderDetails($voucherno, $warehouse);
		// }
		$header		= $this->purchase_model->getPurchaseOrderHeader($this->fields_header, $voucherno);
		$table		= '';
		$success	= true;
		if (empty($details)) {
			// $details	= $this->purchase_model->getImportPurchaseOrderDetails($voucherno, $warehouse);
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
		$temp = $this->input->post($this->fields3);
		foreach ($temp['receiptqty'] as $key => $quantity) {
			if ($quantity < 1) {
				foreach ($this->fields3 as $field) {
					if (is_array($temp[$field])) {
						unset($temp[$field][$key]);
					}
				}
			}
		}
		foreach ($this->fields3 as $field) {
			if (is_array($temp[$field])) {
				$data[$field] = array_values($temp[$field]);
			} else {
				$data[$field] = $temp[$field];
			}
		}
		return $data;
	}

	private function ajax_upload_file()
	{
		$post_data 		= $this->input->post();
		$upload_handler	= new UploadHandler();
		$reference 		= $post_data['reference'];
		$task 			= $post_data['task'];
		$upload_result 	= false;
		unset($post_data['task']);

		if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
			if(!isset($upload_handler->response['files'][0]->error)){
				/**
				 * Generate Attachment Id
				 * @param table
				 * @param group fields
				 * @param custom condition
				 */
				// if ($task=='edit') 
				$attachment_id = $this->purchase_model->getCurrentId("purchasereceipt_attachments", $reference);
				if ($attachment_id=='0'){
					$attachment_id = $this->purchase_model->getNextId("purchasereceipt_attachments","attachment_id");
				}
				// else
					// $attachment_id = $this->purchase_model->getNextId("purchasereceipt_attachments","attachment_id");

				foreach($upload_handler->response['files'] as $key => $row) {
					$post_data['attachment_id'] 	= $attachment_id;
					$post_data['attachment_name'] 	= $row->name;
					$post_data['attachment_type'] 	= $row->type;
					$post_data['attachment_url']	= $row->url;
				}

				if ($task == 'edit')
					$upload_result 	= $this->purchase_model->replaceAttachment($post_data);
				else
					$upload_result 	= $this->purchase_model->uploadAttachment($post_data);

			}else{
				$upload_result 	= false;
			}
		}
		// if($upload_result && $task == 'listing'){
		// 	/**
		// 	 * Update status of Service Quotation to Approved
		// 	 */
		// 	// $this->service_quotation->updateData(array('stat' => 'Approved'), 'servicequotation', " voucherno = '$reference' ");
		// }
		
		// $result = array(
		// 	'upload_result' => $upload_result,
		// 	'msg'			=> $message
		// );
		// return $result;
		// if (isset($upload_handler->response) && isset($upload_handler->response['files'])) {
		// 	if(!$upload_handler->response['files'][0]->error){
		// 		/**
		// 		 * Generate Attachment Id
		// 		 * @param table
		// 		 * @param group fields
		// 		 * @param custom condition
		// 		 */
		// 		$attachment_id = $this->case->getNextId("claim_report_attachments","attachment_id"," AND caseno = '$caseno' AND report_id = '$report' ");

		// 		foreach($upload_handler->response['files'] as $key => $row) {
		// 			if ($row->deleteUrl) {
		// 				$post_data['attachment_id'] 	= $attachment_id;
		// 				$post_data['attachment_name'] 	= $row->name;
		// 				$post_data['type'] 				= $row->type;
		// 				$post_data['url'] uploadAttachmentl;
		// 			}
		// 		}
		// 		$this->case->uploadAttachmuploadAttachment
		// 	}else{
		// 		foreach($upload_handler->ruploadAttachment => $row) {
		// 			if ($row->deleteUrl) {uploadAttachment
		// 				$post_data['attachment_id'] 	= $attachment_id;
		// 				$post_data['attachment_name'] 	= $row->name;
		// 				$post_data['type'] 				= $row->type;
		// 				$post_data['url'] 				= $row->url;
		// 			}
		// 		}
		// 		$this->case->deleteAttachment($post_data);
		// 	}
		// }
	}

}