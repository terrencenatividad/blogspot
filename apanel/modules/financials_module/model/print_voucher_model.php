<?php
class print_voucher_model extends fpdf {

	public $font_size		= '';
	public $companyinfo		= array();
	public $documentinfo	= array();
	public $totalinfo		= array();
	public $widths			= '';
	public $aligns			= '';
	public $document_type	= '';
	public $vendor			= '';
	public $payments		= '';
	public $cheque		= '';
	public $customer		= '';


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

	public function setDocumentDetails($accounts) {
		$this->accounts = $accounts;
		return $this;
	}

	public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}
		
	public function Header() {
		$this->getCompanyInfo();
		/**COMPANY INFO**/
		$companyinfo	= $this->companyinfo;
		$companycode	= $companyinfo->companycode;
		$companyname	= $companyinfo->companyname;
		$address		= $companyinfo->address;
		$email			= $companyinfo->email;
		$tin			= $companyinfo->tin;
		
		/**DOCUMENT INFO**/
		$documentinfo	= $this->documentinfo;
		if ($documentinfo) {
			$document_type	= $this->document_type;
		$transactiondate	= isset($documentinfo->documentdate) ? 	date("M d, Y",strtotime($documentinfo->documentdate)) 	: "";
			$voucherno		= $documentinfo->voucherno;
			$referenceno	= isset($documentinfo->referenceno) ? $documentinfo->referenceno : '';
			$amount			= $documentinfo->amount;
		} else if (DEBUGGING) {
			echo 'Please use setDocumentInfo() to set Header Information';
			exit();
		}
		
		$rowheight		= 5;
		
		$this->SetFont('Arial', 'B', 12);
		//Company Name
		$this->Cell(200, $rowheight, $companyname, 0, 0, 'C');
		$this->Ln();
		
		//Company Name
		$this->SetFont('Arial', '', 12);
		$this->Cell(200, $rowheight, $document_type, 0, 0, 'C');
		$this->Ln();
		$this->Ln();	
		
		//Invoice Date
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(30 ,$rowheight, 'Invoice Date', 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(40, $rowheight, $transactiondate, 'B', 0, 'L');
		
		$this->Cell(60, $rowheight, '', 0, 0, 'L');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(30, $rowheight, 'Voucher Number', 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(40, $rowheight, $voucherno, 'B', 0, 'L');
		$this->Ln();

		if ( ! empty($referenceno)) {
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(30, $rowheight, 'Reference Number', 0, 0, 'L');
			$this->SetFont('Arial', '', 9);
			$this->Cell(40, $rowheight, $referenceno, 'B', 0, 'L');
		}
		
		$this->Cell(60, $rowheight, '', 0, 0, 'L');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(30, $rowheight, '', 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(40, $rowheight, '', 0, 0, 'L');
		$this->Ln();
		$this->Ln();

		if ( ! empty($this->vendor)) {
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(20, $rowheight, 'Vendor ', 0, 0, 'L');
			$this->SetFont('Arial', '', 9);
			$this->Cell(180, $rowheight, $this->vendor, 'B', 0, 'L');
			$this->Ln();
		}

		if ( ! empty($this->customer)) {
			$this->SetFont('Arial', 'B', 9);
			$this->Cell(20,$rowheight, 'Customer ', 0, 0, 'L');
			$this->SetFont('Arial', '', 9);
			$this->Cell(180, $rowheight, $this->customer, 'B', 0, 'L');
			$this->Ln();
		}
		
		/**AMOUNT IN WORDS**/
		$convert		= new convert($amount,'Pesos Only');
		
		$amt_words		= $convert->display() . ' (' . number_format($amount, 2) . ')';
		$first_part		= $amt_words;
		$second_part	= '';
		$wordsLen		= strlen($amt_words);

		if ($wordsLen > 100) {
			$first_part		= substr($amt_words, 0, 100);
			$second_part	= substr($amt_words, 100, 120);
		}

		$this->SetFont('Arial','I',9);
		$this->Cell(30,$rowheight,'Amount In Words ',0,0,'L');
		$this->Cell(170,$rowheight,$first_part,'B',0,'L');
		$this->Ln();
		$this->Cell(200,$rowheight,$second_part,'B',0,'L');
		$this->Ln();
	}
	
	private function drawDocumentDetails() {
		$accounts = $this->accounts;
		$this->AddPage();
		$this->Ln();
		$this->SetFillColor(233, 233, 233);
		$this->SetFont('Arial', 'B', '9');
		$this->Cell(140, 5, 'ACCOUNT CODE - ACCOUNT TITLE', 'LTB', 0, 'C', true);
		$this->Cell(30, 5, 'DEBIT', 'LTB', 0, 'C', true);
		$this->Cell(30, 5, 'CREDIT', 1, 0, 'C', true);
		$this->Ln();
		
		if ($accounts) {
			$this->SetFont('Arial', '', '9');
			$this->setWidths(array(140, 30, 30));
			$this->setAligns(array('L', 'R', 'R'));
			$accountInfo	= array();
			$totaldebit		= 0;
			$totalcredit	= 0;
			$notes			= ($this->documentinfo->remarks == '') ? ($this->documentinfo->remarks) : '';

			foreach ($accounts as $account) {
				$totaldebit			+= $account->debit;
				$totalcredit		+= $account->credit;
				$account->debit		= number_format($account->debit, 2);
				$account->credit	= number_format($account->credit, 2);
				$this->row($account);
			}
			
			$this->SetFont('Arial', 'B', '9');
			$this->Cell(140, 6, 'Total :', 0, 0, 'R');
			$this->Cell(30, 6, number_format($totaldebit, 2), 0, 0, 'R');
			$this->Cell(30, 6, number_format($totalcredit, 2), 0, 0, 'R');
			$this->Ln(10);
			
		} else {
			$this->Cell(140, 6, 'Total :', 0, 0, 'R');
		}
		
		$this->SetFont('Arial', 'B', '9');
		$this->Cell(200, 6, 'NOTES', 1, 0, 'C', true);
		$this->Ln();
		$this->SetFont('Arial', '', '9');
		$this->MultiCell(200, 6, $notes, 1);
		$this->Ln();

		if ( ! empty($this->payments)) {
			$this->SetFont('Arial', 'B', '9');
			$this->Cell(200, 6, 'PAYMENT DETAILS :', 0, 0, 'L');
			$this->Ln();
			$this->Cell(30, 6, 'INVOICE', 1, 0, 'C', true);
			$this->Cell(30, 6, 'CHECK NO', 1, 0, 'C', true);
			$this->Cell(30, 6, 'REFERENCE NO', 1, 0, 'C', true);
			$this->Cell(80, 6, 'REMARKS', 1, 0, 'C', true);
			$this->Cell(30, 6, 'AMOUNT', 1, 0, 'C', true);
			$this->Ln();
			$this->SetFont('Arial','','9');
			$totalpayment = 0;
			$this->SetWidths(array(30, 30, 30, 80, 30));
			$this->SetAligns(array('L', 'L', 'L', 'L', 'R'));
			foreach ($this->payments as $key => $payment) {

				// $payment->checknumber	= (strtolower($payment->paymenttype) == 'cash') ? '' : $payment->checknumber;
				// $payment->referenceno	= (strtolower($payment->paymenttype) == 'cash') ? $payment->referenceno : '';
				$totalpayment			+= $payment->amount;
				$payment->amount		= number_format($payment->amount, 2);
			
				$this->row($payment, 'payments');
			}
			$this->SetFont('Arial', 'B', '9');
			$this->Cell(170, 6, 'Total :', 0, 0, 'R');
			$this->Cell(30, 6, number_format($totalpayment, 2), 0, 0, 'R');
			$this->Ln(10);
		}

		if ( ! empty($this->cheque)) {
			$this->SetFont('Arial','B','9');
			$this->Cell(200, 6, 'CHEQUE DETAILS :', 0, 0, 'L');
			$this->Ln();
			$this->Cell(100, 6, 'CHEQUE ACCOUNT', 1, 0, 'C', true);
			$this->Cell(35, 6, 'CHEQUE NUMBER', 1, 0, 'C', true);
			$this->Cell(35, 6, 'CHEQUE DATE', 1, 0, 'C', true);
			$this->Cell(30, 6, 'AMOUNT', 1, 0, 'C', true);
			$this->Ln();
			$this->SetFont('Arial','','9');
			$totalpayment = 0;
			$this->SetWidths(array(100, 35, 35, 30));
			$this->SetAligns(array('L', 'L', 'L', 'R'));
			foreach ($this->cheque as $key => $cheque) {

				$totalpayment			+= $cheque->chequeamount;
				$cheque->chequedate		= date('M j, Y', strtotime($cheque->chequedate));
				$cheque->chequeamount	= number_format($cheque->chequeamount, 2);
			
				$this->row($cheque, 'cheque');
			}
			$this->SetFont('Arial', 'B', '9');
			$this->Cell(170, 6, 'Total :', 0, 0, 'R');
			$this->Cell(30, 6, number_format($totalpayment, 2), 0, 0, 'R');
			$this->Ln(10);
		}
		
		$this->drawSignature();
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

