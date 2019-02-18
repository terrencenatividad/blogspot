<?php
class print_bir_0619E extends fpdf {

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
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/0619E.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			$monthfilter 	= $documentInfo['monthfilter'];
			$monthfilter 	= implode('  ',str_split($monthfilter));
			$yearfilter 	= $documentInfo['yearfilter'];
			$yearfilter 	= implode('  ',str_split($yearfilter));
			$duedate 		= $documentInfo['duedate'];
			$duedate 		= date('mdY', strtotime(urldecode($duedate)));
			$duedate 		= implode('  ',str_split($duedate));
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

			$rdo 			= isset($documentInfo['rdo']) ? urldecode($documentInfo['rdo']) : 0;
			$rdo 			= implode('   ',str_split($rdo));
			$agentname 		= isset($documentInfo['agentname']) ? urldecode($documentInfo['agentname']) : "";
			$firstaddress 	= isset($documentInfo['firstaddress']) ? urldecode($documentInfo['firstaddress']) : "";
			$secondaddress 	= isset($documentInfo['secondaddress']) ? urldecode($documentInfo['secondaddress']) : "";
			$zipcode 		= isset($documentInfo['zipcode']) ? urldecode($documentInfo['zipcode']) : "";
			$contact 		= isset($documentInfo['contact']) ? urldecode($documentInfo['contact']) : "";
			$category		= isset($documentInfo['category']) ? urldecode($documentInfo['category']) : "yes";
			$email 			= isset($documentInfo['email']) ? urldecode($documentInfo['email']) : "";

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
			$taxbase1		= ($taxbase1 != 0) ? $taxbase1 : '';
			$taxbase2		= isset($documentInfo['taxbase2']) ? urldecode($documentInfo['taxbase2']) : "0.00";
			$taxbase2		= ($taxbase2 != 0) ? $taxbase2 : '';
			$taxbase3		= isset($documentInfo['taxbase3']) ? urldecode($documentInfo['taxbase3']) : "0.00";
			$taxbase3		= ($taxbase3 != 0) ? $taxbase3 : '';
			$taxbase4		= isset($documentInfo['taxbase4']) ? urldecode($documentInfo['taxbase4']) : "0.00";
			$taxbase4		= ($taxbase4 != 0) ? $taxbase4 : '';
			$taxbase5		= isset($documentInfo['taxbase5']) ? urldecode($documentInfo['taxbase5']) : "0.00";
			$taxbase5		= ($taxbase5 != 0) ? $taxbase5 : '';

			$taxrate0		= isset($documentInfo['taxrate0']) ? urldecode($documentInfo['taxrate0']) : "";
			$taxrate0		= ($taxrate0 != 0) ? $taxrate0 : '';
			$taxrate1		= isset($documentInfo['taxrate1']) ? urldecode($documentInfo['taxrate1']) : "";
			$taxrate1		= ($taxrate1 != 0) ? $taxrate1 : '';
			$taxrate2		= isset($documentInfo['taxrate2']) ? urldecode($documentInfo['taxrate2']) : "";
			$taxrate2		= ($taxrate2 != 0) ? $taxrate2 : '';
			$taxrate3		= isset($documentInfo['taxrate3']) ? urldecode($documentInfo['taxrate3']) : "";
			$taxrate3		= ($taxrate3 != 0) ? $taxrate3 : '';
			$taxrate4		= isset($documentInfo['taxrate4']) ? urldecode($documentInfo['taxrate4']) : "";
			$taxrate4		= ($taxrate4 != 0) ? $taxrate4 : '';
			$taxrate5		= isset($documentInfo['taxrate5']) ? urldecode($documentInfo['taxrate5']) : "";
			$taxrate5		= ($taxrate5 != 0) ? $taxrate5 : '';

