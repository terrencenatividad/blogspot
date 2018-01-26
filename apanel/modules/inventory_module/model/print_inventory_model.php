<?php
class print_inventory_model extends fpdf {

	public $font_size		= '';
	public $companyinfo		= array();
	public $documentinfo	= array();
	public $totalinfo		= array();
	public $col_index 		= array();
	public $widths			= '';
	public $aligns			= '';
	public $document 		= '';
	public $document_type	= '';
	public $document_code 	= '';
	public $vendor			= '';
	public $payments		= '';
	public $cheque			= '';
	public $customer		= '';
	public $customerinfo 	= '';
	public $outline 		= 'yes';

	public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
		parent::__construct($orientation, $unit, $size);
		$this->db = new db();
		if( $this->outline == 'yes' ){
			$this->setMargins(8, 8);
		}
		else{
			$this->setMargins(11, 8);
		}
	}

	private function getCompanyInfo() {
		$result = $this->db->setTable('company')
							->setFields('companycode, companyname, address, email, tin, phone, fax,mobile')
							->setLimit(1)
							->runSelect()
							->getRow();
		$this->companyinfo = $result;
	}

	public function setPreviewTitle($title) {
		$this->SetTitle($title . ' - Test', true);
		return $this;
	}

	public function setVendor($vendor) {
		$this->vendor = $vendor;
		return $this;
	}

	public function setCustomer($customer) {
		$this->customer = $customer;
		return $this;
	}

	public function setOutline($outline) {
		$this->outline = $outline;
		return $this;
	}

	public function setDocumentType($document_type) {
		$this->document_type = $document_type;
		return $this;
	}

	public function setDocumentCode($document_code) {
		$this->document_code = $document_code;
		return $this;
	}

	public function setCustomerInfo($details = "") {
		$this->customerinfo = $details;
		return $this;
	}

	public function setDocumentInfo($details) {
		$this->documentinfo = $details;
		return $this;
	}

	public function setPayments($payments) {
		$this->payments = $payments;
		return $this;
	}

	public function setCheque($cheque) {
		$this->cheque = $cheque;
		return $this;
	}

	public function setDocumentDetails($details) {
		$this->document = $details;
		// var_dump($this->document);
		return $this;
	}

	public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}
		
	public function Header() {

		if( $this->outline == 'yes' ){

			/**DOCUMENT INFO**/

			$documentinfo	= $this->documentinfo;
			if ($documentinfo) {
				$document_type	= $this->document_type;
				$documentdate	= date("M d, Y",strtotime($documentinfo->transactiondate));
				$voucherno		= $documentinfo->stocktransferno;
				$source			= isset($documentinfo->source) ? $documentinfo->source : '';
				$destination	= isset($documentinfo->destination) ? $documentinfo->destination : '' ;
				$reference 		= isset($documentinfo->reference)  ?	$documentinfo->reference : '';
				$prepared 		= isset($documentinfo->prepared_by) 	? 	$documentinfo->prepared_by 	: 	'';
				$notes 			= isset($documentinfo->remarks) 	?	$documentinfo->remarks 	: 	'';
			} 
			/**DOCUMENT INFO--END**/

			/**COMPANY INFO**/
			$this->getCompanyInfo();
			
			$companyinfo	= $this->companyinfo;
			$companycode	= $companyinfo->companycode;
			$companyname	= $companyinfo->companyname;
			$address		= $companyinfo->address;
			$email			= $companyinfo->email;
			$tin			= $companyinfo->tin;
			$phone 			= $companyinfo->phone;
			$rowheight		= 5;
		
			$this->SetFont('Arial', 'B', 12);

			if(!empty($companylogo)){
				//Company Logo
				$imageInfo	 	= getimagesize('../../../ajax/uploads/company_logo/'.$companylogo);
				$imageWidth		= $imageInfo[0];
				$imageHeight	= $imageInfo[1];
				
				$halfWidth		= $imageWidth / 2;
				$vertlogo		= ($halfWidth > $imageHeight) ? true : false;
				
				if($vertlogo){
					$this->Image('../../../ajax/uploads/company_logo/'.$companylogo,8,5,70,15);
				}else{
					$this->Image('../../../ajax/uploads/company_logo/'.$companylogo,8,5,20,20);
				}
				
				$this->Ln(6);
			}else{
			
				$this->Cell(140,5,strtoupper($companyname),0,0,'L');
				// $this->Ln(6);
			}

			//Document Name
			$this->SetFont('Arial','B',12);
			$this->Cell(60,5,$document_type,0,0,'L');
			
			$this->SetFont('Arial','',9);
			$this->Ln();
			//Company Address
			$ad_firstline		= (!empty($address)) ? substr($address,0,50) : "";
			$ad_secondline		= (!empty($address)) ? substr($address,50,50) : "";

			$this->SetFont('Arial','',9);
			$this->Cell(140,4,'Address : '.$ad_firstline,0,0,'L'); 
			//Invoice Date
			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'DATE',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$documentdate,1,0,'L');
			$this->Ln();

			$this->Cell(15,4,'',0,0,'L');
			$this->Cell(125,4,$ad_secondline,0,0,'L');
			
			//Invoice Number
			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'ST NO.',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$voucherno,1,0,'L');
			$this->Ln();	

			//Company Email
			$this->Cell(140,4,'Email : '.$email,0,0,'L');

			//Warehouse Source:
			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'Source',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$source,1,0,'L');

			$this->Ln();
			
			//Company TEL
			$this->Cell(140,4,'Tel # : '.$phone,0,0,'L');

			//Warehouse Destination:
			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'Destination',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$destination,1,0,'L');

			$this->Ln();

			// Prepared By
			$note_first			= (!empty($notes)) ? substr($notes,0,50) : "";
			$note_second		= (!empty($notes)) ? substr($notes,50,50) : "";

			$this->SetFont('Arial','',9);
			$this->Cell(140,4,'Notes : '.$note_first,0,0,'L'); 

			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'Reference #',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$reference,1,0,'L');

			$this->Ln();
			
			$this->Cell(15,4,'',0,0,'L');
			$this->Cell(125,4,$note_second,0,0,'L');

			$this->SetFont('Arial','B',9);
			$this->Cell(22,$rowheight,'Prepared By',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(38,$rowheight,$prepared,1,0,'L');

			/**COMPANY INFO - END**/
			
			$customerinfo		= $this->customerinfo;
			if ($customerinfo) {
				$customer		= $customerinfo->first_name . " " . $customerinfo->last_name;
				$cust_company 	= $customerinfo->partnername;
				$cust_address 	= $customerinfo->address1;
				$cust_email		= $customerinfo->email;
				$cust_tinno 	= $customerinfo->tinno;
				$cust_terms 	= $customerinfo->terms;
				$cust_mobile 	= $customerinfo->mobile;
			} 

			if ( ! empty($customer)) {
				//Customer Name
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'SOLD TO','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$cust_company,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');
			}

			
			$this->Ln(10);
				
			$this->SetDrawColor(1,1,1);
			$this->Rect(8,53,200,215,'D');
			/**DOCUMENT INFO - END**/
		} 
		
	}
	
	private function drawDocumentDetails() {
		$document = $this->document;

		$this->AddPage();
		$this->SetFillColor(233, 233, 233);
		$this->SetFont('Arial', 'B', '9');

		if( $this->outline == 'yes' ){
			if ($this->document_code == 'ST' ){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(60, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(25, 5, 'Quantity', 1, 0, 'C', true);
				$this->Cell(25, 5, 'UOM', 1, 0, 'C', true);
				$this->Cell(25, 5, 'Price', 1, 0, 'C', true);
				$this->Cell(25, 5, 'Amount', 1, 0, 'C', true);
				$this->setWidths(array(40, 60, 25, 25,25,25));
			}

			$this->Ln();
			$this->SetFont('Arial', '', '9');

			$totalamount 	= 0;
			$totalqty 		= 0;
	
			if ($document) {
				foreach($document as $data)
				{
					foreach($data as $key => $value){
						if( !in_array($key, $this->col_index) ){
							array_push($this->col_index, $key);
						}
					}
					$this->row($data, $this->document_code);

					$totalqty 		+= $data->qtytoapply;
					$totalamount 	+= $data->amount;
				}

				$totalinfo[0] 		= $totalqty;
				$totalinfo[1] 		= $totalamount;

				$this->totalinfo 	= $totalinfo;
			}
		} else {
			if ($this->document_code == 'ST' ){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(85, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(25, 5, 'Quantity', 1, 0, 'C', true);
				$this->Cell(25, 5, 'Price', 1, 0, 'C', true);
				$this->Cell(25, 5, 'Amount', 1, 0, 'C', true);
				$this->setWidths(array(40, 85, 25,25,25));
			} else { 
				$this->Cell(200,5,'',0,0,'L');
				$this->setWidths(array(22,17.5,101.5,22,31));
				$this->setAligns(array('L', 'R', 'L','R','R'));
			}
		}
	}
	
	private function drawSignature() {
		if( $this->outline == 'yes' ){
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(50, 4, 'Prepared By:', 0, 0, 'L');
			$this->Cell(100, 4, '', '0', 0, 'L');
			$this->Cell(50, 4, 'Checked By:', 0, 0, 'L');
			$this->Ln(4);
			$this->SetFont('Arial', '', 7);
			$this->Cell(50, 4,'', 'B', 0, 'L');
			$this->Cell(100, 4, '', '0', 0, 'L');
			$this->Cell(50, 4,'','B',0,'L');
			$this->Ln();
			$this->SetFont('Arial', '', 7);
			$this->Cell(50, 4, '', 0, 0, 'C');
			$this->Cell(100, 4, '', 0, 0, 'L');
			$this->Cell(50, 4, '', 0, 0, 'C');
			$this->Ln(4);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(50, 4, 'Noted By:', 0, 0, 'L');
			$this->Cell(100, 4, '', '0', 0, 'L');
			$this->Cell(50, 4, 'Approved By:', 0, 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 7);
			$this->Cell(50, 4, '', 'B', 0, 'L');
			$this->Cell(100, 4, '', '0', 0, 'L');
			$this->Cell(50, 4, '', 'B', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 7);
			$this->Cell(50, 4, '', 0, 0, 'C');
			$this->Cell(100, 4, '', 0, 0, 'L');
			$this->Cell(50, 4, '', 0, 0, 'C');
		}
	}

	function Footer(){
		if( $this->outline == 'yes' ){
			$this->SetY(-23);
			$pageNo 		= $this->PageNo();
			$rowheight		= 7;
			
			$totalinfo		= $this->totalinfo;
			$totalqty		= $totalinfo[0];
			$totalamt 		= $totalinfo[1];

			$this->SetFont('Arial','B','9');
			$this->Cell(150,$rowheight,'Total Quantity',0,0,'R');
			$this->Cell(50,$rowheight,number_format($totalqty,2),0,0,'R');
			$this->Ln(5);

			$this->Cell(150,$rowheight,'Total Amount',0,0,'R');
			$this->Cell(50,$rowheight,number_format($totalamt,2),0,0,'R');
			$this->Ln(5);
		} else {
			$this->SetY(-108);
			$pageNo 		= $this->PageNo();
			$rowheight		= 6.5;
			
			$amount_due 	= 0.00;
			$add_vat 		= 0.00;
			$less_vat 		= 0.00;

			$totalinfo		= $this->totalinfo;
			$totalqty		= $totalinfo[0];
			$totalamt 		= $totalinfo[1];
			

			if($this->document_code == 'SQ'){
				$this->Ln(5);
				$this->Ln(5);
				$this->Cell(150,$rowheight,'',0,0,'R');
				$this->Ln(5);
				$this->Ln(5);
				$this->Ln(7);
				$this->Ln();
			} 
			else 
			{
				// Total Sales = VATABLE Sales + VAT ( 12% )
				//$this->Cell(145,$rowheight,'Total Sales',0,0,'R');
				$this->Cell(145,$rowheight,'',0,0,'R');
				$this->Cell(49,$rowheight,number_format($total_sales,2),0,0,'R');
				$this->Ln($rowheight);

				if( $discount <= 0 && $vat_exempt <= 0 )
				{
					$amount_due 	=	0.00;
					$add_vat 		= 	0.00;

					$vat_sales 		= 	0.00;
					$vat_exempt 	= 	0.00;
					$vat_zero 		= 	0.00;
					$vat 			= 	0.00;
				}

				// Less VAT = VAT
				// $this->Cell(145,$rowheight,'Less: VAT',10,0,'R');
				$this->Cell(145,$rowheight,'',0,0,'R');
				$this->Cell(49,$rowheight,number_format($less_vat,2),0,0,'R');
				$this->Ln($rowheight);

				// Amount Net of Vat = Vatable Sales - OK
				// $this->Cell(145,$rowheight,'Amount Net of VAT',0,0,'R');
				$this->Cell(145,$rowheight,'',0,0,'R');
				$this->Cell(49,$rowheight,number_format($vat_sales,2),0,0,'R');
				$this->Ln($rowheight);

				//$this->Cell(65,$rowheight,'VATable Sales',0,0,'R');
				$this->Cell(65,$rowheight,'',0,0,'R');
				$this->Cell(30,$rowheight,number_format($vat_sales,2),0,0,'R');

				//Should be in decimal .. SUM of discounts
				// $this->Cell(55,$rowheight,'Less SC/PWD Discount',0,0,'R');
				$this->Cell(55,$rowheight,'',0,0,'R');
				$this->Cell(44,$rowheight,number_format($discount,2),0,0,'R');
				$this->Ln($rowheight);
				
				//OK
				// $this->Cell(65,$rowheight,'VAT-Exempt Sales',0,0,'R');
				$this->Cell(65,$rowheight,'',0,0,'R');
				$this->Cell(30,$rowheight,number_format($vat_exempt,2),0,0,'R');

				// Total Sales - Discount
				// $this->Cell(55,$rowheight,'Amount Due',0,0,'R');
				$this->Cell(55,$rowheight,'',0,0,'R');
				$this->Cell(44,$rowheight,number_format($amount_due,2),0,0,'R');
				$this->Ln($rowheight);

				//OK
				// $this->Cell(65,$rowheight,'Zero-Rated Sales',0,0,'R');
				$this->Cell(65,$rowheight,'',0,0,'R');
				$this->Cell(30,$rowheight,number_format($vat_zero,2),0,0,'R');

				// $this->Cell(55,$rowheight,'Add: VAT',0,0,'R');
				$this->Cell(55,$rowheight,'',0,0,'R');
				$this->Cell(44,$rowheight,number_format($add_vat,2),0,0,'R');
				$this->Ln($rowheight);

				// OK
				// $this->Cell(65,$rowheight,'VAT Amount',0,0,'R');
				$this->Cell(65,$rowheight,'',0,0,'R');
				$this->Cell(30,$rowheight,number_format($vat,2),0,0,'R');

				$this->SetFont('Arial','B','9');
				// $this->Cell(55,$rowheight,'Total Amount Due',0,0,'R');
				$this->Cell(55,$rowheight,'',0,0,'R');
				$this->Cell(44,$rowheight,number_format($netamt,2),0,0,'R');
				$this->Ln();
			}
		}
	}

	/**START OF MULTICELL TABLE FUNCTION**/
	private function setWidths($w) {
		//Set the array of column widths
		$this->widths = $w;
	}

	private function setAligns($a) {
		//Set the array of column alignments
		$this->aligns = $a;
	}

	private function row($data, $type = 'accounts') {
		//Calculate the height of the row
		$nb = 0;

		$col_index 	=	$this->col_index;
		
		foreach ($this->widths as $index => $width) {
			$nb = max($nb, $this->NbLines($width, $data->{$col_index[$index]}));
		}

		$h = 6.5 * $nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		foreach ($this->widths as $index => $width) {
			$w = $width;
			$a = isset($this->aligns[$index]) ? $this->aligns[$index] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();

			//Print the text
			$this->MultiCell($w, 7, $data->{$col_index[$index]}, 0, $a);
			//Put the position to the right of the cell
			$this->SetXY($x + $w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	private function CheckPageBreak($h) {
		//If the height h would cause an overflow, add a new page immediately
		if ($this->GetY() + $h>$this->PageBreakTrigger) {
			$this->AddPage($this->CurOrientation);
		}
	}

	private function NbLines($w, $txt) {
		//Computes the number of lines a MultiCell of width w will take
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0) {
			$w = $this->w - $this->rMargin - $this->x;
		}
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 && $s[$nb - 1] == "\n") {
			$nb--;
		}
		$sep	= -1;
		$i		= 0;
		$j		= 0;
		$l		= 0;
		$nl		= 1;
		while ($i < $nb) {
			$c = $s[$i];
			if ($c == "\n") {
				$i++;
				$sep	= -1;
				$j		= $i;
				$l		= 0;
				$nl++;
				continue;
			}
			if ($c == ' ') {
				$sep = $i;
			}
			$l += $cw[$c];
			if ($l > $wmax) {
				if ($sep == -1) {
					if ($i == $j) {
						$i++;
					}
				} else {
					$i=$sep+1;
				}
				$sep	= -1;
				$j		= $i;
				$l		= 0;
				$nl++;
			} else {
				$i++;
			}
		}
		return $nl;
	}
	
}

