<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->warehouse 	=	new warehouse_model();
			$this->input 		=	new input();
			$this->ui 			= 	new ui();
			$this->url 			=	new url();
			$this->log 			= 	new log();
			$this->view->header_active = 'maintenance/warehouse/';
			
			$this->fields = array(
				'warehousecode',
				'description'
			);
		}

		public function listing()
		{
			$this->view->title = $this->ui->ListLabel('');
			$data['ui'] 	   = $this->ui;
			$this->view->load('warehouse/warehouse_list', $data);
		}
		
		public function create()
		{
			$this->view->title = $this->ui->AddLabel('');
			
			$data 				= $this->input->post($this->fields);

			$data['ui'] 		= $this->ui;
			$data['ajax_task'] 	= 'add';
			$data['show_input'] = true;
			$data['ajax_post'] 	= '';

			$this->view->load('warehouse/warehouse',  $data);
		}

		public function edit($code)
		{
			$this->view->title = $this->ui->EditLabel('');
			
			$data 			 	= (array) $this->warehouse->retrieveExistingWarehouse($this->fields, $code);
			
			$data['ui'] 		= $this->ui;
			$data['ajax_task'] 	= 'update';
			$data['show_input'] = true;
			$data['ajax_post'] 	= "&code=$code";

			$this->view->load('warehouse/warehouse',  $data);
		}

		public function view($code)
		{
			$this->view->title = $this->ui->ViewLabel('');
			
			$data 			 	= (array) $this->warehouse->retrieveExistingWarehouse($this->fields, $code);
			$data['ui'] 		= $this->ui;
			$data['show_input'] = false;
			$data['ajax_task'] 	= '';
			$data['ajax_post'] 	= "";

			$this->view->load('warehouse/warehouse',  $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}

		private function get_duplicate(){
			$current = $this->input->post('curr_code');
			$old 	 = $this->input->post('old_code');
			$count 	 = 0;

			if( $current!='' && $current != $old )
			{
				$result = $this->warehouse->check_duplicate($current);

				$count = $result[0]->count;
			}
			
			$msg   = "";

			if( $count > 0 )
			{	
				$msg = "exists";
			}

			return $dataArray = array("msg" => $msg);
		}

		private function warehouse_list() {

			$searchkey  	= $this->input->post('search');
			$sort 			= $this->input->post('sort');
			$limit 			= $this->input->post('limit');
			
			$pagination 	= $this->warehouse->retrieveListing($searchkey, $sort);

			$table 	= '';

			if( !empty($pagination->result) ) :
				foreach ($pagination->result as $key => $row) {
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
										 ->addCheckbox()
										 ->setValue($row->warehousecode)
										 ->draw();

					$table .= '<tr>';
					$table .= '<td align = "center">' . $dropdown . '</td>';					
					$table .= '<td>' . $row->warehousecode . '</td>';
					$table .= '<td>' . $row->description. '</td>';
					$table .= '<td>' . $status. '</td>';
					$table .= '</tr>';
				}
			else:
				$table .= "<tr>
								<td colspan = '3' class = 'text-center'>No Records Found</td>
						</tr>";
			endif;

			$pagination->table = $table;
			$pagination->csv   = $this->export();
			return $pagination;
		}

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->warehouse->insertWarehouse($posted_data);

			$warehouse 		= $this->input->post('warehousecode');

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Warehouse [$warehouse]");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array(
				'redirect'	=> BASE_URL . 'maintenance/warehouse',
				'msg'		=> $msg
			);
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 		 	= $this->input->post('code');

			$result 		= $this->warehouse->updateWarehouse($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Warehouse [$code]");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array(
				'redirect'	=> BASE_URL . 'maintenance/warehouse',
				'msg'		=> $msg
			);

		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);

			$result 		= $this->warehouse->deleteWarehouse($id);
		
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Warehouse(s) [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array("msg" => $msg);
		}

		public function export(){
			// $this->log->saveActivity("Exported Warehouses.");

			$search = $this->input->post('search');
			$sort 	= $this->input->post('sort');
			
			$header = array("Warehouse Code","Warehouse Name");
			
			$csv = '';
			$csv .= '"' . implode('", "', $header) . '"';
			$csv .= "\n";
			
			$result = $this->warehouse->export($search, $sort);

			if (!empty($result)){
				foreach ($result as $key => $row){

					$csv .= '"' . $row->warehousecode . '",';
					$csv .= '"' . $row->description . '",';
					$csv .= "\n";
				}
			}

			return $csv;
		}

		public function get_import(){
			header('Content-type: application/csv');
			$header = array('Warehouse Code','Warehouse Name');

			$return = '';
			$return .= '"' . implode('","',$header) . '"';
			$return .= "\n";
			$return .= '"WH001","Caloocan"';
			
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
			
			$headerArr = array('Warehouse Code','Warehouse Name');

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
				$list 	=	array();

				foreach ($z as $b) 
				{
					if ( !empty($b)) 
					{	
						$warehousecode 	   	= $b[0];
						$warehousename      = $b[1];

						$exists = $this->warehouse->check_duplicate($warehousecode);
						$count = $exists[0]->count;

						if( $count > 0 )
						{
							$errmsg[]	= "Warehouse Code [<strong>$warehousecode</strong>] on row $line already exists.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						if( !in_array($warehousecode, $list) ){
							$list[] 	=	$warehousecode;
						}
						else
						{
							$errmsg[]	= "Warehouse Code [<strong>$warehousecode</strong>] on row $line has a duplicate within the document.<br/>";
							$errmsg		= array_filter($errmsg);
						}

						//var_dump($errmsg);
						$warehousecode_[] 	= $warehousecode;
						$warehousename_[]	= $warehousename;

						$line++;
					}
				}

				$proceed 	=	false;

				if( empty($errmsg) )
				{
					$post = array(
						'warehousecode'		=> $warehousecode_,
						'description'		=> $warehousename_
					);
					
					$proceed  				= $this->warehouse->importCustomers($post);

					if( $proceed )
					{
						$this->log->saveActivity("Imported Warehouse.");
					}
				}
			}

			$error_messages		= implode(' ', $errmsg);
			
			return array("proceed" => $proceed,"errmsg"=>$error_messages);
		}

		private function ajax_edit_activate()
		{
			$code = $this->input->post('code');
			$data['stat'] = 'active';

			$result = $this->warehouse->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}

		private function ajax_edit_deactivate()
		{
			$code = $this->input->post('code');
			$data['stat'] = 'inactive';

			$result = $this->warehouse->updateStat($data,$code);
			return array(
				'redirect'	=> MODULE_URL,
				'success'	=> $result
				);
		}

		private function update_multiple_deactivate(){
			$posted_data 			=	$this->input->post(array('ids'));
	
			$data['stat'] 			=	'inactive';
			
			$posted_ids 			=	$posted_data['ids'];
			$id_arr 				=	explode(',',$posted_ids);
			
			foreach($id_arr as $key => $value)
			{
				$result 			= 	$this->warehouse->updateStat($data, $value);
			}
	
			if($result)
			{
				$msg = "success";
			} else {
				$msg = "Failed to Update.";
			}
	
			return $dataArray = array( "msg" => $msg );
		}

		private function update_multiple_activate(){
			$posted_data 			=	$this->input->post(array('ids'));
	
			$data['stat'] 			=	'active';
			
			$posted_ids 			=	$posted_data['ids'];
			$id_arr 				=	explode(',',$posted_ids);
			
			foreach($id_arr as $key => $value)
			{
				$result 			= 	$this->warehouse->updateStat($data, $value);
			}
	
			if($result)
			{
				$msg = "success";
			} else {
				$msg = "Failed to Update.";
			}
	
			return $dataArray = array( "msg" => $msg );
		}
	}
?>