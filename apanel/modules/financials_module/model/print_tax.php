<?php
class print_tax extends Fpdi {

    public function setFile($file) {
	$this->SetFont("Arial", "", 10);
    $this->SetTextColor(0, 0, 0);
	$this->SetFillColor(255, 255, 255);
        $this->setSourceFile($file);
        return $this;
    }
   
    public function setDocumentInfoPayee($details) {
        $pageId = $this->importPage(1);
		$this->AddPage('P', 'Legal');
		$this->useImportedPage($pageId);
		
		$this->SetFont("Arial", "", 10);
		$this->SetTextColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
        $this->setXY(34,36);
		$this->Cell(8, 5, $details['f_mo'], 0, 0, 0, 0);
		$this->setXY(43,36);
		$this->Cell(8, 5, $details['f_dy'], 0, 0, 0, 0);
		$this->setXY(53,36);
		$this->Cell(8, 5, substr($details['f_yr'],2), 0, 0, 0, 0);
	
		$this->setXY(114,36);
		$this->Cell(8, 5, $details['to_mo'], 0, 0, 0, 0);
		$this->setXY(124,36);
		$this->Cell(8, 5, $details['to_dy'], 0, 0, 0, 0);
		$this->setXY(134,36);
		$this->Cell(8, 5, substr($details['to_yr'],2), 0, 0, 0, 0);
        return $this;
	}
	
    public function setDocumentInfoVendor($ven_dts){
		if(!empty($ven_dts["tinno"])):
			$this->SetFont("Arial", "", 10);
			$payeeTINArr1			= explode("-",$ven_dts["tinno"]);
			$payeeTINArra			= implode("",$payeeTINArr1);
			$payeeTINArr 			= str_split($payeeTINArra,3);
			if(!empty($payeeTINArr[0])):
				$this->setXY(39,48);
				$this->Cell(12, 5, $payeeTINArr[0], 0, 0, 0, 0);
			endif;
			if(!empty($payeeTINArr[1])): 
				$this->setXY(56,48);
				$this->Cell(12, 5, $payeeTINArr[1], 0, 0, 0, 0);
			endif;
			if(!empty($payeeTINArr[2])):
				$this->setXY(75,48);
				$this->Cell(12, 5, $payeeTINArr[2], 0, 0, 0, 0);
			endif;
			if(!empty($payeeTINArr[3])):
				$this->setXY(93,48);
				$this->Cell(12, 5, $payeeTINArr[3], 0, 0, 0, 0);
			endif;
		endif;

		$this->Text(41,58,$ven_dts["partnername"]);
		$this->Text(41,67,$ven_dts["address1"]);
		$this->setXY(188,63);
		$this->Cell(19, 5, '', 0, 0, 0, 0);
		$this->Text(41,74,'');
		$this->setXY(188,70);
		$this->Cell(19, 5, '', 0, 0, 0, 0);

		//FOOTER DETAILS PAYEE
		$this->SetFont("Arial", "", 10);
		$this->Text(30,288,$ven_dts["partnername"]);
		$this->Text(100,288,$ven_dts["tinno"]);
		// $this->Text(145,288,$payeeTIN);
		return $this;
	}

	public function setDocumentInfoPayor($payor_details) {
		
		if(!empty($payor_details["tin"])):
			$this->SetFont("Arial", "", 10);
			$payorTINArr1			= explode("-",$payor_details["tin"]);
			$payorTINArra			= implode("",$payorTINArr1);
			$payorTINArr 			= str_split($payorTINArra,3);
			if(!empty($payorTINArr[0])):
				$this->setXY(39,81);
				$this->Cell(12, 5, $payorTINArr[0], 0, 0, 0, 0);
			endif;
			if(!empty($payorTINArr[1])): 
				$this->setXY(56,81);
				$this->Cell(12, 5, $payorTINArr[1], 0, 0, 0, 0);
			endif;
			if(!empty($payorTINArr[2])):
				$this->setXY(75,81);
				$this->Cell(12, 5, $payorTINArr[2], 0, 0, 0, 0);
			endif;
			if(!empty($payorTINArr[3])):
				$this->setXY(93,81);
				$this->Cell(12, 5, $payorTINArr[3], 0, 0, 0, 0);
			endif;
		endif;
		$this->SetFont("Arial", "", 8);
		$this->Text(41,91,$payor_details['companyname']);
		$this->Text(41,101,$payor_details['address']);
		$this->setXY(188,97);
		$this->Cell(19, 5, '', 0, 0, 0, 0);

		// //FOOTER DETAILS PAYOR
		$this->SetFont("Arial", "", 10);
		$this->Text(30,268,$payor_details['companyname']);
		$this->Text(110,268,$payor_details['tin']);
		$this->Text(165,268,'');
        return $this;
	}
	