			$taxwithheld0		= isset($documentInfo['taxwithheld0']) ? urldecode($documentInfo['taxwithheld0']) : "0.00";
			$taxwithheld0		= ($taxwithheld0 != 0) ? $taxwithheld0 : '';
			$taxwithheld1		= isset($documentInfo['taxwithheld1']) ? urldecode($documentInfo['taxwithheld1']) : "0.00";
			$taxwithheld1		= ($taxwithheld1 != 0) ? $taxwithheld1 : '';
			$taxwithheld2		= isset($documentInfo['taxwithheld2']) ? urldecode($documentInfo['taxwithheld2']) : "0.00";
			$taxwithheld2		= ($taxwithheld2 != 0) ? $taxwithheld2 : '';
			$taxwithheld3		= isset($documentInfo['taxwithheld3']) ? urldecode($documentInfo['taxwithheld3']) : "0.00";
			$taxwithheld3		= ($taxwithheld3 != 0) ? $taxwithheld3 : '';
			$taxwithheld4		= isset($documentInfo['taxwithheld4']) ? urldecode($documentInfo['taxwithheld4']) : "0.00";
			$taxwithheld4		= ($taxwithheld4 != 0) ? $taxwithheld4 : '';
			$taxwithheld5		= isset($documentInfo['taxwithheld5']) ? urldecode($documentInfo['taxwithheld5']) : "0.00";
			$taxwithheld5		= ($taxwithheld5 != 0) ? $taxwithheld5 : '';

