<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->ui 			= 	new ui();
			$this->discount 	= 	new discount();
			$this->input 		=	new input();
			$this->url 			=	new url();
			$this->log 			= 	new log();

			$this->view->header_active = 'maintenance/discount/';

			$this->fields = array(
				'discountcode',
				'discountname',
				'discountdesc',
				'discounttype',
				'disc_1',
				'disc_2',
				'disc_3',
				'disc_4'
			);
		}
		
		public function create()
		{
			$data 					= $this->input->post($this->fields);

			$data['discountchoice'] = array('percentage'=>"Percentage", 'amouunt'=>'Amount');

			$data['discount_list'] 	= $this->discount->retrieveDefaultDiscountTableList();

			$data['disc_1'] 		= $data['disc_2'] 	= 	$data['disc_3'] 	= 	$data['disc_4'] 	= '0.00';

			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= true;
			$data['task'] 			= 'add';
			$data['ajax_post'] 		= '';

			$this->view->title 	= $this->ui->AddLabel('');
			$this->view->load('discount/discount_create', $data);
		}

		public function edit($code)
		{
			$this->view->title 	= $this->ui->EditLabel('');

			$data 			 		= (array) $this->discount->retrieveExistingDiscount($this->fields, $code);

			$data['discountchoice'] = array('percentage'=>"Percentage", 'amouunt'=>'Amount');
			$data['discount_list'] 	= $this->discount->retrieveDefaultDiscountTableList();

			$data['task'] 			= 'update';
			$data['show_input'] 	= true;
			$data['ui'] 			= $this->ui;
			$data['ajax_post'] 		= "&code=$code";

			$this->view->load('discount/discount_create',$data);
		}

		public function view($code)
		{
			$this->view->title = $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->discount->retrieveExistingDiscount($this->fields, $code);
			$data['discountchoice'] = array('percentage'=>"Percentage", 'amouunt'=>'Amount');
			$data['discount_list'] 	= $this->discount->retrieveDefaultDiscountTableList();

			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['task'] 		= 'view';
			$data['ajax_post'] 	= "";

			$this->view->load('discount/discount_create',  $data);
		}

		public function tag_customers($code)
		{
			$this->view->title = 'Tag Customers to Discount';

			$data 			 	= (array) $this->discount->retrieveExistingDiscount($this->fields, $code);
			$data['discountchoice'] = array('percentage'=>"Percentage", 'amouunt'=>'Amount');
			//$data['customer_list']  = $this->discount->retrieveCustomerListing($code);
			
			$data['ui'] 		= $this->ui;
			$data['show_input'] = true;
			$data['task'] 		= 'apply_to_discount';
			$data['ajax_post']  = "&discount_code=$code";

			$this->view->load('discount/discount_tag',  $data);
		}

		public function listing()
		{
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('discount/discount_list', $data);
		}

		public function ajax($task) 
		{
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}

		public function get_import(){
			header('Content-type: application/csv');
			$header = array('Discount Code','Discount Name','Discount Description','Discount 1','Discount 2','Discount 3','Discount 4');
			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"DISC_1","Manila","Discount 1","5","5","0","0"';
			$return .= "\n";
			$return .= '"DISC_2","Caloocan","Discount 2","10","0","0","0"';
			echo $return;
		}

		public function get_import_customers(){
			header('Content-type: application/csv');
			$header = array('Customer Code');
			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"CUS001"';
			$return .= "\n";
			$return .= '"CUS002"';
			echo $return;
		}
		
		private function save_import(){
			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext","application/octet-stream");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Discount Code','Discount Name','Discount Description','Discount 1','Discount 2','Discount 3','Discount 4');
			
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
				$list 				= 	array();
				$uniqdiscountcode_ 	=	array();
				
				$prev 			=	'';
				$prev_name  	=	'';
				$prev_desc 		= 	'';
				$prev_disc1 	=	0;
				$prev_disc2 	=	0;
				$prev_disc3 	=	0;
				$prev_disc4 	=	0;
				
				foreach ($z as $b) 
				{
					if ( ! empty($b)) 
					{	
						$discountcode 	   	= (!empty($b[0])) ?	$b[0] 	: 	$prev;
						$discountname       = (!empty($b[1])) ? $b[1] 	: 	$prev_name; 
						$description        = (!empty($b[2])) ? $b[2] 	: 	$prev_desc; 
						$discount1          = (!empty($b[3])) ? $b[3] 	: 	$prev_disc1; 
						$discount2          = (!empty($b[4])) ? $b[4] 	: 	$prev_disc2; 
						$discount3 			= (!empty($b[5])) ? $b[5] 	: 	$prev_disc3; 
						$discount4 			= (!empty($b[6])) ? $b[6] 	: 	$prev_disc4; 
						// $customercode 		= $b[7];

						$prev 				= $discountcode;
						$prev_name 			= $discountname;
						$prev_desc 			= $description;
						$prev_disc1 		= $discount1;
						$prev_disc2 		= $discount2;
						$prev_disc3 		= $discount3;
						$prev_disc4 		= $discount4;

						// Check if Discount exists
						$discount_exists 	= $this->discount->check_duplicate($discountcode,'discount',"discountcode");
						$disc_count  		= $discount_exists[0]->count;

						if( $disc_count > 0 )
						{
							$errmsg[]	= "Discount Code [<strong>$discountcode</strong>] on row $line already exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						
						if(!is_numeric($discount1)){
							$errmsg[] 	= "Discount 1 [ <strong>$discount1</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if(!is_numeric($discount2)){
							$errmsg[] 	= "Discount 2 [ <strong>$discount2</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if(!is_numeric($discount3)){
							$errmsg[] 	= "Discount 3 [ <strong>$discount3</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if(!is_numeric($discount4)){
							$errmsg[] 	= "Discount 4 [ <strong>$discount4</strong> ] on row $line is not a valid amount.<br/>";
							$errmsg		= array_filter($errmsg);
						}
					
						if( !in_array($discountcode, $uniqdiscountcode_) ){
							$uniqdiscountcode_[]= $discountcode;
							$discountname_[]	= $discountname;
							$description_[]		= $description;
							$discount1_[]		= $discount1;
							$discount2_[]		= $discount2;
							$discount3_[]		= $discount3;
							$discount4_[] 		= $discount4;
						}

						$line++;
					}
				}

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$posted_discount = array(
						'discountcode'		=> $uniqdiscountcode_,
						'discountname'		=> $discountname_,
						'discountdesc'		=> $description_,
 						'disc_1'			=> $discount1_,
						'disc_2'			=> $discount2_,
						'disc_3'	 	    => $discount3_,
						'disc_4'		 	=> $discount4_
					);

					$proceed  				= $this->discount->importDiscounts($posted_discount);
			
					if( $proceed )
					{
						$this->log->saveActivity("Imported Discounts.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		private function save_import_customers(){

			$discountcode 	=	$this->input->post('discountcode');

			$file		= fopen($_FILES['file']['tmp_name'],'r') or exit ("File Unable to upload") ;

			$filedir	= $_FILES["file"]["tmp_name"];
	
			$file_types = array( "text/x-csv","text/tsv","text/comma-separated-values", "text/csv", "application/csv", "application/excel", "application/vnd.ms-excel", "application/vnd.msexcel", "text/anytext","application/octet-stream");

			/**VALIDATE FILE IF CORRUPT**/
			if(!empty($_FILES['file']['error'])){
				$errmsg[] = "File being uploaded is corrupted.<br/>";
			}

			/**VALIDATE FILE TYPE**/
			if(!in_array($_FILES['file']['type'],$file_types)){
				$errmsg[]= "Invalid file type, file must be .csv.<br/>";
			}
			
			$headerArr = array('Customer Code');
			
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
				
				$line 				=	1;
				$customercode_ 		= 	array();
				
				foreach ($z as $b) 
				{
					if ( ! empty($b)) 
					{	
						$customercode 	   	= (!empty($b[0])) ?	$b[0] 	: 	'';

						//check if customer code exists
						$customer_exists 	= $this->discount->check_duplicate($customercode,'partners',"partnercode");
						$cust_count  		= $customer_exists[0]->count;

						if( $customercode != "" && $cust_count <= 0 )
						{
							$errmsg[]	= "Customer Code [<strong>$customercode</strong>] on row $line does not exists.<br/>";
							$errmsg		= array_filter($errmsg);
						} 

						// Check if Customer-Discount pair exists in Database
						$dc_exists 			= $this->discount->check_duplicate_pair($customercode, $discountcode);
						$dc_count  			= (!empty($dc_exists)) ? $dc_exists[0]->count 	: 	0 ;
			
						if( $dc_count > 0 )
						{
							$errmsg[]	= "The [<strong>$discountcode - $customercode</strong>] pair on row $line already exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						// Check if Customer already exists in a Discount within document 
						if( $customercode != ""  && !in_array($customercode, $customercode_) ){
							$customercode_[] 	=	$customercode;
						}
						else if( $customercode != "" )
						{
							$errmsg[]	= "Customer Code [<strong>$customercode</strong>] on row $line has a duplicate within the document.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						$line++;
					}
				}

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$posted_pair 	=	array(
						'discountcode' 		=> $discountcode,
						'customercode' 		=> $customercode_
					);

					$proceed 			= $this->discount->importPair($posted_pair);					
			
					if( $proceed )
					{
						$this->log->saveActivity("Imported Customers For Discount [$discountcode] .");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->discount->insertDiscount($posted_data);

			$discountcode 	= $this->input->post('discountcode');

			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Added New Discount [$discountcode] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}
		 
		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->discount->updateDiscount($posted_data, $code);

			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Updated Discount [$code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function apply_to_discount()
		{
			$discount_code 		= $this->input->post('discount_code');
			$tagged_customers 	= $this->input->post('tagged');	

			$result 		= $this->discount->tagCustomer($discount_code, $tagged_customers);
			
			if( $result )
			{	
				$msg = "success";
				$this->log->saveActivity("Tagged Customer(s) [ ". $tagged_customers ." ] to Discount [$discount_code] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->discount->deleteDiscount($id);
			
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Discount [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}
		
		private function tagging_list(){
			$search = $this->input->post("search");
			$code 	= $this->input->post('code');
			$tagged = $this->input->post('tagged');
			$list 	= $this->discount->retrieveCustomerListing($code, $tagged, $search);

			$table 	= '';
			$count 	= 1;	
			$tagged_ = array();
			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {
					
					$customercode 	=	$row->partnercode;
					$customername 	=	$row->partnername;
					$tagged 		=	$row->tagged;
					// echo $tagged;
					$table .= '<tr>';
					$table .= '<td class="text-center">
									<input id = "'.$customercode.'" type = "checkbox" name = "taggedCustomers[]" value = "'.$customercode.'">
									<input type = "hidden" id = "'.$count.'" class="h_checkboxes" value = "'.$tagged.'">	
								 </td>';
					$table .= '<td class = "show_in_view hidden">&nbsp;</td>';
					$table .= '<td>' . $customercode . '</td>';
					$table .= '<td>' . $customername. '</td>';
					$table .= '<td class = "show_in_view hidden">&nbsp;</td>';
					$table .= '</tr>';

					$count++;
					// if( $tagged != "" && isset($tagged) ){
					// 	$tagged_[] 	=	$tagged;
					// }
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$tag_result 	= 	$this->discount->retrieveTagged($code);
		
			if( !empty($tag_result) ) {
				foreach ($tag_result as $key => $row) {
					if( $row->tagged != null )
					{
						$tagged_[] 	=	$row->tagged;
					}
				}
			}

			$list->table 	=	$table;
			$list->tagged 	=	$tagged_;

			return $list;
		}

		private function selected_list(){
			$search = $this->input->post("search");
			// $code 	= $this->input->post('code');
			$tagged = $this->input->post('tagged');
			$list 	= $this->discount->retrieveSelectedCustomerListing($tagged, $search);

			$table 	= '';
			$footer = '';
			$count 	= 1;
			$flag 	= 0;	

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {
					
					$customercode 	=	$row->partnercode;
					$customername 	=	$row->partnername;
					$tagged 		=	$row->tagged;

					$table .= '<tr>';
					$table .= '<td class="text-center">
									<input id = "'.$customercode.'" type = "checkbox" name = "taggedCustomers[]" value = "'.$customercode.'">
									<input type = "hidden" id = "'.$count.'" class="h_checkboxes" value = "'.$tagged.'">	
								 </td>';
					$table .= '<td class = "show_in_view hidden">&nbsp;</td>';
					$table .= '<td>' . $customercode . '</td>';
					$table .= '<td>' . $customername. '</td>';
					$table .= '<td class = "show_in_view hidden">&nbsp;</td>';
					$table .= '</tr>';

					$count++;
				}
				$footer  	.=	'<div class="btn-group">
									<button type="button" class="btn btn-danger btn-flat" id="btnUntag">Untag</button>
								</div>';
			else:
				$flag 	= 1;
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Customer(s) has been tagged yet.</td>
						</tr>";
				
				$footer  	.=	'<div class="btn-group">
									<button type="button" class="btn btn-danger btn-flat" id="btnUntag" disabled>Untag</button>
								</div>';
			endif;

			$list->table 	=	$table;
			$list->footer 	= 	$footer;
			return $list;
		}

		private function discount_list() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit 	= $this->input->post('limit');
			$list 	= $this->discount->retrieveDiscountListing($search, $sort, $limit);

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
										->addOtherTask(
											'Deactivate',
											'arrow-down',
											$show_activate
										)
										 ->addDelete()
										 ->addOtherTask('Tag Customers', 'bookmark')
										 ->addOtherTask('Import Customers', 'import')
										 ->setValue($row->discountcode)
										 ->draw();

					$table .= '<tr>';
					$table .= '<td>' . $dropdown . '</td>';
					$table .= '<td>' . $row->discountcode . '</td>';
					$table .= '<td>' . $row->discountname. '</td>';
					$table .= '<td>' . $row->discountdesc . '</td>';
					$table .= '<td>' . $status . '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '5' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$list->table 	=	$table;
			$list->csv 		= 	$this->export();
			return $list;
		}

		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->discount->check_duplicate($current);
				$count 	= $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function export(){
			
			// $this->log->saveActivity("Exported Discounts.");

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');

			$header = array('Discount Code','Discount Name','Discount Description','Discount 1','Discount 2','Discount 3','Discount 4');
			
			$prev 	= '';
			$next 	= '';

			$csv = '';
			$csv .= '"' . implode('", "', $header) . '"';
			$csv .= "\n";
			
			$result = $this->discount->export($search, $sort);

			if (!empty($result)){
				foreach ($result as $key => $row){
					
					$prev	= 	$row->discountcode;

					$csv .= '"' . $row->discountcode . '",';
					$csv .= '"' . $row->discountname . '",';
					$csv .= '"' . $row->discountdesc . '",';
					$csv .= '"' . $row->disc_1 . '",';
					$csv .= '"' . $row->disc_2 . '",';
					$csv .= '"' . $row->disc_3 . '",';
					$csv .= '"' . $row->disc_4 . '",';
					
					// $csv .= '"' . $row->customercode . '"';
					$csv .= "\n";

					$next 	= $prev;
				}
			}

			return $csv;
		}

		private function ajax_edit_activate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'active';
	
			$result = $this->discount->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
		
		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('id');
			$data['stat'] = 'inactive';
	
			$result = $this->discount->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}
	}
?>