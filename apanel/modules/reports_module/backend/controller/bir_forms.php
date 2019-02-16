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
			'1604E' => 'Form 1604-E',
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
			array('businesstype','tin','rdo_code','lastname','firstname','middlename','companyname','address','postalcode','phone','email')
		);
		$businesstype			= $company_info->businesstype;
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
		$agentname				= (strtolower($businesstype) == 'individual') ? $lastname.', '.$firstname.', '.$middlename : $companyname;
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

	public function view_2551Q() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "2551Q";
		$data['bir_forms'] 		= $this->bir_forms;
		$data['months'] 		= $this->months;
		$data['years'] 			= $this->years;

		$data['year'] 			= $this->getDate('year');
		$data['quarter']		= $this->getDate('quarter');
		$data['month'] 			= $this->getDate('month');

		$company_info 			= $this->bir->getCompanyInfo(
			array('businessline','businesstype','tin','rdo_code','lastname','firstname','middlename','companyname','address','postalcode','phone','mobile','email')
		);
		$data['atc_list']		= $this->bir->getATCCode();
		
		$businessline			= $company_info->businessline;
		$data['businessline']	= $company_info->businessline;
		$data['businesstype']	= $company_info->businesstype;
		$data['tin']			= $company_info->tin;
		$data['rdo_code']		= $company_info->rdo_code;
		$lastname				= $company_info->lastname;
		$firstname				= $company_info->firstname;
		$middlename				= $company_info->middlename;
		$companyname			= $company_info->companyname;
		$address				= $company_info->address;
		$postalcode				= $company_info->postalcode;
		$contact				= $company_info->phone;
		$mobile					= $company_info->mobile;
		$email					= $company_info->email;
		$agentname				= (strtolower($businessline) == 'individual') ? $lastname.', '.$firstname.', '.$middlename : $companyname;
		$data['agentname']		= $agentname;
		$data['agentname1']		= substr($agentname, 0, 26);
		$firstaddress			= substr($address, 0, 40);
		$secondaddress			= (strlen($address) > 40) ? substr($address, 40, 30) : "";
		$data['firstaddress']	= $firstaddress;
		$data['secondaddress']	= $secondaddress;
		$data['zipcode']		= $postalcode;
		$data['contact']		= $contact;
		$data['mobile']			= $mobile;
		$data['email']			= $email;
		
		$this->view->load('bir/2551Q', $data);
	}

	private function get_atc_details()
	{
		$atc_code 	= $this->input->post('atc_code');
		$quarter 	= $this->input->post('quarter');
		
		$result 	= $this->bir->retrieveATCDetails($atc_code,$quarter);
		
		return $result;
	}

	private function get_tax_details()
	{
		$result 	= $this->bir->retrieveWTAX();
		
		return $result;
	}

	public function view_1604E() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "1604E";
		$data['bir_forms'] 		= $this->bir_forms;
		$data['months'] 		= $this->months;
		$data['years'] 			= $this->years;

		$data['year'] 			= $this->getDate('year');
		$data['quarter']		= $this->getDate('quarter');
		$data['month'] 			= $this->getDate('month');

		$company_info 			= $this->bir->getCompanyInfo(
			array('businessline','businesstype','tin','rdo_code','lastname','firstname','middlename','companyname','address','postalcode','phone','mobile','email')
		);
		$data['atc_list']		= $this->bir->getATCCode();
		
		$businessline			= $company_info->businessline;
		$data['businessline']	= $company_info->businessline;
		$data['businesstype']	= $company_info->businesstype;
		$data['tin']			= $company_info->tin;
		$data['rdo_code']		= $company_info->rdo_code;
		$lastname				= $company_info->lastname;
		$firstname				= $company_info->firstname;
		$middlename				= $company_info->middlename;
		$companyname			= $company_info->companyname;
		$address				= $company_info->address;
		$postalcode				= $company_info->postalcode;
		$contact				= $company_info->phone;
		$mobile					= $company_info->mobile;
		$email					= $company_info->email;
		$agentname				= (strtolower($businessline) == 'individual') ? $lastname.', '.$firstname.', '.$middlename : $companyname;
		$data['agentname']		= $agentname;
		$data['agentname1']		= substr($agentname, 0, 26);
		$firstaddress			= substr($address, 0, 40);
		$secondaddress			= (strlen($address) > 40) ? substr($address, 40, 30) : "";
		$data['address']		= $address;
		$data['zipcode']		= $postalcode;
		$data['contact']		= $contact;
		$data['mobile']			= $mobile;
		$data['email']			= $email;
		
		$this->view->load('bir/1604E', $data);
	}

	public function view_2550q() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "2550Q";
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
		$data['businessline']	= $businessline;
		
		$this->view->load('bir/2550Q', $data);
	}

	public function view_2550m() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "2550M";
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
		$data['businessline']	= $businessline;
		
		$this->view->load('bir/2550M', $data);
	}

	public function view_0619E() {
		$data['ui'] 			= $this->ui;
		$data['bir_form'] 		= "0619E";
		$data['bir_forms'] 		= $this->bir_forms;
		$data['months'] 		= $this->months;
		$data['years'] 			= $this->years;

		$date = date('Y-m', strtotime('next month')) . '-10';
		$data['datefilter']		=  $this->date->dateFormat($date);

		$data['year'] 			= $this->getDate('year');
		$data['month']			= $this->getDate('month');

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
		$data['businessline']	= $businessline;
		
		$this->view->load('bir/0619E', $data);
	}

	public function ajax($task, $form = '') {
		$ajax = $this->{$task}($form);
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

	public function load_list($form){
		$data			= $this->input->post();
		if($form == '1601EQ'){
			$atc 		= $this->bir->getQuarterlyRemittance($data);
			$table 		= '';
			$line 		= 13;
			$length 	= 6;
			$i			= 0;
			$firstperiod	= 0;
			$secondperiod 	= 0;
			$quartertotal 	= 0;
			if($atc){
				$atc_arr = array();
				foreach ($atc as $key => $val) {
					$period 		= $val->period;
					$atccode 		= $val->atccode;
					$taxbase 		= $val->taxbase;
					$taxrate 		= $val->taxrate;
					// $taxwithheld 	= $val->taxwithheld;
					$taxwithheld 	= $taxbase * $taxrate;
					$atc_arr[$atccode]['taxbase'][] 	= $taxbase;
					$atc_arr[$atccode]['taxrate'][] 	= $taxrate;
					if(in_array($period,array(1,4,7,10))){
						$firstperiod	+= $taxwithheld;
					}
					if(in_array($period,array(2,5,8,11))){
						$secondperiod	+= $taxwithheld;
					}
					$quartertotal	+= $taxwithheld;
				}
				foreach ($atc_arr as $atc_key => $atc_val) {
					$atc_code 		= $atc_key;
					$taxbase 		= array_sum($atc_val['taxbase']);
					$taxrate 		= $atc_val['taxrate'][0];
					$taxwithheld 	= $taxbase * $taxrate;
					$taxrate		= $taxrate * 100;

					$table .= '<tr>';
					$table .= '<td>';
					$table .= '<strong>'.$line.'</strong>';
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('atc'.$i)
					->setClass('text-right')
					->setValue($atc_code)
					->setAttribute(
						array(
							'readOnly' => 'readOnly'
						)
					)
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('taxbase'.$i)
					->setClass('text-right')
					->setValue(number_format($taxbase,2))
					->setPlaceholder('0.00')
					->setAttribute(
						array(
							'readOnly' => 'readOnly'
						)
					)
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('taxrate'.$i)
					->setClass('text-right')
					->setPlaceholder('0%')
					->setValue(number_format($taxrate,0).'%')
					->setAttribute(
						array(
							'readOnly' => 'readOnly'
						)
					)
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('taxwithheld'.$i)
					->setClass('text-right')
					->setPlaceholder('0.00')
					->setValue(number_format($taxwithheld,2))
					->setAttribute(
						array(
							'readOnly' => 'readOnly'
						)
					)
					->draw(true);
					$table .= '</td>';

					$table .= '</tr>';

					$length--;
					$line++;
					$i++;
				}
			}

			/**	
			 * Add Blank rows
			 */
			for ($x=0; $x < $length; $x++) { 
				$atc_code = '';
				$table .= '<tr>';
				$table .= '<td>';
				$table .= '<strong>'.$line.'</strong>';
				$table .= '</td>';

				$table .= '<td>';
				$table .= $this->ui->formField('text')
				->setName('atc'.$i)
				->setClass('text-right')
				->setValue('')
				->setAttribute(
					array(
						'readOnly' => 'readOnly'
					)
				)
				->draw(true);
				$table .= '</td>';

				$table .= '<td>';
				$table .= $this->ui->formField('text')
				->setName('taxbase'.$i)
				->setClass('text-right')
				->setValue('')
				->setPlaceholder('0.00')
				->setAttribute(
					array(
						'readOnly' => 'readOnly'
					)
				)
				->draw(true);
				$table .= '</td>';

				$table .= '<td>';
				$table .= $this->ui->formField('text')
				->setName('taxrate'.$i)
				->setClass('text-right')
				->setPlaceholder('0%')
				->setValue('')
				->setAttribute(
					array(
						'readOnly' => 'readOnly'
					)
				)
				->draw(true);
				$table .= '</td>';

				$table .= '<td>';
				$table .= $this->ui->formField('text')
				->setName('taxwithheld'.$i)
				->setClass('text-right')
				->setPlaceholder('0.00')
				->setValue('')
				->setAttribute(
					array(
						'readOnly' => 'readOnly'
					)
				)
				->draw(true);
				$table .= '</td>';

				$table .= '</tr>';
				$line++;
				$i++;
			}
			$result = array(
				'atc_table' 	=> $table,
				'quartertotal'	=> number_format($quartertotal,2),
				'firstmonth'	=> number_format($firstperiod,2),
				'secondmonth'	=> number_format($secondperiod,2)
			);
		}
		else if($form == '1604E'){
			$table='';
			for($i=1; $i<13;$i++){
				$atc 		= $this->bir->retrieveWTAX($data,$i);	
				foreach ($atc as $atc_key => $row) {
					$month = 1;
					if($i == '1'){$month = 'JAN';}
					else if($i == '2'){$month = 'FEB';}
					else if($i == '3'){$month = 'MAR';}
					else if($i == '4'){$month = 'APR';}
					else if($i == '5'){$month = 'MAY';}
					else if($i == '6'){$month = 'JUN';}
					else if($i == '7'){$month = 'JUL';}
					else if($i == '8'){$month = 'AUG';}
					else if($i == '9'){$month = 'SEP';}
					else if($i == '10'){$month = 'OCT';}
					else if($i == '11'){$month = 'NOV';}
					else if($i == '12'){$month = 'DEC';}

					if($row->tax == NULL){
						$row->tax = 0;
					}
					$table .= '<tr>';
					$table .= '<td>';
					$table .= '<strong>'.$month.'</strong>';
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('date'.$i)
					->setClass('text-right date')
					->setValue('')
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('bank'.$i)
					->setClass('text-right bank')
					->setValue('')
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('taxwithheld'.$i)
					->setId('taxwithheld'.$i)
					->setClass('text-right tax')
					->setPlaceholder('0.00')
					->setValue(number_format($row->tax,'2'))					
					->setValidation('decimal')
					->setAttribute(array('readOnly' => 'readOnly'))
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('penalties'.$i)
					->setId('penalties'.$i)
					->setClass('text-right penalties')
					->setPlaceholder('0.00')
					->setValue('')
					->setValidation('decimal')
					->draw(true);
					$table .= '</td>';

					$table .= '<td>';
					$table .= $this->ui->formField('text')
					->setName('totalamount'.$i)
					->setId('totalamount'.$i)
					->setClass('text-right totalamount')
					->setPlaceholder('0.00')
					->setValue(number_format($row->tax,'2'))				
					->setValidation('decimal')
					->setAttribute(array('readOnly' => 'readOnly'))
					->draw(true);
					$table .= '</td>';
					
					$table .= '</tr>';

					
				}
			}
			$result = array(
				'tax_table' 	=> $table
			);
		}
		
		return $result;
	}

	public function print_form($form) {
		$query = http_build_query($this->input->post());
		$url = MODULE_URL.'print_'.$form.'/?'.$query;
		return array(
			'url'	=> $url
		);
	}

	public function print_0619E() {
		$company_signatory = $this->bir->getCompanyInfo(array('businesstype','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_0619E('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
		->setDocumentDetails($this->input->get())
		->setSignatory($company_signatory)
		->drawPDF(MODULE_NAME);
	}

	public function print_1601EQ() {
		$company_signatory = $this->bir->getCompanyInfo(array('businesstype','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_1601EQ('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
		->setDocumentDetails($this->input->get())
		->setSignatory($company_signatory)
		->drawPDF(MODULE_NAME);
	}

	public function print_1604E() {
		$company_signatory = $this->bir->getCompanyInfo(array('businesstype','businessline','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_1604E('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
		->setDocumentDetails($this->input->get())
		->setSignatory($company_signatory)
		->drawPDF(MODULE_NAME);
	}

	public function print_2550Q() {
		$company_signatory = $this->bir->getCompanyInfo(array('businessline','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_2550Q('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
		->setDocumentDetails($this->input->get())
		->setSignatory($company_signatory)
		->drawPDF(MODULE_NAME);
	}

	public function print_2550M() {
		$company_signatory = $this->bir->getCompanyInfo(array('businessline','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_2550M('P', 'mm', array(216,330.2));
		$print->setPreviewTitle(MODULE_NAME)
		->setDocumentDetails($this->input->get())
		->setSignatory($company_signatory)
		->drawPDF(MODULE_NAME);
	}

	public function print_2551Q() {
		$company_signatory = $this->bir->getCompanyInfo(array('businesstype','businessline','signatory_name','signatory_role','signatory_tin'));
		$print = new print_bir_2551Q('P', 'mm', array(216,330.2));
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

	private function getPrivate() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPrivate($period, $year);
		return $result;
	}

	private function getGov() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getGov($period, $year);
		return $result;
	}

	private function getZero() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getZero($period, $year);
		return $result;
	}

	private function getExempt() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getExempt($period, $year);
		return $result;
	}

	private function getNotPurchasesExceeded() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');


		$result = $this->bir->getNotPurchasesExceeded($period, $year);
		return $result;
	}

	private function getPurchasesExceeded() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchasesExceeded($period, $year);
		return $result;
	}

	private function getPurchaseGoods() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseGoods($period, $year);
		return $result;
	}

	private function getPurchaseImport() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseImport($period, $year);
		return $result;
	}

	private function getPurchaseServices() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseServices($period, $year);
		return $result;
	}

	private function getPurchaseNonResident() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseNonResident($period, $year);
		return $result;
	}

	private function getPurchaseNotTax() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseNotTax($period, $year);
		return $result;
	}

	private function getPrivateMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPrivateMonthly($period, $year);
		return $result;
	}

	private function getGovMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getGovMonthly($period, $year);
		return $result;
	}

	private function getZeroMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getZeroMonthly($period, $year);
		return $result;
	}

	private function getExemptMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getExemptMonthly($period, $year);
		return $result;
	}

	private function getNotPurchasesExceededMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getNotPurchasesExceededMonthly($period, $year);
		return $result;
	}

	private function getPurchasesExceededMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchasesExceededMonthly($period, $year);
		return $result;
	}

	private function getPurchaseGoodsMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseGoodsMonthly($period, $year);
		return $result;
	}

	private function getPurchaseImportMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseImportMonthly($period, $year);
		return $result;
	}

	private function getPurchaseServicesMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseServicesMonthly($period, $year);
		return $result;
	}

	private function getPurchaseNonResidentMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseNonResidentMonthly($period, $year);
		return $result;
	}

	private function getPurchaseNotTaxMonthly() {
		$period = $this->input->post('period');
		$year = $this->input->post('year');
		$result = $this->bir->getPurchaseNotTaxMonthly($period, $year);
		return $result;
	}

	private function getMonthYear() {
		$year = $this->input->post('year');
		$month = $this->input->post('month');
		
		$result 	= $this->bir->getTotalRemittance($month, $year);
		return $result;
	}
}
?>
