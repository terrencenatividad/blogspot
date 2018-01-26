<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->url 			= new url();
		$this->taxcodes 	= new taxcodes();
		$this->input        = new input();
		$this->ui 			= new ui();
		$this->logs  		= new log; 
		$this->view->title  = 'Tax Codes';
		$this->show_input 	= true;

		$this->companycode  = COMPANYCODE;
	}

	public function listing()
	{
		$data["ui"]                    = $this->ui;
		$data['show_input']            = false;
		$data['import_error_messages'] = array();
		$data["file_import_result"]    = "";
		$cmp 						   = $this->companycode;

		// For Import
		$errmsg 			= array();
		if(isset($_FILES['import_csv']))
		{
			$headerArray = array('Tax Code','Tax Name','Tax Description','Tax Type','Tax Rate','Sales Account','Purchase Account');
			
			// Open File 
			$filewrite	=  fopen( 'modules/maintenance_module/files/error_tax_import.txt', "w+");
			
			$file_types = array( "text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			// Validate File Type
			if(!in_array($_FILES['import_csv']['type'],$file_types))
			{
				$errmsg[]	= "Invalid file type, file must be CSV(Comma Separated Values) File.<br/>";
				fwrite($filewrite, "Invalid file type, file must be CSV(Comma Separated Values) File.\n");
			}

			$file		= fopen($_FILES['import_csv']['tmp_name'],"r");

			// Validate File Contents
			$docData	= array();
			$i			= 0;
			$row		= 2;

			while (($file_data = fgetcsv($file, 1000, ",")) !== FALSE) 
			{
				if(!array_intersect($file_data, $headerArray))
				{
					$taxcode		   = addslashes(htmlentities(trim($file_data[0])));
					$taxname		   = addslashes(htmlentities(trim($file_data[1])));
					$description	   = addslashes(htmlentities(trim($file_data[2])));
					$taxtype		   = addslashes(htmlentities(trim($file_data[3])));
					$taxrate		   = addslashes(htmlentities(trim($file_data[4])));
					$salesaccount	   = addslashes(htmlentities(trim($file_data[5])));
					$purchaseaccount   = addslashes(htmlentities(trim($file_data[6])));
					
					$isSalesaccount    = '';
					$isPurchaseaccount = '';
						
					$taxrate		   = str_replace(',','',$taxrate);
					
					if(empty($taxcode))
					{
						$errmsg[] 	= "Tax Code is required. Row $row should not be empty.";
						fwrite($filewrite, "Tax Code is required. Row $row should not be empty.\n");
					}
					if(empty($taxname))
					{
						$errmsg[] 	= "Tax Name is required. Row $row should not be empty.";
						fwrite($filewrite, "Tax Name is required. Row $row should not be empty.\n");
					}
					if(empty($taxrate))
					{
						$errmsg[] 	= "Tax Rate is required. Row $row should not be empty.";
						fwrite($filewrite, "Tax Rate is required. Row $row should not be empty.\n");
					}
					if(empty($salesaccount))
					{
						$errmsg[] 	= "Sales Account is required. Row $row should not be empty.";
						fwrite($filewrite, "Sales Account is required. Row $row should not be empty.\n");
					}
					if(empty($purchaseaccount))
					{
						$errmsg[] 	= "Purchase Account is required. Row $row should not be empty.";
						fwrite($filewrite, "Purchase Account is required. Row $row should not be empty.\n");
					}
					
					// Validate Tax Code
					$istaxcode = $this->taxcodes->GetValue("fintaxcode", "fstaxcode", "fstaxcode = '$taxcode'");

					if(!empty($istaxcode))
					{
						$errmsg[] 	= "Tax Code [ <strong>$taxcode</strong> ] on row $row already exists.";
						fwrite($filewrite, "Tax Code [ $taxcode ] on row $row already exists.\n");
					}
					
					
					// Validate Tax Type
					$istaxtype_val = "";
					$istaxtype	   = $this->taxcodes->GetValue("wc_option", "code", "value = '$taxtype' AND type = 'tax_type'", false);
					$istaxtype_val = $istaxtype->code;

					if(empty($istaxtype_val))
					{
						$errmsg[] 	= "Tax Type [ <strong>$taxtype</strong> ] on row $row is not a valid value.";
						fwrite($filewrite, "Tax Type [ $taxtype ] on row $row is not a valid value.\n");
					}
					
					/**CHECK IF VALUES NUMBERIC**/
					if(!is_numeric($taxrate))
					{
						$errmsg[] 	= "Tax Rate [ <strong>".number_format($taxrate,2)."</strong> ] on row $row is not a valid amount.";
						fwrite($filewrite, "Tax Rate [ ".number_format($taxrate,2)." ] on row $row is not a valid amount.\n");
					}
					
					
					/**VALIDATE SALES DEBIT ACCOUNT**/
					if(!empty($salesaccount))
					{
						$sales_account_val  = 0;
						$isSalesaccount		=  $this->taxcodes->GetValue("chartaccount", "id", "accountname = '$salesaccount' AND accounttype != 'P'");
						$sales_account_val  = $isSalesaccount->id;

						if(empty($sales_account_val))
						{
							$errmsg[] 	= "Sales Account [ <strong>$salesaccount</strong> ] on row $row is not a valid account.";
							fwrite($filewrite, "Sales Account [ $salesaccount ] on row $row is not a valid account.\n");
						}
					}
					
					/**VALIDATE PURCHASE DEBIT ACCOUNT**/
					if(!empty($purchaseaccount))
					{
						$pur_account_val 	= 0;
						$isPurchaseaccount	= $this->taxcodes->GetValue("chartaccount", "id", "accountname = '$purchaseaccount' AND accounttype != 'P'");
						$pur_account_val    = $isPurchaseaccount->id;

						if(empty($pur_account_val))
						{
							$errmsg[] 	= "Purchase Account [ <strong>$purchaseaccount</strong> ] on row $row is not a valid account.";
							fwrite($filewrite, "Purchase Account [ $purchaseaccount ] on row $row is not a valid account.\n");
						}
					}
					
					/**ASSIGN TO NEW ARRAY**/
					$docData[$i]["fstaxcode"] = $taxcode;
					$docData[$i]["shortname"] = $taxname;
					$docData[$i]["longname"] = $description;
					$docData[$i]["taxtype"] = $istaxtype_val; 
					$docData[$i]["taxrate"] = $taxrate;
					$docData[$i]["salesAccount"] = $sales_account_val;
					$docData[$i]["purchaseAccount"] = $pur_account_val;
					$i++;
					$row++;
				}
			}
			fclose($filewrite);
			fclose($file);

			$errmsg				           = array_filter($errmsg);
			$data['import_error_messages'] = $errmsg;

			// Insert File Data
			if(!empty($docData) && empty($errmsg))
			{
				$file_import_result = $this->taxcodes->fileInsert($docData);
				$data["file_import_result"] = $file_import_result;
			}
		}
		// End Import

		$this->view->load('taxcodes/taxcodes_list', $data);
	}

	public function create()
	{
		// Initialize variables
		$data = $this->input->post(array(
			'fstaxcode',
			'shortname',
			'longname',
			'taxrate',
			'taxtype',
			'salesAccount',
			'purchaseAccount'
		));

		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data['button_name']   = "Save";
		$data["task"] 		   = "create";
		$data["ajax_post"] 	   = "";

		// Retrieve tax type 
		$data["tax_type_list"] = $this->taxcodes->retrieveTaxType();

		// Retrieve account list
		$data["account_list"]  = $this->taxcodes->retrieveAccountList();

		$this->view->load('taxcodes/taxcodes', $data);
	}

	public function view($fstaxcode)
	{
		$data         			  = (array) $this->taxcodes->retrieveEditData($fstaxcode);
		$data["ui"]   			  = $this->ui;
		
		// Retrieve account list
		$data["account_list"]  	  = $this->taxcodes->retrieveAccountList();

		// Retrieve tax type 
		$data["tax_type_list"] 	  = $this->taxcodes->retrieveTaxType();


		$data['show_input'] 	  = false;
		$data["button_name"] 	  = "Edit";
		$data["task"] 	  		  = "view";
		$data["ajax_post"] 		  = "";

		$this->view->load('taxcodes/taxcodes', $data);
	}

	public function edit($fstaxcode)
	{
		$data         		   = (array) $this->taxcodes->retrieveEditData($fstaxcode);

		$data["ui"]            = $this->ui;
		$data['show_input']    = $this->show_input;
		$data['button_name']   = "Edit";
		$data["task"] 		   = "edit";

		// Retrieve tax type 
		$data["tax_type_list"] = $this->taxcodes->retrieveTaxType();

		// Retrieve account list
		$data["account_list"]  = $this->taxcodes->retrieveAccountList();
		$data["ajax_post"] 	   = "&fstaxcode_=$fstaxcode";
		// $data["ajax_post"] 	   = "&fstaxcode_=$fstaxcode&companycode=$companycode";

		$this->view->load('taxcodes/taxcodes', $data);
	}

	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'create') 
		{
			$this->add();
		}
		else if ($task == 'edit') 
		{
			$this->update();
		}
		else if ($task == 'delete') 
		{
			$this->delete();
		}
		else if ($task == 'load_list') 
		{
			$this->load_list();
		}
		else if ($task == 'export') 
		{
			$this->export();
		}
	}

	private function load_list()
	{
		$search = $this->input->post("search");
		$sort 	= $this->input->post("sort");
		$limit 	= $this->input->post("limit");

		$list   = $this->taxcodes->retrieveData($search, $sort, $limit);

		$table  = "";

		if( !empty($list->result) ) :
			foreach($list->result as $key => $row)
			{
				$fstaxcode   = $row->fstaxcode;
				$longname    = $row->longname;
				$code        = $row->code;
				$taxrate     = $row->taxrate;

				$sid 		 = $fstaxcode;
				
				$dropdown 	 = $this->ui->loadElement('check_task')
										->addView()
										->addEdit()
										->addDelete()
										->addCheckbox()
										->setValue($sid)
										->draw();

				$table .= '<tr>
							<td align = "center">'.$dropdown.'</td>
							<td>'.$fstaxcode.'</td>
							<td>'.$longname.'</td>
							<td>'.$code.'</td>
							<td>'.$taxrate.'</td>'; 
						'</tr>';
			}
		else:
			$table .= "<tr>
							<td colspan = '5' class = 'text-center'>No Records Found</td>
					  </tr>";
		endif;

		$dataArray = array( "list" => $table, "pagination" => $list->pagination );
		echo json_encode($dataArray);
	}

	private function add()
	{
		$data_var = array(
			'fstaxcode',
			'shortname',
			'longname',
			'taxrate',
			'tax_type',
			'sales_account',
			'purchase_account'
		);

		$data = $this->input->post($data_var);

		$result = $this->taxcodes->insertData($data);

		if($result)
			$msg = "success";
		else
			$msg = "error_add";

		$dataArray = array("msg" => $msg, "tax_code" => $data["fstaxcode"]);
		echo json_encode($dataArray);
	}

	private function update() 
	{
		$data_var = array(
			'fstaxcode',
			'shortname',
			'longname',
			'taxrate',
			'tax_type',
			'sales_account',
			'purchase_account'
		);

		$data_var2 = array(
			'fstaxcode_'
			// 'companycode'
		);
		
		$data = $this->input->post($data_var);
		$data2 = $this->input->post($data_var2);
		extract($data2);

		/**
		* Update Database
		*/
		$result = $this->taxcodes->editData($data, $fstaxcode_);

		if($result)
			$msg = "success";
		else
			$msg = "error_update";

		$dataArray = array( "msg" => $msg );
		echo json_encode($dataArray);
	}

	private function delete()
	{
		$data_var = array('id');
		$id       = $this->input->post($data_var);

		/**
		* Delete Database
		*/
		$result = $this->taxcodes->deleteData($id);

		$dataArray = array( "msg" => $result );
		echo json_encode($dataArray);
	}

	private function export()
	{
		// $cmp    = $this->companycode;
		$result = $this->taxcodes->fileExport();

		$header = array("Tax Code", "Tax Name", "Tax Description", "Tax Type", "Tax Rate", "Sales Account", "Purchase Account"); 
		$csv 	= '';

		$filename = "export_taxes.csv";
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		$fp = fopen('php://output', 'w');
		fputcsv($fp, $header);

		$a 	= array();

		for($i = 0; $i < count($result); $i++)
		{
			$b = array(); 
			
			foreach($result[$i] as $fields)
			{
				$b[] = $fields;
			}
			$a[] = $b;
		}

		foreach ($a as $fields) 
		{
    		fputcsv($fp, $fields);
		}

		exit;
	}
}