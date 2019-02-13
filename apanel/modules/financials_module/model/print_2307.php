<?php
class print_2307 extends fpdf {

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
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/2307.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			$monthfilterFrom 	= $documentInfo['monthfilterFrom'];
			$monthfilterFrom 	= implode('  ',str_split($monthfilterFrom));
			$dayfilterFrom 		= $documentInfo['dayfilterFrom'];
			$dayfilterFrom 		= implode('  ',str_split($dayfilterFrom));
			$yearfilterFrom 	= $documentInfo['yearfilterFrom'];
			$yearfilterFrom     = date('y',strtotime($yearfilterFrom));
			$yearfilterFrom 	= implode('  ',str_split($yearfilterFrom));

			$monthfilterTo 		= $documentInfo['monthfilterTo'];
			$monthfilterTo 		= implode('  ',str_split($monthfilterTo));
			$dayfilterTo 		= $documentInfo['dayfilterTo'];
			$dayfilterTo 		= implode('  ',str_split($dayfilterTo));
			$yearfilterTo 		= $documentInfo['yearfilterTo'];
			$yearfilterTo     	= date('y',strtotime($yearfilterTo));
			$yearfilterTo 		= implode('  ',str_split($yearfilterTo));
	
			$tin 			= (isset($documentInfo['payee_tin']) && !empty($documentInfo['payee_tin'])) ? $documentInfo['payee_tin'] : '000-000-000-000';
			$tin_arr		= explode('-',$tin);
			$tin1			= ($tin_arr[0]) ? $tin_arr[0] : '000';
			$tin1 			= implode('  ',str_split($tin1));
			$tin2			= ($tin_arr[1]) ? $tin_arr[1] : '000';
			$tin2 			= implode('  ',str_split($tin2));
			$tin3			= ($tin_arr[2]) ? $tin_arr[2] : '000';
			$tin3 			= implode('  ',str_split($tin3));
			$tin4			= ($tin_arr[3]) ? $tin_arr[3] : '000';
			$tin4 			= implode('  ',str_split($tin4));

			$t_tin 			= (isset($documentInfo['payor_tin']) && !empty($documentInfo['payor_tin'])) ? $documentInfo['payor_tin'] : '000-000-000-000';
			$t_tin_arr		= explode('-',$t_tin);
			$t_tin1			= ($t_tin_arr[0]) ? $t_tin_arr[0] : '000';
			$t_tin1 			= implode('  ',str_split($t_tin1));
			$t_tin2			= ($t_tin_arr[1]) ? $t_tin_arr[1] : '000';
			$t_tin2 			= implode('  ',str_split($t_tin2));
			$t_tin3			= ($t_tin_arr[2]) ? $t_tin_arr[2] : '000';
			$t_tin3 			= implode('  ',str_split($t_tin3));
			$t_tin4			= ($t_tin_arr[3]) ? $t_tin_arr[3] : '000';
			$t_tin4 			= implode('  ',str_split($t_tin4));

			$payee_name 	= isset($documentInfo['payee_name']) ? urldecode($documentInfo['payee_name']) : "";
			$payee_address 	= isset($documentInfo['payee_address']) ? urldecode($documentInfo['payee_address']) : "";
			$zipcode 		= isset($documentInfo['payee_zipcode']) ? urldecode($documentInfo['payee_zipcode']) : "";
			$payor_name 	= isset($documentInfo['payor_name']) ? urldecode($documentInfo['payor_name']) : "";
			$payor_address 	= isset($documentInfo['payor_address']) ? urldecode($documentInfo['payor_address']) : "";
			$t_zipcode 		= isset($documentInfo['payor_zipcode']) ? urldecode($documentInfo['payor_zipcode']) : "";

	
			$accountcode1    = isset($documentInfo['accountcode1']) ? urldecode($documentInfo['accountcode1']) : "";
			$accountcode2    = isset($documentInfo['accountcode2']) ? urldecode($documentInfo['accountcode2']) : "";
			$accountcode3    = isset($documentInfo['accountcode3']) ? urldecode($documentInfo['accountcode3']) : "";
			$accountcode4    = isset($documentInfo['accountcode4']) ? urldecode($documentInfo['accountcode4']) : "";
			$accountcode5    = isset($documentInfo['accountcode5']) ? urldecode($documentInfo['accountcode5']) : "";
			$accountcode6    = isset($documentInfo['accountcode6']) ? urldecode($documentInfo['accountcode6']) : "";
			$accountcode7    = isset($documentInfo['accountcode7']) ? urldecode($documentInfo['accountcode7']) : "";
			$accountcode8    = isset($documentInfo['accountcode8']) ? urldecode($documentInfo['accountcode8']) : "";
			$accountcode9    = isset($documentInfo['accountcode9']) ? urldecode($documentInfo['accountcode9']) : "";
			$accountcode10   = isset($documentInfo['accountcode10']) ? urldecode($documentInfo['accountcode10']) : "";
			$accountcode11   = isset($documentInfo['accountcode11']) ? urldecode($documentInfo['accountcode11']) : "";
			$accountcode12   = isset($documentInfo['accountcode12']) ? urldecode($documentInfo['accountcode12']) : "";
			$accountcode13   = isset($documentInfo['accountcode13']) ? urldecode($documentInfo['accountcode13']) : "";

