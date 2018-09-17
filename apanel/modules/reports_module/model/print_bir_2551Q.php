<?php
class print_bir_2551Q extends fpdf {

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
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/2551Q-1.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			// $yearfilter 	= $documentInfo['yearfilter'];
			// $yearfilter 	= implode('   ',str_split($yearfilter));
			$calendar		= isset($documentInfo['calendar_fiscal']) ? $documentInfo['calendar_fiscal'] : "yes";
			$month			= isset($documentInfo['month']) ? $documentInfo['month'] : '';
			$month		 	= implode('  ',str_split($month));
			$year			= isset($documentInfo['year']) ? $documentInfo['year'] : '';
			$year		 	= implode('  ',str_split($year));
			$quarter 		= $documentInfo['quarter'];
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

			$rdo 			= isset($documentInfo['rdo']) ? $documentInfo['rdo'] : 0;
			$rdo 			= implode('  ',str_split($rdo));
			$businessline	= isset($documentInfo['businessline']) ? $documentInfo['businessline'] : "";
			$businessline 	= strtoupper(urldecode($businessline));
			$businessline 	= implode('',str_split($businessline));
			$agentname 		= isset($documentInfo['agentname']) ? $documentInfo['agentname'] : "";
			$agentname 		= strtoupper(urldecode($agentname));
			$agentname1 	= isset($documentInfo['agentname1']) ? $documentInfo['agentname1'] : "";
			$agentname1		= strtoupper(urldecode($agentname1));
			$telephone 		= isset($documentInfo['telephone']) ? $documentInfo['telephone'] : "";
			$telephone 		= strtoupper(urldecode($telephone));
			$telephone 		= implode('   ',str_split($telephone));
			$firstaddress 	= isset($documentInfo['firstaddress']) ? $documentInfo['firstaddress'] : "";
			$firstaddress 	= strtoupper(urldecode($firstaddress));
			$secondaddress 	= isset($documentInfo['secondaddress']) ? $documentInfo['secondaddress'] : "";
			$secondaddress 	= strtoupper(urldecode($secondaddress));
			$zipcode 		= isset($documentInfo['zipcode']) ? $documentInfo['zipcode'] : "";
			$contact 		= isset($documentInfo['mobile']) ? $documentInfo['mobile'] : "";
			$contact 		= strtoupper(urldecode($contact));
			// $category		= isset($documentInfo['category']) ? $documentInfo['category'] : "yes";
			$email 			= isset($documentInfo['email']) ? $documentInfo['email'] : "";
			$email 			= strtoupper(urldecode($email));
			$taxrelief		= isset($documentInfo['taxrelief']) ? $documentInfo['taxrelief'] : "yes";
			$incometax		= isset($documentInfo['incometax']) ? $documentInfo['incometax'] : "yes";
			$specify		= isset($documentInfo['specify']) ? $documentInfo['specify'] : "";
			$specify 		= strtoupper(urldecode($specify));			
			$specify		= str_replace('+',' ',$specify);
			$other			= isset($documentInfo['other']) ? $documentInfo['other'] : "";	
			$other			= str_replace('+',' ',$other);
			// $specify 		= implode('   ',str_split($specify));

			/**
			 * ATC Entries
			 */
			$atc0			= (isset($documentInfo['atc0']) && ($documentInfo['atc0'] != 'none')) ? urldecode($documentInfo['atc0']) : "";
			$atc0 			= str_replace(' ','',$atc0);
			$atc1			= (isset($documentInfo['atc1']) && ($documentInfo['atc1'] != 'none')) ? urldecode($documentInfo['atc1']) : "";
			$atc1 			= str_replace(' ','',$atc1);
			$atc2			= (isset($documentInfo['atc2']) && ($documentInfo['atc2'] != 'none')) ? urldecode($documentInfo['atc2']) : "";
			$atc2 			= str_replace(' ','',$atc2);
			$atc3			= (isset($documentInfo['atc3']) && ($documentInfo['atc3'] != 'none')) ? urldecode($documentInfo['atc3']) : "";
			$atc3 			= str_replace(' ','',$atc3);
			$atc4			= (isset($documentInfo['atc4']) && ($documentInfo['atc4'] != 'none')) ? urldecode($documentInfo['atc4']) : "";
			$atc4 			= str_replace(' ','',$atc4);
			$atc5			= (isset($documentInfo['atc5']) && ($documentInfo['atc5'] != 'none')) ? urldecode($documentInfo['atc5']) : "";
			$atc5 			= str_replace(' ','',$atc5);

