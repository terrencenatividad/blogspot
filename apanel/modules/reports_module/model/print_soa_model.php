<?php
class print_soa_model extends fpdf {

	public $font_size		= '';
	public $companyinfo		= array();
	public $documentinfo	= array();
	public $totalinfo		= array();
	public $widths			= '';
	public $aligns			= '';
	public $document 		= '';
	public $document_type	= '';
	public $document_code 	= '';
	public $vendor			= '';
    public $vendorinfo      = '';
	public $payments		= '';
	public $cheque			= '';
	public $customer		= '';
	public $customerinfo 	= '';

	public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
		parent::__construct($orientation, $unit, $size);
		$this->db = new db();
		$this->setMargins(8, 8);
	}

	private function getCompanyInfo() {
		$result = $this->db->setTable('company')
							->setFields('companycode, companyname, address, email, tin')
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

	public function setDocumentType($document_type) {
		$this->document_type = $document_type;
		return $this;
	}

	public function setDocumentCode($document_code) {
		$this->document_code = $document_code;
		return $this;
	}

	public function setCustomerInfo($details) {
		$this->customerinfo = $details;
		return $this;
	}

    public function setVendorInfo($details) {
		$this->vendorinfo   = $details;
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
		return $this;
	}

	public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}
		
	public function Header() {

		/**COMPANY INFO**/

			$this->getCompanyInfo();
			
			$companyinfo	= $this->companyinfo;
			$companycode	= $companyinfo->companycode;
			$companyname	= $companyinfo->companyname;
			$address		= $companyinfo->address;
			$email			= $companyinfo->email;
			$tin			= $companyinfo->tin;

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
			
				//Company Name
				$this->Cell(140,5,strtoupper($companyname),0,0,'L');
				$this->Ln(6);
			}

			//Company Address
			$ad_firstline		= (!empty($address)) ? substr($address,0,50) : "";
			$ad_secondline		= (!empty($address)) ? substr($address,50,50) : "";

			$this->SetFont('Arial','',9);
			$this->Cell(140,4,'Address : '.$ad_firstline,0,0,'L'); 
			$this->Ln();
			$this->Cell(15,4,'',0,0,'L');
			$this->Cell(140,4,$ad_secondline,0,0,'L');
			$this->Cell(2,$rowheight,'',0,0,'L');
			$this->Ln();
			
			//Company Email
			$this->Cell(140,4,'Email : '.$email,0,0,'L');
			
		/**COMPANY INFO - END**/

		/**DOCUMENT INFO**/

			$documentinfo	= $this->documentinfo;
			if ($documentinfo) {
				$document_type	= $this->document_type;
				$documentdate	= date("M d, Y",strtotime($documentinfo->documentdate));
				$voucherno		= $documentinfo->voucherno;
				$referenceno	= isset($documentinfo->referenceno) ? $documentinfo->referenceno : '';
				$amount			= $documentinfo->amount;
			} else if (DEBUGGING) {
				echo 'Please use setDocumentInfo() to set Header Information';
				exit();
			}
			
			//Document Name
			$this->SetFont('Arial','B',12);
			$this->Cell(60,5,$document_type,0,0,'L');
			
			$this->SetFont('Arial','',9);
			$this->Ln();
			
			//Company TEL
			$this->Cell(140,4,'Tel # : '.$tin,0,0,'L');
			
			//Invoice Date
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'DATE',1,0,'L');
			
			$this->SetFont('Arial','',9);
			$this->Cell(40,$rowheight,$documentdate,1,0,'L');
			$this->Ln();

            $vendorinfo	    	= $this->vendorinfo;
			if ($vendorinfo) {
				$vendor 		= $vendorinfo->first_name . " " . $vendorinfo->last_name;
				$vend_address 	= $vendorinfo->address1;
				$vend_email		= $vendorinfo->email;
				$vend_tinno 	= $vendorinfo->tinno;
				$vend_terms 	= $vendorinfo->terms;
				$vend_mobile 	= $vendorinfo->mobile;
			} else if (DEBUGGING) {
				echo 'Please use setVendorInfo() to set Vendor Information';
				exit();
			}

			if ( ! empty($vendor)) {
				//Customer Name
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'SOLD TO','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$vendor,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');
			}

			//Invoice Number
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'INV NO.',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(40,$rowheight,'',1,0,'L');
			$this->Ln();	

			//Customer Address
			$firstline		= (!empty($vend_address)) ? substr($vend_address,0,75) : "";
			$secondline		= (!empty($vend_address)) ? substr($vend_address,75,75) : "";
			
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'ADDRESS','TL',0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(118,$rowheight,':  '.$firstline,'TR',0,'L');
			$this->Cell(2,$rowheight,'',0,0,'L');

			//Voucher Number
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,$this->document_code.' NO',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(40,$rowheight,$voucherno,1,0,'L');
			$this->Ln();
			
			//Customer Contact #
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'CONTACT #','TL',0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(118,$rowheight,':  '.$vend_mobile,'TR',0,'L');
			$this->Cell(2,$rowheight,'',0,0,'L');

			//Purchase Order Number
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'PO. NO',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(40,$rowheight,'',1,0,'L');
			$this->Ln();
			
			//Customer TIN
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'TIN','TL',0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(118,$rowheight,':  '.$vend_tinno,'TR',0,'L');
			$this->Cell(2,$rowheight,'',0,0,'L');
			
			//Invoice Terms
			$this->SetFont('Arial','B',9);
			$this->Cell(20,$rowheight,'TERMS',1,0,'L');
			$this->SetFont('Arial','',9);
			$this->Cell(40,$rowheight,''.$vend_terms.' days',1,0,'L');
			
			$this->Ln();
			$this->Cell(138,$rowheight,'','T',0,'L');
			$this->Cell(2,$rowheight,'',0,0,'L');
			$this->Cell(60,$rowheight,'','T',0,'L');
		/**DOCUMENT INFO - END**/
	}
	
	private function drawDocumentDetails() {
		$document = $this->document;
		$this->AddPage();
		$this->Ln();
		$this->SetFillColor(233, 233, 233);
		$this->SetFont('Arial', 'B', '9');
		$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
		$this->Cell(60, 5, 'Description', 'LTB', 0, 'C', true);
		$this->Cell(30, 5, 'Quantity', 'LTB', 0, 'C', true);
		$this->Cell(30, 5, 'Price', 'LTB', 0, 'C', true);
		$this->Cell(40, 5, 'Amount', 1, 0, 'C', true);
		$this->Ln();

		if ($document) {
			$this->SetFont('Arial', '', '9');
			$this->setWidths(array(40, 60, 30,30,40));
			$this->setAligns(array('L', 'L', 'R','R','R'));
			$totalamount 	= 0;
			$notes			= $this->documentinfo->remarks;
			$disctype 		= $this->documentinfo->disctype;
			$discount 		= $this->documentinfo->discount;
			$net 			= $this->documentinfo->net;
			$amount 		= $this->documentinfo->amount;
			$vat 			= $this->documentinfo->vat;
			$wtax 			= $this->documentinfo->wtax;

			foreach ($document as $row) {
				$totalamount 		+=	$row->amount; 
				$row->amount 		=	number_format($row->amount,2); 	
				$this->row($row, $this->document_code);
			}
		
			$this->SetDrawColor(1,1,1);
			$this->Rect(8,63,200,175,'D');

			$totalinfo[0] 		= $totalamount;
			$totalinfo[1] 		= $net;
			$totalinfo[2] 		= $disctype;
			$totalinfo[3]		= $discount;
			$totalinfo[4] 		= $vat;
			$totalinfo[5] 		= $amount;
			$totalinfo[6] 		= $wtax;

			$this->totalinfo 	= $totalinfo;	

		} else {
			$this->Cell(140, 6, 'Total :', 0, 0, 'R');
		}
		
	}
	
	private function drawSignature() {
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

	function Footer(){

		$this->SetY(-70);
		$pageNo 		= $this->PageNo();
		$rowheight		= 7;
		
		$totalinfo		= $this->totalinfo;
		$totalqty		= $totalinfo[0];
		$netamt 		= $totalinfo[1];
		$disctype 		= $totalinfo[2];
		$discount 	 	= $totalinfo[3];
		$vat 			= $totalinfo[4];
		$amount 		= $totalinfo[5];
		$wtax 			= $totalinfo[6];
	
		$this->Cell(150,$rowheight,'Total purchase',0,0,'R');
		$this->Cell(50,$rowheight,number_format($amount,2),0,0,'R');
		$this->Ln(5);

		$this->Cell(150,$rowheight, 'Discount', 0, 0, 'R');

		if( $disctype  == 'perc' )
		{
			if( $discount > 0 )
			{
				$this->Cell(50, 6, '( '.number_format($discount, 2).'% ) ', 0, 0, 'R');
			}
			else
			{
				$this->Cell(50, 6, '0.00', 0, 0, 'R');
			}
		}
		else if( $discount == 'amt' )
		{
			$this->Cell(50, 6, number_format($discount, 2), 0, 0, 'R');
		}
		else
		{
			$this->Cell(50, 6, number_format($discount, 2), 0, 0, 'R');
		}
		$this->Ln(5);

		$this->Cell(150,$rowheight,'Total Purchases Tax',0,0,'R');
		$this->Cell(50,$rowheight,number_format($vat,2),0,0,'R');
		$this->Ln(5);

		$this->Cell(150,$rowheight,'Withholding Tax',0,0,'R');
		$this->Cell(50,$rowheight,number_format($wtax,2),0,0,'R');
		$this->Ln(7);

		$this->SetFont('Arial','B','9');
		$this->Cell(150,$rowheight,'Total Amount Due',0,0,'R');
		$this->Cell(50,$rowheight,number_format($netamt,2),0,0,'R');
		$this->Ln();

		$invoiceTerms	= "All bills are payable on demand unless otherwise agreed upon, interest at thirty-six percent (36%) per annum will be charged on all overdue amounts. All claims on correction to this invoice must be made within two (2) days after receipt of goods. Parties expressly submit to the jurisdiction of the courts of Makati on any legal action taken out of this transaction, an additional sum of equal to twenty-five percent (25%) of the amount due will be charged by the vendor for attorney's fees & cost of collection.";
		
		$this->SetFont('Arial','',8);
		$this->MultiCell(200,4,'TERMS & CONDITIONS: '.$invoiceTerms,0,'L');
		
		$this->Cell(20,$rowheight,'Approved By','LT',0,'L');
		$this->Cell(80,$rowheight,'','LTR',0,'L');
		
		$this->Cell(5,$rowheight,'',0,0,'L');
		
		$this->Cell(95,$rowheight,'Received the above good/s in good condition','LTR',0,'L');
		$this->Ln();
		
		$this->Cell(20,$rowheight,'Checked By','LTB',0,'L');
		$this->Cell(80,$rowheight,'',1,0,'L');
		
		$this->Cell(5,$rowheight,'',0,0,'L');
		
		$this->SetFont('Arial','','7');
		$this->Cell(60,$rowheight,'Signature Over Printed Name','LB',0,'C');
		$this->Cell(35,$rowheight,'Date Received','BR',0,'C');
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
		if ($type == 'accounts') {
			$col_index = array('accountname', 'debit', 'credit');
		} else if ($type == 'payments') {
			$col_index = array('sourceno', 'checknumber', 'referenceno', 'remarks', 'amount');
		} else if ($type == 'cheque') {
			$col_index = array('accountname', 'chequenumber', 'chequedate', 'chequeamount');
		} else if ($type == 'PO' || $type == 'SQ') {
			$col_index = array('itemcode', 'description', 'quantity', 'price', 'amount');
		}
		

		foreach ($this->widths as $index => $width) {
			$nb = max($nb, $this->NbLines($width, $data->{$col_index[$index]}));
		}
		$h = 5 * $nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		foreach ($this->widths as $index => $width) {
			$w = $width;
			$a = isset($this->aligns[$index]) ? $this->aligns[$index] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			$this->Rect($x, $y, $w, $h);
			//Print the text
			$this->MultiCell($w, 5, $data->{$col_index[$index]}, 0, $a);
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