			$atc_code1    = isset($documentInfo['atccode1'])  ? urldecode($documentInfo['atccode1']) : "";
			$atc_code2    = isset($documentInfo['atccode2'])  ? urldecode($documentInfo['atccode2']) : "";
			$atc_code3    = isset($documentInfo['atccode3'])  ? urldecode($documentInfo['atccode3']) : "";
			$atc_code4    = isset($documentInfo['atccode4'])  ? urldecode($documentInfo['atccode4']) : "";
			$atc_code5    = isset($documentInfo['atccode5'])  ? urldecode($documentInfo['atccode5']) : "";
			$atc_code6    = isset($documentInfo['atccode6'])  ? urldecode($documentInfo['atccode6']) : "";
			$atc_code7    = isset($documentInfo['atccode7'])  ? urldecode($documentInfo['atccode7']) : "";
			$atc_code8    = isset($documentInfo['atccode8'])  ? urldecode($documentInfo['atccode8']) : "";
			$atc_code9    = isset($documentInfo['atccode9'])  ? urldecode($documentInfo['atccode9']) : "";
			$atc_code10   = isset($documentInfo['atccode10']) ? urldecode($documentInfo['atccode10']) : "";
			$atc_code11   = isset($documentInfo['atccode11']) ? urldecode($documentInfo['atccode11']) : "";
			$atc_code12   = isset($documentInfo['atccode12']) ? urldecode($documentInfo['atccode12']) : "";
			$atc_code13   = isset($documentInfo['atccode13']) ? urldecode($documentInfo['atccode13']) : "";

			$debit1    = isset($documentInfo['debit1']) ? urldecode($documentInfo['debit1']) : "";
			$debit2    = isset($documentInfo['debit2']) ? urldecode($documentInfo['debit2']) : "";
			$debit3    = isset($documentInfo['debit3']) ? urldecode($documentInfo['debit3']) : "";
			$debit4    = isset($documentInfo['debit4']) ? urldecode($documentInfo['debit4']) : "";
			$debit5    = isset($documentInfo['debit5']) ? urldecode($documentInfo['debit5']) : "";
			$debit6    = isset($documentInfo['debit6']) ? urldecode($documentInfo['debit6']) : "";
			$debit7    = isset($documentInfo['debit7']) ? urldecode($documentInfo['debit7']) : "";
			$debit8    = isset($documentInfo['debit8']) ? urldecode($documentInfo['debit8']) : "";
			$debit9    = isset($documentInfo['debit9']) ? urldecode($documentInfo['debit9']) : "";
			$debit10   = isset($documentInfo['debit10']) ? urldecode($documentInfo['debit10']) : "";
			$debit11   = isset($documentInfo['debit11']) ? urldecode($documentInfo['debit11']) : "";
			$debit12   = isset($documentInfo['debit12']) ? urldecode($documentInfo['debit12']) : "";
			$debit13   = isset($documentInfo['debit13']) ? urldecode($documentInfo['debit13']) : "";

			$credit1    = isset($documentInfo['credit1']) ? urldecode($documentInfo['credit1']) : "";
			$credit2    = isset($documentInfo['credit2']) ? urldecode($documentInfo['credit2']) : "";
			$credit3    = isset($documentInfo['credit3']) ? urldecode($documentInfo['credit3']) : "";
			$credit4    = isset($documentInfo['credit4']) ? urldecode($documentInfo['credit4']) : "";
			$credit5    = isset($documentInfo['credit5']) ? urldecode($documentInfo['credit5']) : "";
			$credit6    = isset($documentInfo['credit6']) ? urldecode($documentInfo['credit6']) : "";
			$credit7    = isset($documentInfo['credit7']) ? urldecode($documentInfo['credit7']) : "";
			$credit8    = isset($documentInfo['credit8']) ? urldecode($documentInfo['credit8']) : "";
			$credit9    = isset($documentInfo['credit9']) ? urldecode($documentInfo['credit9']) : "";
			$credit10   = isset($documentInfo['credit10']) ? urldecode($documentInfo['credit10']) : "";
			$credit11   = isset($documentInfo['credit11']) ? urldecode($documentInfo['credit11']) : "";
			$credit12   = isset($documentInfo['credit12']) ? urldecode($documentInfo['credit12']) : "";
			$credit13   = isset($documentInfo['credit13']) ? urldecode($documentInfo['credit13']) : "";

