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
		
		
		return $result;
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