	public function setDetails($set_coa){
		

		// FIRST ROW //

		$arr11 = ($set_coa['details'][0]->atccode);
		$arr12 = ($set_coa['details'][0]->atc_code);
		$str1 = substr($arr11, 0, 30) ; 
		$this->SetFont("Arial", "", 8);
		$this->Text(8,123,$str1);
		$this->Text(60,123,$arr12);	
		$this->setXY(55,120);

		$transactiondate = $set_coa['main']->transactiondate;
		$date = explode('-',$transactiondate);
		$trans_date = $date[1];
		$trans_date = ltrim($trans_date, '0');

		$tot_taxbase = 0;
		$tot_tax = 0;
		if(isset($set_coa["details"])){
			for($i = 0; $i < count($set_coa["details"]); $i++){
				$tot_taxbase		+= $set_coa["details"][$i]->taxbase_amount;
				$tot_tax		+= $set_coa["details"][$i]->credit;
			}
		}
		$tot_taxbase = number_format($tot_taxbase,2);
		$tot_tax = number_format($tot_tax,2);

		$tot_taxbase1 = (in_array($trans_date, range('1','4'))) ?  $tot_taxbase : '';
		$tot_taxbase2 = (in_array($trans_date, range('5','8'))) ?  $tot_taxbase : '';
		$tot_taxbase3 = (in_array($trans_date, range('9','12'))) ?  $tot_taxbase : '';
		
		$taxbase_amount2 = 0;
		if (isset($set_coa['details'])){
			$tax = 0;
			for($i = 0; $i < count($set_coa['details']); $i++){
				$taxbase_amount_ 	= $set_coa['details'][$i]->taxbase_amount;
				$tax 				+= $set_coa['details'][$i]->credit;
				$taxbase_amount		= number_format($taxbase_amount_ ,2);
				$taxbase_amount2    += $taxbase_amount_;
			}
			$tax = number_format($tax,2);
			$taxbase_amount_ 	= $set_coa["details"][0]->taxbase_amount;
			$taxbase_amount		= number_format($taxbase_amount_ ,2);
			$arrval1 = (in_array($trans_date, range('1','4'))) ?  $taxbase_amount : '';
			$arrval2 = (in_array($trans_date, range('5','8'))) ?  $taxbase_amount : '';
			$arrval3 = (in_array($trans_date, range('9','12'))) ?  $taxbase_amount : '';
			$this->Cell(42,4,$arrval1,0,0,'R');
			$this->setXY(80,120);
			$this->Cell(42,4,$arrval2,0,0,'R');
			$this->setXY(105,120);
			$this->Cell(42,4,$arrval3,0,0,'R');
			$this->setXY(130,120);
			$this->Cell(42,4,$taxbase_amount,0,0,'R');
			$this->setXY(165,120);
			$this->Cell(42,4,$tax,0,0,'R');
		}

		if (isset($set_coa['details'][1])){
			$tax = 0;
			for($i = 0; $i < count($set_coa['details'][1]); $i++){
				$taxbase_amount_ 	= $set_coa['details'][1]->taxbase_amount;
				$tax 				+= $set_coa['details'][1]->credit;
				$taxbase_amount		= number_format($taxbase_amount_ ,2);
				$taxbase_amount2    += $taxbase_amount_;
			}
			$tax = number_format($tax,2);
		$arrval1 = (in_array($trans_date, range('1','4'))) ?  $taxbase_amount : '';
		$arrval2 = (in_array($trans_date, range('5','8'))) ?  $taxbase_amount : '';
		$arrval3 = (in_array($trans_date, range('9','12'))) ?  $taxbase_amount : '';
		$arr21 = ($set_coa['details'][1]->atccode);
		$arr22 = ($set_coa['details'][1]->atc_code);
		$str2 = substr($arr21, 0, 30) ; 
		$this->SetFont("Arial", "", 8);
		$this->Text(8,127,$str2);
		$this->Text(60,127,$arr22);
		$this->setXY(55,125);
		$this->Cell(42,4,$arrval1,0,0,'R');
		$this->setXY(80,125);
		$this->Cell(42,4,$arrval2,0,0,'R');
		$this->setXY(105,125);
		$this->Cell(42,4,$arrval3,0,0,'R');
		$this->setXY(130,125);
		$this->Cell(42,4,$taxbase_amount,0,0,'R');
		$this->setXY(165,125);
		$this->Cell(42,4,$tax,0,0,'R');
		}

		if (isset($set_coa['details'][2])){
			$tax = 0;
			for($i = 0; $i < count($set_coa['details'][0]); $i++){
				$taxbase_amount_ 	= $set_coa['details'][0]->taxbase_amount;
				$tax 				+= $set_coa['details'][0]->credit;
				$taxbase_amount		= number_format($taxbase_amount_ ,2);
				$taxbase_amount2    += $taxbase_amount_;
			}
			$tax = number_format($tax,2);
			$arr31 = ($set_coa['details'][2]->atccode);
			$arr32 = ($set_coa['details'][2]->atc_code);
			$str2 = substr($arr31, 0, 30) ; 
		$arrval1 = (in_array($trans_date, range('1','4'))) ?  $taxbase_amount : '';
		$arrval2 = (in_array($trans_date, range('5','8'))) ?  $taxbase_amount : '';
		$arrval3 = (in_array($trans_date, range('9','12'))) ?  $taxbase_amount : '';
		$this->Text(8,132,$str2);
		$this->Text(60,132,$arr32);
		$this->setXY(55,129); 
		$this->setXY(55,129);
		$this->Cell(42,4,$arrval1,0,0,'R');
		$this->setXY(80,129);
		$this->Cell(42,4,$arrval2,0,0,'R');	
		$this->setXY(105,129);
		$this->Cell(42,4,$arrval3,0,0,'R');
		$this->setXY(130,129);
		$this->Cell(42,4,$taxbase_amount,0,0,'R');
		$this->setXY(165,129);
		$this->Cell(42,4,$tax,0,0,'R'); 
		}	


		$this->Text(8,179,"");
		$this->Text(60,179,"");
		$this->setXY(55,176);
		$this->Cell(42,4,($tot_taxbase1) ,0,0,'R'); 
		
		$this->setXY(55,253);
		$this->Cell(42,4,($tot_taxbase1) ,0,0,'R'); 
		
		$this->setXY(80,176);
		$this->Cell(42,4,($tot_taxbase2),0,0,'R'); 
		
		$this->setXY(80,253);
		$this->Cell(42,4,($tot_taxbase2),0,0,'R'); 
		
		$this->setXY(105,176);
		$this->Cell(42,4,($tot_taxbase3),0,0,'R'); 
		
		$this->setXY(105,253);
		$this->Cell(42,4,($tot_taxbase3),0,0,'R'); 

		$this->setXY(130,176);
		$this->Cell(42,4,$tot_taxbase,0,0,'R'); 
	
		$this->setXY(130,253);
		$this->Cell(42,4,$tot_taxbase,0,0,'R'); 
	
		$this->setXY(130,253);
		$this->Cell(42,4,$tot_taxbase,0,0,'R'); 

		$this->setXY(165,176);
		$this->Cell(42,4,$tot_tax,0,0,'R'); 
		
		$this->setXY(165,253);
		$this->Cell(42,4,$tot_tax,0,0,'R'); 
		return $this;
	}

  

}