			$detailparticulars_first1    = isset($documentInfo['detailparticulars_first1'])  ? urldecode($documentInfo['detailparticulars_first1']) : "";
			$detailparticulars_first2    = isset($documentInfo['detailparticulars_first2'])  ? urldecode($documentInfo['detailparticulars_first2']) : "";
			$detailparticulars_first3    = isset($documentInfo['detailparticulars_first3'])  ? urldecode($documentInfo['detailparticulars_first3']) : "";
			$detailparticulars_first4    = isset($documentInfo['detailparticulars_first4'])  ? urldecode($documentInfo['detailparticulars_first4']) : "";
			$detailparticulars_first5    = isset($documentInfo['detailparticulars_first5'])  ? urldecode($documentInfo['detailparticulars_first5']) : "";
			$detailparticulars_first6    = isset($documentInfo['detailparticulars_first6'])  ? urldecode($documentInfo['detailparticulars_first6']) : "";
			$detailparticulars_first7    = isset($documentInfo['detailparticulars_first7'])  ? urldecode($documentInfo['detailparticulars_first7']) : "";
			$detailparticulars_first8    = isset($documentInfo['detailparticulars_first8'])  ? urldecode($documentInfo['detailparticulars_first8']) : "";
			$detailparticulars_first9    = isset($documentInfo['detailparticulars_first9'])  ? urldecode($documentInfo['detailparticulars_first9']) : "";
			$detailparticulars_first10   = isset($documentInfo['detailparticulars_first10']) ? urldecode($documentInfo['detailparticulars_first10']) : "";
			$detailparticulars_first11   = isset($documentInfo['detailparticulars_first11']) ? urldecode($documentInfo['detailparticulars_first11']) : "";
			$detailparticulars_first12   = isset($documentInfo['detailparticulars_first12']) ? urldecode($documentInfo['detailparticulars_first12']) : "";
			$detailparticulars_first13   = isset($documentInfo['detailparticulars_first13']) ? urldecode($documentInfo['detailparticulars_first13']) : "";

			$detailparticulars_second1    = isset($documentInfo['detailparticulars_second1'])  ? urldecode($documentInfo['detailparticulars_second1']) : "";
			$detailparticulars_second2    = isset($documentInfo['detailparticulars_second2'])  ? urldecode($documentInfo['detailparticulars_second2']) : "";
			$detailparticulars_second3    = isset($documentInfo['detailparticulars_second3'])  ? urldecode($documentInfo['detailparticulars_second3']) : "";
			$detailparticulars_second4    = isset($documentInfo['detailparticulars_second4'])  ? urldecode($documentInfo['detailparticulars_second4']) : "";
			$detailparticulars_second5    = isset($documentInfo['detailparticulars_second5'])  ? urldecode($documentInfo['detailparticulars_second5']) : "";
			$detailparticulars_second6    = isset($documentInfo['detailparticulars_second6'])  ? urldecode($documentInfo['detailparticulars_second6']) : "";
			$detailparticulars_second7    = isset($documentInfo['detailparticulars_second7'])  ? urldecode($documentInfo['detailparticulars_second7']) : "";
			$detailparticulars_second8    = isset($documentInfo['detailparticulars_second8'])  ? urldecode($documentInfo['detailparticulars_second8']) : "";
			$detailparticulars_second9    = isset($documentInfo['detailparticulars_second9'])  ? urldecode($documentInfo['detailparticulars_second9']) : "";
			$detailparticulars_second10   = isset($documentInfo['detailparticulars_second10']) ? urldecode($documentInfo['detailparticulars_second10']) : "";
			$detailparticulars_second11   = isset($documentInfo['detailparticulars_second11']) ? urldecode($documentInfo['detailparticulars_second11']) : "";
			$detailparticulars_second12   = isset($documentInfo['detailparticulars_second12']) ? urldecode($documentInfo['detailparticulars_second12']) : "";
			$detailparticulars_second13   = isset($documentInfo['detailparticulars_second13']) ? urldecode($documentInfo['detailparticulars_second13']) : "";

