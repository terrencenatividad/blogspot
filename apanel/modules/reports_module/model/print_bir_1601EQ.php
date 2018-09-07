<?php
class print_bir_1601EQ extends fpdf {

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
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/1601-EQ-1.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			$yearfilter 	= $documentInfo['yearfilter'];
			$yearfilter 	= implode('   ',str_split($yearfilter));
			$quarter 		= $documentInfo['quarter'];
			$amendreturn 	= isset($documentInfo['amendreturn']) ? $documentInfo['amendreturn'] : "yes";
			$anytaxwithheld	= isset($documentInfo['anytaxwithheld']) ? $documentInfo['anytaxwithheld'] : "yes";
			$attachments	= isset($documentInfo['attachments']) ? $documentInfo['attachments'] : 0;
			$attachments 	= implode('  ',str_split(sprintf('%02d', $attachments)));
			
			$tin 			= (isset($documentInfo['tin']) && !empty($documentInfo['tin'])) ? $documentInfo['tin'] : '000-000-000-000';
			$tin_arr		= explode('-',$tin);
			$tin1			= ($tin_arr[0]) ? $tin_arr[0] : '000';
			$tin1 			= implode('   ',str_split($tin1));
			$tin2			= ($tin_arr[1]) ? $tin_arr[1] : '000';
			$tin2 			= implode('   ',str_split($tin2));
			$tin3			= ($tin_arr[2]) ? $tin_arr[2] : '000';
			$tin3 			= implode('   ',str_split($tin3));
			$tin4			= ($tin_arr[3]) ? $tin_arr[3] : '000';
			$tin4 			= implode('   ',str_split($tin4));

			$rdo 			= isset($documentInfo['rdo']) ? $documentInfo['rdo'] : 0;
			$rdo 			= implode('   ',str_split($rdo));
			$agentname 		= isset($documentInfo['agentname']) ? $documentInfo['agentname'] : "";
			$agentname 		= strtoupper(urldecode($agentname));
			$firstaddress 	= isset($documentInfo['firstaddress']) ? $documentInfo['firstaddress'] : "";
			$firstaddress 	= strtoupper(urldecode($firstaddress));
			$secondaddress 	= isset($documentInfo['secondaddress']) ? $documentInfo['secondaddress'] : "";
			$secondaddress 	= strtoupper(urldecode($secondaddress));
			$zipcode 		= isset($documentInfo['zipcode']) ? $documentInfo['zipcode'] : "";
			$contact 		= isset($documentInfo['contact']) ? $documentInfo['contact'] : "";
			$contact 		= strtoupper(urldecode($contact));
			$category		= isset($documentInfo['category']) ? $documentInfo['category'] : "yes";
			$email 			= isset($documentInfo['email']) ? $documentInfo['email'] : "";
			$email 			= strtoupper(urldecode($email));

			/**
			 * ATC Entries
			 */
			$atc0			= isset($documentInfo['atc0']) ? urldecode($documentInfo['atc0']) : "";
			$atc0 			= str_replace(' ','',$atc0);
			$atc1			= isset($documentInfo['atc1']) ? urldecode($documentInfo['atc1']) : "";
			$atc1 			= str_replace(' ','',$atc1);
			$atc2			= isset($documentInfo['atc2']) ? urldecode($documentInfo['atc2']) : "";
			$atc2 			= str_replace(' ','',$atc2);
			$atc3			= isset($documentInfo['atc3']) ? urldecode($documentInfo['atc3']) : "";
			$atc3 			= str_replace(' ','',$atc3);
			$atc4			= isset($documentInfo['atc4']) ? urldecode($documentInfo['atc4']) : "";
			$atc4 			= str_replace(' ','',$atc4);
			$atc5			= isset($documentInfo['atc5']) ? urldecode($documentInfo['atc5']) : "";
			$atc5 			= str_replace(' ','',$atc5);

