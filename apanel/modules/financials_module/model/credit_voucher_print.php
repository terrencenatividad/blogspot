<?php
class credit_voucher_print extends fpdf {

    public function __construct() {
        parent::__construct('P', 'mm', 'A4');
        $this->date = new date();
        $this->total_applied = 0;
    }

    public function setHeaderDetails($data) {
        $this->header_details = $data;
        return $this;
    }

    public function setRVDetails($data) {
        $this->rv_details = $data;
        return $this;
    }

    public function header() {
		$this->SetFont("Arial", "B", "14");
		$this->Cell(192,7, 'CREDIT VOUCHER', 0, 1, 'C');
		if ($this->header_details->stat == 'inactive') {
			$this->SetFont("Arial", "BI", "10");
			$this->Cell(192,7, 'CANCELLED', 0, 1, 'C');
		}

		$this->Ln();

		$this->SetFont("Arial", "", "10");
		$this->Cell(42,7, 'Transaction Date:', 0, 0, 'L');
		$this->Cell(60,7, $this->date->dateFormat($this->header_details->transactiondate), 'B', 1, 'L');
		$this->Cell(42,7, 'Voucher No:', 0, 0, 'L');
		$this->Cell(60,7, $this->header_details->voucherno, 'B', 1, 'L');

		$this->SetFont("Arial", "BI", "11"); 
		$this->Cell(192,7, $this->header_details->partnername, 0, 1, 'L');


		$this->Ln();
		$this->SetFont("Arial", "B", "9");

		$this->Cell(74,7, 'Account', 'LTB', 0, 'C');
		$this->Cell(74,7, 'Description', 'TB', 0, 'C');
		$this->Cell(44,7, 'Credit', 'RTB', 1, 'C');

		$total_credit = 0;

		foreach ($this->rv_details as $row) {
			$this->SetFont("Arial", "", "9");

			$this->Cell(74,7, $row->accountname, 'L', 0, 'L');
			$this->Cell(74,7, $row->detailparticulars, 0, 0, 'C');
			$this->Cell(44,7, number_format($row->credit, 2), 'R', 1, 'R');

			$total_credit += $row->credit;
		}
		
		$this->Cell(192, 40, '', 'LR', 1, 'C');

		$this->SetFont("Arial", "B", "9");
		$this->Cell(148,5, 'Total:', 'LB', 0, 'L');
		$this->Cell(44,5, number_format($total_credit, 2), 'RB', 1, 'R');

		// $this->Ln();

		// $this->SetFont("Arial", "B", "9");

        // $this->Cell(48,7, 'Date', 'LTB', 0, 'C');
		// $this->Cell(48,7, 'RV No.', 'TB', 0, 'C');
		// $this->Cell(48,7, 'SI No.', 'TB', 0, 'C');
		// $this->Cell(48,7, 'Amount', 'RTB', 1, 'C');
    }

    public function footer() {

		$this->Ln();

		$this->SetFont("Arial", "B", "9");

		$this->Cell(42,5, 'Approved By:', 0, 0, 'L');
		$this->Cell(60,5, '', 0, 0, 'RT');
		$this->Cell(90,5, 'Received By', 0, 1, 'C');

		$this->Cell(42,5, '', 0, 0, 'L');
		$this->Cell(60,5, '', 0, 0, 'L');
		$this->Cell(90,5, '', 0, 1, 'C');

		$this->Cell(42,5, 'Checked By:', 0, 0, 'L');
		$this->Cell(60,5, '', 0, 0, 'L');
		$this->Cell(90,5, '', 0, 1, 'C');

		$this->Cell(42,5, '', 0, 0, 'L');
		$this->Cell(60,5, '', 0, 0, 'L');
		$this->SetFont("Arial", "", "9");
		$this->Cell(45,5, 'Signature Over Printed Name', 0, 0, 'C');
		$this->Cell(45,5, 'Date Received', 0, 1, 'C');
    }


}