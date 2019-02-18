<?php
class print_bir_1604E extends fpdf {

	public $font_size		= '';
	public $companyinfo		= array();
	public $documentinfo	= array();
	public $totalinfo		= array();
	public $widths			= '';
	public $aligns			= '';
	public $document_type	= '';
	public $vendor			= '';
	public $payments		= '';
	public $businessline	= '';
	public $signatory		= '';


	public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
		parent::__construct($orientation, $unit, $size);
		$this->db = new db();
		$this->setMargins(8, 8);
	}

	public function setPreviewTitle($title) {
		$this->SetTitle('Oojeema - '.$title, true);
		return $this;
	}

	public function setDocumentDetails($documentinfo) {
		$this->documentinfo = $documentinfo;
		return $this;
	}

	public function setBusinessLine($businessline) {
		$this->businessline = $businessline;
		return $this;
	}

	public function setSignatory($signatory) {
		$this->signatory = $signatory;
		return $this;
	}

	public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}
	
	private function drawDocumentDetails() {
		$this->AddPage();
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/1604E-1.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			$yearfilter 	= $documentInfo['yearfilter'];
			$yearfilter 	= implode('  ',str_split($yearfilter));
			$amendreturn 	= isset($documentInfo['amendreturn']) ? $documentInfo['amendreturn'] : "yes";
			$anytaxwithheld	= isset($documentInfo['anytaxwithheld']) ? $documentInfo['anytaxwithheld'] : "yes";
			$attachments	= isset($documentInfo['attachments']) ? $documentInfo['attachments'] : 0;
			$attachments 	= implode('  ',str_split(sprintf('%02d', $attachments)));
			
			$tin 			= (isset($documentInfo['tin']) && !empty($documentInfo['tin'])) ? $documentInfo['tin'] : '000-000-000-000';
			$tin_arr		= explode('-',$tin);
			$tin1			= ($tin_arr[0]) ? $tin_arr[0] : '000';
			$tin1 			= implode('  ',str_split($tin1));
			$tin2			= ($tin_arr[1]) ? $tin_arr[1] : '000';
			$tin2 			= implode('  ',str_split($tin2));
			$tin3			= ($tin_arr[2]) ? $tin_arr[2] : '000';
			$tin3 			= implode('  ',str_split($tin3));
			$tin4			= ($tin_arr[3]) ? $tin_arr[3] : '000';
			$tin4 			= implode('  ',str_split($tin4));

			$rdo 			= isset($documentInfo['rdo']) ? urldecode($documentInfo['rdo']) : 0;
			$rdo 			= implode('  ',str_split($rdo));
			$agentname 		= isset($documentInfo['agentname']) ? urldecode($documentInfo['agentname']) : "";
			$firstaddress 	= isset($documentInfo['firstaddress']) ? urldecode($documentInfo['firstaddress']) : "";
			$secondaddress 	= isset($documentInfo['secondaddress']) ? urldecode($documentInfo['secondaddress']) : "";
			$zipcode 		= isset($documentInfo['zipcode']) ? urldecode($documentInfo['zipcode']) : "";
			$zipcode 		= implode('   ',str_split($zipcode));
			$contact 		= isset($documentInfo['contact']) ? urldecode($documentInfo['contact']) : "";
			$contact 		= implode('  ',str_split($contact));
			$category		= isset($documentInfo['category']) ? urldecode($documentInfo['category']) : "yes";
			$email 			= isset($documentInfo['email']) ? urldecode($documentInfo['email']) : "";

			/**
			 * Schedule
			 */
			$date0			= isset($documentInfo['date0']) ? urldecode($documentInfo['date0']) : "";
			$date1			= isset($documentInfo['date1']) ? urldecode($documentInfo['date1']) : "";
			$date2			= isset($documentInfo['date2']) ? urldecode($documentInfo['date2']) : "";
			$date3			= isset($documentInfo['date3']) ? urldecode($documentInfo['date3']) : "";
			$date4			= isset($documentInfo['date4']) ? urldecode($documentInfo['date4']) : "";
			$date5			= isset($documentInfo['date5']) ? urldecode($documentInfo['date5']) : "";
			$date6			= isset($documentInfo['date6']) ? urldecode($documentInfo['date6']) : "";
			$date7			= isset($documentInfo['date7']) ? urldecode($documentInfo['date7']) : "";
			$date8			= isset($documentInfo['date8']) ? urldecode($documentInfo['date8']) : "";
			$date9			= isset($documentInfo['date9']) ? urldecode($documentInfo['date9']) : "";
			$date10			= isset($documentInfo['date10']) ? urldecode($documentInfo['date10']) : "";
			$date11			= isset($documentInfo['date11']) ? urldecode($documentInfo['date11']) : "";
			$date12			= isset($documentInfo['date12']) ? urldecode($documentInfo['date12']) : "";

			$bank0			= isset($documentInfo['bank0']) ? urldecode($documentInfo['bank0']) : "";
			$bank1			= isset($documentInfo['bank1']) ? urldecode($documentInfo['bank1']) : "";
			$bank2			= isset($documentInfo['bank2']) ? urldecode($documentInfo['bank2']) : "";
			$bank3			= isset($documentInfo['bank3']) ? urldecode($documentInfo['bank3']) : "";
			$bank4			= isset($documentInfo['bank4']) ? urldecode($documentInfo['bank4']) : "";
			$bank5			= isset($documentInfo['bank5']) ? urldecode($documentInfo['bank5']) : "";
			$bank6			= isset($documentInfo['bank6']) ? urldecode($documentInfo['bank6']) : "";
			$bank7			= isset($documentInfo['bank7']) ? urldecode($documentInfo['bank7']) : "";
			$bank8			= isset($documentInfo['bank8']) ? urldecode($documentInfo['bank8']) : "";
			$bank9			= isset($documentInfo['bank9']) ? urldecode($documentInfo['bank9']) : "";
			$bank10			= isset($documentInfo['bank10']) ? urldecode($documentInfo['bank10']) : "";
			$bank11			= isset($documentInfo['bank11']) ? urldecode($documentInfo['bank11']) : "";
			$bank12			= isset($documentInfo['bank12']) ? urldecode($documentInfo['bank12']) : "";

			$taxwithheld1		= isset($documentInfo['taxwithheld1']) ? urldecode($documentInfo['taxwithheld1']) : "0.00";
			$taxwithheld1		= ($taxwithheld1 != 0) ? $taxwithheld1 : '';
			$taxwithheld1		= str_replace('.','  ',$taxwithheld1);
			$taxwithheld2		= isset($documentInfo['taxwithheld2']) ? urldecode($documentInfo['taxwithheld2']) : "0.00";
			$taxwithheld2		= ($taxwithheld2 != 0) ? $taxwithheld2 : '';
			$taxwithheld2		= str_replace('.','  ',$taxwithheld2);
			$taxwithheld3		= isset($documentInfo['taxwithheld3']) ? urldecode($documentInfo['taxwithheld3']) : "0.00";
			$taxwithheld3		= ($taxwithheld3 != 0) ? $taxwithheld3 : '';
			$taxwithheld3		= str_replace('.','  ',$taxwithheld3);
			$taxwithheld4		= isset($documentInfo['taxwithheld4']) ? urldecode($documentInfo['taxwithheld4']) : "0.00";
			$taxwithheld4		= ($taxwithheld4 != 0) ? $taxwithheld4 : '';
			$taxwithheld4		= str_replace('.','  ',$taxwithheld4);
			$taxwithheld5		= isset($documentInfo['taxwithheld5']) ? urldecode($documentInfo['taxwithheld5']) : "0.00";
			$taxwithheld5		= ($taxwithheld5 != 0) ? $taxwithheld5 : '';
			$taxwithheld5		= str_replace('.','  ',$taxwithheld5);
			$taxwithheld6		= isset($documentInfo['taxwithheld6']) ? urldecode($documentInfo['taxwithheld6']) : "0.00";
			$taxwithheld6		= ($taxwithheld6 != 0) ? $taxwithheld6 : '';
			$taxwithheld6		= str_replace('.','  ',$taxwithheld6);
			$taxwithheld7		= isset($documentInfo['taxwithheld7']) ? urldecode($documentInfo['taxwithheld7']) : "0.00";
			$taxwithheld7		= ($taxwithheld7 != 0) ? $taxwithheld5 : '';
			$taxwithheld7		= str_replace('.','  ',$taxwithheld7);
			$taxwithheld8		= isset($documentInfo['taxwithheld8']) ? urldecode($documentInfo['taxwithheld8']) : "0.00";
			$taxwithheld8		= ($taxwithheld8 != 0) ? $taxwithheld8 : '';
			$taxwithheld8		= str_replace('.','  ',$taxwithheld8);
			$taxwithheld9		= isset($documentInfo['taxwithheld9']) ? urldecode($documentInfo['taxwithheld9']) : "0.00";
			$taxwithheld9		= ($taxwithheld9 != 0) ? $taxwithheld9 : '';
			$taxwithheld9		= str_replace('.','  ',$taxwithheld9);
			$taxwithheld10		= isset($documentInfo['taxwithheld10']) ? urldecode($documentInfo['taxwithheld10']) : "0.00";
			$taxwithheld10		= ($taxwithheld10 != 0) ? $taxwithheld10 : '';
			$taxwithheld10		= str_replace('.','  ',$taxwithheld10);
			$taxwithheld11		= isset($documentInfo['taxwithheld11']) ? urldecode($documentInfo['taxwithheld11']) : "0.00";
			$taxwithheld11		= ($taxwithheld11 != 0) ? $taxwithheld11 : '';
			$taxwithheld11		= str_replace('.','  ',$taxwithheld11);
			$taxwithheld12		= isset($documentInfo['taxwithheld12']) ? urldecode($documentInfo['taxwithheld12']) : "0.00";
			$taxwithheld12		= ($taxwithheld12 != 0) ? $taxwithheld12 : '';
			$taxwithheld12		= str_replace('.','  ',$taxwithheld12);

			$penalties1			= isset($documentInfo['penalties1']) ? urldecode($documentInfo['penalties1']) : "0.00";
			$penalties1			= ($penalties1 != 0) ? $penalties1 : '';
			$penalties1			= str_replace('.','  ',$penalties1);
			$penalties2			= isset($documentInfo['penalties2']) ? urldecode($documentInfo['penalties2']) : "0.00";
			$penalties2			= ($penalties2 != 0) ? $penalties2 : '';
			$penalties2			= str_replace('.','  ',$penalties2);
			$penalties3			= isset($documentInfo['penalties3']) ? urldecode($documentInfo['penalties3']) : "0.00";
			$penalties3			= ($penalties3 != 0) ? $penalties3 : '';
			$penalties3			= str_replace('.','  ',$penalties3);
			$penalties4			= isset($documentInfo['penalties4']) ? urldecode($documentInfo['penalties4']) : "0.00";
			$penalties4			= ($penalties4 != 0) ? $penalties4 : '';
			$penalties4			= str_replace('.','  ',$penalties4);
			$penalties5			= isset($documentInfo['penalties5']) ? urldecode($documentInfo['penalties5']) : "0.00";
			$penalties5			= ($penalties5 != 0) ? $penalties5 : '';
			$penalties5			= str_replace('.','  ',$penalties5);
			$penalties6			= isset($documentInfo['penalties6']) ? urldecode($documentInfo['penalties6']) : "0.00";
			$penalties6			= ($penalties6 != 0) ? $penalties6 : '';
			$penalties6			= str_replace('.','  ',$penalties6);
			$penalties7			= isset($documentInfo['penalties7']) ? urldecode($documentInfo['penalties7']) : "0.00";
			$penalties7			= ($penalties7 != 0) ? $penalties7 : '';
			$penalties7			= str_replace('.','  ',$penalties7);
			$penalties8			= isset($documentInfo['penalties8']) ? urldecode($documentInfo['penalties8']) : "0.00";
			$penalties8			= ($penalties8 != 0) ? $penalties8 : '';
			$penalties8			= str_replace('.','  ',$penalties8);
			$penalties9			= isset($documentInfo['penalties9']) ? urldecode($documentInfo['penalties9']) : "0.00";
			$penalties9			= ($penalties9 != 0) ? $penalties9 : '';
			$penalties9			= str_replace('.','  ',$penalties9);
			$penalties10		= isset($documentInfo['penalties10']) ? urldecode($documentInfo['penalties10']) : "0.00";
			$penalties10		= ($penalties10 != 0) ? $penalties10 : '';
			$penalties10		= str_replace('.','  ',$penalties10);
			$penalties11		= isset($documentInfo['penalties11']) ? urldecode($documentInfo['penalties11']) : "0.00";
			$penalties11		= ($penalties11 != 0) ? $penalties11 : '';
			$penalties11		= str_replace('.','  ',$penalties11);
			$penalties12		= isset($documentInfo['penalties12']) ? urldecode($documentInfo['penalties12']) : "0.00";
			$penalties12		= ($penalties12 != 0) ? $penalties12 : '';
			$penalties12		= str_replace('.','  ',$penalties12);

			$totalamount1		= isset($documentInfo['totalamount1']) ? urldecode($documentInfo['totalamount1']) : "0.00";
			$totalamount1		= ($totalamount1 != 0) ? $totalamount1 : '';
			$totalamount1		= str_replace('.','  ',$totalamount1);
			$totalamount2		= isset($documentInfo['totalamount2']) ? urldecode($documentInfo['totalamount2']) : "0.00";
			$totalamount2		= ($totalamount2 != 0) ? $totalamount2 : '';
			$totalamount2		= str_replace('.','  ',$totalamount2);
			$totalamount3		= isset($documentInfo['totalamount3']) ? urldecode($documentInfo['totalamount3']) : "0.00";
			$totalamount3		= ($totalamount3 != 0) ? $totalamount3 : '';
			$totalamount3		= str_replace('.','  ',$totalamount3);
			$totalamount4		= isset($documentInfo['totalamount4']) ? urldecode($documentInfo['totalamount4']) : "0.00";
			$totalamount4		= ($totalamount4 != 0) ? $totalamount4 : '';
			$totalamount4		= str_replace('.','  ',$totalamount4);
			$totalamount5		= isset($documentInfo['totalamount5']) ? urldecode($documentInfo['totalamount5']) : "0.00";
			$totalamount5		= ($totalamount5 != 0) ? $totalamount5 : '';
			$totalamount5		= str_replace('.','  ',$totalamount5);
			$totalamount6		= isset($documentInfo['totalamount6']) ? urldecode($documentInfo['totalamount6']) : "0.00";
			$totalamount6		= ($totalamount6 != 0) ? $totalamount6 : '';
			$totalamount6		= str_replace('.','  ',$totalamount6);
			$totalamount7		= isset($documentInfo['totalamount7']) ? urldecode($documentInfo['totalamount7']) : "0.00";
			$totalamount7		= ($totalamount7 != 0) ? $totalamount7 : '';
			$totalamount7		= str_replace('.','  ',$totalamount7);
			$totalamount8		= isset($documentInfo['totalamount8']) ? urldecode($documentInfo['totalamount8']) : "0.00";
			$totalamount8		= ($totalamount8 != 0) ? $totalamount8 : '';
			$totalamount8		= str_replace('.','  ',$totalamount8);
			$totalamount9		= isset($documentInfo['totalamount9']) ? urldecode($documentInfo['totalamount9']) : "0.00";
			$totalamount9		= ($totalamount9 != 0) ? $totalamount9 : '';
			$totalamount9		= str_replace('.','  ',$totalamount9);
			$totalamount10		= isset($documentInfo['totalamount10']) ? urldecode($documentInfo['totalamount10']) : "0.00";
			$totalamount10		= ($totalamount10 != 0) ? $totalamount10 : '';
			$totalamount10		= str_replace('.','  ',$totalamount10);
			$totalamount11		= isset($documentInfo['totalamount11']) ? urldecode($documentInfo['totalamount11']) : "0.00";
			$totalamount11		= ($totalamount11 != 0) ? $totalamount11 : '';
			$totalamount11		= str_replace('.','  ',$totalamount11);
			$totalamount12		= isset($documentInfo['totalamount12']) ? urldecode($documentInfo['totalamount12']) : "0.00";
			$totalamount12		= ($totalamount12 != 0) ? $totalamount12 : '';
			$totalamount12		= str_replace('.','  ',$totalamount12);

			$totalwithheld		= isset($documentInfo['totalwithheld']) ? urldecode($documentInfo['totalwithheld']) : "0.00";
			$totalwithheld		= ($totalwithheld != '') ? $totalwithheld : '';
			$totalwithheld		= str_replace('.','  ',$totalwithheld);
			$totalpenalties		= isset($documentInfo['totalpenalties']) ? urldecode($documentInfo['totalpenalties']) : "0.00";
			$totalpenalties		= ($totalpenalties != '') ? $totalpenalties : '';
			$totalpenalties		= str_replace('.','  ',$totalpenalties);
			$total				= isset($documentInfo['total']) ? urldecode($documentInfo['total']) : "0.00";
			$total				= ($total != '') ? $total : '';
			$total				= str_replace('.','  ',$total);

		
		}

		$signatory_arr		= $this->signatory;
		$businesstype		= $signatory_arr->businesstype;
		$businessline		= $signatory_arr->businessline;
		$signatory_name		= $signatory_arr->signatory_name;
		$signatory_role		= $signatory_arr->signatory_role;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', '', '12');
		$this->SetY(35.9);
		$this->SetX(43.5);
		$this->Cell(20, 5, $yearfilter, 0, 0, 'C');

	

		/**
		 * Amend Return
		 */
		$this->SetFont('Arial', '', '12');
		if($amendreturn == "yes"){
			$this->SetX(99);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(114);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Sheets Attached
		 */
		$this->SetFont('Arial', '', '13');
		$this->SetX(187.5);
		$this->Cell(10, 5, $attachments, 0, 0, 'C');

		$this->SetY(45);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		$this->SetFont('Arial', '', '12');
		
		$this->SetX(14);
		$this->Cell(20, 5, $tin1, 0, 0, 'C');

		$this->SetX(32);
		$this->Cell(20, 5, $tin2, 0, 0, 'C');

		$this->SetX(49.5);
		$this->Cell(20, 5, $tin3, 0, 0, 'C');

		$this->SetX(68);
		$this->Cell(20, 5, $tin4, 0, 0, 'C');

		/**
		 * RDO Code
		 */
		$this->SetX(109);
		$this->Cell(15, 5, $rdo, 0, 0, 'R');
	
		$this->SetY(53.5);
		/**
		 * Agent's Name
		 */
		$this->SetFont('Arial', '', '11');
		$this->SetX(17);
		$this->Cell(15, 5, $agentname, 0, 0, 'L');

		/**
		 * Contact number
		 */
		$this->SetX(170);
		$this->Cell(15, 5, $contact, 0, 0, 'L');

		$this->SetY(62);		
		/**
		 * First Address Line
		 */
		$this->SetFont('Arial', '', '9');
		$this->SetX(17);
		$this->Cell(15, 5, $firstaddress, 0, 0, 'L');

		/**
		 * ZIP Code
		 */
		$this->SetFont('Arial', '', '12');		
		$this->SetTextColor(0,0,0);
		$this->SetY(62);		
		$this->SetX(178);
		$this->Cell(15, 5, $zipcode, 0, 0, 'L');


		/**
		 * Category of Withholding Tax
		 */
		$this->SetFont('Arial', '', '11');
		$this->SetY(67);
		if($category == "private"){
			$this->SetX(59);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(78);			
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Email
		 */
		$this->SetY(94.5);
		$this->SetX(36.7);
		$array = str_split($email);
		$this->cellSpacing($array);

		$line = 1;
		for ($x=1; $x <=12 ; $x++){
			
			/**
			 * Date
			 */
			$this->SetFont('Arial', '', '9');
			$this->SetTextColor(0,0,0);
			$this->SetY(88+$line);
			$this->SetX(22.5);
			$this->Cell(20, 4, ${'date' . $x}, 0, 0, 'C');

			/**
			 * Tax Base
			 */
			$this->SetX(42);
			$this->Cell(43, 4, ${'bank' . $x}, 0, 0, 'C');

			$this->SetFont('Arial', '', '9');
			/**
			 * Tax Withheld
			 */
			$this->SetX(83.5);
			$this->Cell(42, 4, ${'taxwithheld' . $x}, 0, 0, 'R');

			/**
			 * Penalties
			 */
			$this->SetX(130.2);
			$this->Cell(31, 4, ${'penalties' . $x}, 0, 0, 'R');

			/**
			 * Total Amount Remitted
			 */
			$this->SetX(166);
			$this->Cell(40, 4, ${'totalamount' . $x}, 0, 0, 'R');

			$line += 3.9;
		}

		/**	
		 * Total Taxes Withheld
		 */
		$this->SetY(138.5);
		$this->SetX(83.5);
		$this->Cell(42, 4, $totalwithheld, 0, 0, 'R');

		/**	
		 * Total Penalties
		 */
		$this->SetY(139);
		$this->SetX(119.4);
		$this->Cell(42, 4, $totalpenalties, 0, 0, 'R');

		/**	
		 * Total Amount
		 */
		$this->SetY(139);
		$this->SetX(164);
		$this->Cell(42, 4, $total, 0, 0, 'R');

		/**
		 * Signatory
		 */
		$this->SetY(285);
		$this->SetX(20);
		if(strtolower($businesstype) == 'individual'){
			$this->Cell(80, 5, $signatory_name, 0, 0, 'C');
		}else{
			$this->Cell(80, 5, $signatory_name, 0, 0, 'C');
		}
		$this->SetX(110);
		$this->Cell(40, 5, $signatory_role, 0, 0, 'C');

		/**
		 * Second Page
		 */
		$this->AddPage();
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/1604E-2.jpg',0,0,216,330.2);
	}

	private function cellSpacing($array = [],$cs = 5.05) {
		foreach ($array as $char) {
			if($char!=''){
				$this->SetTextColor(0,0,0);
				$this->Cell($cs, 5, $char, 0, 0, 'C');
			}else{
				$this->SetTextColor(255,255,255);
				$this->Cell($cs, 5, $char, 0, 0, 'C');
			}
		}
	}

	private function cellAmount($amount,$char_limit=12){
		$amount_arr			= explode('.',$amount);
		$amount_decimal_arr	= explode(',',$amount_arr[0]);
		$amount_decimal_arr	= array_reverse($amount_decimal_arr);
		if($amount < 0 && (strlen($amount) > 5 && (strlen($amount) % 2 != 0))){
			$curX 	= $this->GetX();
			$newX	= $curX - 4.9;
			$this->SetX($newX);
		}
		if(empty($amount_decimal_arr[3])){
			$array = ($char_limit == 12) ? str_split('   ') : str_split('  ');
		}else{
			$array = str_split(str_pad($amount_decimal_arr[3],3," ",STR_PAD_LEFT));
		}
		$this->cellSpacing($array);

		if(empty($amount_decimal_arr[2])){
			$array = str_split('   ');
		}else{
			$array = str_split(str_pad($amount_decimal_arr[2],3," ",STR_PAD_LEFT));
		}
		$this->cellSpacing($array);

		if(empty($amount_decimal_arr[1])){
			$array = str_split('   ');
		}else{
			$array = str_split(str_pad($amount_decimal_arr[1],3," ",STR_PAD_LEFT));
		}
		$this->cellSpacing($array);

		if($amount_decimal_arr[0] == ''){
			$array = str_split('   ');
		}else{
			$array = str_split(str_pad($amount_decimal_arr[0],3," ",STR_PAD_LEFT));
		}
		$this->cellSpacing($array);

		$decimal_position = $this->GetX() + 5.9;
		$this->SetX($decimal_position);
		if(empty($amount_arr[1])){
			$this->SetTextColor(255,255,255);
			$array = str_split('   ');
		}else{
			$this->SetTextColor(0,0,0);
			$array = str_split($amount_arr[1]);
		}
		$this->cellSpacing($array);
	}
}

