<section class="content">
    <input type = "hidden" id = "valid" name = "valid" value = "0"/>
	<input type = "hidden" id = "prefix" name = "prefix" value = "<?= $prefix ?>"/>
	<input type = "hidden" id = "noCashAccounts" name = "noCashAccounts" value = "<?= $noCashAccounts ?>"/>

    <!--DELETE PAYMENT CONFIRMATION MODAL-->
    <div class="modal fade" id="deletePaymentModal" tabindex="-1" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    Confirmation
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this payment?
                </div>
                <div class="modal-footer">
                    <div class="row row-dense">
                        <div class="col-md-12 center">
                            <div class="btn-group">
                                <button type="button" class = "btn btn-primary btn-flat" id="btnYes">Yes</button>
                            </div>
                                &nbsp;&nbsp;&nbsp;
                            <div class="btn-group">
                                <button type="button" class = "btn btn-default btn-flat" data-dismiss="modal">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>	
    <!--END DELETE PAYMENT CONFIRMATION MODAL-->

    <div class = "well well-lg">
        <div class = "panel panel-default">
            <div class = "panel-heading">
                <form class = "form-horizontal hidden" method="POST" id="receiptForm">
                    <input value = "<?= $v_voucherno ?>" name="invoiceno[1]" id="invoiceno[1]" type="hidden">
                    <input value = "<?= $v_customercode ?>" name="customer[1]" id="customer[1]" type="hidden">
                    <input value = "accounts_receivable" name="type" id="type" type="hidden">
                    <input value = "<?= $v_convertedamount?>" name="totalInvoice" id="totalInvoice" type="hidden">
                    <input value = "" name = "paymentrow" id = "paymentrow" type = "hidden">
                    <input value = "<?= $v_exchangerate ?>" name = "payablerate" id = "payablerate" type = "hidden">
                    <input value = "<?= $v_exchangerate ?>" name="exchangerate[1]" id="exchangerate[1]" type="hidden">
                    <input value = "<?= $v_convertedamount ?>" name="paymentamount[1]" id="paymentamount[1]" type="hidden">

                    <div class="row">
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-5">
                                    <h4><strong>Account Receivable : </strong><?=$v_voucherno?></h4>
                                </div>
                                <div class="col-md-6">
                                <?php
                                    echo $ui->formField('text')
                                            ->setLabel('Payment Date')
                                            ->setSplit('col-md-offset-3 col-md-4', 'col-md-5')
                                            ->setName('paymentdate[1]')
                                            ->setId('paymentdate[1]')
                                            ->setClass('input-sm datepicker-input')
                                            ->setAttribute(array('readonly' => '', 'data-date-start-date' => $close_date))
                                            ->setAddon('calendar')
                                            ->setValue($date)
                                            ->setValidation('required')
                                            ->draw(true);
                                ?>
                                </div>
                            </div>
                            <hr/>

                            <div class="row" id="payment">
                                <div class = "col-md-6">
                                    <?php
                                        echo $ui->formField('dropdown')
                                                ->setLabel('Payment Mode')
                                                ->setSplit('col-md-4', 'col-md-8')
                                                ->setClass("input-sm payment_mode")
                                                ->setName('paymentmode[1]')
                                                ->setId('paymentmode[1]')
                                                ->setList(
                                                    array(
                                                        "cash" => "Cash", 
                                                        "cheque" => "Cheque",
                                                        "transfer" => "Bank Transfer"
                                                    )
                                                )
                                                ->setValidation('required')
                                                ->setAttribute(
                                                    array(
                                                        'onChange' => 'toggleCheckInfo(this.value)'
                                                    )
                                                )
                                                ->setValue("")
                                                ->draw(true);
                                    ?>
                                </div>

                                <div class = "col-md-6" id = "check_field">
                                    <?php
                                        echo $ui->formField('text')
                                                ->setLabel('Reference Number')
                                                ->setSplit('col-md-4', 'col-md-8')
                                                ->setClass("input-sm")
                                                ->setName('paymentreference[1]')
                                                ->setId('paymentreference[1]')
                                                ->setAttribute(array("maxlength" => "50"))
                                                ->setValue("")
                                                ->draw(true);
                                    ?>
                                    <span class="help-block hidden small req-color" id = "paymentreference[1]_help" style = "margin-bottom: 0px"><i class="glyphicon glyphicon-exclamation-sign"></i> Field is required.</span>
                                </div>

                                <div class="col-md-2 hidden" id="payment_field">

                                    <?php
                                        echo $ui->formField('text')
                                                ->setSplit('col-md-4', 'col-md-8')
                                                ->setClass("input-sm text-right")
                                                ->setName('paymentamountfield')
                                                ->setId('paymentamountfield')
                                                ->setValue("")
                                                ->draw(true);
                                    ?>

                                    <span class="help-block hidden small" id = "paymentamountfield_help"><i class="glyphicon glyphicon-exclamation-sign"></i> Please specify amount.</span>
                                </div>

                            </div>

                            <div class="row" id="cash_payment_details">
                                <div class = "col-md-6">
                                    <input type = "hidden" id = "prevpayment"/>
                                    <?php
                                        echo $ui->formField('text')
                                                ->setLabel('Payment Amount')
                                                ->setSplit('col-md-4', 'col-md-8')
                                                ->setClass("input-sm pay_amount")
                                                ->setName('convertedamount[1]')
                                                ->setId('convertedamount[1]')
                                                ->setAttribute(
                                                    array(
                                                        "maxlength" => "50", 
                                                        "onBlur" => "checkBalance();formatNumber(this.id); computeExchangeRate('paymentRateForm','paymentnewamount');"
                                                    )
                                                )
                                                ->setValidation('required')
                                                ->setValue("")
                                                ->draw(true);
                                    ?>
                                    <input class="form-control " maxlength="50" value="" name="paymentnumber[1]" id="paymentnumber[1]" type="hidden">
                                </div>

                                <div class = "col-md-6 remove-margin">
                                    <?php
                                        echo $ui->formField('dropdown')
                                                ->setLabel('Paid To')
                                                ->setSplit('col-md-4', 'col-md-8')
                                                ->setClass("input-sm pay_account")
                                                ->setPlaceholder('Filter Accounts')
                                                ->setName('paymentaccount[1]')
                                                ->setId('paymentaccount[1]')
                                                ->setList($cash_account_list)
                                                ->setValidation('required')
                                                ->draw(true);
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class = "col-md-6">
                                    <?php
                                        echo $ui->formField('text')
                                                ->setLabel('Discount')
                                                ->setSplit('col-md-4', 'col-md-8 field_col')
                                                ->setClass("input-sm pay_discount format_values")
                                                ->setName('paymentdiscount[1]')
                                                ->setId('paymentdiscount[1]')
                                                ->setPlaceHolder("0.00")
                                                ->setAttribute(
                                                    array(
                                                        "onBlur" => "computeDiscount(); formatNumber(this.id);"
                                                    )
                                                )
                                                ->setValue("")
                                                ->draw(true);
                                    ?>
                                </div>

                                <div class = "col-md-6 hidden">
                                    <div class="form-group">
                                        <label for="paymentexchangerate[1]" class="control-label col-md-4">Exchange Rate</label>
                                        <div class="col-md-8">
                                        

                                        <a href="javascript:void(0);" role="button" class="btn btn-success btn-block" onClick="toggleExchangeRate('payment');" style="text-align:right;">
                                            <strong id="btnRate">
                                                <?=number_format($v_exchangerate,2);?>&nbsp;&nbsp;
                                            </strong>
                                        </a>
                                    </div>
                                    <?php
                                        // echo $ui->formField('text')
                                        //         ->setLabel('Exchange Rate')
                                        //         ->setSplit('col-md-4', 'col-md-8')
                                        //         ->setName('paymentexchangerate[1]')
                                        //         ->setId('paymentexchangerate[1]')
                                        //         ->setClass("btn btn-success btn-flat text-right text-bold payexrate")
                                        //         ->setValue($v_exchangerate)
                                        //         ->draw(true);
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                        echo $ui->formField('textarea')
                                                ->setLabel('Notes')
                                                ->setSplit('col-md-2', 'col-md-10')
                                                ->setName('paymentnotes[1]')
                                                ->setId('paymentnotes[1]')
                                                ->setAttribute(
                                                    array(
                                                        'rows' => 4
                                                    )
                                                )
                                                ->setValue("")
                                                ->draw(true);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- MULTIPLE CHEQUES -->
                            <span id="chequeAmountError" class="text-danger hidden small">
                                <i class="glyphicon glyphicon-exclamation-sign"></i> 
                                Please complete the fields on the highlighted row(s)<br/>
                            </span>
                            <span id="paymentAmountError" class="text-danger hidden small">
                                <i class="glyphicon glyphicon-exclamation-sign"></i> 
                                Please make sure that the total cheque amount applied (<strong id="disp_tot_cheque">0</strong>) should be equal to the total payment amount (<strong id="disp_tot_payment">0</strong>)<br/>
                            </span>
                            <span id="checkNumberError" class="text-danger hidden small">
                                <i class="glyphicon glyphicon-exclamation-sign"></i> 
                                The Cheque Number you entered has already been used<br/>
                            </span>

                            <div class="panel panel-default hidden" id="check_details">
                                <div class="panel-heading">
                                    <strong>Cheque Details</strong>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-condensed table-bordered table-hover" id="chequeTable">
                                        <thead>
                                            <tr class="info">
                                                <th class="col-md-3 text-center">Bank Account</th>
                                                <th class="col-md-3 text-center">Cheque Number</th>
                                                <th class="col-md-2 text-center">Cheque Date</th>
                                                <th class="col-md-2 text-center">Currency Amount</th>
                                                <!-- <th class="col-md-2 text-center">Converted Amount</th> -->
                                                <th class="col-md-1 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="clone">
                                                <td class="">
                                                    <?php
                                                        echo $ui->formField('dropdown')
                                                                ->setSplit('', 'col-md-12 field_col')
                                                                ->setPlaceholder('Select One')
                                                                ->setClass("test")
                                                                ->setName('chequeaccount[1]')
                                                                ->setId('chequeaccount[1]')
                                                                ->setList($cash_account_list)
                                                                ->setValue("")
                                                                ->draw(true);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        echo $ui->formField('text')
                                                                ->setSplit('', 'col-md-12 field_col')
                                                                ->setClass("input-sm")
                                                                ->setName('chequenumber[1]')
                                                                ->setId('chequenumber[1]')
                                                                ->setAttribute(array("maxlength" => "100", "onBlur" => "validateChequeNumber(this.id, this.value, this)"))
                                                                ->setValue("")
                                                                ->draw(true);
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="input-group date remove-margin">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>

                                                        <?php
                                                            echo $ui->formField('text')
                                                                    ->setSplit('', 'col-md-12 field_col')
                                                                    ->setClass("input-sm datepicker-input")
                                                                    ->setName('chequedate[1]')
                                                                    ->setId('chequedate[1]')
                                                                    ->setAttribute(array("maxlength" => "50"))
                                                                    ->setValue($date)
                                                                    ->draw(true);
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                        echo $ui->formField('text')
                                                                ->setSplit('', 'col-md-12 field_col')
                                                                ->setClass("input-sm text-right chequeamount")
                                                                ->setName('chequeamount[1]')
                                                                ->setId('chequeamount[1]')
                                                                ->setAttribute(array("maxlength" => "20"))
                                                                ->setValue("0.00")
                                                                ->draw(true);
                                                    ?>
                                                </td>
                                                <!-- <td>
                                                    <?php
                                                        // echo $ui->formField('text')
                                                        //         ->setSplit('', 'col-md-12 field_col')
                                                        //         ->setClass("input-sm text-right")
                                                        //         ->setName('chequeconvertedamount[1]')
                                                        //         ->setId('chequeconvertedamount[1]')
                                                        //         ->setAttribute(array("maxlength" => "20"))
                                                        //         ->setValue("0.00")
                                                        //         ->draw(true);
                                                    ?>
                                                    
                                                </td> -->
                                                <td class="text-center">
                                                    <!--<button type="button" class="btn btn-sm btn-success btn-flat hidden" id="checkprint[1]" style="outline:none;" onClick="confirmChequePrint(1);" title="Print Cheque"><span class="glyphicon glyphicon-print"></span></button>
                                                    &nbsp;-->
                                                    <button type="button" class="btn btn-sm btn-danger btn-flat confirm-delete" name="chk[]" style="outline:none;" onClick="confirmChequeDelete(1);"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">
                                                    <a type="button" class="btn btn-sm btn-link add-cheque" style="text-decoration:none; outline:none;" href="javascript:void(0);">Add a New Line</a>
                                                </td>
                                                <td class="text-right"><label class="control-label">Total</label></td>
                                                <td class="text-right">
                                                    <?php
                                                        echo $ui->formField('text')
                                                                ->setSplit('', 'col-md-12 field_col')
                                                                ->setClass("text-right input_label")
                                                                ->setId("total")
                                                                ->setValue(number_format(0, 2))
                                                                ->draw(true);
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                        echo $ui->formField('text')
                                                                ->setSplit('', 'col-md-12 field_col')
                                                                ->setClass("text-right input_label")
                                                                ->setId("total_converted")
                                                                ->setValue(number_format(0, 2))
                                                                ->draw(true);
                                                    ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-11 text-right">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" onClick="savePaymentRow(event,'button1');" >Save</button>
                            &nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-default btn-sm btn-flat" onClick="clearPaymentRow(event)">Clear</button>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-md-12">
                            <hr/>
                        </div>
                    </div>
                </form>
            </div>
            <div class = "panel-body">
                <div class = "row">
                    <div class="col-md-8 col-sm-8 col-xs-8">
                        <h3>
                            <?php
                                echo $status_badge;
                            ?>
                        </h3>
                        <h2><strong>Accounts Receivable</strong> <small><?='('.$v_voucherno.')'?></small></h2>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4" style="vertical-align:middle;">
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
                                <h4>Date</h4>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
                                <h4>: <strong><?=date("M d, Y",strtotime($v_transactiondate));?></strong></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
                                <h4>Due Date</h4>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
                                <h4>: <strong><?=date("M d, Y",strtotime($v_duedate));?></strong></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
                                <h4>Invoice No</h4>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
                                <h4>: <strong><?=$v_invoiceno;?></strong></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-sm-5 col-xs-5" style="vertical-align:middle;">
                                <h4>Reference No</h4>
                            </div>
                            <div class="col-md-7 col-sm-7 col-xs-7" style="vertical-align:middle;">
                                <h4>: <strong><?=$v_referenceno;?></strong></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class = "row">
                    <div class="col-md-8 col-sm-8 col-xs-8">
                        <h4>customer :</h4>
                        <h4><strong><?=$v_customer?></strong></h4>
                        <div class="row">
                            <div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
                                Email
                            </div>
                            <div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
                                : <?=$v_email?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
                                TIN
                            </div>
                            <div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
                                : <?=$v_tinno?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 col-sm-1 col-xs-1" style="vertical-align:middle;">
                                Address
                            </div>
                            <div class="col-md-11 col-sm-11 col-xs-11" style="vertical-align:middle;">
                                : <?=$v_address1?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class = "row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <hr/>
                    </div>
                </div>
                <div class = "panel panel-default">
                    <div class="box-body table-responsive no-pad">
                        <table class="table table-hover">
                            <thead>
                                <tr class="info">
                                    <th class="col-md-3 text-center">Account</th>
                                    <th class="col-md-4 text-center">Description</th>
                                    <th class="col-md-2 text-center">Debit</th>
                                    <th class="col-md-2 text-center">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $total_debit 	= 0;
                                    $total_credit 	= 0;
                                    
                                    if(!is_null($data["details"]) && !empty($sid))
                                    {
                                        for($i = 0; $i < count($data["details"]); $i++)
                                        {
                                            $accountlevel		= $data["details"][$i]->accountcode;
                                            $accountname		= $data["details"][$i]->accountname;
                                            $accountcode		= ($task != 'view') ? $accountlevel : $accountname;
                                            $detailparticular	= $data["details"][$i]->detailparticulars;
                                            $debit				= $data["details"][$i]->debit;
                                            $credit				= $data["details"][$i]->credit;
                                            
                                            echo '<tr>';	
                                                echo '<td>'.$accountname.'</td>';
                                                echo '<td>'.$detailparticular.'</td>';
                                                echo '<td class="text-right">'.number_format($debit,2).'</td>';
                                                echo '<td class="text-right">'.number_format($credit,2).'</td>';
                                            echo '</tr>';
                                            
                                            $total_debit += $debit;
                                            $total_credit += $credit;
                                        }
                                    }


                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right" style="border-top:1px solid #DDDDDD;">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_debit,2)?></strong></td>
                                    <td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_credit,2)?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php
                if(!empty($v_notes))
                {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4>
                            <strong>Notes :</strong>
                            <br/><br/>
                            <p><em id="particulars"><?php echo $v_notes;?></em></p>
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr/>
                    </div>
                </div>
                <?php
                }
                ?>
                <div class = "row">
                    <div class="col-md-2 col-sm-2 col-xs-2 left">
                    &nbsp;
                    </div>
                    <div class="col-md-8 col-sm-8 col-xs-8 text-center">
                        <?if($v_balance > 0){?>
                        <button type="button" class="btn btn-primary btn-md btn-flat" id="btnReceive">Receive Payment</button>
                        &nbsp;
                        <?}?>
                        
                        <?if(empty($data["payments"]) && $data['checker'] != "import" ){?>
                        <a href="<?=BASE_URL?>financials/accounts_receivable/edit/<?=$sid?>" class="btn btn-primary btn-md btn-flat">Edit</a>
                        <?}?>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2 text-right">
                        <a href="<?=BASE_URL?>financials/accounts_receivable" role="button" class="btn btn-primary btn-md btn-flat" id="btnExit" >Exit</a>
                    </div>
                </div>

                 <!--PAYMENT ISSUED-->
                <br/>
                <div id="pd_div" class = "table-responsive hidden">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Received Payments</strong>
                        </div>
                        <table class="table table-striped table-condensed table-bordered" id="paymentsTable">
                            <thead>
                                <tr class="info">
                                    <th class="col-md-1 text-center">Date</th>
                                    <th class="col-md-1 text-center">Mode</th>
                                    <th class="col-md-2 text-center">Reference</th>
                                    <th class="col-md-2 text-center">Payment Account</th>
                                    <th class="col-md-2 text-center">Amount</th>
                                    <th class="col-md-2 text-center">Discount</th>
                                    <th class="col-md-1 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $totalPayment	= 0;
                                $totaldiscount	= 0;
                                $row = 1;
                                if(!is_null($data["payments"]) && !empty($data["payments"]))
                                {
                                    for($i = 0; $i < count($data["payments"]); $i++)
                                    {
                                        $paymentnumber		= $data["payments"][$i]->voucherno;
                                        $paymentdate		= $data["payments"][$i]->transactiondate;
                                        $paymentdate		= $this->date->dateFormat($paymentdate);
                                        $paymentaccountcode	= $data["payments"][$i]->accountcode;
                                        $paymentaccount		= $data["payments"][$i]->accountname;
                                        // $paymenttaxcode		= $data["payments"][$i]->wtaxcode;
                                        // $paymenttax			= $applicationArr[5];
                                        $paymentmode		= $data["payments"][$i]->paymenttype;
                                        $modeOfPayment		= ($paymentmode == 'transfer') ? 'Bank Transfer' : ucwords($paymentmode);
                                        $reference			= $data["payments"][$i]->referenceno;
                                        $paymentamount		= $data["payments"][$i]->amount;
                                        $paymentstat		= $data["payments"][$i]->stat;
                                        $paymentcheckdate	= $data["payments"][$i]->checkdate;
                                        $paymentcheckdate	= ($paymentcheckdate != '0000-00-00') ? $this->date->dateFormat($paymentcheckdate) : "";
                                        $paymentatccode		= $data["payments"][$i]->atcCode;
                                        $paymentnotes		= $data["payments"][$i]->particulars;
                                        $checkstat			= $data["payments"][$i]->checkstat;
                                        $paymentdiscount	= $data["payments"][$i]->discount;
                                        $paymentrate		= (isset($data["payments"][$i]->exchangerate) && !empty($data["payments"][$i]->exchangerate)) ? $data["payments"][$i]->exchangerate : 1;
                                        $paymentconverted	= (isset($data["payments"][$i]->convertedamount) && $data["payments"][$i]->convertedamount > 0) ? $data["payments"][$i]->convertedamount : $paymentamount;
                                        
                                        $cheque_values		= (!is_null($rollArray) && !empty($rollArray[$paymentnumber])) ? json_encode($rollArray[$paymentnumber]) : "";

                                        echo '<tr>';
                                                echo '<td>';
                                                echo $ui->formField('text')
                                                        ->setClass("input_label")
                                                        ->setName('paymentdate'.$row)
                                                        ->setId('paymentdate'.$row)
                                                        ->setValue($paymentdate)
                                                        ->setAttribute(array("readonly" => "readonly"))
                                                        ->draw(true);
                                                echo '<input value="'.$paymentnumber.'" name = "paymentnumber'.$row.'" id = "paymentnumber'.$row.'" type = "hidden">';
                                                echo '</td>';

                                                echo '<td>';
                                                echo $ui->formField('text')
                                                        ->setClass("input_label")
                                                        ->setName("pmode1".$row)
                                                        ->setId("pmode1".$row)
                                                        ->setAttribute(array("disabled" => "disabled"))
                                                        ->setValue($modeOfPayment)
                                                        ->draw(true);
                                                
                                                echo '<input value="'.$paymentmode.'" name = "paymentmode'.$row.'" id = "paymentmode'.$row.'" type = "hidden">';
                                                echo '</td>';

                                                echo '<td>';
                                                echo $ui->formField('text')
                                                        ->setClass("input_label")
                                                        ->setName("paymentreference".$row)
                                                        ->setId("paymentreference".$row)
                                                        ->setAttribute(array("readonly" => "readonly"))
                                                        ->setValue($reference)
                                                        ->draw(true);
                                                echo '<input value="'.$paymentcheckdate.'" name = "paymentcheckdate'.$row.'" id = "paymentcheckdate'.$row.'" type = "hidden">';
                                                echo '<input value="'.$paymentnotes.'" name = "paymentnotes'.$row.'" id = "paymentnotes'.$row.'" type = "hidden">';
                                                echo '</td>';

                                                echo '<td>';
                                                echo $ui->formField('text')
                                                        ->setClass("input_label")
                                                        ->setName("pacct".$row)
                                                        ->setId("pacct".$row)
                                                        ->setValue($paymentaccount)
                                                        ->setAttribute(array("readonly" => "readonly"))
                                                        ->draw(true);

                                                echo '<input value="'.$paymentaccountcode.'" name = "paymentaccount'.$row.'" id = "paymentaccount'.$row.'" type = "hidden">';
                                                echo '</td>';

                                                echo '<td>';
                                                echo '<input value="'.number_format($paymentamount,2).'" name = "paymentamount'.$row.'" id = "paymentamount'.$row.'" type = "hidden">';
                                                echo '<input value="'.number_format($paymentrate,2).'" name = "paymentrate'.$row.'" id = "paymentrate'.$row.'" type = "hidden">';
                                                
                                                echo $ui->formField('text')
                                                        ->setClass("input_label text-right")
                                                        ->setName("paymentconverted".$row)
                                                        ->setId("paymentconverted".$row)
                                                        ->setAttribute(array("readonly" => "readonly"))
                                                        ->setValue(number_format($paymentconverted,2))
                                                        ->draw(true);

                                                echo $ui->formField('textarea')
                                                        ->setClass("hidden")
                                                        ->setName("chequeInput".$row)
                                                        ->setId("chequeInput".$row)
                                                        ->setValue($cheque_values)
                                                        ->draw(true);
                                                echo '</td>';

                                                echo '<td>';
                                                echo $ui->formField('text')
                                                        ->setClass("input_label text-right")
                                                        ->setName("paymentdiscount".$row)
                                                        ->setId("paymentdiscount".$row)
                                                        ->setAttribute(array("readonly" => "readonly"))
                                                        ->setValue(number_format($paymentdiscount,2))
                                                        ->draw(true);
                                                echo '</td>';

                                                echo '<td class="text-center">';
                                                // echo (strtolower($checkstat) != 'cleared') ? '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row.'\');" title="Edit Payment" ><span class="glyphicon glyphicon-pencil"></span></button>
                                                //     <button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
                                                //     <a role="button" class="btn btn-default btn-xs" target="_blank" href="'.BASE_URL.'financials/receipt_voucher/print_preview/'.$paymentnumber.'" title="Print Receipt Voucher"><span class="glyphicon glyphicon-print"></span></a>' : '<a role="button" class="btn btn-default btn-xs" href="'.BASE_URL.'financials/receipt_voucher/print_preview/'.$paymentnumber.'" title="Print Receipt Voucher" ><span class="glyphicon glyphicon-print"></span></a>';
                                                echo '<button class="btn btn-default btn-xs" onClick="editPaymentRow(event,\'edit'.$row.'\');" title="Edit Payment" ><span class="glyphicon glyphicon-pencil"></span></button>
                                                    <button class="btn btn-default btn-xs" onClick="deletePaymentRow(event,\'delete'.$row.'\');" title="Delete Payment" ><span class="glyphicon glyphicon-trash"></span></button>
                                                    <a role="button" class="btn btn-default btn-xs" target="_blank" href="'.BASE_URL.'financials/receipt_voucher/print_preview/'.$paymentnumber.'" title="Print Receipt Voucher"><span class="glyphicon glyphicon-print"></span></a>';
                                                echo '</td>';

                                        echo '</tr>';

                                        $row++;

                                        $totalPayment += $paymentconverted;
                                        $totaldiscount+= $paymentdiscount;
                                    }
                                    
                                }
                                else
                                {
                                    echo '<tr><td colspan = "7" class = "text-center">No payments received for this receivable</td></tr>';
                                }
                            ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="4" style="border-top:1px solid #DDDDDD;" >
                                        <label for="subtotal" class="control-label">Total </label>
                                    </td>
                                    <td style="border-top:1px solid #DDDDDD;" class="text-right">
                                        <label class="control-label" id="totalPaymentCaption" style = "padding: 0 12px 0 12px;"><?=number_format($totalPayment,2)?></label>
                                    </td>
                                    <td style="border-top:1px solid #DDDDDD;" class="text-right">
                                        <label class="control-label" id="totalDiscountCaption" style = "padding: 0 12px 0 12px;"><?=number_format($totaldiscount,2)?></label>
                                    </td>
                                    <td style="border-top:1px solid #DDDDDD;">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!--PAYMENT RECEIVED : END-->
                
                <div id='hidden_values'>
                    <input class="form_iput" value="<?= $totalPayment?>" name="totalPayment" id="totalPayment" type="hidden">
                    <input class="form_iput" value="<?= $totaldiscount?>" name="totalDiscount" id="totalDiscount" type="hidden">
                    <input class="form_iput" value="<?= $forexamount?>" name="totalForex" id="totalForex" type="hidden">
                    <input class="form_iput" value="<?= $show_paymentdetails?>" name="show_paymentdetails" id="show_paymentdetails" type="hidden">
                    <!-- <input class="form_iput" value="<?= $show_chequedetails?>" name="show_chequedetails" id="show_chequedetails" type="hidden"> -->
                </div>

                <br/>
                <!-- Cheque Details-->
               <? if( !is_null($data["rollArrayv"])) { ?> 
                    <!-- <div class = "panel panel-default">
                        <div class="panel-heading">
                            <strong>Cheque Details</strong>
                        </div>

                        <div class="box-body table-responsive no-pad">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr class="info">
                                        <th class="col-md-2 text-center">Cheque Date</th>
                                        <th class="col-md-5 text-center">Bank Account</th>
                                        <th class="col-md-3 text-center">Cheque Number</th>
                                        <th class="col-md-2 text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_cheque_amount 	= 0;

                                        if(!is_null($data["rollArrayv"]) && !empty($sid))
                                        {
                                            for($i = 0; $i < count($data["rollArrayv"]); $i++)
                                            {
                                               
                                                $accountname	= $data["rollArrayv"][$i]["chequeaccountname"];
                                                $chequenumber	= $data["rollArrayv"][$i]["chequenumber"];
                                                $chequedate		= $data["rollArrayv"][$i]["chequedate"];
                                                $chequeamount	= $data["rollArrayv"][$i]["chequeamount"];
                                                
                                                echo '<tr>';	
                                                    echo '<td class="text-left">'.$chequedate.'</td>';
                                                    echo '<td class="text-left">'.$accountname.'</td>';
                                                    echo '<td class="text-left">'.$chequenumber.'</td>';
                                                    echo '<td class="text-right">'.number_format($chequeamount,2).'</td>';
                                                echo '</tr>';
                                                
                                                $total_cheque_amount += $chequeamount;
                                            }
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right" style="border-top:1px solid #DDDDDD;">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="text-right" style="border-top:1px solid #DDDDDD;"><strong><?=number_format($total_cheque_amount,2)?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div> -->
                <? } ?>
                <!-- Cheque Details END-->
            </div>
        </div>

         <!--PAYMENT EXCHANGE RATE MODAL-->
        <div class="modal fade" id="paymentRateModal" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        Exchange Rate
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="paymentRateForm">
                            <div class="alert alert-warning alert-dismissable hidden" id="sequenceAlert">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p>&nbsp;</p>
                            </div>
                            <div class="well well-md">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row row-dense">
                                            <?php
                                                echo $ui->formField('text')
                                                    ->setLabel('Currency Amount')
                                                    ->setSplit('col-md-offset-1 col-md-3','col-md-8')
                                                    ->setClass("text-right")
                                                    ->setName('paymentoldamount')
                                                    ->setId('paymentoldamount')
                                                    ->setValidation('required')
                                                    ->setAttribute(
                                                        array(
                                                            "onClick" => "SelectAll(this.id);",
                                                            "onBlur" => "computeExchangeRate('paymentRateForm',this.id); formatNumber(this.id);",
                                                        )
                                                    )
                                                    ->setValue(number_format(0,2))
                                                    ->draw(true);
                                            ?>
                                        </div>
                                        <br/>
                                        <div class="row row-dense">
                                            <?php
                                                echo $ui->formField('text')
                                                    ->setLabel('Currency Rate')
                                                    ->setSplit('col-md-offset-1 col-md-3','col-md-8')
                                                    ->setClass("text-right")
                                                    ->setName('paymentrate')
                                                    ->setId('paymentrate')
                                                    ->setValidation('required')
                                                    ->setAttribute(
                                                        array(
                                                            "onClick" => "SelectAll(this.id);",
                                                            "onBlur" => "computeExchangeRate('paymentRateForm',this.id); formatNumber(this.id);",
                                                        )
                                                    )
                                                    ->setValue(number_format($v_exchangerate,2))
                                                    ->draw(true);
                                            ?>
                                        </div>
                                        <div class="row row-dense">
                                            <?php
                                                echo $ui->formField('text')
                                                    ->setLabel('Amount')
                                                    ->setSplit('col-md-offset-1 col-md-3','col-md-8')
                                                    ->setClass("text-right")
                                                    ->setName("paymentnewamount")
                                                    ->setId("paymentnewamount")
                                                    ->setValidation('required')
                                                    ->setAttribute(
                                                        array(
                                                            "onClick" => "SelectAll(this.id);",
                                                            "onBlur" => "computeExchangeRate('paymentRateForm',this.id); formatNumber(this.id);",
                                                        )
                                                    )
                                                    ->setValue(number_format(0,2))
                                                    ->draw(true);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-dense">
                                <div class="col-md-12 text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info" id="btnProceed" >Apply</button>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <div class="btn-group">
                                        <a href="javascript:void(0);" class="btn btn-small btn-default" role="button" data-dismiss="modal" style="outline:none;">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</section>

<script>
$(document).ready(function() 
{
    var show_pd   = $('#show_paymentdetails').val();

    if( show_pd == 1 ){
        $('#pd_div').removeClass('hidden');
    }

    var hash = window.location.hash.substring(1);

    if(hash == 'payment'){
        var totalTax = 0;
        if(document.getElementById('totalCreditable') != null){
            totalTax		= document.getElementById('totalCreditable').value;
            totalTax 			= totalTax.replace(/,/g,'');
        }
        
        var noCashAccounts 	= document.getElementById('noCashAccounts').value;
        var totalInvoice 	= document.getElementById('totalInvoice').value;
        totalInvoice 		= totalInvoice.replace(/,/g,'');
        var totalPayment 	= document.getElementById('totalPayment').value;
        totalPayment 		= totalPayment.replace(/,/g,'');
        var totalDiscount 	= document.getElementById('totalDiscount').value;
        totalDiscount 		= totalDiscount.replace(/,/g,'');
        var totalForex 		= document.getElementById('totalForex').value;
        totalForex 			= totalForex.replace(/,/g,'');
        var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
        //var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalTax);
    
        var result 			= addCommas(balance.toFixed(2));
    
        if ($("#receiptForm.hidden")[0]){
            $("#receiptForm").removeClass('hidden');
            $("#receiptForm #convertedamount\\[1\\]").val(result);
            $("#receiptForm #amount\\[1\\]").val(result);
        }
        
    }else{
        if ($("#receiptForm.hidden")[0]){
            $("#receiptForm").addClass('hidden');
        }
        $("#invoice_label").removeClass('hidden');
    }

    /**TOGGLE ISSUE PAYMENT**/
	$("#btnReceive").click(function()
	{
		if ($("#receiptForm.hidden")[0])
		{
			var totalInvoice 	= document.getElementById('totalInvoice').value;
			totalInvoice 		= totalInvoice.replace(/,/g,'');
			var totalPayment 	= document.getElementById('totalPayment').value;
			totalPayment 		= totalPayment.replace(/,/g,'');
			var totalDiscount 	= document.getElementById('totalDiscount').value;
			totalDiscount 		= totalDiscount.replace(/,/g,'');
			var totalForex 		= document.getElementById('totalForex').value;
			totalForex 			= totalForex.replace(/,/g,'');
			var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
            var appnotes        = ($('#particulars').length) ? $('#particulars').html() : '';
			var result 			= addCommas(balance.toFixed(2));
		
			$("#receiptForm").removeClass('hidden');
            $("#receiptForm #paymentamount\\[1\\]").val(result);
			$("#receiptForm #convertedamount\\[1\\]").val(result);
            $("#receiptForm #paymentnotes\\[1\\]").val(appnotes);
            
			$('html, body').animate({ scrollTop: 0 }, 'slow');
		}
		else
		{
			$("#receiptForm").addClass('hidden');
		}
	});

    /**
    * Apply Exchange Rate and converted amount
    */
    $('#paymentRateForm #btnProceed').click(function(e){
        var valid 			= 0;
        var oldamount 		= $('#paymentRateForm #paymentoldamount').val();
        oldamount			= oldamount.replace(/,/g,'');
        var exchangerate 	= $('#paymentRateForm #paymentrate').val();
        exchangerate		= exchangerate.replace(/,/g,'');
        var newamount 		= $('#paymentRateForm #paymentnewamount').val();

        var amount 			= $('#receiptForm #amount').val();

        $('#paymentRateForm #paymentoldamount').trigger('blur');
        $('#paymentRateForm #paymentrate').trigger('blur');
        $('#paymentRateForm #paymentnewamount').trigger('blur');

        if ($(this).find('.form-group.has-error').length == 0)
        {
            $('#receiptForm #btnRate').html(exchangerate+'&nbsp;&nbsp;');

            $('#receiptForm #paymentamount\\[1\\]').val(oldamount);
            $('#receiptForm #exchangerate\\[1\\]').val(exchangerate);
            $('#receiptForm #convertedamount\\[1\\]').val(newamount);

            $('#paymentRateModal').modal('hide');
            
        }else{
            bootbox.dialog({
                message: "Please complete all required fields.",
                title: "Warning",
                buttons: {
                    success: {
                        label: "Ok",
                        className: "btn-info",
                        callback: function() {

                        }
                    }
                }
            });
        }
        
    });

    /**ADD NEW BANK ROW**/
	$('body').on('click', '.add-cheque', function() 
	{
		$('#chequeTable tbody tr.clone select').select2('destroy');

		var clone1 = $("#chequeTable tbody tr.clone:first").clone(true);

		var ParentRow = $("#chequeTable tbody tr.clone").last();
		
		clone1.clone(true).insertAfter(ParentRow);
		
		setChequeZero();
		
		$('#chequeTable tbody tr.clone select').select2({width: "100%"});
		$('#chequeTable tbody tr.clone:last .input-group.date ').datepicker({format: 'M dd, yyyy', autoclose: true});
	});

    /**DELETE RECEIVED PAYMENT : START**/
	$('#deletePaymentModal #btnYes').click(function() 
	{
		var invoice		= $("#invoiceno\\[1\\]").val();
		var table 		= document.getElementById('paymentsTable');
		
		var id 	= $('#deletePaymentModal').data('id');
		var row = $('#deletePaymentModal').data('row');
        
		$.post("<?= BASE_URL?>financials/accounts_receivable/ajax/delete_payments", "voucher=" + id)
		.done(function( data ) 
		{	
			if(data.msg == "success")
			{
				table.deleteRow(row);
				$('#deletePaymentModal').modal('hide');
				location.reload();
			}
			else
			{
				console.log(data.msg);
			}
		});
	});

    $('.chequeamount').on('blur click', function(e) 
	{
		if(e.type == "blur")
		{
			formatNumber(e.target.id); 
			// computeExchangeRate(e.target.form.id,e.target.id,'1');
			
			computeExchangeRate('paymentRateForm',e.target.id,'1');
		}
		if(e.type == "click")
		{
			SelectAll(e.target.id);
		}
	});
});

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function checkBalance()
{
	var payablerate			= $('#receiptForm #payablerate').val();
	var exchangerate		= $('#receiptForm #exchangerate\\[1\\]').val();
	payablerate				= payablerate.replace(/,/g,'');
	exchangerate			= exchangerate.replace(/,/g,'');

	var paymentrow			= $('#receiptForm #paymentrow').val();
	row 					= paymentrow.replace(/[a-z]/g, '');
	var paymentrowamount	= 0;
	var paymentapplied		= 0;
	var discountapplied		= 0;

	if(paymentrow != '')
	{
		var paymentapplied	= document.getElementById('paymentamount'+row).value;
		paymentapplied		= paymentapplied.replace(/,/g,'');
		var discountapplied	= document.getElementById('paymentdiscount'+row).value;
		discountapplied		= discountapplied.replace(/,/g,'');
		
		paymentrowamount	= parseFloat(paymentapplied) + parseFloat(discountapplied);
	}
	
	var paymentmode		= $('#receiptForm #paymentmode\\[1\\]').val();

	var payment			= $('#receiptForm #convertedamount\\[1\\]').val();
	payment				= payment.replace(/,/g,'');
	
	var prevpayment1 	= document.getElementById('prevpayment').value;
	prevpayment 		= prevpayment1.replace(/,/g,'');
	
	var totalInvoice 	= document.getElementById('totalInvoice').value;
	totalInvoice 		= totalInvoice.replace(/,/g,'');
	var totalPayment 	= document.getElementById('totalPayment').value;
	totalPayment 		= totalPayment.replace(/,/g,'');
	var totalDiscount 	= document.getElementById('totalDiscount').value;
	totalDiscount 		= totalDiscount.replace(/,/g,'');
	var totalForex 		= document.getElementById('totalForex').value;
	totalForex 			= totalForex.replace(/,/g,'');

	var default_payment	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	default_payment		= addCommas(default_payment.toFixed(2));;
	
	var discount 		= $('#receiptForm #paymentdiscount\\[1\\]').val();
	discount 			= discount.replace(/,/g,'');
	
	if(paymentrowamount)
	{
		var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(paymentrowamount) + parseFloat(totalForex);
	}
	else
	{
		var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
	}
	
	/**Include discount in checking**/
	if(discount)
	{
		balance		 		= parseFloat(balance) - parseFloat(discount);
	}

	/**
	* Check if exchangerate is different from payable
	*/
	var forex_gain_loss	= false;
	if(parseFloat(totalForex) > 0 || (parseFloat(payablerate) != parseFloat(exchangerate)))
	{
		forex_gain_loss	= true;
	}

	var result 			= parseFloat(balance).toFixed(2);
	if((parseFloat(payment) > parseFloat(result)) && !forex_gain_loss)
	{
		bootbox.dialog({
			message: "Payment amount is greater the remaining balance of this Receivable.",
			title: "Warning",
			buttons: {
				success: {
					label: "OK",
					className: "btn-primary btn-flat",
					callback: function() {
						if(paymentmode == 'cheque')
						{
							document.getElementById('paymentdiscount[1]').value 	= '0.00';
						}
						else
						{
							if(prevpayment != '')
							{
								document.getElementById('convertedamount[1]').value 	= prevpayment;
								document.getElementById('paymentamountfield').value 	= prevpayment;
								document.getElementById('paymentdiscount[1]').value 	= '0.00';
							}
							else
							{
								document.getElementById('convertedamount[1]').value 	= default_payment;
								document.getElementById('paymentamountfield').value 	= default_payment;
								document.getElementById('paymentdiscount[1]').value 	= '0.00';
							}
						}
					}	
				}
			}
		});
		return 1;
	}else{
		return 0;
	}
}

/**FORMAT NUMBERS TO DECIMAL**/
function formatNumber(id)
{
	var amount = document.getElementById(id).value;
	amount     = amount.replace(/\,/g,'');
	var result = amount * 1;
	document.getElementById(id).value = addCommas(result.toFixed(2));
}

function computeDiscount()
{
    var totalInvoice 	= document.getElementById('totalInvoice').value;
    totalInvoice 		= totalInvoice.replace(/,/g,'');
    var totalPayment 	= document.getElementById('totalPayment').value;
    totalPayment 		= totalPayment.replace(/,/g,'');
    var totalDiscount 	= document.getElementById('totalDiscount').value;
    totalDiscount 		= totalDiscount.replace(/,/g,'');
    var totalForex 		= document.getElementById('totalForex').value;
    totalForex 			= totalForex.replace(/,/g,'');
    
    var balance		 	= parseFloat(totalInvoice) - parseFloat(totalPayment) - parseFloat(totalDiscount) + parseFloat(totalForex);
    
    //var paymentamount	= $('#receiptForm #paymentamount\\[1\\]').val();
    var paymentamount	= $('#receiptForm #convertedamount\\[1\\]').val();
    paymentamount    	= paymentamount.replace(/\,/g,'');
    var paymentdiscount	= $('#receiptForm #paymentdiscount\\[1\\]').val();
    paymentdiscount    	= paymentdiscount.replace(/\,/g,'');
    
    if(parseFloat(paymentdiscount) > 0)
    {
        if(parseFloat(paymentamount) > 0 && (parseFloat(paymentamount) == parseFloat(balance)))
        {
            var new_amount	= parseFloat(paymentamount) - parseFloat(paymentdiscount);

            if(new_amount >= 0){
                new_amount		= addCommas(new_amount.toFixed(2));
                //$('#receiptForm #paymentamount\\[1\\]').val(new_amount);
                $('#receiptForm #convertedamount\\[1\\]').val(new_amount);
                $('#receiptForm	#paymentamountfield').val(new_amount);		
            }else{
                balance 		= addCommas(balance.toFixed(2));
                //alert('Payment amount and discount should be less than or equal to '+balance);
                bootbox.dialog({
                    message: "Payment amount and discount should be less than or equal to "+balance,
                    title: "Warning",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-info",
                            callback: function() {
                                $('#receiptForm #paymentdiscount\\[1\\]').val('0.00');
                            }
                        }
                    }
                });
                
            }
        }
        checkBalance();
        
    }else{
        var new_amount	= parseFloat(paymentamount) - parseFloat(paymentdiscount);

        balance 		= addCommas(balance.toFixed(2));
        //$('#receiptForm #paymentamount\\[1\\]').val(balance);
    }
}

