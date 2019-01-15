<?php
class print_sales_model extends fpdf {

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

	public function setCustomerInfo($details) {
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
		return $this;
	}

	public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}
		
	public function Header() {

		if ($this->document_code == 'SQ'){
			if( $this->outline == 'yes' ){
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
					$amount			= isset($documentinfo->amount) ? $documentinfo->amount : '' ;
					$plno			= isset($documentinfo->packing_no) ? $documentinfo->packing_no : '' ;
				} else if (DEBUGGING) {
					echo 'Please use setDocumentInfo() to set Header Information';
					exit();
				}
					
				//Document Name
				$this->SetFont('Arial','B',12);
				$this->Cell(60,5,strtoupper($document_type),0,0,'L');
				
				$this->SetFont('Arial','',9);
				$this->Ln();
				
				//Company TEL
				$this->Cell(140,4,'Tel # : '.$phone,0,0,'L');
				
				//Invoice Date
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'DATE',1,0,'L');
				
				$this->SetFont('Arial','',9);
				$this->Cell(40,$rowheight,$documentdate,1,0,'L');
				$this->Ln();
					
				$customerinfo		= $this->customerinfo;
				if ($customerinfo) {
					$customer		= $customerinfo->first_name . " " . $customerinfo->last_name;
					$cust_company 	= $customerinfo->partnername;
					$cust_address 	= $customerinfo->address1;
					$cust_email		= $customerinfo->email;
					$cust_tinno 	= $customerinfo->tinno;
					$cust_terms 	= $customerinfo->terms;
					$cust_mobile 	= $customerinfo->mobile;
				} else if (DEBUGGING) {
					echo 'Please use setCustomerInfo() to set Customer Information';
					exit();
				}

				if ( ! empty($customer)) {
					//Customer Name
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'SOLD TO','TL',0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(118,$rowheight,':  '.$cust_company,'TR',0,'L');
					$this->Cell(2,$rowheight,'',0,0,'L');
				}

				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,$this->document_code.' NO',1,0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(40,$rowheight,$voucherno,1,0,'L');
				$this->Ln();

				//Customer Address
				$firstline		= (!empty($cust_address)) ? substr($cust_address,0,75) : "";
				$secondline		= (!empty($cust_address)) ? substr($cust_address,75,75) : "";
				
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'ADDRESS','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$firstline,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');

				//Voucher Number
				if( $this->document_code == "SO" ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				} else if( $this->document_code == "PL" ||  $this->document_code == "DR"){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'SO NO',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,$referenceno,1,0,'L');
					$this->Ln();
				} else{
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				}
				
				//Customer Contact #
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'CONTACT #','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$cust_mobile,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');

				//Purchase Order Number
				if( $this->document_code == "DR" ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'PL NO',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,$plno,1,0,'L');
					$this->Ln();
				} else {
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				}
				
				//Customer TIN
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'','TLB',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,'','TRB',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');
				
				//Invoice Terms
				if( $this->document_code == 'SO' ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
				} else {
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
				}
				
				$this->Ln(10);
			} else {
		
				$this->getCompanyInfo();
				
				$companyinfo	= $this->companyinfo;
				$companycode	= $companyinfo->companycode;
				$companyname	= $companyinfo->companyname;
				$address		= $companyinfo->address;
				$email			= $companyinfo->email;
				$tin			= $companyinfo->tin;

				$rowheight		= 6;
			
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
					$this->Cell(200,5,'',0,0,'L');
					$this->Ln(7);
				}

				$customerinfo		= $this->customerinfo;
				if ($customerinfo) {
					$customer		= $customerinfo->first_name . " " . $customerinfo->last_name;
					$cust_code 		= $customerinfo->partnercode;
					$cust_company 	= $customerinfo->partnername;
					$cust_address 	= $customerinfo->address1;
					$cust_email		= $customerinfo->email;
					$cust_tinno 	= $customerinfo->tinno;
					$cust_terms 	= $customerinfo->terms;
					$cust_mobile 	= $customerinfo->mobile;
				} else if (DEBUGGING) {
					echo 'Please use setCustomerInfo() to set Customer Information';
					exit();
				}

				//Company Address
				$ad_firstline		= (!empty($address)) ? substr($address,0,50) : "";
				$ad_secondline		= (!empty($address)) ? substr($address,50,50) : "";

				$this->SetFont('Arial','',8);
				$this->Cell(90,$rowheight,'',0,0,'L'); 

				// Sold To
				$this->Cell(110,$rowheight+1,$cust_company,0,0,'L');
				$this->Ln();

				$this->Cell(90,$rowheight,'',0,0,'L'); 

				// Customer Address
				$this->Cell(110,$rowheight,$cust_address,0,0,'L');
				$this->Ln();
				
				//Company Email
				$this->Cell(80,$rowheight,'',0,0,'L');

				//Customer Tin No
				$this->Cell(110,$rowheight-2,$cust_tinno,0,0,'L');

				/**DOCUMENT INFO**/

				$documentinfo	= $this->documentinfo;
				if ($documentinfo) {
					$document_type	= $this->document_type;
					$documentdate	= date("m - d - Y",strtotime($documentinfo->documentdate));
					$voucherno		= $documentinfo->voucherno;
					$referenceno	= isset($documentinfo->referenceno) ? $documentinfo->referenceno : '';
					$amount			= isset($documentinfo->amount) ? $documentinfo->amount : '' ;
				} else if (DEBUGGING) {
					echo 'Please use setDocumentInfo() to set Header Information';
					exit();
				}
				$this->Ln();
				
				$rowheight		= 5;

				// Blank
				$this->Cell(200,$rowheight,'',0,0,'L');
				$this->Ln(9.5);
				
				$this->SetFont('Arial','B',7);
				//Customer No.
				$this->Cell(26,$rowheight,$cust_code,0,0,'L');
				// Res Cert.
				$this->Cell(26,$rowheight,'',0,0,'L');
				// P.O.No
				$this->Cell(30,$rowheight,'',0,0,'L');
				// DR No.
				$this->Cell(23,$rowheight,'',0,0,'L');
				// Terms
				$this->Cell(23,$rowheight,$cust_terms,0,0,'L');
				// SM
				$this->Cell(23,$rowheight,'',0,0,'L');
				// Blank
				$this->Cell(20,$rowheight,'',0,0,'L');
				//Date
				$this->Cell(30,$rowheight,$documentdate,0,0,'L');

				/**DOCUMENT INFO - END**/
			}
		} else {
			if( $this->outline == 'yes' ){
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
					$amount			= isset($documentinfo->amount) ? $documentinfo->amount : '' ;
					$plno			= isset($documentinfo->packing_no) ? $documentinfo->packing_no : '' ;
				} else if (DEBUGGING) {
					echo 'Please use setDocumentInfo() to set Header Information';
					exit();
				}
					
				//Document Name
				$this->SetFont('Arial','B',12);
				$this->Cell(60,5,strtoupper($document_type),0,0,'L');
				
				$this->SetFont('Arial','',9);
				$this->Ln();
				
				//Company TEL
				$this->Cell(140,4,'Tel # : '.$phone,0,0,'L');
				
				//Invoice Date
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'DATE',1,0,'L');
				
				$this->SetFont('Arial','',9);
				$this->Cell(40,$rowheight,$documentdate,1,0,'L');
				$this->Ln();
					
				$customerinfo		= $this->customerinfo;
				if ($customerinfo) {
					$customer		= $customerinfo->first_name . " " . $customerinfo->last_name;
					$cust_company 	= $customerinfo->partnername;
					$cust_address 	= $customerinfo->address1;
					$cust_email		= $customerinfo->email;
					$cust_tinno 	= $customerinfo->tinno;
					$cust_terms 	= $customerinfo->terms;
					$cust_mobile 	= $customerinfo->mobile;
				} else if (DEBUGGING) {
					echo 'Please use setCustomerInfo() to set Customer Information';
					exit();
				}

				if ( ! empty($customer)) {
					//Customer Name
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'SOLD TO','TL',0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(118,$rowheight,':  '.$cust_company,'TR',0,'L');
					$this->Cell(2,$rowheight,'',0,0,'L');
				}

				//Invoice Number
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,$this->document_code.' NO',1,0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(40,$rowheight,$voucherno,1,0,'L');
				$this->Ln();

				//Customer Address
				$firstline		= (!empty($cust_address)) ? substr($cust_address,0,75) : "";
				$secondline		= (!empty($cust_address)) ? substr($cust_address,75,75) : "";
				
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'ADDRESS','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$firstline,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');

				//Voucher Number
				if( $this->document_code == "SO" ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				} else if( $this->document_code == "PL" ||  $this->document_code == "DR"){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'SO NO',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,$referenceno,1,0,'L');
					$this->Ln();
				} else{
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				}
				
				//Customer Contact #
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'CONTACT #','TL',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$cust_mobile,'TR',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');

				//Purchase Order Number
				if( $this->document_code == "DR" ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'PL NO',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,$plno,1,0,'L');
					$this->Ln();
				} else {
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
					$this->Ln();
				}
				
				//Customer TIN
				$this->SetFont('Arial','B',9);
				$this->Cell(20,$rowheight,'TIN','TLB',0,'L');
				$this->SetFont('Arial','',9);
				$this->Cell(118,$rowheight,':  '.$cust_tinno,'TRB',0,'L');
				$this->Cell(2,$rowheight,'',0,0,'L');
				
				//Invoice Terms
				if( $this->document_code == 'SO' ){
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,'',1,0,'L');
				} else {
					$this->SetFont('Arial','B',9);
					$this->Cell(20,$rowheight,'TERMS',1,0,'L');
					$this->SetFont('Arial','',9);
					$this->Cell(40,$rowheight,''.$cust_terms.' days',1,0,'L');
				}
				
				$this->Ln(10);
					
				/**DOCUMENT INFO - END**/
			} else {
				$this->getCompanyInfo();
				
				$companyinfo	= $this->companyinfo;
				$companycode	= $companyinfo->companycode;
				$companyname	= $companyinfo->companyname;
				$address		= $companyinfo->address;
				$email			= $companyinfo->email;
				$tin			= $companyinfo->tin;

				$rowheight		= 6;
			
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
					$this->Cell(200,5,'',0,0,'L');
					$this->Ln(7);
				}

				$customerinfo		= $this->customerinfo;
				if ($customerinfo) {
					$customer		= $customerinfo->first_name . " " . $customerinfo->last_name;
					$cust_code 		= $customerinfo->partnercode;
					$cust_company 	= $customerinfo->partnername;
					$cust_address 	= $customerinfo->address1;
					$cust_email		= $customerinfo->email;
					$cust_tinno 	= $customerinfo->tinno;
					$cust_terms 	= $customerinfo->terms;
					$cust_mobile 	= $customerinfo->mobile;
				} else if (DEBUGGING) {
					echo 'Please use setCustomerInfo() to set Customer Information';
					exit();
				}

				//Company Address
				$ad_firstline		= (!empty($address)) ? substr($address,0,50) : "";
				$ad_secondline		= (!empty($address)) ? substr($address,50,50) : "";

				$this->SetFont('Arial','',8);
				$this->Cell(90,$rowheight,'',0,0,'L'); 

				// Sold To
				$this->Cell(110,$rowheight+1,$cust_company,0,0,'L');
				$this->Ln();

				$this->Cell(90,$rowheight,'',0,0,'L'); 

				// Customer Address
				$this->Cell(110,$rowheight,$cust_address,0,0,'L');
				$this->Ln();
				
				//Company Email
				$this->Cell(80,$rowheight,'',0,0,'L');

				//Customer Tin No
				$this->Cell(110,$rowheight-2,$cust_tinno,0,0,'L');

				/**DOCUMENT INFO**/

				$documentinfo	= $this->documentinfo;
				if ($documentinfo) {
					$document_type	= $this->document_type;
					$documentdate	= date("m - d - Y",strtotime($documentinfo->documentdate));
					$voucherno		= $documentinfo->voucherno;
					$referenceno	= isset($documentinfo->referenceno) ? $documentinfo->referenceno : '';
					$amount			= isset($documentinfo->amount) ? $documentinfo->amount : '' ;
				} else if (DEBUGGING) {
					echo 'Please use setDocumentInfo() to set Header Information';
					exit();
				}
				$this->Ln();

				$rowheight		= 5;

				// Blank
				$this->Cell(200,$rowheight,'',0,0,'L');
				$this->Ln(9.5);
				
				$this->SetFont('Arial','B',7);
				//Customer No.
				$this->Cell(26,$rowheight,$cust_code,0,0,'L');
				// Res Cert.
				$this->Cell(26,$rowheight,'',0,0,'L');
				// P.O.No
				$this->Cell(30,$rowheight,'',0,0,'L');
				// DR No.
				$this->Cell(23,$rowheight,'',0,0,'L');
				// Terms
				$this->Cell(23,$rowheight,$cust_terms,0,0,'L');
				// SM
				$this->Cell(23,$rowheight,'',0,0,'L');
				// Blank
				$this->Cell(20,$rowheight,'',0,0,'L');
				//Date
				$this->Cell(30,$rowheight,$documentdate,0,0,'L');

				/**DOCUMENT INFO - END**/
			}
		}
	}
	
	private function drawDocumentDetails() {

		$document = $this->document;
		$this->AddPage();
		$this->SetFillColor(233, 233, 233);
		$this->SetFont('Arial', 'B', '9');
		
		if( $this->outline == 'yes' ){
			if ($this->document_code == 'PL' || $this->document_code == 'DR'){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(100, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(30, 5, 'Qty', 1, 0, 'C', true);
				$this->Cell(30, 5, 'UOM', 1, 0, 'C', true);
				$this->setWidths(array(40, 100, 30, 30));
				$this->setAligns(array('L', 'L', 'R','L'));
			} else if ($this->document_code == 'SQ'){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(100, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(20, 5, 'UOM', 'LTB', 0, 'C', true);
				$this->Cell(40, 5, 'Price', 1, 0, 'C', true);
				$this->setWidths(array(40, 100, 20, 40));
				$this->setAligns(array('L', 'L', 'L','R'));
			} else { 
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(60, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(20, 5, 'Qty', 'LTB', 0, 'C', true);
				$this->Cell(20, 5, 'UOM', 'LTB', 0, 'C', true);
				$this->Cell(30, 5, 'Price', 'LTB', 0, 'C', true);
				$this->Cell(30, 5, 'Amount', 1, 0, 'C', true);
				$this->setWidths(array(40, 60, 20, 20, 30, 30));
				$this->setAligns(array('L', 'L', 'R', 'L','R','R'));
			}

			$this->Ln();
			
			$this->SetFont('Arial', '', '9');
		
			$totalamount 	= 0;
			$totalqty 		= 0;
			$notes			= $this->documentinfo->remarks;
			$disctype 		= isset($this->documentinfo->disctype)? $this->documentinfo->disctype : "";
			$discount 		= isset($this->documentinfo->discount)? $this->documentinfo->discount : 0.00;
			$net 			= isset($this->documentinfo->net)? $this->documentinfo->net : 0.00;
			$amount 		= isset($this->documentinfo->amount)? $this->documentinfo->amount : 0.00;
			$vat_sales 		= isset($this->documentinfo->vat_sales)? $this->documentinfo->vat_sales : 0.00;
			$vat_exempt 	= isset($this->documentinfo->vat_exempt)? $this->documentinfo->vat_exempt : 0.00;
			$vat_zerorated 	= isset($this->documentinfo->vat_zerorated)? $this->documentinfo->vat_zerorated : 0.00;
			$vat 			= isset($this->documentinfo->vat)? $this->documentinfo->vat : 0.00;
		
			if ($document) {
				foreach($document as $data)
				{
					foreach($data as $key => $value){
						if( !in_array($key, $this->col_index) ){
							array_push($this->col_index, $key);
						}
					}
					if( isset($data->quantity) ){
						$totalqty 	+=	$data->quantity;
					}
					if( isset($data->price) ){
						$totalamount 	+=	$data->price;
					}
					$this->row($data, $this->document_code);
				}

				$this->SetDrawColor(1,1,1);
				if( $this->document_code == 'SO' ){
					$this->Rect(8,63,200,205,'D');
				} else if( $this->document_code == 'PL' || $this->document_code == 'DR' ){
					$this->Rect(8,63,200,185,'D');
				} else if( $this->document_code == 'SQ') {
					$this->Rect(8,63,200,190,'D');
				} else {
					$this->Rect(8,63,200,175,'D');
				}

				$totalinfo[0] 		= $totalamount;
				$totalinfo[1] 		= $net;
				$totalinfo[2] 		= $disctype;
				$totalinfo[3]		= $discount;
				$totalinfo[4] 		= $vat;
				$totalinfo[5] 		= $totalamount;
				$totalinfo[6] 		= $vat_sales;
				$totalinfo[7] 		= $vat_exempt;
				$totalinfo[8] 		= $vat_zerorated;
				$totalinfo[9] 		= $totalqty;

				$this->totalinfo 	= $totalinfo;	

			} else {
				$this->Cell(140, 6, 'Total :', 0, 0, 'R');
			}
		} else {
			if ($this->document_code == 'PL' || $this->document_code == 'DR'){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(100, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(30, 5, 'Qty', 1, 0, 'C', true);
				$this->Cell(30, 5, 'Unit', 1, 0, 'C', true);
				$this->setWidths(array(40, 100, 30, 30));
				$this->setAligns(array('L', 'L', 'R','L'));
			} else if ($this->document_code == 'SQ'){
				$this->Cell(40, 5, 'Item Code', 'LTB', 0, 'C', true);
				$this->Cell(120, 5, 'Description', 'LTB', 0, 'C', true);
				$this->Cell(40, 5, 'Qty', 1, 0, 'C', true);
				$this->setWidths(array(40, 120, 40));
				$this->setAligns(array('L', 'L', 'R','R','R'));
			} else { 
				// $this->Cell(26, 5, 'Item Code', 'LTB', 0, 'C', true);
				// $this->Cell(20, 5, 'Unit', 'LTB', 0, 'C', true);
				// $this->Cell(105, 5, 'Description', 'LTB', 0, 'C', true);
				// $this->Cell(23, 5, 'Unit Price', 'LTB', 0, 'C', true);
				// $this->Cell(26, 5, 'Amount', 1, 0, 'C', true);
				$this->Cell(200,5,'',0,0,'L');
				$this->setWidths(array(22,17.5,101.5,22,31));
				$this->setAligns(array('L', 'R', 'L','R','R'));
			}

			$this->Ln(13);
			
			$this->SetFont('Arial', '', '7');
		
			$totalamount 	= 0;
			$totalqty 		= 0;
			$notes			= $this->documentinfo->remarks;
			$disctype 		= isset($this->documentinfo->disctype)? $this->documentinfo->disctype : "";
			$discount 		= isset($this->documentinfo->discount)? $this->documentinfo->discount : 0.00;
			$net 			= isset($this->documentinfo->net)? $this->documentinfo->net : 0.00;
			$amount 		= isset($this->documentinfo->amount)? $this->documentinfo->amount : 0.00;
			$vat_sales 		= isset($this->documentinfo->vat_sales)? $this->documentinfo->vat_sales : 0.00;
			$vat_exempt 	= isset($this->documentinfo->vat_exempt)? $this->documentinfo->vat_exempt : 0.00;
			$vat_zerorated 	= isset($this->documentinfo->zerorated)? $this->documentinfo->zerorated : 0.00;
			$vat 			= isset($this->documentinfo->vat)? $this->documentinfo->vat : 0.00;
		
			if ($document) {
				foreach($document as $data)
				{
					foreach($data as $key => $value){
						if( !in_array($key, $this->col_index) ){
							array_push($this->col_index, $key);
						}
					}
					
					$totalqty 	+=	$data->quantity;
					$this->row($data, $this->document_code);
				}

				//$this->SetDrawColor(0,0,0);
				//$this->Rect(8,63,200,175,'D');

				$totalinfo[0] 		= $totalamount;
				$totalinfo[1] 		= $net;
				$totalinfo[2] 		= $disctype;
				$totalinfo[3]		= $discount;
				$totalinfo[4] 		= $vat;
				$totalinfo[5] 		= $amount;
				$totalinfo[6] 		= $vat_sales;
				$totalinfo[7] 		= $vat_exempt;
				$totalinfo[8] 		= $vat_zerorated;
				$totalinfo[9] 		= $totalqty;

				$this->totalinfo 	= $totalinfo;	

			} else {
				$this->Cell(140, 6, 'Total :', 0, 0, 'R');
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
			if( $this->document_code == 'SQ' ){
				$this->SetY(-35);
			} else {
				$this->SetY(-75);
			}
			$pageNo 		= $this->PageNo();
			$rowheight		= 7;
			
			$totalinfo		= $this->totalinfo;
			$totalamt		= $totalinfo[0];
			$netamt 		= $totalinfo[1];
			$disctype 		= $totalinfo[2];
			$discount 	 	= $totalinfo[3];
			$vat 			= $totalinfo[4];
			$amount 		= $totalinfo[5];
			$vat_sales 		= $totalinfo[6];
			$vat_exempt 	= $totalinfo[7];
			$vat_zero 		= $totalinfo[8];
			$totalqty		= $totalinfo[9];

			if($this->document_code == 'SQ' ){
				// $this->Ln(20);
				
				$this->SetFont('Arial','B','9');
				$this->Cell(150,$rowheight,'Total Amount',0,0,'R');
				$this->Cell(50,$rowheight,number_format($amount,2),0,0,'R');
			} else if( $this->document_code == "SO" ){
				$this->SetY(-20);
				$this->SetFont('Arial','B','9');
				$this->Cell(150,$rowheight,'Total Amount',0,0,'R');
				$this->Cell(50,$rowheight,number_format($netamt,2),0,0,'R');
				$this->Ln();
			}  else if( $this->document_code == "PL" || $this->document_code == "DR" ){
				$this->Ln(35);
				$this->SetFont('Arial','B','9');
				$this->Cell(150,$rowheight,'Total Qty',0,0,'R');
				$this->Cell(50,$rowheight,$totalqty,0,0,'R');
				$this->Ln();
			} else {
				$this->SetFont('Arial','','9');
				$this->Cell(150,$rowheight,'VATable Sales',0,0,'R');
				$this->Cell(50,$rowheight,number_format($vat_sales,2),0,0,'R');
				$this->Ln(5);

				$this->Cell(150,$rowheight,'VAT-Exempt Sales',0,0,'R');
				$this->Cell(50,$rowheight,number_format($vat_exempt,2),0,0,'R');
				$this->Ln(5);

				$this->Cell(150,$rowheight,'Total Sales',0,0,'R');
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

				$this->Cell(150,$rowheight,'Add 12% Tax',0,0,'R');
				$this->Cell(50,$rowheight,number_format($vat,2),0,0,'R');
				$this->Ln(7);

				$this->SetFont('Arial','B','9');
				$this->Cell(150,$rowheight,'Total Amount',0,0,'R');
				$this->Cell(50,$rowheight,number_format($netamt,2),0,0,'R');
				// $this->Ln();
			}

			if ($this->document_code == 'SQ'){

				$invoiceTerms	= "All bills are payable on demand unless otherwise agreed upon, interest at thirty-six percent (36%) per annum will be charged on all overdue amounts. All claims on correction to this invoice must be made within two (2) days after receipt of goods. Parties expressly submit to the jurisdiction of the courts of Makati on any legal action taken out of this transaction, an additional sum of equal to twenty-five percent (25%) of the amount due will be charged by the vendor for attorney's fees & cost of collection.";
				$this->Ln(10);
				$this->SetFont('Arial','',8);
				$this->MultiCell(200,4,'',0,'L');

				$this->Cell(20,$rowheight,'Prepared By','LT',0,'L');
				$this->Cell(80,$rowheight,'','LTR',0,'L');
				
				$this->Cell(5,$rowheight,'',0,0,'L');
				
				$this->Cell(95,$rowheight,'CONFORME : ','LTR',0,'L');
				$this->Ln();
				
				$this->Cell(20,$rowheight,'Checked By','LTB',0,'L');
				$this->Cell(80,$rowheight,'',1,0,'L');
				
				$this->Cell(5,$rowheight,'',0,0,'L');
				
				$this->SetFont('Arial','','7');
				$this->Cell(60,$rowheight,'Signature Over Printed Name','LB',0,'C');
				$this->Cell(35,$rowheight,'Date Received','BR',0,'C');

			} else if ($this->document_code == 'SO'){ 
				
			} else if ($this->document_code == 'PL' || $this->document_code == 'DR'){ 
				$this->Ln(7);
				$this->Cell(25,$rowheight,'Approved By','LT',0,'L');
				$this->Cell(75,$rowheight,'','LTR',0,'L');
				
				$this->Cell(5,$rowheight,'',0,0,'L');
				
				$this->Cell(95,$rowheight,'Received the above good/s in good condition','LTR',0,'L');
				$this->Ln();
				
				$this->Cell(25,$rowheight,'Checked By','LTB',0,'L');
				$this->Cell(75,$rowheight,'',1,0,'L');
				
				$this->Cell(5,$rowheight,'',0,0,'L');
				
				$this->SetFont('Arial','','7');
				$this->Cell(60,$rowheight,'Signature Over Printed Name','LB',0,'C');
				$this->Cell(35,$rowheight,'Date Received','BR',0,'C');
			} else {

				$invoiceTerms	= "All bills are payable on demand unless otherwise agreed upon, interest at thirty-six percent (36%) per annum will be charged on all overdue amounts. All claims on correction to this invoice must be made within two (2) days after receipt of goods. Parties expressly submit to the jurisdiction of the courts of Makati on any legal action taken out of this transaction, an additional sum of equal to twenty-five percent (25%) of the amount due will be charged by the vendor for attorney's fees & cost of collection.";
				$this->ln(10);
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
		} else {
			$this->SetY(-108);
			$pageNo 		= $this->PageNo();
			$rowheight		= 6.5;
			
			$amount_due 	= 0.00;
			$add_vat 		= 0.00;
			$less_vat 		= 0.00;

			$totalinfo		= $this->totalinfo;
			$totalamt		= $totalinfo[0];
			$netamt 		= $totalinfo[1];
			$disctype 		= $totalinfo[2];
			$discount 	 	= $totalinfo[3];
			$vat 			= $totalinfo[4];
			$amount 		= $totalinfo[5];
			$vat_sales 		= $totalinfo[6];
			$vat_exempt 	= $totalinfo[7];
			$vat_zero 		= $totalinfo[8];
			$totalqty		= $totalinfo[9];
			
			/** For Testing **/
				//$discount 		= 0;
		
			$less_vat 		= $vat;
			$add_vat 		= $vat;
			$total_sales 	= $vat_sales + $vat_exempt + $vat;
			$amount_due 	= $total_sales 	- 	$discount;
			
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
				if( $this->document_code == "PL" || $this->document_code == "DR" ){
					$this->Cell(145,$rowheight,'',0,0,'R');
					$this->Cell(49,$rowheight,number_format($total_sales,2),0,0,'R');
					$this->Ln($rowheight);
				} else {
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
		//var_dump($data);
		$col_index 	=	$this->col_index;
		//var_dump($col_index);
		
		// if ($type == 'accounts') {
		// 	$col_index = array('accountname', 'debit', 'credit');
		// 	$this->setWidths(array(40, 60, 30,30,40));
		// } else if ($type == 'payments') {
		// 	$col_index = array('sourceno', 'checknumber', 'referenceno', 'remarks', 'amount');
		// 	$this->setWidths(array(40, 60, 30,30,40));
		// } else if ($type == 'cheque') {
		// 	$col_index = array('accountname', 'chequenumber', 'chequedate', 'chequeamount');
		// 	$this->setWidths(array(40, 60, 30,30,40));
		// } else if ($type == 'SO' || $type == 'SI') {
		// 	$col_index = array('itemcode', 'description', 'quantity', 'price', 'amount');
		// 	$this->setWidths(array(40, 60, 30,30,40));
		// } else if ($type == 'PL' || $type == 'DR'){
		// 	$col_index = array('itemcode', 'description', 'quantity');
		// 	$this->setWidths(array(40, 120,40));
		// } else if ($type == 'SQ'){
		// 	$col_index = array('itemcode','description','price');
		// 	$this->setWidths(array(40, 120,40));
		// }
		

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
			//Draw the border
			// if( $this->outline == 'yes' ){
			// 	$this->Rect($x, $y, $w, $h);
			// }
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