			$detailparticulars_third1    = isset($documentInfo['detailparticulars_third1'])  ? urldecode($documentInfo['detailparticulars_third1']) : "";
			$detailparticulars_third2    = isset($documentInfo['detailparticulars_third2'])  ? urldecode($documentInfo['detailparticulars_third2']) : "";
			$detailparticulars_third3    = isset($documentInfo['detailparticulars_third3'])  ? urldecode($documentInfo['detailparticulars_third3']) : "";
			$detailparticulars_third4    = isset($documentInfo['detailparticulars_third4'])  ? urldecode($documentInfo['detailparticulars_third4']) : "";
			$detailparticulars_third5    = isset($documentInfo['detailparticulars_third5'])  ? urldecode($documentInfo['detailparticulars_third5']) : "";
			$detailparticulars_third6    = isset($documentInfo['detailparticulars_third6'])  ? urldecode($documentInfo['detailparticulars_third6']) : "";
			$detailparticulars_third7    = isset($documentInfo['detailparticulars_third7'])  ? urldecode($documentInfo['detailparticulars_third7']) : "";
			$detailparticulars_third8    = isset($documentInfo['detailparticulars_third8'])  ? urldecode($documentInfo['detailparticulars_third8']) : "";
			$detailparticulars_third9    = isset($documentInfo['detailparticulars_third9'])  ? urldecode($documentInfo['detailparticulars_third9']) : "";
			$detailparticulars_third10   = isset($documentInfo['detailparticulars_third10']) ? urldecode($documentInfo['detailparticulars_third10']) : "";
			$detailparticulars_third11   = isset($documentInfo['detailparticulars_third11']) ? urldecode($documentInfo['detailparticulars_third11']) : "";
			$detailparticulars_third12   = isset($documentInfo['detailparticulars_third12']) ? urldecode($documentInfo['detailparticulars_third12']) : "";
			$detailparticulars_third13   = isset($documentInfo['detailparticulars_third13']) ? urldecode($documentInfo['detailparticulars_third13']) : "";