			$taxbase0		= isset($documentInfo['taxbase0']) ? urldecode($documentInfo['taxbase0']) : "0.00";
			$taxbase0		= ($taxbase0 > 0) ? $taxbase0 : '';
			$taxbase1		= isset($documentInfo['taxbase1']) ? urldecode($documentInfo['taxbase1']) : "0.00";
			$taxbase1		= ($taxbase1 > 0) ? $taxbase1 : '';
			$taxbase2		= isset($documentInfo['taxbase2']) ? urldecode($documentInfo['taxbase2']) : "0.00";
			$taxbase2		= ($taxbase2 > 0) ? $taxbase2 : '';
			$taxbase3		= isset($documentInfo['taxbase3']) ? urldecode($documentInfo['taxbase3']) : "0.00";
			$taxbase3		= ($taxbase3 > 0) ? $taxbase3 : '';
			$taxbase4		= isset($documentInfo['taxbase4']) ? urldecode($documentInfo['taxbase4']) : "0.00";
			$taxbase4		= ($taxbase4 > 0) ? $taxbase4 : '';
			$taxbase5		= isset($documentInfo['taxbase5']) ? urldecode($documentInfo['taxbase5']) : "0.00";
			$taxbase5		= ($taxbase5 > 0) ? $taxbase5 : '';

			$taxrate0		= isset($documentInfo['taxrate0']) ? urldecode($documentInfo['taxrate0']) : "";
			$taxrate0		= ($taxrate0 > 0) ? $taxrate0 : '';
			$taxrate1		= isset($documentInfo['taxrate1']) ? urldecode($documentInfo['taxrate1']) : "";
			$taxrate1		= ($taxrate1 > 0) ? $taxrate1 : '';
			$taxrate2		= isset($documentInfo['taxrate2']) ? urldecode($documentInfo['taxrate2']) : "";
			$taxrate2		= ($taxrate2 > 0) ? $taxrate2 : '';
			$taxrate3		= isset($documentInfo['taxrate3']) ? urldecode($documentInfo['taxrate3']) : "";
			$taxrate3		= ($taxrate3 > 0) ? $taxrate3 : '';
			$taxrate4		= isset($documentInfo['taxrate4']) ? urldecode($documentInfo['taxrate4']) : "";
			$taxrate4		= ($taxrate4 > 0) ? $taxrate4 : '';
			$taxrate5		= isset($documentInfo['taxrate5']) ? urldecode($documentInfo['taxrate5']) : "";
			$taxrate5		= ($taxrate5 > 0) ? $taxrate5 : '';

			$taxwithheld0		= isset($documentInfo['taxwithheld0']) ? urldecode($documentInfo['taxwithheld0']) : "0.00";
			$taxwithheld0		= ($taxwithheld0 > 0) ? $taxwithheld0 : '';
			$taxwithheld1		= isset($documentInfo['taxwithheld1']) ? urldecode($documentInfo['taxwithheld1']) : "0.00";
			$taxwithheld1		= ($taxwithheld1 > 0) ? $taxwithheld1 : '';
			$taxwithheld2		= isset($documentInfo['taxwithheld2']) ? urldecode($documentInfo['taxwithheld2']) : "0.00";
			$taxwithheld2		= ($taxwithheld2 > 0) ? $taxwithheld2 : '';
			$taxwithheld3		= isset($documentInfo['taxwithheld3']) ? urldecode($documentInfo['taxwithheld3']) : "0.00";
			$taxwithheld3		= ($taxwithheld3 > 0) ? $taxwithheld3 : '';
			$taxwithheld4		= isset($documentInfo['taxwithheld4']) ? urldecode($documentInfo['taxwithheld4']) : "0.00";
			$taxwithheld4		= ($taxwithheld4 > 0) ? $taxwithheld4 : '';
			$taxwithheld5		= isset($documentInfo['taxwithheld5']) ? urldecode($documentInfo['taxwithheld5']) : "0.00";
			$taxwithheld5		= ($taxwithheld5 > 0) ? $taxwithheld5 : '';

