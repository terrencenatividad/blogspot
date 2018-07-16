<?php
class print_check extends fpdf {


    public $font_size		= '';
	public $companyinfo		= array();
	public $documentinfo	= array();
	public $totalinfo		= array();
	public $widths			= '';
	public $aligns			= '';
	public $document_type	= '';
	public $vendor			= '';
	public $payments		= '';
	public $cheque		    = '';
    public $customer		= '';
    var $angle              =0;


	public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
		parent::__construct($orientation, $unit, $size);
		$this->db = new db();
		$this->setMargins(8, 8);
	}

	public function setPreviewTitle($title) {
		$this->SetTitle($title . ' - Test', true);
		return $this;
    }
    
    public function drawPDF($filename = 'print_preview') {
		$this->drawDocumentDetails();
		$this->Output($filename . '.pdf', 'I');
	}

    public function setDocumentType($document_type) {
		$this->document_type = $document_type;
		return $this;
    }
    
    public function drawDocumentDetails() {
        return $this;
    }
    
    public function setDocumentInfo($dts){
        $this->AddPage();
		$this->Ln();
		$this->SetFont('Times','',6);
		    $this->SetTextColor(0, 0, 0);

        $vendor = $dts[0]->partnername;
        $amount = $dts[0]->chequeconvertedamount;
        $date = $dts[0]->chequedate;
        $date  = date('M j, Y', strtotime($date));


        /**AMOUNT IN WORDS**/
		$convert		= new convert($amount,'Pesos', true);
		$rowheight		= 5;
		$amt_words		= $convert->display() ;
		$first_part		= $amt_words;
		$wordsLen		= strlen($amt_words);

		if ($wordsLen > 100) {
			$first_part		= substr($amt_words, 0, 100);
			$second_part	= substr($amt_words, 100, 120);
		}

		// $this->SetFont('Arial','I',9);
        // $this->RotatedText(98.5, 180,'**'.strtoupper($first_part).'**', 90);

        $this->SetFont('Times','',10);
        $this->Cell(160,4,'',0,0,'R');
        $this->SetFont('Times','B',10);
        $this->RotatedText(82, 46, $date, 90);
        $this->Ln(8);

        $this->SetFont('Times','B',10);
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(30,3,"",0,0,'L');
        $this->Ln(4);
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(40,3,"",0,0,'L');
        $this->RotatedText(90.5, 165,'**'.strtoupper($vendor).'**', 90);
        $this->Cell(2,3,'',0,0,'C');
        $this->SetFont('Times','B',10);
        $this->RotatedText(90.5, 43,number_format($amount,2), 90);
        $this->SetFont('Times','B',10);
        
        $this->Ln(12);
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(13,3,"",0,0,'L');
        $this->RotatedText(98.5, 180,'**'.strtoupper($first_part).'**', 90);
        
        $this->Ln(6);
        $this->Cell(2,3,'',0,0,'C');
        $this->Cell(3,3,'',0,0,'C');
        $this->Cell(50,15,"",0,0,'C');
        $this->Cell(4,15,'',0,0,'C');
        $this->Cell(65,15,"",'0',0,'L');
        $this->Cell(5,15,'',0,0,'C');
        $this->Cell(65,15,"",'0',0,'L');
        return $this;
    }

    function RotatedText($x, $y, $txt, $angle){
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function Rotate($angle,$x=-1,$y=-1) 
    {

        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)

        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
	} 
  

}