			$amountremittance	= isset($documentInfo['amountremittance']) ? urldecode($documentInfo['amountremittance']) : "0.00";
			$amountremittance	= ($amountremittance != 0) ? $amountremittance : '';
			$amountremitted		= isset($documentInfo['amountremitted']) ? urldecode($documentInfo['amountremitted']) : "0.00";
			$amountremitted		= ($amountremitted != 0) ? $amountremitted : '';
			$netamount			= isset($documentInfo['netamount']) ? urldecode($documentInfo['netamount']) : "0.00";
			$netamount			= ($netamount != 0) ? $netamount : '';
			$totalremittance	= isset($documentInfo['totalremittance']) ? urldecode($documentInfo['totalremittance']) : "0.00";
			$totalremittance	= ($totalremittance != '') ? $totalremittance : '';

			
			$surcharge			= isset($documentInfo['surcharge']) ? urldecode($documentInfo['surcharge']) : "0.00";
			$surcharge			= ($surcharge != 0) ? $surcharge : '';
			$interest			= isset($documentInfo['interest']) ? urldecode($documentInfo['interest']) : "0.00";
			$interest			= ($interest != 0) ? $interest : '';
			$compromise			= isset($documentInfo['compromise']) ? urldecode($documentInfo['compromise']) : "0.00";
			$compromise			= ($compromise != 0) ? $compromise : '';
			$penalties			= isset($documentInfo['penalties']) ? urldecode($documentInfo['penalties']) : "0.00";
			$penalties			= ($penalties != 0) ? $penalties : '';
			$amountdue			= isset($documentInfo['amountdue']) ? urldecode($documentInfo['amountdue']) : "0.00";
			$amountdue			= ($amountdue != 0) ? $amountdue : '';
		}

		$signatory_arr		= $this->signatory;
		$businesstype		= $signatory_arr->businesstype;
		$signatory_name		= $signatory_arr->signatory_name;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', '', '13');
		$this->SetY(57);
		$this->SetX(6);
		$this->Cell(20, 5, $monthfilter, 0, 0, 'R');

		$this->SetX(26.5);
		$this->Cell(20, 5, $yearfilter, 0, 0, 'C');

		$this->SetFont('Arial', '', '13');

		$this->SetX(61.5);
		$this->Cell(20, 5, $duedate, 0, 0, 'C');

		/**
		 * Quarter
		 */
		// $this->SetFont('Arial', 'B', '12');
		// if($quarter == 1){
		// 	$this->SetX(41.3);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }else if($quarter == 2){
		// 	$this->SetX(56.8);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }else if($quarter == 3){
		// 	$this->SetX(72.1);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }else if($quarter == 4){
		// 	$this->SetX(87.6);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }

		/**
		 * Amend Return
		 */
		$this->SetFont('Arial', '', '12');
		$this->SetY(56);
		if($amendreturn == "yes"){
			$this->SetX(97);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(112.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Taxes Withheld
		 */
		$this->SetFont('Arial', '', '12');
		if($anytaxwithheld == "yes"){
			$this->SetX(133);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(148.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		$this->SetY(71);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		
		$this->SetX(74.5);
		$this->Cell(20, 5, $tin1, 0, 0, 'C');

		$this->SetX(95);
		$this->Cell(20, 5, $tin2, 0, 0, 'C');

		$this->SetX(115.5);
		$this->Cell(20, 5, $tin3, 0, 0, 'C');

		$this->SetX(136.5);
		$this->Cell(20, 5, $tin4, 0, 0, 'C');

		/**
		 * RDO Code
		 */
		$this->SetX(189.5);
		$this->Cell(15, 5, $rdo, 0, 0, 'R');

		$this->SetY(83);

		/**
		 * Agent's Name
		 */
		$this->SetFont('Arial', '', '12');
		$this->SetX(6.4);
		$array = str_split($agentname);
		$this->cellSpacing($array);

		/**
		 * First Address Line
		 */
		$this->SetY(95);
		$this->SetX(6.4);
		$array = str_split($firstaddress);
		$this->cellSpacing($array);

		/**
		 * Second Address Line
		 */
		$this->SetY(103);
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
		$this->SetY(110.5);
		$this->SetX(37);
		$array = str_split($contact);
		$this->cellSpacing($array);

		/**
		 * Category of Withholding Tax
		 */
		$this->SetFont('Arial', '', '11');
		$this->SetTextColor(0,0,0);
		$this->SetY(109.5);
		if($category == "private"){
			$this->SetX(157);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(182.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Email
		 */
		$this->SetY(122.5);
		$this->SetX(6.5);
		$array = str_split($email);
		$this->cellSpacing($array);

		$this->SetFont('Arial', '', '11');
		/**	
		 * Amount of Remittance
		 */
		$this->SetY(136.5);
		$this->SetX(133.5);
		$this->cellAmount($amountremittance,11);

		/**	
		 * Amount Remitted
		 */
		$this->SetY(144.5);
		$this->SetX(133.5);
		$this->cellAmount($amountremitted,11);

		/**	
		 * Amount Remitted
		 */
		$this->SetY(144.5);
		$this->SetX(133.5);
		$this->cellAmount($amountremitted,11);

		/**	
		 * Net Amount of Remittance
		 */
		$this->SetY(152);
		$this->SetX(133.5);
		$this->cellAmount($netamount,11);

		/**	
		 * Surcharge
		 */
		$this->SetY(164);
		$this->SetX(133.5);
		$this->cellAmount($surcharge,11);

		/**	
		 * Interest
		 */
		$this->SetY(171.5);
		$this->SetX(133.5);
		$this->cellAmount($interest,11);

		/**	
		 * Compromise
		 */
		$this->SetY(179);
		$this->SetX(133.5);
		$this->cellAmount($compromise,11);


		/**	
		 * Total Penalties
		 */
		$this->SetY(186.5);
		$this->SetX(133.5);
		$this->cellAmount($penalties,11);

		/**	
		 * Total Amount Due
		 */
		$this->SetY(194.5);
		$this->SetX(133.5);
		$this->cellAmount($amountdue,11);

		/**
		 * Over-Remittance options
		 */
		// $this->SetFont('Arial', 'B', '11');
		// $this->SetY(225.7);
		// if($remittance == "refunded"){
		// 	$this->SetX(67.4);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }else if($remittance == "taxcredit"){
		// 	$this->SetX(94.2);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }else if($remittance == "carriedover"){
		// 	$this->SetX(145.6);
		// 	$this->Cell(5, 5, 'X', 0, 0, 'C');
		// }

		/**
		 * Signatory
		 */
		$this->SetY(217);
		$this->SetX(7);
		if(strtolower($businesstype) == 'individual'){
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
		if($amount < 0 && (strlen($amount) > 5 && (strlen($amount) % 2 != 0))){
			$curX 	= $this->GetX();
			$newX	= $curX - 4.9;
			$this->SetX($newX);
		}
		if(empty($amount_decimal_arr[3])){
			$array = ($char_limit == 12) ? str_split('   ') : str_split('   ');
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