			$totalwithheld		= isset($documentInfo['totalwithheld']) ? urldecode($documentInfo['totalwithheld']) : "0.00";
			$totalwithheld		= ($totalwithheld > 0) ? $totalwithheld : '';
			$firstremittance	= isset($documentInfo['firstremittance']) ? urldecode($documentInfo['firstremittance']) : "0.00";
			$firstremittance	= ($firstremittance > 0) ? $firstremittance : '';
			$secondremittance	= isset($documentInfo['secondremittance']) ? urldecode($documentInfo['secondremittance']) : "0.00";
			$secondremittance	= ($secondremittance > 0) ? $secondremittance : '';
			$previouslyfiled	= isset($documentInfo['previouslyfiled']) ? urldecode($documentInfo['previouslyfiled']) : "0.00";
			$previouslyfiled	= ($previouslyfiled > 0) ? $previouslyfiled : '';
			$overremittance		= isset($documentInfo['overremittance']) ? urldecode($documentInfo['overremittance']) : "0.00";
			$overremittance		= ($overremittance > 0) ? $overremittance : '';
			$totalremittance	= isset($documentInfo['totalremittance']) ? urldecode($documentInfo['totalremittance']) : "0.00";
			$totalremittance	= ($totalremittance > 0) ? $totalwithheld : '';

