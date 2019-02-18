<?php
class print_bir_2550M extends fpdf {

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
	
	public function Header() {
		// $this->getCompanyInfo();
		// /**COMPANY INFO**/
		// $companyinfo	= $this->companyinfo;
		// $companycode	= $companyinfo->companycode;
		// $companyname	= $companyinfo->companyname;
		// $address		= $companyinfo->address;
		// $email			= $companyinfo->email;
		// $tin			= $companyinfo->tin;
	}
	
	private function drawDocumentDetails() {
		$this->AddPage();
		$this->Image(BASE_URL.'modules/reports_module/backend/view/bir/forms/2550M.jpg',0,0,216,330.2);
		
		$documentInfo = $this->documentinfo;
		if($documentInfo){
			$monthfilter 	= $documentInfo['monthfilter'];
			$yearfilter 	= $documentInfo['yearfilter'];
			$monthfilter 	= $monthfilter >= 1 && $monthfilter <= 9 ? '0'. ' '. ' ' .$monthfilter : $monthfilter;
			$yearfilter 	= implode('   ',str_split($yearfilter));
			$amendreturn 	= isset($documentInfo['amendreturn']) ? $documentInfo['amendreturn'] : "yes";
			$shortperiod 	= isset($documentInfo['shortperiod']) ? $documentInfo['shortperiod'] : "yes";
			$attachments	= isset($documentInfo['attachments']) ? $documentInfo['attachments'] : 0;
			$attachments 	= implode('  ',str_split(sprintf('%02d', $attachments)));
			
			$tin 			= (isset($documentInfo['tin']) && !empty($documentInfo['tin'])) ? $documentInfo['tin'] : '000-000-000-000';
			$tin_arr		= explode('-',$tin);
			$tin1			= ($tin_arr[0]) ? $tin_arr[0] : '000';
			$tin1 			= implode(' ',str_split($tin1));
			$tin2			= ($tin_arr[1]) ? $tin_arr[1] : '000';
			$tin2 			= implode(' ',str_split($tin2));
			$tin3			= ($tin_arr[2]) ? $tin_arr[2] : '000';
			$tin3 			= implode(' ',str_split($tin3));
			$tin4			= ($tin_arr[3]) ? $tin_arr[3] : '000';
			$tin4 			= implode('  ',str_split($tin4));

			$rdo 			= isset($documentInfo['rdo']) ? $documentInfo['rdo'] : 0;
			$rdo 			= implode('   ',str_split($rdo));
			$agentname 		= isset($documentInfo['agentname']) ? $documentInfo['agentname'] : "";
			$agentname 		= strtoupper(urldecode($agentname));
			$firstaddress 	= isset($documentInfo['firstaddress']) ? $documentInfo['firstaddress'] : "";
			$firstaddress 	= strtoupper(urldecode($firstaddress));
			$secondaddress 	= isset($documentInfo['secondaddress']) ? $documentInfo['secondaddress'] : "";
			$secondaddress 	= strtoupper(urldecode($secondaddress));
			$zipcode 		= isset($documentInfo['zipcode']) ? $documentInfo['zipcode'] : "";
			$zipcode 		= implode('   ',str_split($zipcode));
			$contact 		= isset($documentInfo['contact']) ? $documentInfo['contact'] : "";
			$contact 		= strtoupper(urldecode($contact));
			$category		= isset($documentInfo['category']) ? $documentInfo['category'] : "yes";
			$email 			= isset($documentInfo['email']) ? $documentInfo['email'] : "";
			$email 			= strtoupper(urldecode($email));
			$tax_relief 	= isset($documentInfo['tax_relief']) ? $documentInfo['tax_relief'] : "no";
			$specify 	= isset($documentInfo['specify']) ? $documentInfo['specify'] : "";

			/**
			 * ATC Entries
			 */

			$vat_privateA	= isset($documentInfo['vat_privateA']) ? urldecode($documentInfo['vat_privateA']) : "0.00";
			$vat_privateB	= isset($documentInfo['vat_privateB']) ? urldecode($documentInfo['vat_privateB']) : "0.00";
			$vat_govA		= isset($documentInfo['vat_govA']) ? urldecode($documentInfo['vat_govA']) : "0.00";	
			$vat_govB		= isset($documentInfo['vat_govB']) ? urldecode($documentInfo['vat_govB']) : "0.00";
			$vat_zero		= isset($documentInfo['vat_zero']) ? urldecode($documentInfo['vat_zero']) : "0.00";
			$vat_exempt			= isset($documentInfo['vat_exempt']) ? urldecode($documentInfo['vat_exempt']) : "0.00";
			$totalsalesA			= $vat_privateA + $vat_govA + $vat_zero + $vat_exempt;
			$totalsalesB			= $vat_privateB + $vat_govB;
			
			$carriedover20A		= isset($documentInfo['carriedover20A']) ? urldecode($documentInfo['carriedover20A']) : "0.00";
			$deferred20B		= isset($documentInfo['deferred20B']) ? urldecode($documentInfo['deferred20B']) : "0.00";
			$transitionalinputtax20C		= isset($documentInfo['transitionalinputtax20C']) ? urldecode($documentInfo['transitionalinputtax20C']) : "0.00";
			$presumptiveinputtax26D		= isset($documentInfo['presumptiveinputtax26D']) ? urldecode($documentInfo['presumptiveinputtax26D']) : "0.00";
			$others20E		= isset($documentInfo['others20E']) ? urldecode($documentInfo['others20E']) : "0.00";
			$totalsum20F		= isset($documentInfo['totalsum20F']) ? urldecode($documentInfo['totalsum20F']) : "0.00";
			
			$cgnotexceed21A		= isset($documentInfo['cgnotexceed21A']) ? urldecode($documentInfo['cgnotexceed21A']) : "";
			$cgnotexceed21B		= isset($documentInfo['cgnotexceed21B']) ? urldecode($documentInfo['cgnotexceed21B']) : "";
			$cgexceed21C		= isset($documentInfo['cgexceed21C']) ? urldecode($documentInfo['cgexceed21C']) : "";
			$cgexceed21D		= isset($documentInfo['cgexceed21D']) ? urldecode($documentInfo['cgexceed21D']) : "";
			$dompurchase21E		= isset($documentInfo['dompurchase21E']) ? urldecode($documentInfo['dompurchase21E']) : "";
			$dompurchase21F		= isset($documentInfo['dompurchase21F']) ? urldecode($documentInfo['dompurchase21F']) : "";

			$importation21G		= isset($documentInfo['importation21G']) ? urldecode($documentInfo['importation21G']) : "0.00";
			$importation21H		= isset($documentInfo['importation21H']) ? urldecode($documentInfo['importation21H']) : "0.00";
			$dompurchaseserv21I		= isset($documentInfo['dompurchaseserv21I']) ? urldecode($documentInfo['dompurchaseserv21I']) : "0.00";
			$dompurchaseserv21J		= isset($documentInfo['dompurchaseserv21J']) ? urldecode($documentInfo['dompurchaseserv21J']) : "0.00";
			$servicerenderedK		= isset($documentInfo['servicerenderedK']) ? urldecode($documentInfo['servicerenderedK']) : "0.00";
			$servicerenderedL		= isset($documentInfo['servicerenderedL']) ? urldecode($documentInfo['servicerenderedL']) : "0.00";

			$purchasenotqualified21M		= isset($documentInfo['purchasenotqualified21M']) ? urldecode($documentInfo['purchasenotqualified21M']) : "0.00";
			$others21N	= isset($documentInfo['others21N']) ? urldecode($documentInfo['others21N']) : "0.00";
			$others2O	= isset($documentInfo['others2O']) ? urldecode($documentInfo['others2O']) : "0.00";
			$totalpurchases21P	= isset($documentInfo['totalpurchases21P']) ? urldecode($documentInfo['totalpurchases21P']) : "0.00";
			$total22	= isset($documentInfo['total22']) ? urldecode($documentInfo['total22']) : "0.00";
			$totalavailableinputtax23A		= isset($documentInfo['totalavailableinputtax23A']) ? urldecode($documentInfo['totalavailableinputtax23A']) : "0.00";
			$totalavailableinputtax23B	= isset($documentInfo['totalavailableinputtax23B']) ? urldecode($documentInfo['totalavailableinputtax23B']) : "0.00";

			$taxallocable23C			= isset($documentInfo['taxallocable23C']) ? urldecode($documentInfo['taxallocable23C']) : "";

			$vatrefund23D				= isset($documentInfo['vatrefund23D']) ? urldecode($documentInfo['vatrefund23D']) : "0.00";
			$other23E				= isset($documentInfo['other23E']) ? urldecode($documentInfo['other23E']) : "0.00";
			$total23F				= isset($documentInfo['total23F']) ? urldecode($documentInfo['total23F']) : "0.00";
			$totalallowableinputtax24				= isset($documentInfo['totalallowableinputtax24']) ? urldecode($documentInfo['totalallowableinputtax24']) : "0.00";
			$netpayable25				= isset($documentInfo['netpayable25']) ? urldecode($documentInfo['netpayable25']) : "0.00";
			$monthlyvat26A				= isset($documentInfo['monthlyvat26A']) ? urldecode($documentInfo['monthlyvat26A']) : "0.00";
			$creditablevat26B				= isset($documentInfo['creditablevat26B']) ? urldecode($documentInfo['creditablevat26B']) : "0.00";
			$sugarandflour26C				= isset($documentInfo['sugarandflour26C']) ? urldecode($documentInfo['sugarandflour26C']) : "0.00";
			$inputtaxsale26D				= isset($documentInfo['inputtaxsale26D']) ? urldecode($documentInfo['inputtaxsale26D']) : "0.00";
			$vatpaid26E				= isset($documentInfo['vatpaid26E']) ? urldecode($documentInfo['vatpaid26E']) : "0.00";
			$advpaymentsmade26F				= isset($documentInfo['advpaymentsmade26F']) ? urldecode($documentInfo['advpaymentsmade26F']) : "0.00";
			$otherstaxcredits26G				= isset($documentInfo['otherstaxcredits26G']) ? urldecode($documentInfo['otherstaxcredits26G']) : "0.00";
			$totaltaxcredits26H				= isset($documentInfo['totaltaxcredits26H']) ? urldecode($documentInfo['totaltaxcredits26H']) : "0.00";
			$taxstillpayable27				= isset($documentInfo['taxstillpayable27']) ? urldecode($documentInfo['taxstillpayable27']) : "0.00";

			$surcharge			= isset($documentInfo['surcharge']) ? urldecode($documentInfo['surcharge']) : "0.00";
			$interest			= isset($documentInfo['interest']) ? urldecode($documentInfo['interest']) : "0.00";
			$compromise			= isset($documentInfo['compromise']) ? urldecode($documentInfo['compromise']) : "0.00";
			$penalties			= isset($documentInfo['penalties']) ? urldecode($documentInfo['penalties']) : "0.00";
			$total_payable			= isset($documentInfo['total_payable']) ? urldecode($documentInfo['total_payable']) : "0.00";

			$taxagent			= isset($documentInfo['taxagent']) ? urldecode($documentInfo['taxagent']) : "";
			$signature			= isset($documentInfo['signature']) ? urldecode($documentInfo['signature']) : "";
			$dateissuance			= isset($documentInfo['dateissuance']) ? urldecode($documentInfo['dateissuance']) : "";
			$expiry			= isset($documentInfo['expiry']) ? urldecode($documentInfo['expiry']) : "";
			$position1			= isset($documentInfo['position1']) ? urldecode($documentInfo['position1']) : "";
			$position2			= isset($documentInfo['position2']) ? urldecode($documentInfo['position2']) : "";
			$tin_signatory1			= isset($documentInfo['tin_signatory1']) ? urldecode($documentInfo['tin_signatory1']) : "";
			$tin_signatory2			= isset($documentInfo['tin_signatory2']) ? urldecode($documentInfo['tin_signatory2']) : "";
			
		}

		$signatory_arr		= $this->signatory;
		$businessline		= $signatory_arr->businessline;
		$signatory_name		= $signatory_arr->signatory_name;
		/**	
		 * For the Year
		 */
		$this->SetFont('Arial', 'B', '11');
		$this->SetY(29.5);
		$this->SetX(60);
		$this->Cell(20, 5, $monthfilter, 0, 0, 'C');
		$this->SetY(29.5);
		$this->SetX(75);
		$this->Cell(20, 5, $yearfilter, 0, 0, 'C');
		/**
		 * Amend Return
		 */
		$this->SetFont('Arial', 'B', '8');
		if($amendreturn == "yes"){
			$this->SetY(30.5);
			$this->SetX(120);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetY(30.2);
			$this->SetX(132.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		$this->SetFont('Arial', 'B', '11');
		$this->SetX(189);
		$this->Cell(10, 3, $attachments, 0, 0, 'C');

		/**
		 * Taxpayer Identification Number (TIN)
		 */
		$this->SetFont('Arial', 'B', '12');
		$this->SetY(40);
		$this->Cell(42, 5, $tin1, 0, 0, 'C');

		$this->SetY(40);
		$this->Cell(69, 5, $tin2, 0, 0, 'C');

		$this->SetY(40);
		$this->Cell(96, 5, $tin3, 0, 0, 'C');

		/**
		 * RDO Code
		 */
		$this->SetY(40);
		$this->Cell(105, 5, $rdo, 0, 0, 'R');

		$this->SetY(39);
		$this->SetX(170);
		$this->Cell(5, 5, $businessline, 0, 0, 'C');

		/**
		 * Agent's Name
		 */
		$this->SetFont('Arial', 'B', '10');
		$this->SetX(50);
		$this->Cell(2, 23, $agentname, 0, 0, 'C');

		/**
		 * Contact number
		 */
		$this->SetX(180);
		$this->Cell(5, 22, $contact, 0, 0, 'C');

		/**
		 * First Address Line
		 */
		$this->SetFont('Arial', 'B', '10');
		$this->SetY(49);
		$this->SetX(85);
		$array = str_split($firstaddress);
		$this->Cell(5, 20, $firstaddress . $secondaddress, 0, 0, 'C');

		 // * ZIP Code

		$this->SetX(192);
		$this->Cell(5, 19, $zipcode, 0, 0, 'C');
		/**
		 * Category of Withholding Tax
		 */
		$this->SetFont('Arial', 'B', '9');
		$this->SetY(87.4);
		if($tax_relief == "yes"){
			$this->SetY(63);
			$this->SetX(87);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}else{
			$this->SetY(63);
			$this->SetX(105.5);
			$this->Cell(5, 5, 'X', 0, 0, 'C');
		}

		$this->SetFont('Arial', 'B', '10');
		$this->SetY(55);
		$this->SetX(153);
		$this->Cell(5, 19, $specify, 0, 0, 'C');
		$this->SetFont('Arial', 'B', '9');
		$this->SetY(66);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($vat_privateA, 2), 0, 0, 'R');
		$this->SetX(199);
		$this->Cell(5, 19, number_format($vat_privateB,2), 0, 0, 'R');
		$this->SetY(69);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($vat_govA,2), 0, 0, 'R');
		$this->SetX(199);
		$this->Cell(5, 19, number_format($vat_govB,2), 0, 0, 'R');
		$this->SetY(72);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($vat_zero,2), 0, 0, 'R');
		$this->SetY(75.5);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($vat_exempt,2), 0, 0, 'R');
		$this->SetY(80);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($totalsalesA,2), 0, 0, 'R');
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totalsalesB,2), 0, 0, 'R');
		$this->SetY(84.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($carriedover20A,2), 0, 0, 'R');
		$this->SetY(88);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($deferred20B,2), 0, 0, 'R');
		$this->SetY(92);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($transitionalinputtax20C,2), 0, 0, 'R');
		$this->SetY(96);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($presumptiveinputtax26D,2), 0, 0, 'R');
		$this->SetY(99);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($others20E,2), 0, 0, 'R');
		$this->SetY(102);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totalsum20F,2), 0, 0, 'R');

		$this->SetY(109);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($cgnotexceed21A,2), 0, 0, 'R');
		$this->SetY(109);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($cgnotexceed21B,2), 0, 0, 'R');
		$this->SetY(112.5);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($cgexceed21C,2), 0, 0, 'R');
		$this->SetY(112.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($cgexceed21D,2), 0, 0, 'R');
		$this->SetY(116);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($dompurchase21E,2), 0, 0, 'R');
		$this->SetY(116);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($dompurchase21F,2), 0, 0, 'R');
		$this->SetY(119);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($importation21G,2), 0, 0, 'R');
		$this->SetY(119);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($importation21H,2), 0, 0, 'R');
		$this->SetY(123);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($dompurchaseserv21I,2), 0, 0, 'R');
		$this->SetY(123);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($dompurchaseserv21J,2), 0, 0, 'R');
		$this->SetY(126);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($servicerenderedK,2), 0, 0, 'R');
		$this->SetY(126);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($servicerenderedL,2), 0, 0, 'R');
		$this->SetY(129);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($purchasenotqualified21M,2), 0, 0, 'R');
		$this->SetY(132);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($others21N,2), 0, 0, 'R');
		$this->SetY(132.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($others2O,2), 0, 0, 'R');
		$this->SetY(135);
		$this->SetX(149);
		$this->Cell(5, 19, number_format($totalpurchases21P,2), 0, 0, 'R');
		$this->SetY(139);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($total22,2), 0, 0, 'R');

		$this->SetY(148.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totalavailableinputtax23A,2), 0, 0, 'R');
		$this->SetY(152);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totalavailableinputtax23B,2), 0, 0, 'R');
		$this->SetY(155);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($taxallocable23C,2), 0, 0, 'R');
		$this->SetY(158);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($vatrefund23D,2), 0, 0, 'R');
		$this->SetY(161);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($other23E,2), 0, 0, 'R');
		$this->SetY(165);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($total23F,2), 0, 0, 'R');
		$this->SetY(169);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totalallowableinputtax24,2), 0, 0, 'R');
		$this->SetY(172);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($netpayable25,2), 0, 0, 'R');
		$this->SetY(178.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($monthlyvat26A,2), 0, 0, 'R');
		$this->SetY(182);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($creditablevat26B,2), 0, 0, 'R');
		$this->SetY(185.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($sugarandflour26C,2), 0, 0, 'R');
		$this->SetY(189);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($inputtaxsale26D,2), 0, 0, 'R');
		$this->SetY(192);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($vatpaid26E,2), 0, 0, 'R');
		$this->SetY(195);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($advpaymentsmade26F,2), 0, 0, 'R');
		$this->SetY(198);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($otherstaxcredits26G,2), 0, 0, 'R');
		$this->SetY(202);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($totaltaxcredits26H,2), 0, 0, 'R');

		$this->SetFont('Arial', 'B', '7');
		$this->SetY(208.5);
		$this->SetX(68);
		$this->Cell(5, 19, number_format($surcharge,2), 0, 0, 'R');
		$this->SetY(208.5);
		$this->SetX(107);
		$this->Cell(5, 19, number_format($interest,2), 0, 0, 'R');
		$this->SetY(208.5);
		$this->SetX(147);
		$this->Cell(5, 19, number_format($compromise,2), 0, 0, 'R');
		$this->SetFont('Arial', 'B', '9');
		$this->SetY(208.5);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($penalties,2), 0, 0, 'R');
		$this->SetY(212);
		$this->SetX(199);
		$this->Cell(5, 19, number_format($total_payable,2), 0, 0, 'R');

		$this->SetY(222);
		$this->SetX(80);
		$this->Cell(5, 19, $agentname, 0, 0, 'R');

		$this->SetY(222);
		$this->SetX(160);
		$this->Cell(5, 19, $signature, 0, 0, 'R');

		$this->SetY(235);
		$this->SetX(40);
		$this->Cell(5, 19, $position1, 0, 0, 'R');

		$this->SetY(235);
		$this->SetX(105);
		$this->Cell(5, 19, $tin_signatory1, 0, 0, 'R');

		$this->SetY(235);
		$this->SetX(170);
		$this->Cell(5, 19, $position2, 0, 0, 'R');

		$this->SetY(243);
		$this->SetX(35);
		$this->Cell(5, 19, $taxagent, 0, 0, 'R');

		$this->SetY(243);
		$this->SetX(85);
		$this->Cell(5, 19, $dateissuance, 0, 0, 'R');

		$this->SetY(243);
		$this->SetX(118);
		$this->Cell(5, 19, $expiry, 0, 0, 'R');

		$this->SetY(243);
		$this->SetX(178);
		$this->Cell(5, 19, $tin_signatory2, 0, 0, 'R');
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

