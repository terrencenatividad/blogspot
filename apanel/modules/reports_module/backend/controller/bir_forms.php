<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui 					= new ui();
		$this->view->header_active 	= 'report/';
		$this->view->title 			= MODULE_NAME;
		$this->bir 					= new bir();
		$this->input    			= new input();
		$session        			= new session();
		
		$this->bir_forms = array(
			'0605' => 'Form 0605',
			'2550M' => 'Form 2550M',
			'2550Q' => 'Form 2550Q',
			'2551Q' => 'Form 2551Q',
			'0619E' => 'Form 0619-E',
			'1601EQ' => 'Form 1601-EE',
			'sales_relief' => 'Sales Relief',
			'purchase_relief' => 'Purchase Relief'
		);

		$this->months 	= $this->monthOptions();
		$this->years	= $this->yearOptions();
	}

	public function view() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "1601EQ";
		$data['bir_forms'] 		= $this->bir_forms;
		$data['months'] 		= $this->months;
		$data['years'] 			= $this->years;

		$data['year'] 			= $this->getDate('year');
		$data['quarter']		= $this->getDate('quarter');

		$company_info 			= $this->bir->getCompanyInfo(
										array('businessline','tin','rdo_code','lastname','firstname','middlename','companyname','address','postalcode','phone','email')
									);
		$businessline			= $company_info->businessline;
		$data['tin']			= $company_info->tin;
		$data['rdo_code']		= $company_info->rdo_code;
		$lastname				= $company_info->lastname;
		$firstname				= $company_info->firstname;
		$middlename				= $company_info->middlename;
		$companyname			= $company_info->companyname;
		$address				= $company_info->address;
		$postalcode				= $company_info->postalcode;
		$contact				= $company_info->phone;
		$email					= $company_info->email;
		$agentname				= (strtolower($businessline) == 'individual') ? $lastname.', '.$firstname.', '.$middlename : $companyname;
		$data['agentname']		= $agentname;
		$firstaddress			= substr($address, 0, 40);
		$secondaddress			= (strlen($address) > 40) ? substr($address, 40, 30) : "";
		$data['firstaddress']	= $firstaddress;
		$data['secondaddress']	= $secondaddress;
		$data['zipcode']		= $postalcode;
		$data['contact']		= $contact;
		$data['email']			= $email;
		
		$this->view->load('bir/1601EQ', $data);
	}

	public function ajax($task, $form = '') {
		$ajax = $this->{$task}($form);
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	public function print_form($form) {
		$query = http_build_query($this->input->post());
		$url = MODULE_URL.'print_'.$form.'/?'.$query;
		return array(
			'url'	=> $url
		);
	}

	public function print_1601EQ() {
		$company_signatory = $this->bir->getCompanyInfo(array('businessline','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_1601EQ('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
				->setDocumentDetails($this->input->get())
				->setSignatory($company_signatory)
				->drawPDF(MODULE_NAME);
	}

	private function monthOptions(){
		$months = array();
		foreach (range(1, 12) as $month){
			$months[$month] = sprintf("%02d", $month);
		}
		return $months;
	}

	private function yearOptions(){
		$current_year	= date("Y");
		$years			= array();
		for($i = ($current_year-5); $i <= $current_year; $i++){
			$years[$i]	= $i;
		}
		return $years;
	}
	
	private function getDate($type = '')
	{
		$date = date("Y-m-d");
		if(!empty($type)){
			switch ($type) {
				case 'month':
					$date 		= date("m");
					break;
				case 'quarter':
					$curMonth 	= date("m", time());
					$date 		= ceil($curMonth/3);
					break;
				case 'year':
					$date 		= date("Y");
					break;
				default:
					$date = date("Y-m-d");
					break;
			}
		}
		return $date;
	}
}
?>