			$taxdue				= isset($documentInfo['taxdue']) ? urldecode($documentInfo['taxdue']) : "0.00";
			$taxdue				= ($taxdue > 0) ? $taxdue : '';
			$surcharge			= isset($documentInfo['surcharge']) ? urldecode($documentInfo['surcharge']) : "0.00";
			$surcharge			= ($surcharge > 0) ? $surcharge : '';
			$interest			= isset($documentInfo['interest']) ? urldecode($documentInfo['interest']) : "0.00";
			$interest			= ($interest > 0) ? $interest : '';
			$compromise			= isset($documentInfo['compromise']) ? urldecode($documentInfo['compromise']) : "0.00";
			$compromise			= ($compromise > 0) ? $compromise : '';
			$penalties			= isset($documentInfo['penalties']) ? urldecode($documentInfo['penalties']) : "0.00";
			$penalties			= ($penalties > 0) ? $penalties : '';
			$amountdue			= isset($documentInfo['amountdue']) ? urldecode($documentInfo['amountdue']) : "0.00";
			$amountdue			= ($amountdue > 0) ? $amountdue : '';
			$remittance			= isset($documentInfo['remittance']) ? urldecode($documentInfo['remittance']) : "0.00";
		}

		$signatory_arr		= $this->signatory;
		$businessline		= $signatory_arr->businessline;
		$signatory_name		= $signatory_arr->signatory_name;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', 'B', '10');
		$this->SetY(42.8);
		$this->SetX(11.5);
		$this->Cell(20, 5, $yearfilter, 0, 0, 'C');

		/**
		 * Quarter
		 */
		$this->SetFont('Arial', 'B', '12');
		if($quarter == 1){
			$this->SetX(41.3);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 2){
			$this->SetX(56.8);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 3){
			$this->SetX(72.1);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 4){
			$this->SetX(87.6);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Amend Return
		 */
		$this->SetFont('Arial', 'B', '12');
		if($amendreturn == "yes"){
			$this->SetX(107.7);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(123.2);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Taxes Withheld
		 */
		$this->SetFont('Arial', 'B', '12');
		if($anytaxwithheld == "yes"){
			$this->SetX(143.3);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(158.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Sheets Attached
		 */
		$this->SetFont('Arial', 'B', '11');
		$this->SetX(184.5);
		$this->Cell(10, 5, $attachments, 0, 0, 'C');

		$this->SetY(54.5);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		
		$this->SetX(80);
		$this->Cell(20, 5, $tin1, 0, 0, 'C');

		$this->SetX(100.5);
		$this->Cell(20, 5, $tin2, 0, 0, 'C');

		$this->SetX(120.5);
		$this->Cell(20, 5, $tin3, 0, 0, 'C');

		$this->SetX(140.5);
		$this->Cell(20, 5, $tin4, 0, 0, 'C');

		/**
		 * RDO Code
		 */
		$this->SetX(194.5);
		$this->Cell(15, 5, $rdo, 0, 0, 'R');

		$this->SetY(64.5);

		/**
		 * Agent's Name
		 */
		$this->SetFont('Arial', 'B', '12');
		$this->SetX(6.4);
		$array = str_split($agentname);
		$this->cellSpacing($array);

		/**
		 * First Address Line
		 */
		$this->SetY(75);
		$this->SetX(6.4);
		$array = str_split($firstaddress);
		$this->cellSpacing($array);

		/**
		 * Second Address Line
		 */
		$this->SetY(81.5);
		$this->SetX(6.4);
		$array = str_split($secondaddress);
		$this->cellSpacing($array);

		/**
		 * ZIP Code
		 */
		$this->SetX(189);
		$array = str_split($zipcode);
		$this->cellSpacing($array);

		/**
		 * Contact number
		 */
		$this->SetY(87.3);
		$this->SetX(37);
		$array = str_split($contact);
		$this->cellSpacing($array);

		/**
		 * Category of Withholding Tax
		 */
		$this->SetFont('Arial', 'B', '11');
		$this->SetY(87.4);
		if($category == "private"){
			$this->SetX(158);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(183.21);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Email
		 */
		$this->SetY(94.5);
		$this->SetX(36.7);
		$array = str_split($email);
		$this->cellSpacing($array);

		$line = 0;
		for ($x=0; $x <=5 ; $x++){
			
			/**
			 * ATC Code
			 */
			$this->SetY(110+$line);
			$this->SetX(11.7);
			$array = str_split(${'atc' . $x});
			$this->cellSpacing($array);

			/**
			 * Tax Base
			 */
			$this->cellAmount(${'taxbase' . $x});

			/**
			 * Tax Rates
			 */
			$this->SetX(112.5);
			$this->Cell(25.5, 5, ${'taxrate' . $x}, 0, 0, 'R');

			/**
			 * Tax Withheld
			 */
			$this->cellAmount(${'taxwithheld' . $x},11);

			$line += 6.5;
		}

		/**	
		 * Total Taxes Withheld
		 */
		$this->SetY(148.5);
		$this->SetX(138);
		$this->cellAmount($totalwithheld,11);

		/**	
		 * Less : Remittances Made > 1st Month
		 */
		$this->SetY(155);
		$this->SetX(138);
		$this->cellAmount($firstremittance,11);
		
		/**	
		 * Less : Remittances Made > 2nd Month
		 */
		$this->SetY(161.5);
		$this->SetX(138);
		$this->cellAmount($secondremittance,11);

		/**	
		 * Tax Remitted in Return Previously Filed
		 */
		$this->SetY(168);
		$this->SetX(138);
		$this->cellAmount($previouslyfiled,11);

		/**	
		 * Over-remittance from previous quarter
		 */
		$this->SetY(174.5);
		$this->SetX(138);
		$this->cellAmount($overremittance,11);

		/**	
		 * Total Remittances
		 */
		$this->SetY(181);
		$this->SetX(138);
		$this->cellAmount($totalremittance,11);

		/**	
		 * Tax Still Due
		 */
		$this->SetY(187.5);
		$this->SetX(138);
		$this->cellAmount($taxdue,11);

		/**	
		 * Surcharge
		 */
		$this->SetY(194);
		$this->SetX(138);
		$this->cellAmount($surcharge,11);

		/**	
		 * Interest
		 */
		$this->SetY(200.5);
		$this->SetX(138);
		$this->cellAmount($interest,11);

		/**	
		 * Compromise
		 */
		$this->SetY(207);
		$this->SetX(138);
		$this->cellAmount($compromise,11);


		/**	
		 * Total Penalties
		 */
		$this->SetY(213.5);
		$this->SetX(138);
		$this->cellAmount($penalties,11);

		/**	
		 * Total Amount Due
		 */
		$this->SetY(220);
		$this->SetX(138);
		$this->cellAmount($amountdue,11);

		/**
		 * Over-Remittance options
		 */
		$this->SetFont('Arial', 'B', '11');
		$this->SetY(225.7);
		if($remittance == "refunded"){
			$this->SetX(67.4);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($remittance == "taxcredit"){
			$this->SetX(94.2);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($remittance == "carriedover"){
			$this->SetX(145.6);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Signatory
		 */
		$this->SetY(245.5);
		$this->SetX(7);
		if(strtolower($businessline) == 'individual'){
			$this->Cell(100.5, 5, $signatory_name, 0, 0, 'C');
			$this->Cell(102, 5, '', 0, 0, 'C');
		}else{
			$this->Cell(100.5, 5,'', 0, 0, 'C');
			$this->Cell(102, 5, $signatory_name, 0, 0, 'C');
		}

		/**
		 * Second Page
		 */
		$this->AddPage();
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/1601-EQ-2.jpg',0,0,216,330.2);
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

		if(empty($amount_decimal_arr[0])){
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