			$taxamount0		= isset($documentInfo['taxamount0']) ? urldecode($documentInfo['taxamount0']) : "0.00";
			$taxamount0		= ($taxamount0 > 0) ? $taxamount0 : '';
			$taxamount1		= isset($documentInfo['taxamount1']) ? urldecode($documentInfo['taxamount1']) : "0.00";
			$taxamount1		= ($taxamount1 > 0) ? $taxamount1 : '';
			$taxamount2		= isset($documentInfo['taxamount2']) ? urldecode($documentInfo['taxamount2']) : "0.00";
			$taxamount2		= ($taxamount2 > 0) ? $taxamount2 : '';
			$taxamount3		= isset($documentInfo['taxamount3']) ? urldecode($documentInfo['taxamount3']) : "0.00";
			$taxamount3		= ($taxamount3 > 0) ? $taxamount3 : '';
			$taxamount4		= isset($documentInfo['taxamount4']) ? urldecode($documentInfo['taxamount4']) : "0.00";
			$taxamount4		= ($taxamount4 > 0) ? $taxamount4 : '';
			$taxamount5		= isset($documentInfo['taxamount5']) ? urldecode($documentInfo['taxamount5']) : "0.00";
			$taxamount5		= ($taxamount5 > 0) ? $taxamount5 : '';

			$taxrate0		= isset($documentInfo['taxrate0']) ? urldecode($documentInfo['taxrate0']) : "";
			$taxrate0		= ($taxrate0 > 0) ? $taxrate0 : '';
			$taxrate0 		= implode('  ',str_split($taxrate0));
			$taxrate1		= isset($documentInfo['taxrate1']) ? urldecode($documentInfo['taxrate1']) : "";
			$taxrate1		= ($taxrate1 > 0) ? $taxrate1 : '';
			$taxrate1 		= implode('  ',str_split($taxrate1));
			$taxrate2		= isset($documentInfo['taxrate2']) ? urldecode($documentInfo['taxrate2']) : "";
			$taxrate2		= ($taxrate2 > 0) ? $taxrate2 : '';
			$taxrate2 		= implode('  ',str_split($taxrate2));
			$taxrate3		= isset($documentInfo['taxrate3']) ? urldecode($documentInfo['taxrate3']) : "";
			$taxrate3		= ($taxrate3 > 0) ? $taxrate3 : '';
			$taxrate3 		= implode('  ',str_split($taxrate3));
			$taxrate4		= isset($documentInfo['taxrate4']) ? urldecode($documentInfo['taxrate4']) : "";
			$taxrate4		= ($taxrate4 > 0) ? $taxrate4 : '';
			$taxrate4 		= implode('  ',str_split($taxrate4));
			$taxrate5		= isset($documentInfo['taxrate5']) ? urldecode($documentInfo['taxrate5']) : "";
			$taxrate5		= ($taxrate5 > 0) ? $taxrate5 : '';
			$taxrate5 		= implode('  ',str_split($taxrate5));

			$taxdue0		= isset($documentInfo['taxdue0']) ? urldecode($documentInfo['taxdue0']) : "0.00";
			$taxdue0		= ($taxdue0 > 0) ? $taxdue0 : '';
			$taxdue1		= isset($documentInfo['taxdue1']) ? urldecode($documentInfo['taxdue1']) : "0.00";
			$taxdue1		= ($taxdue1 > 0) ? $taxdue1 : '';
			$taxdue2		= isset($documentInfo['taxdue2']) ? urldecode($documentInfo['taxdue2']) : "0.00";
			$taxdue2		= ($taxdue2 > 0) ? $taxdue2 : '';
			$taxdue3		= isset($documentInfo['taxdue3']) ? urldecode($documentInfo['taxdue3']) : "0.00";
			$taxdue3		= ($taxdue3 > 0) ? $taxdue3 : '';
			$taxdue4		= isset($documentInfo['taxdue4']) ? urldecode($documentInfo['taxdue4']) : "0.00";
			$taxdue4		= ($taxdue4 > 0) ? $taxdue4 : '';
			$taxdue5		= isset($documentInfo['taxdue5']) ? urldecode($documentInfo['taxdue5']) : "0.00";
			$taxdue5		= ($taxdue5 > 0) ? $taxdue5 : '';