/**
    * This function toggles the field for getting the foreign amount
    * @param  {float} rate - exchange rate to be used to compute the converted amount
    */
function toggleExchangeRate(tp)
{
    tp = typeof tp !== 'undefined' ? tp : '';

    if(tp == ''){

        var amount 				= $('#payableForm #amount').val();
        var exchangerate 		= $('#payableForm #exchangerate').val();
        var convertedamount 	= $('#payableForm #convertedamount').val();

        var oldamount 	= amount * 1;
        var rate 		= exchangerate * 1;
        var newamount 	= convertedamount * 1;

        $('#rateForm #oldamount').val(addCommas(oldamount.toFixed(2)));
        $('#rateForm #rate').val(addCommas(rate.toFixed(2)));
        $('#rateForm #newamount').val(addCommas(newamount.toFixed(2)));

        $('#rateModal').modal('toggle');

    }else{

        var amount 				= $('#receiptForm #paymentamount\\[1\\]').val();
        var exchangerate 		= $('#receiptForm #exchangerate\\[1\\]').val();
        var convertedamount 	= $('#receiptForm #convertedamount\\[1\\]').val();

        amount 					= amount.replace(/,/g,'');
        exchangerate 			= exchangerate.replace(/,/g,'');
        convertedamount 		= convertedamount.replace(/,/g,'');

        var oldamount 	= amount * 1;
        var rate 		= exchangerate * 1;
        var newamount 	= convertedamount * 1;

        $('#paymentRateForm #paymentoldamount').val(addCommas(oldamount.toFixed(2)));
        $('#paymentRateForm #paymentrate').val(addCommas(rate.toFixed(2)));
        $('#paymentRateForm #paymentnewamount').val(addCommas(newamount.toFixed(2)));

        computeExchangeRate('paymentRateForm','paymentnewamount');

        $('#receiptForm #paymentamount\\[1\\]').val($('#paymentRateForm #paymentoldamount').val());

        $('#paymentRateModal').modal('toggle');
    }
    
}

