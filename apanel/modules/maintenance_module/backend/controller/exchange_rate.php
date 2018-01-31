<?php
	class controller extends wc_controller 
	{
		public function __construct()
		{
			parent::__construct();

			$this->exchange_rate 	=	new exchange_rate();
			$this->input 		    =	new input();
			$this->ui 			    = 	new ui();
			$this->url 			    =	new url();
			$this->log 				= 	new log();

			$this->view->header_active = 'maintenance/exchange_rate/';

			$this->fields = array(
				'basecurrencycode',
				'exchangecurrencycode',
                'exchangerate',
                'effectivedate',
				'code'
			);
		}

		public function listing(){
			$this->view->title = 'Exchange Rate Listing';
			$data['ui'] 	   = $this->ui;
			$this->view->load('exchange_rate/exchange_rate_list', $data);
		}

		public function create()
		{
			$this->view->title = 'Add Exchange Rate';

			$data 				    = $this->input->post($this->fields);
            $data['currencylist']   = $this->exchange_rate->retrieveExchangeRateDropdown();
			$data['effectivedate'] 	= $this->date->dateFormat();
			$data['ui'] 		    = $this->ui;
			$data['task'] 		    = 'add';
			$data['show_input']     = true;
			$data['ajax_post'] 	    = '';

			$this->view->load('exchange_rate/exchange_rate', $data);
		}

		public function edit($code)
		{
			$this->view->title = 'Edit Exchange Rate';
			
			$data 			 		= (array) $this->exchange_rate->retrieveExistingRate($this->fields, $code);
			$data['effectivedate'] 	= $this->date->dateFormat($data['effectivedate']);
			$data['currencylist']   = $this->exchange_rate->retrieveExchangeRateDropdown();
			$data['ui'] 			= $this->ui;
			$data['task'] 			= 'update';
			$data['show_input'] 	= true;
			$data['ajax_post'] 		= "&code=$code";

			$this->view->load('exchange_rate/exchange_rate',  $data);
		}

		public function view($code)
		{
			$this->view->title 	= 'View Exchange Rate';
			
			$data 			 		= (array) $this->exchange_rate->retrieveExistingRate($this->fields, $code);
			$data['effectivedate'] 	= $this->date->dateFormat($data['effectivedate']);
			
			$data['currencylist']   = $this->exchange_rate->retrieveExchangeRateDropdown();
			$data['ui'] 			= $this->ui;
			$data['show_input'] 	= false;
			$data['task'] 			= "";
			$data['ajax_post'] 		= "";

			$this->view->load('exchange_rate/exchange_rate', $data);
		}

		public function ajax($task) {
			$ajax = $this->{$task}();
			if ($ajax) {
				header('Content-type: application/json');
				echo json_encode($ajax);
			}
		}
		
		private function exchange_rate_list() {

			$search = $this->input->post("search");
			$sort 	= $this->input->post('sort');
			$limit 	= $this->input->post('limit');
			$list 	= $this->exchange_rate->retrieveListing($search, $sort, $limit);

			$table 	= '';

			if( !empty($list->result) ) :
				foreach ($list->result as $key => $row) {
					
					$dropdown = $this->ui->loadElement('check_task')
										->addView()
										->addEdit()
										->addDelete()
										->addCheckbox()
										->setValue($row->code)
										->draw();

					$table .= '<tr>';
					$table .= '<td align = "center">' . $dropdown . '</td>';
					$table .= '<td>' . $this->date->dateFormat($row->effectivedate) . '</td>';
					$table .= '<td>' . $row->basecurrencycode . '</td>';
					$table .= '<td>' . $row->exchangecurrencycode . '</td>';
					$table .= '<td>' . $row->exchangerate. '</td>';
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

		private function add()
		{
			$posted_data 	= $this->input->post($this->fields);	
			$result  		= $this->exchange_rate->insertExchangeRate($posted_data);
		
			$base 			= $posted_data['basecurrencycode'];
			$exchange 		= $posted_data['exchangecurrencycode'];

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Added New Exchange Rate [$base] to [$exchange].");
			}
			else
			{
				$msg = $result;
			}
			
			return $dataArray = array( "msg" => $msg, "basecurrencycode" => $posted_data["basecurrencycode"], "exchangecurrencycode" => $posted_data["exchangecurrencycode"] );
			
		}

		private function update()
		{
			$posted_data 	= $this->input->post($this->fields);
			$code 			= $this->input->post('code');
			$base 		 	= $this->input->post('base');
			$exchange 		= $this->input->post('exchange');

			$result 		= $this->exchange_rate->updateExchangeRate($posted_data, $code);

			if( $result )
			{
				$msg = "success";
				$this->log->saveActivity("Updated Exchange Rate [$base] to [$exchange] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray 		= array( "msg" => $msg, "basecurrencycode" => $posted_data["basecurrencycode"], "exchangecurrencycode" => $posted_data["exchangecurrencycode"] );
		}

		private function delete()
		{
			$id_array 		= array('id');
			$id       		= $this->input->post($id_array);
			
			$result 		= $this->exchange_rate->deleteExchangeRate($id);
			
			if( empty($result) )
			{
				$msg = "success";
				$this->log->saveActivity("Deleted Exchange Rate(s) [". implode($id, ', ') ."] ");
			}
			else
			{
				$msg = $result;
			}

			return $dataArray = array( "msg" => $msg );
		}
		
	}
?>