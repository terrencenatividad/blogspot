<?php
class controller extends wc_controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->companyclass = new companyclass();
		$this->input        = new input();
	}

	public function edit()
	{
		$this->url 			= new url();
		$ui 	 			= new ui();
		$this->view->title  = 'Company';
		
		/**
		 * Initiate Variables
         */
		$data_var = array(
			'companycode' 	=> '',
			'companyname' 	=> '',
			'businesstype'	=> '',
			'contactname'	=> '',
			'contactrole'	=> '',
			'phone'			=> '',
			'mobile'		=> '',
			'address'		=> '',
			'email'			=> ''
		);

		/**
		* Retrieve Data
		*/
		$companydata = $this->companyclass->retrieveData($data_var," companycode = 'CID' ");
		if(!empty($companydata))
		{
			/**
			* Convert retrieved data to array
			*/
			$data_var = (array) $companydata;
		}

		/**
		* Pass ui class object to be tagged 
		*/
        $data_var['ui'] = $ui;
		$this->view->load('company', $data_var);
	}

	/**
	* Function to handle ajax calls
	*/
	public function ajax($task)
	{
		header('Content-type: application/json');

		if ($task == 'add') {
			$this->add();
		}else if($task == 'update'){
			$condition = " companycode = 'CID' ";
			$this->update($condition);
		}
	}

	/**
	* Function to add new record
	*/
	private function add()
	{
		$data_var = array(
			'companycode',
			'companyname',
			'businesstype',
			'contactname',
			'contactrole',
			'phone',
			'mobile',
			'address',
			'email'
		);
		
		/**
		 * Handle POST values
         */
		$data = $this->input->post($data_var);

		/**
		* Insert to Database
		*/
		$result = $this->companyclass->updateData('add',$data);

		$code   = ($result) ? 1 : 0;
		$msg 	= $result;

		$returnArray	= array(
						'code'=>$code,
						'msg'=>$msg
					);
	
		return json_encode($returnArray);
	}

	/**
	* Function to update record
	* @param condition
	*/
	private function update($condition)
	{
		$data_var = array(
			'companycode',
			'companyname',
			'businesstype',
			'contactname',
			'contactrole',
			'phone',
			'mobile',
			'address',
			'email'
		);

		/**
		 * Handle POST values
         */
		$data = $this->input->post($data_var);

		/**
		* Update Database
		*/
		$result = $this->companyclass->updateData('update',$data,$condition);

		$code   = ($result) ? 1 : 0;
		$msg 	= $result;

		$returnArray	= array(
						'code'=>$code,
						'msg'=>$msg
					);
	
		echo json_encode($returnArray);
	}

}