/**
    * This computes the converted amount based on the exchange rate and foreign amount
    */
function computeExchangeRate(activeForm,active,row)
{
    row = typeof row !== 'undefined' ? row : '';
    
    if(row == ''){
        if(activeForm == 'paymentRateForm'){
            var amount 	= $('#'+activeForm+' #paymentoldamount').val();
            amount 		= amount.replace(/,/g,'');
            var rate 	= $('#'+activeForm+' #paymentrate').val();
            rate 		= rate.replace(/,/g,'');
            var base 	= $('#'+activeForm+' #paymentnewamount').val();
            base 		= base.replace(/,/g,'');

            var newamount = 0;

            if(parseFloat($('#'+activeForm+' #paymentrate').val()) > 0){
                
                if(active == 'paymentoldamount' && parseFloat(base) > 0 && (parseFloat(rate) == 0)){
                    
                    newamount = parseFloat(base) / parseFloat(amount);
                    $('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

                }else if(active == 'paymentoldamount' && parseFloat(rate) > 0){
                    
                    newamount = parseFloat(amount) * parseFloat(rate);
                    $('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'paymentrate' && parseFloat(amount) > 0){

                    newamount = parseFloat(amount) * parseFloat(rate);
                    $('#'+activeForm+' #paymentnewamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'paymentrate' && parseFloat(rate) > 0){

                    newamount = parseFloat(base) / parseFloat(rate);
                    $('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'paymentnewamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1)){

                    newamount = parseFloat(base) / parseFloat(amount);
                    $('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));

                }else if(active == 'paymentnewamount' && parseFloat(rate) > 0){

                    newamount = parseFloat(base) / parseFloat(rate);
                    $('#'+activeForm+' #paymentoldamount').val(addCommas(newamount.toFixed(2)));

                }

            }else{
                $('#'+activeForm+' #convertedamount').val('0.00');
                $('#'+activeForm+' #exchangerate').val('1.00');
                $('#'+activeForm+' #amount').val('0.00');
            }

        }else{

            var amount 	= $('#'+activeForm+' #oldamount').val();
            amount 		= amount.replace(/,/g,'');
            var rate 	= $('#'+activeForm+' #rate').val();
            rate 		= rate.replace(/,/g,'');
            var base 	= $('#'+activeForm+' #newamount').val();
            base 		= base.replace(/,/g,'');

            var newamount = 0;

            if(parseFloat($('#'+activeForm+' #rate').val()) > 1){

                if(active == 'oldamount' && parseFloat(base) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1)){

                    newamount = parseFloat(base) / parseFloat(amount);
                    $('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));

                }else if(active == 'oldamount' && parseFloat(rate) > 0){

                    newamount = parseFloat(amount) * parseFloat(rate);
                    $('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'rate' && parseFloat(amount) > 0){

                    newamount = parseFloat(amount) * parseFloat(rate);
                    $('#'+activeForm+' #newamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'rate' && parseFloat(rate) > 0){

                    newamount = parseFloat(base) / parseFloat(rate);
                    $('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));
                    
                }else if(active == 'newamount' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1)){

                    newamount = parseFloat(base) / parseFloat(amount);
                    $('#'+activeForm+' #rate').val(addCommas(newamount.toFixed(2)));

                }else if(active == 'newamount' && parseFloat(rate) > 0){

                    newamount = parseFloat(base) / parseFloat(rate);
                    $('#'+activeForm+' #oldamount').val(addCommas(newamount.toFixed(2)));

                }

            }else{
                $('#'+activeForm+' #convertedamount').val('0.00');
                $('#'+activeForm+' #exchangerate').val('1.00');
                $('#'+activeForm+' #amount').val('0.00');
            }
        }
        

    }else{
        // var amount 	= $('#receiptForm #chequeamount\\['+row+'\\]').val();
        // amount 		= amount.replace(/,/g,'');
        // var rate 	= $('#'+activeForm+' #paymentrate').val();
        // rate 		= rate.replace(/,/g,'');
        // //var base 	= $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val();
        // var base 	= $('#receiptForm #chequeamount\\['+row+'\\]').val();
        // base 		= base.replace(/,/g,'');
        
        // var newamount = 0;

        // if(active == 'chequeamount['+row+']' && parseFloat(base) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1)){

        //     newamount = parseFloat(base) / parseFloat(amount);
        //     $('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

        // }else if(active == 'chequeamount['+row+']' && parseFloat(rate) > 0){
            
        //     newamount = parseFloat(amount) * parseFloat(rate);
        //     //$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));
            
        // }else if(active == 'paymentrate' && parseFloat(amount) > 0){

        //     newamount = parseFloat(amount) * parseFloat(rate);
        //    // $('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));
            
        // }else if(active == 'paymentrate' && parseFloat(rate) > 0){

        //     newamount = parseFloat(base) / parseFloat(rate);
        //     $('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));
            
        // }else if(active == 'chequeconvertedamount['+row+']' && parseFloat(amount) > 0 && (parseFloat(rate) == 0 || parseFloat(rate) == 1)){

        //     newamount = parseFloat(base) / parseFloat(amount);
        //     $('#'+activeForm+' #paymentrate').val(addCommas(newamount.toFixed(2)));

        // }else if(active == 'chequeconvertedamount['+row+']' && parseFloat(rate) > 0){

        //     newamount = parseFloat(base) / parseFloat(rate);
        //     $('#receiptForm #chequeamount\\['+row+'\\]').val(addCommas(newamount.toFixed(2)));	
        // }

        addAmounts();
    }
}

/**TOGGLE CHECK DATE FIELD**/
function toggleCheckInfo(val)
{	
    if(val == 'cheque'){
        $("#receiptForm #check_label").text('');
        
        $("#receiptForm #check_field").addClass('hidden');
        
        $("#receiptForm #cash_payment_details").addClass('hidden');
        $("#receiptForm #check_details").removeClass('hidden');
    }else{
        $("#receiptForm #check_label").text('Reference Number');
        
        $("#receiptForm #check_field").removeClass('hidden');
        $("#receiptForm #payment_field").addClass('hidden');
        
        $("#receiptForm #cash_payment_details").removeClass('hidden');
        $("#receiptForm #check_details").addClass('hidden');
    }
}

/**HIGHTLIGHT CONTENT OF INPUT**/
function SelectAll(id)
{
    document.getElementById(id).focus();
    document.getElementById(id).select();
}

/**UPDATE PAYMENT ROW**/
function savePaymentRow(e,id)
{
    e.preventDefault();
    id 				= id.replace(/[a-z]/g, '');
    var type		= document.getElementById('type').value;
    
    var table 			= document.getElementById('paymentsTable');
    var paymentmode 	= document.getElementById('paymentmode[1]').value;
    var paymentamount 	= document.getElementById('convertedamount[1]').value;
    paymentamount		= paymentamount.replace(/,/g,'');

    //var wtax 		= 'WTAX';
    var row 		= table.rows[id];
    var valid		= 0;
    

    /**validate payment fields**/
    //valid		+= validateField('receiptForm','paymentdate\\['+id+'\\]');
    //valid		+= validateField('receiptForm','paymentmode\\['+id+'\\]');
    

    if(paymentmode == 'cash' || paymentmode == 'transfer'){
        if(parseFloat(Number(paymentamount)) > 0)
        {
            $('#receiptForm #paymentaccount\\['+id+'\\]').trigger('blur');
        }
    }else{
        valid	+= validateCheques();
        valid	+= totalPaymentGreaterThanChequeAmount();
    }
    
    //valid		+= validateField('receiptForm','paymentamount\\['+id+'\\]');
    //valid		+= validateField('receiptForm','convertedamount\\['+id+'\\]');
    $('#receiptForm #convertedamount\\['+id+'\\]').trigger('blur');

    valid		+= checkBalance();
    
    //$('#paymentRateForm #paymentnewamount').trigger('blur');

    valid += $('#receiptForm').find('.form-group.has-error').length;

    if(valid == 0){
    
        var invoiceno				= $('#receiptForm #invoiceno\\['+id+'\\]').val();
        var paymentamount			= $('#receiptForm #paymentamount\\['+id+'\\]').val();
        var exchangerate			= $('#receiptForm #exchangerate\\['+id+'\\]').val();
        var convertedamount			= $('#receiptForm #convertedamount\\['+id+'\\]').val();
        var paymentaccount			= $('#receiptForm #paymentaccount\\['+id+'\\]').val();
        var paymentdate				= $('#receiptForm #paymentdate\\['+id+'\\]').val();
        var paymentmode				= $('#receiptForm #paymentmode\\['+id+'\\]').val();
        var paymentreference		= $('#receiptForm #paymentreference\\['+id+'\\]').val();
        var paymentnotes			= $('#receiptForm #paymentnotes\\['+id+'\\]').val();
        var paymentvendor			= $('#receiptForm #customer\\['+id+'\\]').val();
        var paymentnumber			= $('#receiptForm #paymentnumber\\['+id+'\\]').val();
        var paymentdiscount			= $('#receiptForm #paymentdiscount\\['+id+'\\]').val();
        
        var selected 				= [];
        var selectedamount 			= [];
        var selectedrate 			= [];
        var selectedconverted 		= [];

        var selecteddate 			= [];
        var selectedaccount			= [];
        var selectedmode			= [];
        var selectedreference		= [];
        var selectednotes			= [];
        var selectedvendor		= [];
        var selectednumber			= [];
        var selecteddiscount		= [];
        
        var selectedcheque			= [];
        var selectedchequenumber	= [];
        var selectedchequedate		= [];
        var selectedchequeamount 	= [];
        var selectedchequeconvamount= [];
        
        selected.push(invoiceno);
        //selectedamount.push(paymentamount);
        
        selectedrate.push(exchangerate);
        selectedconverted.push(convertedamount);

        selecteddate.push(paymentdate);
        if(paymentmode == 'cash' || paymentmode == 'transfer'){
            selectedaccount.push(paymentaccount);
            selectedamount.push(convertedamount);
        }else{
            selectedamount.push(paymentamount);
        }
        selectedmode.push(paymentmode);
        selectedreference.push(paymentreference);
        //selectedcheck.push(paymentcheckdate);
        selectednotes.push(paymentnotes);
        //selectednotes.push(paymentvendor);
        selectednumber.push(paymentnumber);
        selecteddiscount.push(paymentdiscount);
        
        /**Multiple Cheque payments**/
        var chequeTable		= document.getElementById('chequeTable');
        var chequeCount		= chequeTable.rows.length - 2;
        
        for(var j=1;j<=chequeCount;j++){
            var chequeRow   = chequeTable.rows[j];
            
            if(document.getElementById('chequeaccount['+j+']').value != '')
            {
                var chequeaccount 			= document.getElementById('chequeaccount['+j+']').value;
                var chequenumber 			= document.getElementById('chequenumber['+j+']').value;
                var chequedate 				= document.getElementById('chequedate['+j+']').value;
                var chequeamount 			= document.getElementById('chequeamount['+j+']').value;
                //var chequeconvertedamount 	= document.getElementById('chequeconvertedamount['+j+']').value;
                var chequeconvertedamount 	= chequeamount;

                selectedcheque.push(chequeaccount);
                selectedchequenumber.push(chequenumber);
                selectedchequedate.push(chequedate);
                selectedchequeamount.push(chequeamount);
                selectedchequeconvamount.push(chequeconvertedamount);
            }
        }
                
        //$.post("./ajax/apply_payments.php",
        $.post("<?=BASE_URL?>financials/accounts_receivable/ajax/apply_payments",
        {
            "type": type, 
            "invoiceno[]": selected, 
            "paymentdate[]": selecteddate, 
            "paymentnumber[]": selectednumber, 
            "paymentaccount[]": selectedaccount,
            "paymentmode[]": selectedmode,
            "paymentreference[]": selectedreference,
            "paymentamount[]": selectedamount,
            "paymentdiscount[]": selecteddiscount,
            "paymentnotes[]": selectednotes,
            "paymentrate[]": selectedrate,
            "paymentconverted[]": selectedconverted,
            "customer[]": paymentvendor,
            "chequeaccount[]": selectedcheque,
            "chequenumber[]": selectedchequenumber,
            "chequedate[]": selectedchequedate,
            "chequeamount[]": selectedchequeamount,
            "chequeconvertedamount[]": selectedchequeconvamount
        }).done(function( data ) {
            var hash 		= window.location.hash.substring(1);
            if(hash != ''){
                var url 				= document.URL;
                var newurl 				= url.replace('#payment','');
                document.location.href	= newurl;
            }else{
                location.reload();
            }
        });
    }
}

/**VALIDATE FIELD**/
function validateField(form,id)
{
    var field	= $("#"+form+" #"+id).val();

    if(id.indexOf('_chosen') != -1){
        var id2	= id.replace("_chosen","");
        field	= $("#"+form+" #"+id2).val();

    }

    if(field == '' || parseFloat(field) == 0){
        $("#"+form+" #"+id)
            .closest('.field_col')
            .addClass('has-error');

        $("#"+form+" #"+id)
            .next(".help-block")
            .removeClass('hidden');
            
        if($("#"+form+" #"+id).parent().next(".help-block")[0]){
            $("#"+form+" #"+id)
            .parent()
            .next(".help-block")
            .removeClass('hidden');
        }
        return 1;
    }else{
        $("#"+form+" #"+id)
            .closest('.field_col')
            .removeClass('has-error');

        $("#"+form+" #"+id)
            .next(".help-block")
            .addClass('hidden');
            
        if($("#"+form+" #"+id).parent().next(".help-block")[0]){
            $("#"+form+" #"+id)
            .parent()
            .next(".help-block")
            .addClass('hidden');
        }
        return 0;
    }
}

function validateCheques()
{
	var table 	= document.getElementById('chequeTable');
	count		= table.rows.length - 2;
	var valid	= 0;
	
	var selected	= 0;
	if(count > 0 && document.getElementById('chequeaccount[1]') != null)
	{
		for(var i=1;i<=count;i++)
		{
			var chequeaccount = $('#chequeaccount\\['+i+'\\]').val();

			if(chequeaccount != '')
			{
				selected++;
			}
		}
	}

	if(selected == 0 && (count > 0))
	{
		$("#receiptForm #chequeCountError").removeClass('hidden');
		valid++;
	}
	else
	{
		$("#receiptForm #chequeCountError").addClass('hidden');
	}
	
	if(valid == 0 && count > 0)
	{
		for(var i=1;i<=count;i++)
		{
			var chequeaccount 			= $('#chequeaccount\\['+i+'\\]').val(); //$('#chequeaccount\\['+i+'\\]').chosen().val();
			var chequenumber 			= $('#chequenumber\\['+i+'\\]').val();
			var chequedate 				= $('#chequedate\\['+i+'\\]').val();
			var chequeamount 			= $('#chequeamount\\['+i+'\\]').val();
			var chequeconvertedamount 	= $('#chequeamount\\['+i+'\\]').val();
			
			if(chequeaccount == '' || chequenumber == '' || chequedate == '' || parseFloat(chequeamount) <= 0 || chequeamount == '' || parseFloat(chequeconvertedamount) <= 0)
			{
				$('#chequeaccount\\['+i+'\\]').closest('tr').addClass('danger');
				valid++;
			}
			else
			{
				$('#chequeaccount\\['+i+'\\]').closest('tr').removeClass('danger');
			}
		}
	}
		
	if(valid > 0)
	{
		$("#receiptForm #chequeAmountError").removeClass('hidden');
	}
	else
	{
		$("#receiptForm #chequeAmountError").addClass('hidden');
	}
	
	if(valid > 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function setChequeZero()
{
	resetChequeIds();
	
	var table 		= document.getElementById('chequeTable');
	var newid 		= table.rows.length - 2;
	var account		= document.getElementById('chequeaccount['+newid+']');
	
	if(document.getElementById('chequeaccount['+newid+']')!=null)
	{
		document.getElementById('chequeaccount['+newid+']').value 			= '';
		document.getElementById('chequenumber['+newid+']').value 			= '';
		document.getElementById('chequeamount['+newid+']').value 			= '0.00';
		//document.getElementById('chequeconvertedamount['+newid+']').value 	= '0.00';
	}
}

function resetChequeIds()
{
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	x = 1;
	for(var i = 1;i<=count;i++)
	{
		var row = table.rows[i];
		
		row.cells[0].getElementsByTagName("select")[0].id 	= 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].id 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].id 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].id 	= 'chequeamount['+x+']';
		//row.cells[4].getElementsByTagName("input")[0].id 	= 'chequeconvertedamount['+x+']';

		row.cells[0].getElementsByTagName("select")[0].name = 'chequeaccount['+x+']';
		row.cells[1].getElementsByTagName("input")[0].name 	= 'chequenumber['+x+']';
		row.cells[2].getElementsByTagName("input")[0].name 	= 'chequedate['+x+']';
		row.cells[3].getElementsByTagName("input")[0].name 	= 'chequeamount['+x+']';
		//row.cells[4].getElementsByTagName("input")[0].name 	= 'chequeconvertedamount['+x+']';
		
		row.cells[2].getElementsByTagName("input")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		row.cells[2].getElementsByTagName("div")[0].setAttribute('onClick','clearInput(\'chequedate['+x+']\')');
		
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeamount['+x+']\'); computeExchangeRate(\'paymentRateForm\',\'chequeamount['+x+']\',\''+x+'\');');
		row.cells[3].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeamount['+x+']\')');

		row.cells[3].getElementsByTagName("input")[0].classList.remove("chequeamount"); 
		
		//row.cells[4].getElementsByTagName("input")[0].setAttribute('onBlur','formatNumber(\'chequeconvertedamount['+x+']\'); computeExchangeRate(\'paymentRateForm\',\'chequeamount['+x+']\',\''+x+'\');');
		//row.cells[4].getElementsByTagName("input")[0].setAttribute('onClick','SelectAll(\'chequeconvertedamount['+x+']\')');

		// row.cells[5].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequePrint('+x+')');
		row.cells[4].getElementsByTagName("button")[0].setAttribute('onClick','confirmChequeDelete('+x+')');
		x++;
	}
}

function validateChequeNumber(id, value, n)
{
	id = id.replace(/[a-z\[\]]/g, '');
	
	$.post("<?=BASE_URL?>financials/payment/ajax/check", "chequevalue=" + value)
	.done(function(data)
	{
		if(data.success)
		{
			$(n).closest('.form-group').addClass('has-error');
			$("#chequeTable #chequenumber\\["+ id +"\\]").val("");

			$("#checkNumberError").removeClass("hidden");
		}
		else
		{
			$(n).closest('.form-group').removeClass('has-error');

			$("#checkNumberError").addClass("hidden");
		}
			
	});
}

/**COMPUTE TOTAL CHEQUE AMOUNT**/
function addAmounts() 
{
	var subconverted= 0;
	var subtotal 	= 0;
	
	var subData 			= 0;
	var subDataConverted	= 0;
	
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;
	
	for(i = 1; i <= count; i++) 
	{  
		var inputamt		= document.getElementById('chequeamount['+i+']');
		//var convertedamt	= document.getElementById('chequeconvertedamount['+i+']');
		
		if(document.getElementById('chequeamount['+i+']')!=null)
		{          
			if(inputamt.value && inputamt.value != '0' && inputamt.value != '0.00')
			{                            
				subData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				subData = 0;
			}

			// if(convertedamt.value && convertedamt.value != '0' && convertedamt.value != '0.00')
			// {                            
			// 	subDataConverted = convertedamt.value.replace(/,/g,'');
			// }
			// else
			// {             
			// 	subDataConverted = 0;
			// }
            subDataConverted = 0;

			subtotal 	= parseFloat(subtotal) + parseFloat(subData);
			subconverted= parseFloat(subconverted) + parseFloat(subDataConverted);
		}	
	}

	subtotal	 = Math.round(1000*subtotal)/1000;
	subconverted = Math.round(1000*subconverted)/1000;

	document.getElementById('total').value 					= addCommas(subtotal.toFixed(2));
	document.getElementById('total_converted').value 		= addCommas(subconverted.toFixed(2));

	document.getElementById('paymentamount[1]').value 			= addCommas(subtotal.toFixed(2));
	document.getElementById('convertedamount[1]').value 		= addCommas(subconverted.toFixed(2));
}

/**COMPARE TOTAL CHEQUE AMOUNT WITH PAYMENT**/
function totalPaymentGreaterThanChequeAmount()
{
	var total_payment	= document.getElementById('paymentamount[1]').value;
	var total_cheque	= $('#receiptForm #total').val();
	
	$('#receiptForm #disp_tot_payment').html(total_payment);
	$('#receiptForm #disp_tot_cheque').html(total_cheque);
	
	total_payment    	= total_payment.replace(/\,/g,'');
	total_cheque    	= total_cheque.replace(/\,/g,'');

	if(parseFloat(total_payment) == parseFloat(total_cheque))
	{
		$("#receiptForm #paymentAmountError").addClass('hidden');
		return 0;
	}
	else
	{
		$("#receiptForm #paymentAmountError").removeClass('hidden');
		return 1;
	}
}

/**CLEAR PAYMENT ROW**/
function clearPaymentRow(e)
{	
	e.preventDefault();
	
	clearInput('paymentmode[1]');
	clearInput('paymentreference[1]');
	clearInput('convertedamount[1]');
	clearInput('exchangerate[1]');
	clearInput('paymentamount[1]');
	clearInput('prevpayment');
	clearInput('paymentnumber[1]');
	clearInput('paymentaccount[1]');
	clearInput('paymentnotes[1]');
	clearInput('paymentdiscount[1]');
	toggleCheckInfo('');
}

/**EDIT RECIEVED PAYMENTS**/
function editPaymentRow(e,id)
{
	e.preventDefault();
	
	$("#receiptForm").removeClass('hidden');
	row 			= id.replace(/[a-z]/g, '');
	
	var paymentmode			= document.getElementById('paymentmode'+row).value;
	var paymentdate			= document.getElementById('paymentdate'+row).value;
	var paymentreference	= document.getElementById('paymentreference'+row).value;
	var paymentcheckdate	= document.getElementById('paymentcheckdate'+row).value;
	var paymentamount		= document.getElementById('paymentamount'+row).value;
	var paymentconverted	= document.getElementById('paymentconverted'+row).value;
	var paymentrate			= document.getElementById('paymentrate'+row).value;
	var paymentnumber		= document.getElementById('paymentnumber'+row).value;
	var paymentaccount		= document.getElementById('paymentaccount'+row).value;
	var paymentnotes		= document.getElementById('paymentnotes'+row).value;
	var paymentdiscount		= document.getElementById('paymentdiscount'+row).value;

	document.getElementById('paymentdate[1]').value			= paymentdate;
	$("#paymentmode\\[1\\]").val(paymentmode).trigger("change");
	document.getElementById('paymentreference[1]').value	= paymentreference;
	document.getElementById("convertedamount[1]").value		= paymentconverted;
	
	document.getElementById('exchangerate[1]').value		= paymentrate;
	document.getElementById('paymentamount[1]').value		= paymentamount;
	document.getElementById('paymentamountfield').value		= paymentconverted;
	
	document.getElementById('prevpayment').value			= paymentconverted;
	document.getElementById('paymentnumber[1]').value		= paymentnumber;
	
	$("#paymentaccount\\[1\\]").val(paymentaccount).trigger("change");
	document.getElementById('paymentnotes[1]').value		= paymentnotes;
	document.getElementById('paymentdiscount[1]').value		= paymentdiscount;
	
	document.getElementById('paymentrow').value				= id;
	
	$('#receiptForm #paymentexchangerate\\[1\\]').val(paymentrate);

	loadCheques(row);
	
	toggleCheckInfo(paymentmode);
	
	$('html, body').animate({ scrollTop: 0 }, 'slow');
}

/**LOAD CHEQUES**/
function loadCheques(i)
{
	var cheques 		= $('#paymentsTable #chequeInput'+i).val();

	if(cheques != '')
	{
		var arr_from_json 	= JSON.parse(cheques);
		var arr_len			= arr_from_json.length;

		var row		= 1;
		for(var x=0;x < arr_len;x++)
		{	
			var chequeaccount			= arr_from_json[x]['chequeaccount'];
			var chequenumber			= arr_from_json[x]['chequenumber'];
			var chequedate				= arr_from_json[x]['chequedate'];
			var chequeamount			= arr_from_json[x]['chequeamount'];
			var chequeconvertedamount	= arr_from_json[x]['chequeconvertedamount'];

			$('#receiptForm #chequeaccount\\['+row+'\\]').val(chequeaccount).trigger("change");

			$('#receiptForm #chequenumber\\['+row+'\\]').val(chequenumber);
			$('#receiptForm #chequedate\\['+row+'\\]').val(chequedate);
			$('#receiptForm #chequeamount\\['+row+'\\]').val(chequeamount);
			$('#receiptForm #chequeconvertedamount\\['+row+'\\]').val(chequeconvertedamount);

			/**Add new row based on number of rolls**/
			if(row != arr_len)
			{
				$('body .add-cheque').trigger('click');
			}
			$('#receiptForm #'+row).addClass('disabled');

			$('#receiptForm #checkprint\\['+row+'\\]').removeClass('hidden');
			row++;
		}
		addAmounts();
	}

	/**
	* Script to delete extra added lines
	*/
	var table 	= document.getElementById('chequeTable');
	var count	= table.rows.length - 2;

	if(count > arr_len)

	for(j=count;j > arr_len;j--)
	{
		table.deleteRow(j);	
	}
}

function confirmChequeDelete(row)
{
	var table 		= document.getElementById('chequeTable');
	var rowCount 	= table.rows.length - 2;
	var valid		= 1;
	var rowindex	= table.rows[row];
	
	if($('#chequeaccount\\['+row+'\\]').val() != '')
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();
		}
		else
		{	
			document.getElementById('chequeaccount['+row+']').value 		= '';
			$('#chequeaccount\\['+row+'\\]').val('');

			// $(destino).val("x").trigger("change")
			$('#chequeaccount\\['+row+'\\]').trigger("change");
			
			document.getElementById('chequenumber['+row+']').value 			= '';
			document.getElementById('chequedate['+row+']').value 			= '<?= $date ?>';
			document.getElementById('chequeamount['+row+']').value 			= '0.00';
			//document.getElementById('chequeconvertedamount['+row+']').value = '0.00';
			
			addAmounts();
		}
	}
	else
	{
		if(rowCount > 1)
		{
			table.deleteRow(row);	
			resetChequeIds();
			addAmounts();
		}
		else
		{
			document.getElementById('chequeaccount['+row+']').value 		= '';
			$('#chequeaccount\\['+row+'\\]').val('');
			$('#chequeaccount\\['+row+'\\]').trigger("change");
			document.getElementById('chequenumber['+row+']').value 			= '';
			document.getElementById('chequedate['+row+']').value 			= '<?= $date ?>';
			document.getElementById('chequeamount['+row+']').value 			= '0.00';
			//document.getElementById('chequeconvertedamount['+row+']').value = '0.00';
			addAmounts();
		}
	}
}


/**CANCEL PAYMENT ROW**/
function deletePaymentRow(e,id)
{	
	e.preventDefault();
	row 			= id.replace(/[a-z]/g, '');
	var table 		= document.getElementById('paymentsTable');
	var count		= table.rows.length - 2;
	
	if(document.getElementById('paymentnumber'+row)!=null)
	{
		if(document.getElementById('paymentnumber'+row).value != '')
		{
			var voucher	= document.getElementById('paymentnumber'+row).value;
			var amount	= document.getElementById('paymentamount'+row).value;
			
			$('#deletePaymentModal').data('id', voucher);
			$('#deletePaymentModal').data('row', row);
			$('#deletePaymentModal').data('amount', amount);
			$('#deletePaymentModal').modal('show');
		}
		else
		{
			table.deleteRow(row);
		}
	}
	addPayments();
	resetPaymentRow();
}

/**COMPUTE TOTAL PAYMENTS**/
function addPayments() 
{
	var sum 		= 0;
	var total 		= 0;
	var inData 		= 0;
	
	var table 	= document.getElementById('paymentsTable');
	var count	= table.rows.length - 1;
	
	for(i = 1; i < count; i++) 
	{  
		var inputamt	= document.getElementById('paymentamount'+i);

		if(document.getElementById('paymentamount'+i)!=null)
		{          
			if(inputamt.value && inputamt != '0' && inputamt.value != '0.00')
			{                            
				inData = inputamt.value.replace(/,/g,'');
			}
			else
			{             
				inData = 0;
			}

			total = parseFloat(total) + parseFloat(inData);
		}	
	}

	total		= Math.round(1000*total)/1000;
	
	document.getElementById('totalPaymentCaption').innerHTML 	= addCommas(total.toFixed(2));
	document.getElementById('totalPayment').value 				= addCommas(total.toFixed(2));
}

/**RESET GENERATED ID OF PAYMENT ROWS**/
function resetPaymentRow()
{
	var table 	= document.getElementById('paymentsTable');
	var wtax 	= 'wtax';
	var count	= table.rows.length - 2;

	for(var x = 1; x <= count; x++)
	{
		var row = table.rows[x];
		
		row.cells[0].getElementsByTagName("input")[0].id 	= 'paymentdate'+x;
		row.cells[0].getElementsByTagName("input")[1].id 	= 'paymentnumber'+x;
		
		row.cells[1].getElementsByTagName("input")[0].id 	= 'paymentmode'+x;
		row.cells[2].getElementsByTagName("input")[0].id 	= 'paymentreference'+x;
		row.cells[2].getElementsByTagName("input")[1].id 	= 'paymentcheckdate'+x;
			
		if(wtax != '')
		{
			row.cells[3].getElementsByTagName("input")[0].id 	= 'paymentaccount'+x;
			
			row.cells[4].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[4].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[4].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;	
		}
		else
		{
			row.cells[3].getElementsByTagName("input")[0].id 	= 'paymentaccount'+x;
			
			row.cells[4].getElementsByTagName("input")[0].id 	= 'paymenttaxcode'+x;
			row.cells[5].getElementsByTagName("input")[0].id 	= 'paymentamount'+x;
			row.cells[5].getElementsByTagName("input")[1].id 	= 'paymentrate'+x;
			row.cells[5].getElementsByTagName("input")[2].id 	= 'paymentconverted'+x;
		}
		
		row.cells[0].getElementsByTagName("input")[0].name 	= '';
		row.cells[0].getElementsByTagName("input")[1].name 	= '';
		row.cells[1].getElementsByTagName("input")[0].name = '';
		row.cells[2].getElementsByTagName("input")[0].name 	= '';
		row.cells[2].getElementsByTagName("input")[1].name 	= '';
		
		if(wtax != '')
		{
			row.cells[3].getElementsByTagName("input")[0].name = '';
			row.cells[4].getElementsByTagName("input")[0].name 	= '';
			row.cells[4].getElementsByTagName("input")[1].name 	= '';
			row.cells[4].getElementsByTagName("input")[2].name 	= '';
		}
		else
		{
			row.cells[3].getElementsByTagName("input")[0].name = '';
			row.cells[4].getElementsByTagName("input")[0].name = '';
			row.cells[5].getElementsByTagName("input")[0].name 	= '';
			row.cells[5].getElementsByTagName("input")[1].name 	= '';
			row.cells[5].getElementsByTagName("input")[2].name 	= '';
		}
	}
}

function clearInput(id)
{
	document.getElementById(id).value = '';
}
</script>