<style>
#vendorDetails2 .col-md-3 > .form-group,
#vendorDetails2 .col-md-2 > .form-group  {
    margin: 0;
}

.text-right {
    text-align: right;
}

.text-bold {
    font-weight: bold;
    color: #fff;
}

.remove-margin > .form-group,
.remove-margin .form-group {
    margin-bottom: 0;
}

.vendor_div > .form-group {
    margin-bottom: 5px;
}

.width35 {
    width: 35%;
}

.width27 {
    width: 27%;
}

.no-bg {
    border: 0px solid transparent;
    background-color: transparent !important;
}

</style>

<section class="content">
    <div class = "well well-lg">
        <div class = "panel panel-default">
            <div class = "panel-heading">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3><?php echo $stat ?></h3>
                                    <h2><strong>Accounts Payable</strong><small> (<?php echo $voucherno ?>)</small></h2>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h4>Date</h4>
                                        </div>
                                        <div class="col-md-5">
                                            <h4><strong>:<?php echo $transactiondate; ?></strong></h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h4>Due Date</h4>
                                        </div>
                                        <div class="col-md-5">
                                            <h4><strong>:<?php echo $duedate; ?></strong></h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h4>Invoice No</h4>
                                        </div>
                                        <div class="col-md-5">
                                            <h4><strong>:<?php echo $invoiceno; ?></strong></h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <h4>Reference No</h4>
                                        </div>
                                        <div class="col-md-5">
                                            <h4><strong>:<?php echo $referenceno; ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Vendor : </h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <h4><strong><?php echo $vendor; ?></strong></h4>
                                </div>
                                <?php if(!empty($job_no)) : ?>
                                    <div class="col-md-4">
                                        <div class="col-md-6">
                                            Job Tagged
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo substr($job_no, 0, 20) . '...'; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="col-md-1">
                                        Email
                                    </div>
                                    <div class="col-md-11">
                                        : <?php echo $email; ?>
                                    </div>
                                </div>
                                <?php if(!empty($assetid)) : ?>
                                    <div class="col-md-4">
                                        <div class="col-md-6">
                                         Asset Code
                                     </div>
                                     <div class="col-md-6">
                                        <?php echo $assetid; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="col-md-1">
                                    TIN
                                </div>
                                <div class="col-md-11">
                                    : <?php echo $tinno; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="col-md-1">
                                    Address
                                </div>
                                <div class="col-md-11">
                                    : <?php echo $address; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="panel panel-default">
                    <div class="box-body table-responsive no-pad">
                        <table class="table table-hover table-condensed " id="itemsTable">
                            <thead>
                                <tr class="info">
                                    <th class="col-md-1 text-center">Withholding Tax</th>
                                    <th class="col-md-2 text-center">Account</th>
                                    <th class="col-md-3 text-center">Description</th>
                                    <th class="col-md-2 text-center" colspan = "2">Debit</th>
                                    <th class="col-md-2 text-center" colspan = "2">Credit</th>
                                    <th class="col-md-3 text-center">Currency Amount</th>
                                    <?if($ajax_task != 'view'){?>
                                        <th class="col-md-1 center"></th>
                                        <?}?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($details)) : ?>
                                        <?php foreach ($details as $key => $row) : ?>
                                            <tr class="clone" valign="middle">
                                                <?php if($ajax_task == 'ajax_view') { ?>
                                                    <td class = "checkbox-select remove-margin text-center">
                                                        <div class="hidden">
                                                            <?php
                                                            echo $ui->formField('checkbox')
                                                            ->setSplit('', 'col-md-12')
                                                            ->setId("wtax[]")
                                                            ->setClass("wtax")
                                                            ->setDefault("")
                                                            ->setValue(1)
                                                            ->setAttribute(array("disabled" => "disabled"))
                                                            ->draw($show_input);
                                                            ?>
                                                        </div>
                                                    </td>
                                                <?php } else { ?>
                                                    <td class = "checkbox-select remove-margin text-center">
                                                        <?php
                                                        echo $ui->formField('checkbox')
                                                        ->setSplit('', 'col-md-12')
                                                        ->setId("wtax[]")
                                                        ->setClass("wtax")
                                                        ->setDefault("")
                                                        ->setValue(1)
                                                        ->setAttribute(array("disabled" => "disabled"))
                                                        ->draw($show_input);
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                <td class = "remove-margin">
                                                    <?php
                                                    echo $ui->formField('dropdown')
                                                    ->setPlaceholder('Select One')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName("accountcode[]")
                                                    ->setId("accountcode")
                                                    ->setClass('accountcode')
                                                    ->setList($account_list)
                                                    ->setValue($row->accountcode)
                                                    ->draw($show_input);
                                                    ?>
                                                </td>
                                                <td class = "remove-margin">
                                                    <?php
                                                    echo $ui->formField('text')
                                                    ->setSplit('', 'col-md-12')
                                                    ->setName('detailparticulars[]')
                                                    ->setId('detailparticulars')
                                                    ->setAttribute(array("maxlength" => "100"))
                                                    ->setClass('detailparticulars')
                                                    ->setValue($row->description)
                                                    ->draw($show_input);
                                                    ?>
                                                </td>
                                                <td class = "remove-margin" colspan = "2">
                                                    <div class="col-md-2">
                                                        <span class="label label-default currency_symbol"><?php echo $currencycode ?></span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <div class="debit text-right">
                                                            <?php echo number_format($row->debit, 2);?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class = "remove-margin" colspan = "2">
                                                   <div class="col-md-2">
                                                    <span class="label label-default currency_symbol"><?php echo $currencycode ?></span>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="credit text-right">
                                                        <?php echo number_format($row->credit, 2);?>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php if($row->debit == 0)  { ?>
                                                <td class = "remove-margin">
                                                    <div class="col-md-12 currencyamount text-right">
                                                        <?php echo number_format($row->convertedcredit, 2);?>
                                                    </div>
                                                </td>
                                            <?php } else { ?>
                                                <td class = "remove-margin">
                                                    <div class="col-md-12 currencyamount text-right">
                                                        <?php echo number_format($row->converteddebit, 2);?>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                            <input type="hidden" name="linenum[]" value = "<?php echo $row->linenum; ?>" class = "linenum">
                                            <?php $val = ($ajax_task) == 'ajax_view' ? 'hidden' : ''; ?>
                                            <td class="text-center" <?php echo $val; ?>>
                                                <button type="button" class="btn btn-danger btn-flat confirm-delete" data-id="1" name="chk[]" style="outline:none;"><span class="glyphicon glyphicon-trash"></span></button>
                                            </td>           
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>  
                                <tr id="total">
                                    <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                                    <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                                    <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                                    <td class="right" style="border-top:1px solid #DDDDDD;">
                                        <label class="control-label">Total</label>
                                    </td>
                                    <td class="right" style="border-top:1px solid #DDDDDD;">
                                     <div class="col-md-12 text-right">
                                         <span id = "total_debit"></span>
                                     </div>
                                 </td>
                                 <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                                 <td class="right" style="border-top:1px solid #DDDDDD;">
                                     <div class="col-md-12 text-right">
                                         <span id = "total_credit"></span>
                                     </div>
                                 </td>
                                 <td class="right" style="border-top:1px solid #DDDDDD;">
                                     <div class="col-md-12 text-right">
                                         <span id = "total_currency"></span>
                                     </div>
                                 </td>
                                 <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                             </tr>   
                         </tfoot>
                     </table>
                 </div>
             </div>
             <div class="row">
                 <div class="col-md-12 text-right">
                    <a href="<?=MODULE_URL?>" class = "btn btn-primary btn-flat">Exit</a>
                </div>
            </div>
            <div class="panel panel-default" <?php echo $table ?>>
                <div class="panel-heading">
                    <strong>Issued Payments</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-condensed " id="issuedPayments">
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
                            <?php if(isset($payments)) { ?>
                                <?php foreach ($payments as $key => $row) : ?>
                                   <tr>
                                    <td class = "remove-margin">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('date[]')
                                        ->setId('date')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('date')
                                        ->setValue($row->date)
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('mode[]')
                                        ->setId('mode')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('mode')
                                        ->setValue(strtoupper($row->mode))
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('reference[]')
                                        ->setId('reference')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('reference')
                                        ->setValue($row->reference)
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('paymentaccount[]')
                                        ->setId('paymentaccount')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('paymentaccount')
                                        ->setValue($row->paymentaccount)
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('amount[]')
                                        ->setId('amount')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('amount')
                                        ->setValue($row->amount)
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "remove-margin text-right">
                                        <?php
                                        echo $ui->formField('text')
                                        ->setSplit('', 'col-md-12')
                                        ->setName('discount[]')
                                        ->setId('discount')
                                        ->setAttribute(array("maxlength" => "100"))
                                        ->setClass('discount')
                                        ->setValue($row->discount)
                                        ->draw($show_input);
                                        ?>
                                    </td>
                                    <td class = "text-center">
                                        <a href="<?=MODULE_URL?>print_preview/<?=$voucherno?>" style = "text-decoration: none; color : black;" class = "btn btn-default btn-xs">
                                            <span class="glyphicon glyphicon-print"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right" colspan="4" style="border-top:1px solid #DDDDDD;">
                                <label for="subtotal" class="control-label">Total </label>
                            </td>
                            <td style="border-top:1px solid #DDDDDD;" class="text-right">
                                <label class="control-label" style="padding: 0 12px 0 12px;">2.00</label>
                            </td>
                            <td style="border-top:1px solid #DDDDDD;" class="text-right">
                                <label class="control-label" style="padding: 0 12px 0 12px;">0.00</label>
                            </td>
                            <td style="border-top:1px solid #DDDDDD;" class = "text-center">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<br>
<div class="row">
    <div class="col-md-12 col-sm-12 text-center">
        <div class="btn-group" id="save_group">
            <?php if($status == 'unpaid') : ?>
                <a href="<?=MODULE_URL?>edit/<?php echo $voucherno ?>" class = "btn btn-info btn-flat">Edit</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class = "col-md-12">&nbsp;</div>
</div>
</section>
<script>
    <?php if($ajax_post != 'create') : ?>
        $(document).ready(function() {
            sumDebit();
            sumCredit();
            sumCurrencyAmount();
        });
    <?php endif; ?>

    function consoler(console) {
        console.log(console);
    }

    function sumDebit() {
        var total_debit = 0;
        var debit = 0;
        var curr_val = 0;
        $('.debit').each(function() {
            debit = removeComma($(this).html());
            total_debit += +debit;
            $('#total_debit').html(addComma(total_debit));
        });
    }

    function sumCredit() {
        var total_credit = 0;
        var credit = 0;
        var curr_val = 0;
        $('.credit').each(function() {
            credit = removeComma($(this).html());
            total_credit += +credit;
            $('#total_credit').html(addComma(total_credit));
        });
    }

    function sumCurrencyAmount() {
        var total_currency = 0;
        var currency = 0;
        $('.currencyamount').each(function() {
            currency = removeComma($(this).html());
            total_currency += +currency;
            $('#total_currency').html(addComma(total_currency));
        });
    }
</script>