<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->ui 			= 	new ui();
			$this->pricelist 	= 	new pricelist();
			$this->input 		=	new input();
			$this->url 			=	new url();
			$this->log 			= 	new log();

			$this->view->header_active = 'maintenance/pricelist/';

			$this->fields = array(
				'itemPriceCode',
				'itemPriceName',
				'itemPriceDesc'
			);
		}

        public function ajax($task) 
		{
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}

		private function final_saving($save_status)
		{
			$code 					= $this->input->post('pricelistcode');	

			$isExist 				= $this->pricelist->getValue("price_list", array("itemPriceCode"), "itemPriceCode = '$code'");

			if($isExist[0]->itemPriceCode)
			{
				$update_info				= array();
				$update_info['stat']		= 'active';
				$update_condition			= "itemPriceCode = '$code'";
				$updateTempRecord			= $this->pricelist->updateData($update_info,"price_list",$update_condition);
				$updateTempRecord			= $this->pricelist->updateData($update_info,"price_list_details",$update_condition);

				if( $updateTempRecord )
				{
					$this->log->saveActivity("Created Price List [$code] ");
				}

				if( $updateTempRecord && $save_status == 'final' )
				{
					$this->url->redirect(BASE_URL . 'maintenance/pricelist');
				}
			}
		}

        public function create()
		{
			$data 					= $this->input->post($this->fields);

			$cc_entry_data          = array("itemcode ind","itemcode val");
			$data["itemcodes"] 		= $this->pricelist->getValue("items", $cc_entry_data,'',"itemcode");

			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= true;
			$data['task'] 			= 'create';
			$data['ajax_post'] 		= '';

			$save_status 			= $this->input->post('save');
		
			if( $save_status == "final" )
			{
				$this->final_saving($save_status);
			}

			$this->view->title 	= $this->ui->AddLabel('');
			$this->view->load('pricelist/pricelist', $data);
		}

		public function view($code)
		{
			$retrieved_data 		= $this->pricelist->retrieveExisting($code);

			//Header Data
			$data["itemPriceCode"]  = $retrieved_data["header"]->tpl_code;
			$data["itemPriceName"]  = stripslashes($retrieved_data["header"]->tpl_name);
			$data["itemPriceDesc"]  = stripslashes($retrieved_data["header"]->tpl_desc);
			
			//Details
			$data['details'] 		 = $retrieved_data['details'];

			//Item Codes
			$cc_entry_data          = array("itemcode ind","itemcode val");
			$data["itemcodes"] 		= $this->pricelist->getValue("items", $cc_entry_data,'',"itemcode");

			$data['sid'] 			= $code;
			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= false;
			$data['task'] 			= 'view';
			$data['ajax_post'] 		= '';

			$this->view->title 	= $this->ui->ViewLabel('');
			$this->view->load('pricelist/pricelist', $data);
		}

		public function edit($code)
		{
			$retrieved_data 		= $this->pricelist->retrieveExisting($code);

			//Header Data
			$data["itemPriceCode"]  = $retrieved_data["header"]->tpl_code;
			$data["itemPriceName"]  = stripslashes($retrieved_data["header"]->tpl_name);
			$data["itemPriceDesc"]  = stripslashes($retrieved_data["header"]->tpl_desc);
			
			//Details
			$data['details'] 		 = $retrieved_data['details'];

			//Item Codes
			$cc_entry_data          = array("itemcode ind","itemcode val");
			$data["itemcodes"] 		= $this->pricelist->getValue("items", $cc_entry_data,'',"itemcode");

			$data['sid'] 			= $code;
			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= true;
			$data['task'] 			= 'edit';
			$data['ajax_post'] 		= "&itempricecode=$code";

			$this->view->title 	= $this->ui->EditLabel('');
			$this->view->load('pricelist/pricelist', $data);
		}

        public function listing()
		{
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('pricelist/pricelist_list', $data);
		}

		public function master()
		{
			$data 					= $this->input->post($this->fields);

			$cc_entry_data          = array("itemcode ind","itemcode val");
			$data["itemcodes"] 		= $this->pricelist->getValue("items", $cc_entry_data,'',"itemcode");

			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= true;
			$data['task'] 			= 'create';
			$data['ajax_post'] 		= '';

			$this->view->title 	= "Master List";
			$this->view->load('pricelist/pricelist_masterlist', $data);
		}

		private function master_list()
		{
			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit 	= $this->input->post('limit');
			$list 	= $this->pricelist->retrieve_masterlist($search, $sort, $limit);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$table .= '<tr>';
					$table .= '<td>' . $row->code . '</td>';
					$table .= '<td>' . $row->name. '</td>';
					$table .= '<td>
									<input type="text" name="'.$row->code.'" class="form-control" disabled value="'.number_format($row->itemprice,2).'" data-validation="decimal" placeholder="0.00">
							   </td>';
					$table .= '<td>' . $row->uomcode. '</td>';
					$table .= '<td class="text-center">
									<button type="button" class="btn btn-primary edit_row"><i class="glyphicon glyphicon-pencil"></i></button>
							   </td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '4' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			$list->csv 		= 	$this->export_masterlist();
			return $list;
		}

		private function master_list_purchases(){
			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$list 	= $this->pricelist->retrieve_purchase_masterlist($search, $sort);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$table .= '<tr>';
					$table .= '<td>' . $row->code . '</td>';
					$table .= '<td>' . $row->name. '</td>';
					$table .= '<td>' . number_format($row->purchase,2). '</td>';
					$table .= '<td>' . $row->uomcode. '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			return $list;
		}

        private function price_list() 
		{
			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$list 	= $this->pricelist->retrievepricelistListing($search, $sort);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {
					$stat = $row->stat;
					if($stat == 'active'){
						$status = '<span class="label label-success">ACTIVE</span>';								
					}else{
						$status = '<span class="label label-warning">INACTIVE</span>';
					}
		
					$show_activate 		= ($stat != 'inactive');
					$show_deactivate 	= ($stat != 'active');

					$dropdown = $this->ui->loadElement('check_task')
										 ->addView()
										 ->addEdit()
										 ->addOtherTask(
											'Activate',
											'arrow-up',
											$show_deactivate
										)
										//  ->addDelete()
										 ->addOtherTask('Tag Customers', 'bookmark')
										 ->addOtherTask(
											'Deactivate',
											'arrow-down',
											$show_activate
										)	
										 ->setValue($row->itemPriceCode)
										 ->draw();

					$table .= '<tr>';
					$table .= '<td class="text-center">' . $dropdown . '</td>';
					$table .= '<td>' . $row->itemPriceCode . '</td>';
					$table .= '<td>' . stripslashes($row->itemPriceName). '</td>';
					$table .= '<td>' . stripslashes($row->itemPriceDesc) . '</td>';
					$table .= '<td>' . $status . '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			$list->csv 		= 	$this->export_pricelist();
			return $list;
		}

		private function item_list() 
		{

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$code 	= $this->input->post('plcode');

			$list 	= $this->pricelist->retrieveitemPriceListing($code, $search, $sort);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {

					$table .= '<tr>';
					$table .= '<td>' . $row->itemcode . '</td>';
					$table .= '<td>' . $row->description. '</td>';
					$table .= '<td>' . number_format($row->original,2). '</td>';
					$table .= '<td>' . number_format($row->adjusted_price,2) . '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			return $list;
		}

		private function customer_list()
		{
			$code 		= $this->input->post("plcode");
			$search 	= $this->input->post("search");
		
			$customers  = $this->pricelist->retrieveCustomerListing($code, $search);

			$table 	= '';
			$tagged_= array();

			if( !empty($customers->result) ) :
				$count 	= 	1;
				foreach ($customers->result as $key => $row) {
					$table .= '<tr>';
					$table .= '<td class="hide_in_view text-center">
							  		<input id = "'.$row->partnercode.'" type = "checkbox" name = "taggedCustomers[]" value = "'.$row->partnercode.'" >
									<input id = "'.$count.'" type="hidden" value = "'.$row->tagged.'" class = "h_checkboxes" >
							   </td>';
					$table .= '<td class = "show_in_view hidden">&nbsp;</td>';
					$table .= '<td>' . $row->partnercode . '</td>';
					$table .= '<td>' . $row->partnername. '</td>';
				 	$table .= '<td class = "show_in_view hidden" >&nbsp;</td>';
					$table .= '</tr>';

					$count++;
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$tag_result 	= 	$this->pricelist->retrieveTagged($code);
			
			if( !empty($tag_result) ) {
				foreach ($tag_result as $key => $row) {
					if( $row->tagged != null )
					{
						$tagged_[] 	=	$row->tagged;
					}
				}
			}
	
			$customers->table 	=	$table;
			$customers->tagged 	= 	$tagged_;
			return $customers;
		}

		//Master List
		public function get_import_masterlist(){
			header('Content-type: application/csv');

			$header = array('Item Code','Item Name','Selling Price');

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";

			$lists 	=	$this->pricelist->getImportList();	

			if(!empty($lists))
			{
				foreach($lists as $key){
					$return .= '"'.$key->code.'","'.$key->name.'","0.00"';
					$return .= "\n";
				}
			}
			else{
				$return .= '"CALC001","Scientific Calculator","360.00"';
			}

			echo $return;
		}

		private function save_import_masterlist(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext", "application/octet-stream");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Item Code','Item Name','Selling Price');

			if( empty($errmsg) )
			{
				$row_start = 2;
				//$x = file_get_contents($_FILES['file']['tmp_name']);
				$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

				for ($n = 0; $n < count($x); $n++) {
					if($n==0)
					{
						$layout = count($headerArr);
						$template = count($x);
						$header = $x[$n];
						
						for ($m=0; $m< $layout; $m++)
						{
							$template_header = $header[$m];

							$error = (empty($template_header) && !in_array($template_header,$headerArr)) ? "error" : "";
						}	

						$errmsg[]	= (!empty($error) || $error != "" ) ? "Invalid template. Please download template from the system first.<br/>" : "";
						
						$errmsg		= array_filter($errmsg);

					}

					if ($n > 0) 
					{
						$z[] = $x[$n];
					}
				}
				
				$list 	= 	array();
				$line 	=	1;

				foreach ($z as $b) 
				{
					if ( !empty($b)) 
					{	
						$itemcode 			= trim($b[0]);
						$itemname 			= trim($b[1]);
						$itemprice 			= str_replace(',','',trim($b[2]));

						//Check if Itemcode Exists
						$exists 			= $this->pricelist->check_if_exists('itemcode','items'," itemcode = '$itemcode' ");
						$itemcode_count 	= $exists[0]->count;
			
						if( $itemcode_count <= 0 )
						{
							$errmsg[]	= "Item Code [<strong>$itemcode</strong>] on row $line does not exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						// Check if Price is valid amount & not empty
						if( !empty($itemprice) && !is_numeric($itemprice)){
							$errmsg[] 	= "Selling Price [ <strong>$itemprice</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}
						else if ( $itemprice == "" ){
							$errmsg[] 	= "Selling Price [ <strong>$itemprice</strong> ] on row $line should not be empty.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if( !in_array($itemcode, $list) ){
							$list[] 	=	$itemcode;
						}
						else
						{
							$errmsg[]	= "Item Code [<strong>$itemcode</strong>] on row $line has a duplicate within the document.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if( $itemprice > 0 || $itemprice > 0.00 ){
							$itemcode_[] 		= $itemcode;
							$itemprice_[] 		= $itemprice;
						}

						$line++;
					}
				}

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$posted_tpl = array(
						'itemcode'		=> $itemcode_,
						'itemprice'		=> $itemprice_
					);

					$list_of_codes 	=	implode("','",$itemcode_);

					$proceed 	=	$this->pricelist->deleteData('items_price'," itemcode IN ( '".$list_of_codes."' ) ");
					
					if( $proceed )
					{
						$proceed  				= $this->pricelist->import($posted_tpl,'items_price');
				
						if( $proceed )
						{
							$this->log->saveActivity("Imported Price List.");
						}
					}
						
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		//Price List
		public function get_import(){
			header('Content-type: application/csv');

			$header = array('Price List Code','Price List Name','Description',"Item Code","Adjusted Price");

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"TPL01","TEMPLATE1","TEMPLATE - PENS","PEN001","14"';
			
			echo $return;
		}

		private function save_import(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Price List Code','Price List Name','Description',"Item Code","Adjusted Price");

			if( empty($errmsg) )
			{
				$row_start = 2;
				//$x = file_get_contents($_FILES['file']['tmp_name']);
				$x = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

				for ($n = 0; $n < count($x); $n++) {
					if($n==0)
					{
						$layout = count($headerArr);
						$template = count($x);
						$header = $x[$n];
						
						for ($m=0; $m< $layout; $m++)
						{
							$template_header = $header[$m];

							$error = (empty($template_header) && !in_array($template_header,$headerArr)) ? "error" : "";
						}	

						$errmsg[]	= (!empty($error) || $error != "" ) ? "Invalid template. Please download template from the system first.<br/>" : "";
						
						$errmsg		= array_filter($errmsg);

					}

					if ($n > 0) 
					{
						$z[] = $x[$n];
					}
				}
				
				$line 	=	1;
				$list 			= 	array();
				$uniquecode_ 	=	array();
				
				$prev 			=	'';
				$prev_name  	=	'';
				$prev_desc 		= 	'';

				foreach ($z as $b) 
				{
					if ( !empty($b)) 
					{	
						$itempricecode 	   	= (!empty($b[0])) ?	trim($b[0]) 	: 	$prev;
						$itempricename      = (!empty($b[1])) ? trim($b[1]) 	: 	$prev_name; 
						$description        = (!empty($b[2])) ? trim($b[2]) 	: 	$prev_desc; 
						$itemcode 			= trim($b[3]);
						$itemprice 			= trim($b[4]);
						$name			    = trim($b[1]); 
						$desc			    = trim($b[2]); 

						$prev 				= $itempricecode;
						$prev_name 			= $itempricename;
						$prev_desc 			= $description;

						// Check if Price List exists
						$tpl_exists 		= $this->pricelist->check_duplicate($itempricecode,'price_list',"itemPriceCode");
						$tpl_count  		= $tpl_exists[0]->count;

						if( $tpl_count > 0 )
						{
							$errmsg[]	= "Price List Code [<strong>$itempricecode</strong>] on row $line already exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if(empty($name)){
							$errmsg[] 	= "Price List Name is required. Row $line should not be empty.<br>";
						}
						if(empty($desc)){
							$errmsg[] 	= "Description is required. Row $line should not be empty.<br>";
						}
						
						// Check if Itemcode Exists
						$item_exists 		= $this->pricelist->check_duplicate($itemcode,'items',"itemcode");
						$item_count  		= $item_exists[0]->count;

						if( $item_count <= 0 )
						{
							$errmsg[]	= "Item Code [<strong>$itemcode</strong>] on row $line does not exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						// Check if Price is valid amount & not empty
						if( !empty($itemprice) && !is_numeric($itemprice)){
							$errmsg[] 	= "Adjusted Price [ <strong>$itemprice</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}
						else if ( $itemprice == "" ){
							$errmsg[] 	= "Adjusted Price [ <strong>$itemprice</strong> ] on row $line should not be empty.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						
						// Check if Price List - Itemcode Pair already exists
						// $tpl_code_exists 	= $this->pricelist->check_duplicate_pair('price_list_details', " itemPriceCode = '$itempricecode' AND itemDtlCode = '$itemcode' " );
						// $tpl_code_count 	= (!empty($tpl_code_exists)) ? $tpl_code_exists[0]->count 	: 	0 ;
			
						// if( $tpl_code_count > 0 )
						// {
						// 	$errmsg[]	= "The [<strong>$itempricecode - $itemcode</strong>] pair on row $line already exists.<br/>";
						// 	$errmsg		= array_filter($errmsg);
						// }

						// Check if Itemcode already exists in a Price List within the document
						// if( !in_array($itempricecode, $list) ){
						// 	$list[] 	=	$itempricecode;
						// }
						// else
						// {
						// 	$errmsg[]	= "Price List Code [<strong>$itempricecode</strong>] on row $line has a duplicate within the document.<br/>";
						// 	$errmsg		= array_filter($errmsg);
						// }

						if( !in_array($itempricecode, $uniquecode_) ){
							$uniquecode_[]		= $itempricecode;
							$itempricename_[]	= $itempricename;
							$description_[]		= $description;
						}

						$itempricecode_[] 	= $itempricecode;
  						$itemcode_[] 		= $itemcode;
						$itemprice_[] 		= $itemprice;

						$line++;
					}
				}

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$posted_tpl = array(
						'itemPriceCode'		=> $uniquecode_,
						'itemPriceName'		=> $itempricename_,
						'itemPriceDesc'		=> $description_,
						'stat' 				=> 'active'
					);
					
					$posted_pair 	=	array(
						'itemPriceCode'		=> $itempricecode_,
						'itemDtlCode' 		=> $itemcode_,
						'sellPrice' 		=> $itemprice_
					);

					$proceed  				= $this->pricelist->import($posted_tpl,'price_list');
			
					if( $proceed )
					{
						$proceed 			= $this->pricelist->importPair($posted_pair);

						if( $proceed )
						{
							$this->log->saveActivity("Imported Price List.");
						}
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

        private function export_masterlist()
		{
			
			// $this->log->saveActivity("Exported Item Master List.");

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');

			$header = array('Item Code','Item Name','Selling Price','Selling UOM');

			$csv 	 = '';
			$csv 	.= '"' . implode('","', $header) . '"';
			$csv 	.= "\n";

			$result 	= $this->pricelist->export_masterlist($search, $sort);
			
			if (!empty($result)){
				foreach ($result as $key => $row){

					$csv .= '"' . $row->code . '",';
					$csv .= '"' . $row->name . '",';
					$csv .= '"' . number_format($row->itemprice,2) . '",';
					$csv .= '"' . $row->uomcode . '",';
					$csv .= "\n";
				}
			}

			return $csv;
			
		}

		private function export_pricelist()
		{
			// $this->log->saveActivity("Exported Price List Templates.");

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');

			$header = array('Price List Code','Price List Name','Description',"Item Code","Adjusted Price");

			$prev 	= '';
			$next 	= '';

			$csv 	 = '';
			$csv 	.= '"' . implode('", "', $header) . '"';
			$csv 	.= "\n";

			$result = $this->pricelist->export_pricelist($search, $sort);
			
			if (!empty($result)){
				foreach ($result as $key => $row){

					$prev	= 	$row->itemPriceCode;

					if( $prev != '' && $prev != $next)
					{	
						$csv .= '"' . $row->itemPriceCode . '",';
						$csv .= '"' . $row->itemPriceName . '",';
						$csv .= '"' . $row->itemPriceDesc . '",';
					}
					else
					{
						$csv .= '"",';
						$csv .= '"",';
						$csv .= '"",';
					}
					
					$csv .= '"' . $row->itemDtlCode . '",';
					$csv .= '"' . number_format($row->sellPrice,2) . '"';
					$csv .= "\n";

					$next 	= $prev;
				}
			}

			return $csv;
			
		}

		private function get_item_details()
		{
			$itemcode 	= $this->input->post('itemcode');

			$result 	= $this->pricelist->retrieveItemDetails($itemcode);
			
			return $result;
		}

		private function save_temp_data()
		{
			$data_post 	= $this->input->post();

			$task 		= $this->input->post('task');

			$result    = $this->pricelist->processTransaction($data_post, $task);
				
			if(!empty($result))
			{
				$msg = $result;
			}
			else
			{
				$msg = "success";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->pricelist->check_duplicate($current, 'price_list', 'itemPriceCode');

				$count = $result[0]->count;
			}
					
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function delete_row()
		{
			$table 		= $this->input->post('table');
			$itemcode 	= $this->input->post('itemcode');
			$code 		= $this->input->post('pl_code');

			$cond 		= " itemPriceCode = '$code' AND itemDtlCode = '$itemcode' ";

			$result = $this->pricelist->deleteData($table, $cond);

			if($result)
				$msg = "success";
			else
				$msg = "The system has encountered an error in deleting. Please contact admin to fix this issue.";

			return	$dataArray = array( "msg" => $msg );
		}
		
		private function delete_template()
		{
			$code 			=	$this->input->post('code');

			$data['stat'] 	=	"deleted";
			
			$cond 			=	" itemPriceCode = '$code' ";

			$result 	=	$this->pricelist->updateData($data, "price_list", $cond);

			if( $result )
			{
				$result 	=	$this->pricelist->updateData($data, "price_list_details", $cond);
			}

			if($result == '1')
			{
				$this->log->saveActivity("Deleted Price List Template [$code] ");
				$msg = "success";
			}
			else
			{
				$msg = "Failed to Delete.";
			}

			return $dataArray = array( "msg" => $msg );
		}

		private function cancel()
		{
			$code 		= 	$this->input->post("itempricecode");

			$result 	=	$this->pricelist->delete_temp_transactions($code, "price_list", "price_list_details");

			if($result == '1')
				$msg = "success";
			else
				$msg = "Failed to Delete.";

			return $dataArray = array( "msg" => $msg );
		}

		public function tag_customers($code)
		{
			$this->view->title 		= 'Tag Customers to Price List Template';

			$retrieved_data 		= $this->pricelist->retrieveExisting($code);
	
			//Header Data
			$data["itemPriceCode"]  = $retrieved_data["header"]->tpl_code;
			$data["itemPriceName"]  = stripslashes($retrieved_data["header"]->tpl_name);
			$data["itemPriceDesc"]  = stripslashes($retrieved_data["header"]->tpl_desc);
			
			// $data['customer_list']  = $this->pricelist->retrieveCustomerListing($code);
			
			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= true;
			$data['task'] 			= 'apply_to_template';
			$data['ajax_post']  	= "&itemPriceCode=".$code;

			$this->view->load('pricelist/pricelist_tag',  $data);
		}

		private function apply_to_template()
		{
			$code 				= $this->input->post('itemPriceCode');
			$tagged_customers 	= $this->input->post('tagged');	
		
			$result 			= $this->pricelist->tagCustomer($code, $tagged_customers);
			
			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Tagged Customer(s) [ ". $tagged_customers ." ] to Price List Template [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}
	
		private function save_sp()
		{
			$itemcode 		=	$this->input->post('itemcode');
			$selling_price 	=	$this->input->post('price');
			
			$existing 		=	$this->pricelist->getValue('items_price',array('itemcode')," itemcode = '$itemcode' ");
			$ret_code 		= 	isset($existing[0]->itemcode) 	? 	$existing[0]->itemcode 	:	'';

			if(!empty($ret_code) && !is_null($ret_code) && $ret_code != ""){

				$update_data['itemprice'] 	= str_replace(',','',$selling_price);
				$update_condition			= "itemcode = '$itemcode'";

				$result 	=  	$this->pricelist->updateData($update_data,'items_price',$update_condition);

				if( $result )
				{
					$this->log->saveActivity("Item [ $itemcode ] Price updated to [ $selling_price ].");

					$msg = "success";
				}
				else
				{
					$msg 	=	"Failed.";
				}
			}
			else
			{
				$insert_data['itemcode'] 	= $itemcode;
				$insert_data['itemprice'] 	= str_replace(',','',$selling_price);	

				$result 	=  	$this->pricelist->insertPrice($insert_data);

				if( $result )
				{
					$this->log->saveActivity("Set Price [ $selling_price ] for Item [ $itemcode ].");

					$msg = "success";
				}
				else
				{
					$msg  	=	"Failed.";
				}
			}

			return $dataArray = array("msg" => $msg);
		}

		private function ajax_edit_activate()
		{
			$id = $this->input->post('id');
			$data['stat'] = 'active';
	
			$result = $this->pricelist->updateStat($data,$id);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
	
		private function ajax_edit_deactivate()
		{
			$id = $this->input->post('id');
			$data['stat'] = 'inactive';
	
			$result = $this->pricelist->updateStat($data,$id);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
	}
?>