			$totaltaxdue	= isset($documentInfo['totaltaxdue6']) ? urldecode($documentInfo['totaltaxdue6']) : "0.00";
			$totaltaxdue	= ($totaltaxdue > 0) ? $totaltaxdue : '';

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

			$taxdue				= isset($documentInfo['tax_due']) ? urldecode($documentInfo['tax_due']) : "0.00";
			$taxdue				= ($taxdue > 0) ? $taxdue : '';
			$creditableperc		= isset($documentInfo['creditablepercentage']) ? urldecode($documentInfo['creditablepercentage']) : "0.00";
			$creditableperc		= ($creditableperc > 0) ? $creditableperc : '';
			$taxpaid			= isset($documentInfo['taxpaid']) ? urldecode($documentInfo['taxpaid']) : "0.00";
			$taxpaid			= ($taxpaid > 0) ? $taxpaid : '';
			$othertax			= isset($documentInfo['othertax']) ? urldecode($documentInfo['othertax']) : "0.00";
			$othertax			= ($othertax > 0) ? $othertax : '';
			$totaltax			= isset($documentInfo['totaltax']) ? urldecode($documentInfo['totaltax']) : "0.00";
			$totaltax			= ($totaltax > 0) ? $totaltax : '';
			$totalpayable		= isset($documentInfo['totalpayable']) ? urldecode($documentInfo['totalpayable']) : "0.00";
			$totalpayable		= ($totalpayable > 0) ? $totalpayable : $totalpayable;
			$surcharge			= isset($documentInfo['surcharge']) ? urldecode($documentInfo['surcharge']) : "0.00";
			$interest			= isset($documentInfo['interest']) ? urldecode($documentInfo['interest']) : "0.00";
			$compromise			= isset($documentInfo['compromise']) ? urldecode($documentInfo['compromise']) : "0.00";
			$penalties			= isset($documentInfo['penalties']) ? urldecode($documentInfo['penalties']) : "0.00";
			$amountdue			= isset($documentInfo['totalamountpayable']) ? urldecode($documentInfo['totalamountpayable']) : "0.00";
			$amountdue			= ($amountdue > 0) ? $amountdue : $amountdue;
			$remittance			= isset($documentInfo['remittance']) ? urldecode($documentInfo['remittance']) : "0.00";
		}

		$signatory_arr		= $this->signatory;
		$businesstype		= $signatory_arr->businesstype;
		$signatory_name		= $signatory_arr->signatory_name;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', 'B', '13');
		$this->SetX(11.5);
		// $this->Cell(20, 5, $yearfilter, 0, 0, 'C');
		// $this->SetFont('Arial', 'B', '12');
		if($calendar == 1){
			$this->SetY(40);
			$this->SetX(30);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($calendar == 2){
			$this->SetY(40);
			$this->SetX(55.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Month
		 */
		// $this->SetFont('Arial', 'B', '13');
		$this->SetY(46);
		$this->SetX(48);		
		$this->Cell(10, 5, $month, 0, 0, 'R');

		/**
		 * Year
		 */
		// $this->SetFont('Arial', 'B', '13');
		$this->SetY(46);
		$this->SetX(68);		
		$this->Cell(10, 5, $year, 0, 0, 'R');

		/**
		 * Quarter
		 */
		// $this->SetFont('Arial', 'B', '10');
		$this->SetY(44.5);
		if($quarter == 1){
			$this->SetX(86);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 2){
			$this->SetX(101);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 3){
			$this->SetX(116);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($quarter == 4){
			$this->SetX(131);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Amend Return
		 */
		// $this->SetFont('Arial', 'B', '10');
		if($amendreturn == "yes"){
			$this->SetX(150);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetX(165);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Sheets Attached
		 */
		// $this->SetFont('Arial', 'B', '12');
		$this->SetY(46);
		$this->SetX(198.5);
		$this->Cell(10, 5, $attachments, 0, 0, 'L');

		$this->SetY(54.5);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		// $this->SetFont('Arial', 'B', '12');
		$this->SetY(58);
		$this->SetX(82);		
		$this->Cell(10, 5, $tin1, 0, 0, 'R');

		$this->SetY(58);
		$this->SetX(102);		
		$this->Cell(10, 5, $tin2, 0, 0, 'R');

		$this->SetY(58);		
		$this->SetX(122);		
		$this->Cell(10.5, 5, $tin3, 0, 0, 'R');

		$this->SetY(58);		
		$this->SetX(142);		
		$this->Cell(10, 5, $tin4, 0, 0, 'R');

		

		/**
		 * RDO Code
		 */
		$this->SetY(58);
		$this->SetX(192.5);	
		$this->Cell(15, 5, $rdo, 0, 0, 'R');

		/**
		 * Business Line
		 */
		// $this->SetFont('Arial', 'B', '11');
		// $this->SetY(62.5);
		// $this->Cell(189, 5, $businessline, 0, 0, 'R');

		/**
		 * Agent's Name
		 */
		// $this->SetFont('Arial', 'B', '13');
		$this->SetY(68);
		$this->SetX(7.6);
		$array = str_split($agentname);
		$this->cellSpacing($array);

		/**
		 * First Address Line
		 */
		$this->SetY(79);
		$this->SetX(7.6);
		$array = str_split($firstaddress);
		$this->cellSpacing($array);

		/**
		 * Second Address Line
		 */
		$this->SetY(85);
		$this->SetX(7.6);	
		$array = str_split($secondaddress);
		$this->cellSpacing($array);

		/**
		 * ZIP Code
		 */
		$this->SetX(188);
		$array = str_split($zipcode);
		$this->cellSpacing($array);

		/**
		 * Contact
		 */
		$this->SetY(95);
		$this->SetX(7.6);
		$array = str_split($contact);
		$this->cellSpacing($array);

		/**
		 * Email
		 */
		$this->SetY(95);
		$this->SetX(67.3);
		$array = str_split($email);
		$this->cellSpacing($array);


		/**
		 * Tax Relief
		 */
		// $this->SetFont('Arial', 'B', '11');
		if($taxrelief == "yes"){
			$this->SetY(101);
			$this->SetX(67.5);			
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetY(101);
			$this->SetX(83);			
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		/**
		 * Specify
		 */
		$this->SetY(101.9);
		$this->SetX(128);
		$array = str_split($specify);
		$this->cellSpacing($array);

		/**
		 * Tax Income
		 */
		$this->SetTextColor(0,0,0);
		// $this->SetFont('Arial', 'B', '12');
		if($incometax == "yes"){
			$this->SetY(115.5);
			$this->SetX(64.1);			
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetY(115.5);
			$this->SetX(123);			
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}
		
		// $this->SetFont('Arial', 'B', '13');		
		/**	
		 * Tax Still Due
		 */
		$this->SetY(129);
		$this->SetX(137.5);
		$this->cellAmount($taxdue,11);
		
		/**	
		 * Creditable Percentage
		 */
		$this->SetY(140);
		$this->SetX(137.5);
		$this->cellAmount($creditableperc,11);

		/**	
		 * Tax Paid
		 */
		$this->SetY(146);
		$this->SetX(137.5);
		$this->cellAmount($taxpaid,11);

		/**	
		 * Other Tax Credit
		 */
		$this->SetY(152);
		$this->SetX(137.5);
		$this->cellAmount($othertax,11);

		/**	
		 * Other
		 */
		$this->SetY(151);
		$this->SetX(79);
		$this->Cell(50, 5, $other, 0, 0, 'L');	

		/**	
		 * Total Tax Credit
		 */
		$this->SetY(159);
		$this->SetX(137.5);
		$this->cellAmount($totaltax,11);
		
		/**	
		 * Total Tax Credit
		 */
		$this->SetY(165);
		$this->SetX(137.5);
		$this->cellAmount($totalpayable,11);

		/**	
		 * Surcharge
		 */
		$this->SetY(176);
		$this->SetX(137.5);
		$this->cellAmount($surcharge,11);

		/**	
		 * Interest
		 */
		$this->SetY(183);
		$this->SetX(137.5);
		$this->cellAmount($interest,11);

		/**	
		 * Compromise
		 */
		$this->SetY(189);
		$this->SetX(137.5);
		$this->cellAmount($compromise,11);


		/**	
		 * Total Penalties
		 */
		$this->SetY(196);
		$this->SetX(137.5);
		$this->cellAmount($penalties,11);

		/**	
		 * Total Amount Due
		 */
		$this->SetY(202);
		$this->SetX(137.5);
		$this->cellAmount($amountdue,11);

		/**
		 * Over-Remittance options
		 */
		// $this->SetFont('Arial', 'B', '11');
		$this->SetY(207);
		if($remittance == "refunded"){
			$this->SetX(69.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else if($remittance == "taxcredit"){
			$this->SetX(104.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}
		
		/**
		 * Signatory
		 */
		$this->SetY(228.5);
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
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/2551Q-2.jpg',0,0,216,330.2);


		/**
		 * Taxpayer Identification Number (TIN)
		 */
		$this->SetFont('Arial', 'B', '13');
		$this->SetY(40);
		$this->SetX(12.5);		
		$this->Cell(10, 5, $tin1, 0, 0, 'R');

		// $this->SetY(58);
		$this->SetX(28);		
		$this->Cell(10, 5, $tin2, 0, 0, 'R');

		// $this->SetY(58);		
		$this->SetX(42);		
		$this->Cell(10.5, 5, $tin3, 0, 0, 'R');

		// $this->SetY(58);		
		$this->SetX(58);		
		$this->Cell(10, 5, $tin4, 0, 0, 'R');
		
		/**
		 * Agent's Name
		 */
		$this->SetFont('Arial', 'B', '13');
		$this->SetY(40);
		$this->SetX(78);
		$array = str_split($agentname1);
		$this->cellSpacing($array);

		/**
		 * ATC
		 */
		$this->SetFont('Arial', 'B', '13');
		
		$this->SetY(58.5);
		$this->SetX(13);
		$array = str_split($atc0);
		$this->cellSpacing($array);

		$this->SetY(65);
		$this->SetX(13);
		$array = str_split($atc1);
		$this->cellSpacing($array);

		$this->SetY(71.5);
		$this->SetX(13);
		$array = str_split($atc2);
		$this->cellSpacing($array);

		$this->SetY(78);
		$this->SetX(13);
		$array = str_split($atc3);
		$this->cellSpacing($array);
		
		$this->SetY(84.5);
		$this->SetX(13);
		$array = str_split($atc4);
		$this->cellSpacing($array);

		$this->SetY(91);
		$this->SetX(13);
		$array = str_split($atc5);
		$this->cellSpacing($array);

		$this->SetTextColor(0,0,0);
		
		$this->SetY(58.5);
		$this->SetX(42.5);	
		$this->cellAmount($taxamount0,11);		

		$this->SetY(65);
		$this->SetX(42.5);			
		$this->cellAmount($taxamount1,11);


		$this->SetY(71.5);
		$this->SetX(42.5);	
		$this->cellAmount($taxamount2,11);		

		$this->SetY(78);
		$this->SetX(42.5);	
		$this->cellAmount($taxamount3,11);				
		
		$this->SetY(84.5);
		$this->SetX(42.5);	
		$this->cellAmount($taxamount4,11);

		$this->SetY(91);
		$this->SetX(42.5);		
		$this->cellAmount($taxamount5,11);

		$this->SetTextColor(0,0,0);
		
		$this->SetY(58.5);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate0, 0, 0, 'R');	

		$this->SetY(65);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate1, 0, 0, 'R');

		$this->SetY(71.5);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate2, 0, 0, 'R');
		
		$this->SetY(78);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate3, 0, 0, 'R');
		
		$this->SetY(84.5);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate4, 0, 0, 'R');

		$this->SetY(91);
		$this->SetX(113);
		$this->Cell(10, 5, $taxrate5, 0, 0, 'R');


		$this->SetTextColor(0,0,0);
		
		$this->SetY(58.5);
		$this->SetX(132.5);
		$this->cellAmount($taxdue0,11);	

		$this->SetY(65);
		$this->SetX(132.5);		
		$this->cellAmount($taxdue1,11);		

		$this->SetY(71.5);
		$this->SetX(132.5);			
		$this->cellAmount($taxdue2,11);					

		$this->SetY(78);
		$this->SetX(132.5);		
		$this->cellAmount($taxdue3,11);					
		
		$this->SetY(84.5);
		$this->SetX(132.5);			
		$this->cellAmount($taxdue4,11);	

		$this->SetY(91);
		$this->SetX(132.5);			
		$this->cellAmount($taxdue5,11);	


		$this->SetY(98);
		$this->SetX(132.5);		
		$this->cellAmount($totaltaxdue,11);

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
