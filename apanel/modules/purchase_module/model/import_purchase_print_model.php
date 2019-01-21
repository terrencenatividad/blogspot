<?php
class import_purchase_print_model extends fpdf {

	private $margin_side		= 8;
	private $margin_top			= 8;
	private $termsandcondition	= '';
	private $footer_height		= 0;
	private $has_received		= false;
	private $has_footer_details	= false;
	private $vendor_details		= array();
	private $document_details	= array();
	private $footer_details		= array();
	private $summary_width		= array();
	private $summary_align		= array();
	private $summary_font_style	= array();
	private $header_width		= array();
	private $header_align		= array();
	private $header				= array();
	private $row_align			= array();
	private $requestor_details	= array();
	
	public function __construct($orientation = 'P', $unit = 'mm', $size = 'Letter') {
		parent::__construct($orientation, $unit, $size);
		$this->db = new db();
		$this->setMargins($this->margin_side, $this->margin_top);
		$this->SetAutoPageBreak(true, 8);
	}

	public function header() {
		$company = $this->db->setTable('company')
		->setFields('companycode, companyimage, companyname, address, email, tin, phone, fax,mobile')
		->setWhere("companycode = '" . COMPANYCODE . "'")
		->setLimit(1)
		->runSelect()
		->getRow();

		$this->SetFont('Arial', 'B', 12);
		$this->Cell(140, 5, strtoupper($company->companyname), 0, 0, 'L');
		$this->Ln();

		$this->SetFont('Arial', '', 8);
		$this->MultiCell(100, 4, $company->address, 0, 'L');
		$this->SetX($this->margin_side);
		$this->Cell(100, 4, 'Email: ' . $company->email . ' / Tel No.: ' . $company->phone, 0, 0, 'L');
		$this->Ln();

		$detail_start = 26;
		$detail_width = 135;

		$vendor		= (isset($this->vendor_details->vendor))	? $this->vendor_details->vendor		: '';
		$address	= (isset($this->vendor_details->address))	? $this->vendor_details->address		: '';
		$contactno	= (isset($this->vendor_details->contactno))	? $this->vendor_details->contactno	: '';
		$tinno		= (isset($this->vendor_details->tinno))		? $this->vendor_details->tinno		: '';

		$requestor	= (isset($this->requestor_details->requestor))	? $this->requestor_details->requestor		: '';
		$address	= (isset($this->requestor_details->address))	? $this->requestor_details->address		: $address;
		$email		= (isset($this->requestor_details->email))	? $this->requestor_details->email		: '';
		$contactno	= (isset($this->requestor_details->contactno))	? $this->requestor_details->contactno		: $contactno;

		$document_detail_height = 6;
		$document_detail_offset = 6;
		$rect_height = 24;
		$companyimage = str_replace('/apanel', '', BASE_URL) . 'uploads/company_logo/' . $company->companyimage;
		if (@is_array(getimagesize($companyimage))) {
			list($image_width, $image_height) = getimagesize($companyimage);
			$image_ratio = 11 / $image_height;
			$image_width = $image_width * $image_ratio;
			$image_height = $image_height * $image_ratio;
			$this->Image($companyimage, 216 - $image_width - $this->margin_side, $this->margin_top, 0, 11);
			$document_detail_height = 5;
			$document_detail_offset = 0;
			$rect_height = 25;
		}

		if (count($this->document_details) > 5) {
			$rect_height += ((count($this->document_details) - 5) * $document_detail_height);
		}
		$this->Rect($this->margin_side, $detail_start, $detail_width, $rect_height);
		$this->SetY($detail_start + 1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 4, (($this->requestor_details) ? 'REQUEST BY' : 'ORDER FROM'), 0, 0, 'L');
		$this->Cell(17, 4, ':', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetX(33);
		$this->MultiCell(97, 4, (($this->requestor_details) ? $requestor:  $vendor), 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 4, (($this->requestor_details) ? 'EMAIL' : 'ADDRESS'), 0, 0, 'L');
		$this->Cell(17, 4, ':', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetX(33);
		$this->MultiCell(97, 4, (($this->requestor_details) ? $email:  $address), 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 4, 'CONTACT #', 0, 0, 'L');
		$this->Cell(17, 4, ':', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetX(33);
		$this->MultiCell(97, 4, $contactno, 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, 4, (($this->requestor_details) ? '' : 'TIN'), 0, 0, 'L');
		$this->Cell(17, 4, (($this->requestor_details) ? '' : ':'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetX(33);
		$this->MultiCell(97, 4, $tinno, 0, 'L');

		$content_width	= 40;
		$gap			= 2;
		$this->SetFillColor(230,230,230);

		$this->SetY($detail_start - $document_detail_offset - 6);
		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->SetFont('Arial', 'B', 13);
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap), 6, strtoupper($this->document_type), 0, 0, 'C');
		$this->Ln();

		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLR', 0, 'L', true);
		$this->Cell($content_width, $document_detail_height, '', 'TR', 0, 'L');
		$this->Ln();
		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLR', 0, 'L', true);
		$this->Cell($content_width, $document_detail_height, '', 'TR', 0, 'L');
		$this->Ln();
		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLR', 0, 'L', true);
		$this->Cell($content_width, $document_detail_height, '', 'TR', 0, 'L');
		$this->Ln();
		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLR', 0, 'L', true);
		$this->Cell($content_width, $document_detail_height, '', 'TR', 0, 'L');
		$this->Ln();
		for ($x = 5; $x < count($this->document_details); $x++) {
			$this->SetX($this->margin_side + ($detail_width + $gap));
			$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLR', 0, 'L', true);
			$this->Cell($content_width, $document_detail_height, '', 'TR', 0, 'L');
			$this->Ln();
		}
		$this->SetX($this->margin_side + ($detail_width + $gap));
		$this->Cell(216 - ($this->margin_side * 2) - ($detail_width + $gap) - $content_width, $document_detail_height, '', 'TLRB', 0, 'L', true);
		$this->Cell($content_width, $document_detail_height, '', 'TRB', 0, 'L');
		$header_end = $this->GetY();

		if ($this->document_details) {
			$this->SetY($detail_start - $document_detail_offset);
			$this->SetX($this->margin_side + ($detail_width + $gap));
			foreach ($this->document_details as $document_label => $document_details) {
				$this->SetFont('Arial', 'B', 9);
				$this->SetX($this->margin_side + ($detail_width + $gap));
				$this->Cell($content_width, $document_detail_height, strtoupper($document_label), 0, 0, 'L');
				$this->SetFont('Arial', '', 9);
				$this->SetX(216 - $this->margin_side - $content_width);
				$this->Cell($content_width, $document_detail_height, strtoupper($document_details), 0, 0, 'L');
				$this->Ln();
			}
		}
		$document_info = $this->document_info;
		$amount = $document_info->amount;
		$wtaxamount = $document_info->wtax;
		$vat = $document_info->vat;
		$taxcode = $document_info->wtaxcode;
		$taxrate = $document_info->wtaxrate;
		$netamount = $document_info->net;
		$freight = $document_info->freight;
		$insurance = $document_info->insurance;
		$packaging = $document_info->packaging;
		$converted_freight = $document_info->converted_freight;
		$converted_insurance = $document_info->converted_insurance;
		$converted_packaging = $document_info->converted_packaging;
		$convertedamount = $document_info->convertedamount;
		$exchangecurrency = $document_info->exchangecurrency;
		$remarks = $document_info->remarks;

		$this->SetFont('Arial', 'B', 8);
		$this->SetY(205);
		$this->SetX(8);
		$this->Cell(20, 4, 'Notes:', 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(210);
		$this->SetX(8);
		$this->Cell(90, 4, $remarks, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(205);
		$this->SetX(145);
		$this->Cell(8, 4, $exchangecurrency, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(215);
		$this->SetX(145);
		$this->Cell(8, 4, $exchangecurrency, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(225);
		$this->SetX(145);
		$this->Cell(8, 4, $exchangecurrency, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(235);
		$this->SetX(145);
		$this->Cell(8, 4, $exchangecurrency, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(243);
		$this->SetX(145);
		$this->Cell(8, 4, $exchangecurrency, 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(205);
		$this->SetX(176.5);
		$this->Cell(8, 4, 'PHP', 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(215);
		$this->SetX(176.5);
		$this->Cell(8, 4, 'PHP', 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(225);
		$this->SetX(176.5);
		$this->Cell(8, 4, 'PHP', 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(235);
		$this->SetX(176.5);
		$this->Cell(8, 4, 'PHP', 0, 0, 'L');

		$this->SetFont('Arial', '', 8);
		$this->SetY(243);
		$this->SetX(176.5);
		$this->Cell(8, 4, 'PHP', 0, 0, 'L');

		$this->SetFont('Arial', 'B', 8);
		$this->SetY(205);
		$this->SetX(115);
		$this->Cell(20, 4, 'Total Purchase', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetY(205);
		$this->SetX(156);
		$this->Cell(20, 4, number_format($amount, 2), 0, 0, 'R');
		$this->SetFont('Arial', '', 8);
		$this->SetY(205);
		$this->SetX(188);
		$this->Cell(20, 4, number_format($convertedamount, 2), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(215);
		$this->SetX(115);
		$this->Cell(20, 4, 'Freight', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetY(215);
		$this->SetX(156);
		$this->Cell(20, 4, number_format($freight, 2), 0, 0, 'R');
		$this->SetFont('Arial', '', 8);
		$this->SetY(215);
		$this->SetX(188);
		$this->Cell(20, 4, number_format($converted_freight, 2), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(225);
		$this->SetX(115);
		$this->Cell(20, 4, 'Insurance', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetY(225);
		$this->SetX(156);
		$this->Cell(20, 4, number_format($insurance, 2), 0, 0, 'R');
		$this->SetFont('Arial', '', 8);
		$this->SetY(225);
		$this->SetX(188);
		$this->Cell(20, 4, number_format($converted_insurance, 2), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(235);
		$this->SetX(115);
		$this->Cell(20, 4, 'Packaging', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetY(235);
		$this->SetX(156);
		$this->Cell(20, 4, number_format($packaging, 2), 0, 0, 'R');
		$this->SetFont('Arial', '', 8);
		$this->SetY(235);
		$this->SetX(188);
		$this->Cell(20, 4, number_format($converted_packaging, 2), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(243);
		$this->SetX(115);
		$this->Cell(20, 4, 'Total Amount Due', 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetY(243);
		$this->SetX(156);
		$this->Cell(20, 4, number_format(($amount+$freight+$insurance+$packaging), 2), 0, 0, 'R');
		$this->SetFont('Arial', '', 8);
		$this->SetY(243);
		$this->SetX(188);
		$this->Cell(20, 4, number_format(($convertedamount+$converted_freight+$converted_insurance+$converted_packaging), 2), 0, 0, 'R');
		$this->SetY($header_end);
		$this->Ln();
	
	}

	public function footer() {
		$this->SetY(279 - $this->margin_top - $this->footer_height + 1);
		if ($this->termsandcondition) {
			$this->SetFont('Arial', '', 8);
			$this->MultiCell(200, 4, $this->termsandcondition);
			$this->Ln(1);
		}
		$content_width	= 0;
		$label_width	= 0;
		$footer_start = $this->GetY();
		// if ($this->has_received) {
		// 	if ($this->has_footer_details) {
		// 		$content_width	= 75;
		// 		$label_width	= 37;
		// 		$this->SetFillColor(230,230,230);
		// 		$this->SetX($this->margin_side);
		// 		$this->Cell(37, 5, '', 'TLR', 0, 'L', true);
		// 		$this->Cell($content_width, 5, '', 'T', 0, 'L');
		// 		$this->Ln();
		// 		$this->SetX($this->margin_side);
		// 		$this->Cell(37, 5, '', 'TLR', 0, 'L', true);
		// 		$this->Cell($content_width, 5, '', 'T', 0, 'L');
		// 		$this->Ln();
		// 		$this->SetX($this->margin_side);
		// 		$this->Cell(37, 5, '', 'TLR', 0, 'L', true);
		// 		$this->Cell($content_width, 5, '', 'T', 0, 'L');
		// 		$this->Ln();
		// 		$this->SetX($this->margin_side);
		// 		$this->Cell(37, 5, '', 'TLRB', 0, 'L', true);
		// 		$this->Cell($content_width, 5, '', 'TB', 0, 'L');
		// 		if ($this->footer_details) {
		// 			$this->SetY($footer_start);
		// 			$this->SetX($this->margin_side);
		// 			foreach ($this->footer_details as $footer_details) {
		// 				$this->SetFont('Arial', 'B', 8);
		// 				$this->Cell(30, 5, $footer_details, 0, 0, 'L');
		// 				$this->Ln();
		// 			}
		// 		}
		// 	}

		// 	$receipt_width = 216 - ($this->margin_side * 2) - $content_width - $label_width;
		// 	$this->SetFont('Arial', 'B', 8);
		// 	$this->Rect($this->margin_side + $content_width + $label_width, $footer_start, $receipt_width, 20);
		// 	$this->SetY($footer_start);
		// 	$this->SetX($this->margin_side + $content_width + $label_width);
		// 	$this->Cell($receipt_width, 5, '', '', 0, 'C');
		// 	$this->Ln(15);
		// 	$this->SetFont('Arial', '', 8);
		// 	$this->SetX($this->margin_side + $content_width + $label_width);
		// 	$this->Cell($receipt_width - round($receipt_width * 0.5), 5, '', 0, 0, 'C');
		// 	$this->Cell(round($receipt_width * 0.5), 5, '', 0, 0, 'C');
		// } else if ($this->has_footer_details) {
			$content_width		= 69;
			$label_width		= 30;
			$footer_label_1	= isset($this->footer_details) ? $this->footer_details[0] : '';
			$footer_label_2	= isset($this->footer_details) ? $this->footer_details[1] : '';
			$footer_label_3	= isset($this->footer_details) ? $this->footer_details[2] : '';
			$this->Cell(67, 8, $footer_label_1.':', 'TLRB', 0, 'L');
			$this->Cell(67, 8, $footer_label_2.':', 'TLRB', 0, 'L');
			$this->Cell(66, 8, $footer_label_3.':', 'TLRB', 1, 'L');
			$this->Cell(67, 8, '', 'LRB', 0, 'L');
			$this->Cell(67, 8, '', 'LRB', 0, 'L');
			$this->Cell(66, 8, '', 'LRB', 1, 'L');
		// 	$this->SetFillColor(230,230,230);
		// 	$this->SetFont('Arial', 'B', 8);
		// 	$this->SetX($this->margin_side);
		// 	$this->Cell(30, 10, $footer_label_left, 'TLRB', 0, 'C', true);
		// 	$this->Cell($content_width, 10, '', 'TLRB', 0, 'L');
		// 	$this->SetX($this->margin_side + 101);
		// 	$this->Cell(30, 10, $footer_label_right, 'TLRB', 0, 'C', true);
		// 	$this->Cell($content_width, 10, '', 'TLRB', 0, 'L');
		// }
	}

	public function setRequestorDetails($requestor_details) {
		$this->requestor_details = $requestor_details;
		return $this;
	}

	public function setVendorDetails($vendor_details) {
		$this->vendor_details = $vendor_details;
		return $this;
	}
	
	public function setDocumentType($document_type) {
		$this->document_type = $document_type;
		return $this;
	}

	public function setDocumentDetails($document_details) {
		$this->document_details = $document_details;
		return $this;
	}

	public function setDocumentInfo($document_info) {
		$this->document_info = $document_info;
		return $this;
	}

	public function setFooterDetails($footer_details) {
		$this->has_footer_details = true;
		$this->footer_height += 10;
		$this->footer_details = $footer_details;
		return $this;
	}
	
	public function setSummaryWidth($width) {
		$this->summary_width = $width;
		return $this;
	}

	public function setHeaderWidth($width) {
		$this->header_width = $width;
		return $this;
	}

	public function setHeaderAlign($align) {
		$this->header_align = $align;
		return $this;
	}
	
	public function setHeader($header) {
		$this->header = $header;
		return $this;
	}

	public function setRowAlign($align) {
		$this->row_align = $align;
		return $this;
	}

	// public function addRow($row) {
	// 	foreach($this->header as $key => $header) {
	// 		$width			= isset($this->header_width[$key]) ? $this->header_width[$key] : 0;
	// 		$align			= isset($this->row_align[$key]) ? $this->row_align[$key] : 'L';
	// 		$array_values	= array_values((array) $row);
	// 		$data			= isset($row->$header) ? $row->$header : $array_values[$key];
	// 		$this->Cell($width, 5, $data, 0, 0, $align);
	// 	}
	// 	$this->Ln();
	// }

	public function addRow($row) {
		$h 	=	6;
		foreach($this->header as $key => $header) {
			$x = $this->GetX();
     		$y = $this->GetY();
			$width			= isset($this->header_width[$key]) ? $this->header_width[$key] : 0;
			$align			= isset($this->row_align[$key]) ? $this->row_align[$key] : 'L';
			$array_values	= array_values((array) $row);
			$data			= isset($row->$header) ? $row->$header : $array_values[$key];
			$array_values[10] = isset($array_values[10]) ? $array_values['10'] : 1;
			// if ($array_values[6] == '' || $array_values[10] == '') {
			// 	$this->setFont('Arial','B',9);
			// 	if($key == 2){
			// 		$this->setFont('Times','B',9);
			// 	}
			// }
			// else {
				// $this->setFont('Arial','',9);
				// if($key == 2){
				// 	$this->setFont('Times','',9);
				// }
			// }
			$this->MultiCell($width, 6, $array_values[$key], 0, $align);
			$y2 = $this->GetY();
			if (($y2 - $y) > $h) {
				$h = $y2 - $y;
			}
			$this->SetXY($x + $width, $y);
		}
		// if (($y + $h + $this->footer_height) >= $this->summary_start) {
		// 	$this->next_page = true;
		// }
		$this->SetY($y + $h - 6);
		$this->Ln();
	}

	public function addTermsAndCondition() {
		$this->termsandcondition = "TERMS & CONDITIONS: All bills are payable on demand unless otherwise agreed upon, interest at thirty-six percent (36%) per annum will be charged on all overdue amounts. All claims on correction to this invoice must be made within two (2) days after receipt of goods. Parties expressly submit to the jurisdiction of the courts of Makati on any legal action taken out of this transaction, an additional sum of equal to twenty-five percent (25%) of the amount due will be charged by the vendor for attorney's fees & cost of collection.";
		$this->footer_height += 17;
		return $this;
	}

	public function addReceived() {
		$this->has_received		= true;
		$this->footer_height	+= 21;
		if ($this->has_footer_details) {
			$this->footer_height	-= 10;
		}
		return $this;
	}

	public function drawHeader() {
		$this->addPage();
		$this->SetY($this->GetY() + 2);
		$this->SetFillColor(230,230,230);
		$this->Rect($this->margin_side, $this->GetY(), 216 - ($this->margin_side * 2), 279 - $this->GetY() - $this->margin_top - $this->footer_height - 2);
		$this->SetFont('Arial', 'B', 8);
		foreach($this->header as $key => $header) {
			$width = isset($this->header_width[$key]) ? $this->header_width[$key] : 0;
			$align = isset($this->header_align[$key]) ? $this->header_align[$key] : 'L';
			$this->Cell($width, 6, $header, 1, 0, $align, true);
		}
		$this->SetFont('Arial', '', 8);
		$this->Ln();
	}
	
	public function drawSummary($summary) {
		$summary_height	= count($summary) * 5;
		$summary_start	= 279 - $this->margin_top - $this->footer_height - $summary_height - 2;
		// $this->Line(8, $summary_start, 29, $summary_start);
		$this->summary_start 	=	$summary_start;
		$alignment		= $this->summary_align;
		$font_style		= $this->summary_font_style;
		$this->SetY($summary_start);
		foreach($summary as $index => $summary_row) {
			if (is_array($summary_row)) {
				if (isset($this->summary_align[$index]) && is_array($this->summary_align[$index])) {
					$alignment = $this->summary_align[$index];
				}
				if (isset($this->summary_font_style[$index]) && is_array($this->summary_font_style[$index])) {
					$font_style = $this->summary_font_style[$index];
				}
				$y = $this->GetY();
				$x = $this->GetX();
				foreach($this->summary_width as $key => $width) {
					$this->SetY($y);
					$this->SetX($x);
					$x += $width;
					$align	= isset($alignment[$key]) ? $alignment[$key] : 'R';
					$style	= isset($font_style[$key]) ? $font_style[$key] : '';
					$data		= $summary_row[$key];
					$this->SetFont('Arial', $style, 9);
					$this->MultiCell($width, 5, $data, 0, $align);
				}
			} else {
				$summary_content	= $summary_row;
				$summary_label		= $index;
				$this->SetFont('Arial', 'B', 9);
				$this->Cell($this->summary_width[0], 5, $summary_label, 0, 0, 'R');
				$this->SetFont('Arial', '', 9);
				$this->Cell($this->summary_width[1], 5, $summary_content, 0, 0, 'R');
				$this->Ln();
			}
		}
		$this->Ln();
	}

	public function drawPDF($filename = 'print_preview') {
		// $this->initializePage();
		$this->Output($filename . '.pdf', 'I');
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
			$col_index = array('chequenumber', 'chequedate', 'accountname', 'chequeamount');
		} else if ($type == 'cheque') {
			$col_index = array('accountname', 'chequenumber', 'chequedate', 'chequeamount');
		} else if ($type == 'applied_payment'){
			$col_index = array('voucherno', 'si_no', 'amount', 'discount');
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