			$totaldetailparticulars_first   = isset($documentInfo['totaldetailparticulars_first']) ? urldecode($documentInfo['totaldetailparticulars_first']) : "";
			$totaldetailparticulars_second  = isset($documentInfo['totaldetailparticulars_second']) ? urldecode($documentInfo['totaldetailparticulars_second']) : "";
			$totaldetailparticulars_third   = isset($documentInfo['totaldetailparticulars_third']) ? urldecode($documentInfo['totaldetailparticulars_third']) : "";
			$totaldebit   = isset($documentInfo['totaldebit']) ? urldecode($documentInfo['totaldebit']) : "";
			$totalcredit   = isset($documentInfo['totalcredit']) ? urldecode($documentInfo['totalcredit']) : "";


	
		}

		$signatory_arr		= $this->signatory;
		$businesstype		= $signatory_arr->businesstype;
		$signatory_name		= $signatory_arr->signatory_name;
		$signatory_tin		= $signatory_arr->signatory_tin;
		$signatory_role		= $signatory_arr->signatory_role;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', 'B', '11');
		$this->SetY(34);
		$this->SetX(22);
		$this->Cell(20, 5, $monthfilterFrom, 0, 0, 'R');
		$this->SetX(38);
		$this->Cell(20, 5, $dayfilterFrom, 0, 0, 'C');
		$this->SetX(47.5);
		$this->Cell(20, 5, $yearfilterFrom, 0, 0, 'C');

		$this->SetX(102);
		$this->Cell(20, 5, $monthfilterTo, 0, 0, 'R');
		$this->SetX(118.5);
		$this->Cell(20, 5, $dayfilterTo, 0, 0, 'C');
		$this->SetX(128.5);
		$this->Cell(20, 5, $yearfilterTo, 0, 0, 'C');

	
		$this->SetFont('Arial', 'B', '10');

		$this->SetY(44);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		
		$this->SetX(35.5);
		$this->Cell(20, 5, $tin1, 0, 0, 'C');

		$this->SetX(51.5);
		$this->Cell(20, 5, $tin2, 0, 0, 'C');

		$this->SetX(70);
		$this->Cell(20, 5, $tin3, 0, 0, 'C');

		$this->SetX(88.5);
		$this->Cell(20, 5, $tin4, 0, 0, 'C');

		$this->SetY(50.5);

		/**
		 * Payee's Name
		 */
		$this->SetFont('Arial', 'B', '10');
		$this->SetX(40);
		$this->Cell(20, 5, $payee_name, 0, 0, 'L');

		$this->SetFont('Arial', 'B', '10');
		/**
		 * Address
		 */
		$this->SetY(59);
		$this->SetX(40);
		$this->Cell(20, 5, $payee_address, 0, 0, 'L');

			/**
		 * ZIP Code
		 */
		// $this->SetY(59);
		// $this->SetX(186.5);
		// $array = str_split($zipcode);
		// $this->cellSpacing($array);

	
		$this->SetFont('Arial', 'B', '10');

		$this->SetY(75);

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		
		$this->SetX(35.5);
		$this->Cell(20, 5, $t_tin1, 0, 0, 'C');

		$this->SetX(51.5);
		$this->Cell(20, 5, $t_tin2, 0, 0, 'C');

		$this->SetX(70);
		$this->Cell(20, 5, $t_tin3, 0, 0, 'C');

		$this->SetX(88.5);
		$this->Cell(20, 5, $t_tin4, 0, 0, 'C');

		/**
		 * Payee's Name
		 */
		$this->SetFont('Arial', 'B', '10');
		$this->SetY(81);
		$this->SetX(40);
		$this->Cell(20, 5, $payor_name, 0, 0, 'L');

		$this->SetFont('Arial', 'B', '10');
		/**
		 * Address
		 */
		$this->SetY(89.5);
		$this->SetX(40);
		$this->Cell(20, 5, $payor_address, 0, 0, 'L');

		/**
		 * ZIP Code
		 */
		// $this->SetY(89.5);
		// $this->SetX(186.5);
		// $array = str_split($t_zipcode);
		// $this->cellSpacing($array);

		$this->SetFont('Arial', 'B', '7');

		$this->SetY(44);

		$line = 0;
		for ($x=1; $x <=13 ; $x++){
			
			/**
			 * Account Code
			 */
			$this->SetY(111+$line);
			$this->SetX(6.5);
			$this->Cell(50, 5, ${'accountcode' . $x}, 0, 0, 'L');

			$this->SetX(56);
			$this->Cell(19, 5, ${'atc_code' . $x}, 0, 0, 'L');

			$this->SetX(75.5);
			$this->Cell(24, 5, ${'detailparticulars_first' . $x}, 0, 0, 'R');

			$this->SetX(100);
			$this->Cell(24, 5, ${'detailparticulars_second' . $x}, 0, 0, 'R');

			$this->SetX(124);
			$this->Cell(24, 5, ${'detailparticulars_third' . $x}, 0, 0, 'R');

			$this->SetX(148);
			$this->Cell(24, 5, ${'debit' . $x}, 0, 0, 'R');

			$this->SetX(173);
			$this->Cell(36, 5, ${'credit' . $x}, 0, 0, 'R');

			$line += 4;
		}

			$this->SetY(163);

			$this->SetX(75.5);
			$this->Cell(24, 5, $totaldetailparticulars_first, 0, 0, 'R');

			$this->SetX(100);
			$this->Cell(24, 5, $totaldetailparticulars_second, 0, 0, 'R');

			$this->SetX(124);
			$this->Cell(24, 5, $totaldetailparticulars_third, 0, 0, 'R');

			$this->SetX(148);
			$this->Cell(24, 5, $totaldebit, 0, 0, 'R');

			$this->SetX(173);
			$this->Cell(36, 5, $totalcredit, 0, 0, 'R');

		/**
		 * Signatory
		 */
		$this->SetY(245);
		$this->SetX(7);
		if(strtolower($businesstype) == 'individual'){
			$this->Cell(5, 5, $payee_name, 0, 0, 'C');
			$this->Cell(75, 5, '', 0, 0, 'C');
		}else{
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(75, 5, $payee_name, 0, 0, 'C');
		}

		$this->SetX(40);

		$this->SetY(264.5);
		$this->SetX(7);
		if(strtolower($businesstype) == 'individual'){
			$this->Cell(5, 5, $signatory_name, 0, 0, 'C');
			$this->Cell(75, 5, '', 0, 0, 'C');
		}else{
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(75, 5, $signatory_name, 0, 0, 'C');
		}

		$this->SetX(91);
		if(strtolower($businesstype) == 'individual'){
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(37, 5, $signatory_tin, 0, 0, 'C');
		}else{
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(37, 5, $signatory_tin, 0, 0, 'C');
		}

		$this->SetX(132);
		if(strtolower($businesstype) == 'individual'){
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(35, 5, $signatory_role, 0, 0, 'C');
		}else{
			$this->Cell(5, 5,'', 0, 0, 'C');
			$this->Cell(35, 5, $signatory_role, 0, 0, 'C');
		}

		

		/**
		 * Second Page
		 */
		$this->AddPage();
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/1601-EQ-2.jpg',0,0,216,330.2);
	}

	private function cellSpacing($array = [],$cs = 5.5) {
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

