<style>

tbody  {
   font-size: 14px;
}
</style>

<section class="content">
    <div class="row">
        <div class="col-md-12 text-center">
            <div><h3>Certificate of Creditable Tax<br/>Withheld at Source</h3></div>
            <div><h4>2307</h4></div>
            <div><h6>September 2005 (ENCS)</h6></div>
        </div>
    </div> 
    <br><br>
    <input type="hidden"  id="voucherno" value="<?=$sid?>" class="form-control">
<div class="container">
    <div class="row">
        <div class="col-md-2"><div class="row"><div class="col-md-12">1 For the Period From</div></div></div>
        <div class="col-md-4">
            <div class="form-group"> 
                <div class="col-md-3 col-sm-3">
                    <input type="text" readonly name="monthfilterFrom" value="<?=$f_mo?>" class="form-control">
                </div>	
                <div class="col-md-4 col-sm-3">
                    <input type="text" readonly name="dayfilterFrom" value="<?=$f_dy?>" class="form-control">
                </div>	 
                <div class="col-md-5 col-sm-4">
                    <input type="text" readonly name="yearfilterFrom" value="<?=$f_yr?>" class="form-control">		
                </div>
            </div>
            <br>
            <h6>(MM/DD/YYYY)</h6> 
        </div>
        <div class="col-md-2"><div class="row"><div class="col-md-12">To</div></div></div>
        <div class="col-md-4">
            <div class="form-group"> 
                <div class="col-md-3 col-sm-3">
                    <input type="text" readonly name="monthfilterTo" value="<?=$to_mo?>" class="form-control">
                </div>	
                <div class="col-md-4 col-sm-3">
                    <input type="text" readonly name="dayfilterTo" value="<?=$to_dy?>" class="form-control">
                </div>	 
                <div class="col-md-5 col-sm-4">
                    <input type="text" readonly name="yearfilterTo" value="<?=$f_yr?>" class="form-control">
                </div>
            </div>
            <br>
            <h6>(MM/DD/YYYY)</h6> 
        </div>
    </div>

    <div class="row bg-info">
        <div class="col-md-2"><h4>Part I</h4></div>
            <div class="col-md-10 text-center"><h4>Payee Information</h4></div>
        </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col-md-2">2 Taxpayer Identification Number</div> 
            <div class="col-md-5" id="payee_tin_div">
                <input required type="text" id="payee_tin" value="<?=$tinno?>" name="payee_tin"  class="form-control" placeholder="000-000-000-000" readonly>
            </div>
    </div>
    <div class="row">
	    <div class="col-md-2">3 Payee Name</div> 
            <div class="col-md-10">
                <input required type="text" id="payee_name" name="payee_name" value="<?=$partnername?>" readonly class="form-control">
            </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
            <div class="col-md-10 text-center"><h6>(Last Name, First Name, Middle Name for Individuals) (Registered Name for Non-Individuals)</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">4 Registered Address</div>
        <div class="col-md-6" id="payee_address_div"><input  value="<?=$address1?>" type="text" readonly id="payee_address"  name="payee_address" class="form-control"></div>
        <div class="col-md-2">4A Zip Code</div>
        <div class="col-md-2" id="payee_zipcode_div"><input readonly type="text" id="payee_zipcode" name="payee_zipcode" value="" class="form-control"></div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
	    <div class="col-md-2">5 Foreign Address</div>
	    <div class="col-md-6" id="payee_foreign_address_div"><input type="text" id="payee_foreign_address" name="payee_foreign_address" value="" class="form-control" readonly></div>
	    <div class="col-md-2">5A Zip Code</div>
	    <div class="col-md-2"  id="payee_foreign_zipcode_div" ><input type="text"  id="payee_foreign_zipcode" name="payee_foreign_zipcode" value="" class="form-control" readonly></div>
	    <input type="hidden" name="payee_position" value="" >
    </div> 
    <div class="row">&nbsp;</div>
    <div class="row bg-info">
	    <div class="col-md-2"></div>
	    <div class="col-md-10 text-center"><h4>Payor Information</h4></div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col-md-2">6 Taxpayer Identification Number</div> 
        <div class="col-md-5"><input required type="text" name="payor_tin" value="<?=$tin?>" readonly class="form-control" placeholder="000-000-000-000"></div>
    </div>
    <div class="row">
        <div class="col-md-2">7 Payee Name</div> 
        <div class="col-md-10"><input type="text" name="payor_name" value="<?=$companyname?>" readonly class="form-control"></div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-10 text-center"><h6>(Last Name, First Name, Middle Name for Individuals) (Registered Name for Non-Individuals)</h6></div>
    </div>
    <div class="row">
        <div class="col-md-2">8 Registered Address</div>
        <div class="col-md-6"><input type="text" readonly name="payor_address" value="<?=$address?>" class="form-control"></div>
        <div class="col-md-2">8A Zip Code</div>
        <div class="col-md-2"><input type="text" readonly name="payor_zipcode" value="" class="form-control"></div>
        <input type="hidden" name="payor_role" value="">
    </div>
    <div class="row">&nbsp;</div>
    <div class="row bg-info">
	    <div class="col-md-2"><h4>Part II</h4></div>
	    <div class="col-md-10 text-center"><h4>Details of Monthly Income Payments and Tax Withheld for the Quarter</h4></div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-condensed table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2">Income Payments Subject to Expanded Withholding Tax</th>
                        <th colspan="4" class="text-center">AMOUNT OF INCOME PAYMENTS</th>
                        <th class="text-center" rowspan="2">Tax Withheld for the Quarter</th>
                    </tr>
                    <tr>
                        <th class="text-center">1st Month of the Quarter</th>
                        <th class="text-center">2nd Month of the Quarter</th>
                        <th class="text-center">3rd Month of the Quarter</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <!-- <tbody>
                    <tr>
                        <td>
                        <input type="hidden" name="incomepayment1" value="WC158" >
                        <input type="text"  class="form-control" readonly value="Top 20K Corp - Goods" >
                        
                        </td>
                        <td><input type="text" name="amount_1st_month1" id="amount_1st_month1" value="0.00" class="form-control right" readonly></td>
                        <td><input type="text" name="amount_2nd_month1" id="amount_2nd_month1" value="0.00" readonly class="form-control right"></td>
                        <td><input type="text" name="amount_3rd_month1" id="amount_3rd_month1" readonly value="8,177.65"  class="form-control right"></td>
                        <td><input type="text" id="total_amount_income_payments1" name="total_amount_income_payments1" readonly value="8,177.65" class="form-control right"></td>
                        <td><input type="text" id="tax_withheld1" name="tax_withheld1" value="81.78" readonly class="right form-control right"></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td><input type="text" name="total_amount_1st_month" id="total_amount_1st_month" readonly value="0.00" class="form-control right"></td>
                        <td><input type="text" name="total_amount_2nd_month" id="total_amount_2nd_month" readonly value="0.00" class="form-control right"></td>
                        <td><input type="text" name="total_amount_3rd_month" id="total_amount_3rd_month" readonly value="8,177.65" class="form-control right"></td>
                        <td><input type="text" name="grand_total_amount_income_payments" id="grand_total_amount_income_payments" readonly value="8,177.65" class="form-control right"></td>
                        <td><input type="text" name="total_tax_withheld" id="total_tax_withheld" readonly value="81.78" class="form-control right"></td>
                    </tr>

                    </tr>
                </tbody> -->
                <tbody class="tbody" >
                    <?
                        $transactiondate = $main->transactiondate;
                        $date = explode('-',$transactiondate);
                        $trans_date = $date[1];
                        $trans_date = ltrim($trans_date, '0');
                        if(!empty($sid)){
                            $row 			= 1;
                            $total_debit 	= 0;
                            $total_credit 	= 0;
                            $taxbase_amount2 = 0;
                            $tot_taxbase_amount = 0;
                            for($i = 0; $i < count($data["wtax_details"]); $i++)
                            {
                                $accountlevel		= $data["wtax_details"][$i]->accountcode;
                                $accountname		= $data["wtax_details"][$i]->atccode;
                                $accountcode		= $accountname;
                                $detailparticulars	= $data["wtax_details"][$i]->detailparticulars;
                                $debit				= $data["wtax_details"][$i]->debit;
                                $credit				= $data["wtax_details"][$i]->credit;
                                $debit_attr			= array();
                                $credit_attr		= array();
                                $taxcode 			= $data["wtax_details"][$i]->taxcode;
                                $taxbase_amount_ 	= $data["wtax_details"][$i]->taxbase_amount;
                                $taxbase_amount		= number_format($taxbase_amount_ ,2);
                                $taxbase_amount2    += $taxbase_amount_;
                                $tot_taxbase_amount		= number_format($taxbase_amount2 ,2);
                                $total_credit 	    +=      $credit;
                        ?>	
                                <tr class="clone" valign="middle">
                                    <td class = "remove-margin">
                                        <?php
                                            echo $ui->formField('dropdown')
                                                    ->setPlaceholder('Select One')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName("accountcode[".$row."]")
                                                    ->setClass('accountcode')
                                                    ->setId("accountcode[".$row."]")
                                                    ->setValue($accountname)
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('detailparticulars['.$row.']')
                                                    ->setId('detailparticulars['.$row.']')
                                                    ->setAttribute(array("maxlength" => "100"))
                                                    ->setValue((in_array($trans_date, range('1','4'))) ?  $taxbase_amount : '')
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('detailparticulars['.$row.']')
                                                    ->setId('detailparticulars['.$row.']')
                                                    ->setAttribute(array("maxlength" => "100"))
                                                    ->setValue((in_array($trans_date, range('5','8'))) ?  $taxbase_amount : '')
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                       <?php
                                           echo $ui->formField('text')
                                                   ->setSplit('', 'col-md-12')
                                                   ->setName('detailparticulars['.$row.']')
                                                   ->setId('detailparticulars['.$row.']')
                                                   ->setAttribute(array("maxlength" => "100"))
                                                   ->setValue((in_array($trans_date, range('9','12'))) ?  $taxbase_amount : '')
                                                   ->draw($show_input);
                                       ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('debit['.$row.']')
                                                    ->setId('debit['.$row.']')
                                                    ->setClass("format_values_db format_values text-right")
                                                    ->setValue($taxbase_amount)
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('credit['.$row.']')
                                                    ->setId('credit['.$row.']')
                                                    ->setClass("format_values_cr format_values text-right credit")
                                                    ->setValue(number_format($credit,2))
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                </tr>
                        <?	
                                $row++;	
                            }
                        }       
                    ?>          
                                <tr>
                                <td class = "remove-margin">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setPlaceholder('Select One')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName("accountcode[".$row."]")
                                                    ->setClass('accountcode')
                                                    ->setId("accountcode[".$row."]")
                                                    ->setLabel("Total")
                                                    // ->setValue($accountname)
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('detailparticulars['.$row.']')
                                                    ->setId('detailparticulars['.$row.']')
                                                    ->setAttribute(array("maxlength" => "100"))
                                                    ->setValue((in_array($trans_date, range('1','4'))) ?  $tot_taxbase_amount : '')
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('detailparticulars['.$row.']')
                                                    ->setId('detailparticulars['.$row.']')
                                                    ->setAttribute(array("maxlength" => "100"))
                                                    ->setValue((in_array($trans_date, range('5','8'))) ?  $tot_taxbase_amount : '')
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                       <?php
                                           echo $ui->formField('text')
                                                   ->setSplit('', 'col-md-12')
                                                   ->setName('detailparticulars['.$row.']')
                                                   ->setId('detailparticulars['.$row.']')
                                                   ->setAttribute(array("maxlength" => "100"))
                                                   ->setValue((in_array($trans_date, range('9','12'))) ?  $tot_taxbase_amount : '')
                                                   ->draw($show_input);
                                       ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('debit['.$row.']')
                                                    ->setId('debit['.$row.']')
                                                    ->setClass("format_values_db format_values text-right")
                                                    ->setValue($tot_taxbase_amount)
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                            echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('credit['.$row.']')
                                                    ->setId('credit['.$row.']')
                                                    ->setClass("format_values_cr format_values text-right credit")
                                                    ->setValue(number_format($total_credit,2))
                                                    ->draw($show_input);
                                        ?>
                                    </td>
                                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissable">We suggest that you don't close this page (the page you type into), until you have viewed and are satisfied with the generated .pdf. Make any ammendments on this page and then press the 'Generate PDF' button again. Inputted Values will not be saving in the database.</div>
        </div>
    </div> 
    <div class="row"><div class="col-md-12 text-center">
        <input type="submit" class="btn btn-primary btn-md generate_pdf" id="print_2307" value="Generate PDF" name="generate_pdf">
    </div></div>
    </form>
    </div>
</div>         
</section>

<script>
$('#print_2307').click(function(){
    var vno =  $('#voucherno').val();
    console.log(vno);
    window.location = '<?=MODULE_URL?>generate_pdf/' + vno;
});
</script>
