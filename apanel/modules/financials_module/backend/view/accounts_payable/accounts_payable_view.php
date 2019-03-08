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
    <ul id='nav' class="nav nav-tabs">
      <li class="active"><a href="Details" data-toggle="tab">Details</a></li>
      <?if(!empty($attachment_filename)):?><li><a href="Attachment" data-toggle="tab">Attachments</a></li><?endif;?>
  </ul>

  <div id="Details" class="tab-pane">
    <div class="well well-lg">
        <div class="panel panel-default">
            <div class = "panel-heading">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3><?php echo $stat ?></h3>
                                    <h2><strong>Accounts Payable</strong><small> (<span id="voucherno"><?php echo $voucherno ?></span>)</small></h2>
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
                                    <h4>Supplier : </h4>
                                </div>
                            </div>
                                <!-- <div class="row">
                                    <div class="col-md-12">
                                        <h4>Attachment : <a target="_blank" href="<?php echo $attachment_url ?>"><?php echo $attachment_filename ?></a></h4>
                                    </div>
                                </div> -->
                                <div class="row">
                                    <div class="col-md-8">
                                        <h4><strong><?php echo $vendor; ?></strong></h4>
                                    </div>
                                    <?php if(!empty($job_no)) : ?>
                                        <?php $jobs = explode(',', $job_no); ?>
                                        <div class="col-md-4">
                                            <div class="col-md-6">
                                                <h4>Job Tagged</h4>
                                            </div>
                                            <div class="col-md-6">
                                                <ul>
                                                    <?php foreach($jobs as $row) : ?>
                                                        <li><?php echo $row ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
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
                                            <div class="col-md-7">
                                                <h4>Asset Code</h4>
                                            </div>
                                            <div class="col-md-5">
                                                <h4><strong> <?php echo $assetid; ?></strong></h4>
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
                                            <th class="col-md-1 text-center <?=$toggle_wtax?>">Withholding Tax</th>
                                            <?php if($toggle_wtax == 'hidden') {  ?>
                                                <th class="col-md-2 text-center">Budget Code</th>
                                            <?php } else { ?>
                                                <th class="col-md-1 text-center">Budget Code</th>
                                            <?php } ?>
                                            <th class="col-md-2 text-center">Account</th>
                                            <th class="col-md-2 text-center">Description</th>
                                            <th class="col-md-2 text-center" colspan = "2">Debit</th>
                                            <th class="col-md-2 text-center" colspan = "2">Credit</th>
                                            <th class="col-md-2 text-center">Base Currency Amount</th>
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
                                                            <td class = "checkbox-select remove-margin text-center <?=$toggle_wtax?>">
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
                                                            <td class = "checkbox-select remove-margin text-center <?=$toggle_wtax?>">
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
                                                            ->setList($budget_list)
                                                            ->setValue($row->budgetcode)
                                                            ->draw($show_input);
                                                            ?>
                                                        </td>
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
                                                                <div class="col-md-2">
                                                                    <span class="label label-default base_symbol">PHP</span>
                                                                </div>
                                                                <div class="col-md-10 currencyamount text-right">
                                                                    <?php echo '('. number_format($row->convertedcredit, 2) .')';?>
                                                                </div>
                                                            </td>
                                                        <?php } else { ?>
                                                            <td class = "remove-margin">
                                                                <div class="col-md-2">
                                                                    <span class="label label-default base_symbol">PHP</span>
                                                                </div>
                                                                <div class="col-md-10 currencyamount text-right">
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
                                                <?php if($toggle_wtax != 'hidden') {  ?>
                                                    <td style="border-top:1px solid #DDDDDD;">&nbsp;</td>
                                                <?php }  ?>
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
                                            <th class="col-md-1 text-center">Mode / Cheque no.</th>
                                            <th class="col-md-2 text-center">Reference</th>
                                            <th class="col-md-2 text-center">Payment Account</th>
                                            <th class="col-md-2 text-center">Amount</th>
                                            <th class="col-md-2 text-center">Discount</th>
                                            <th class="col-md-1 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $amount = 0; ?>
                                        <?php if(isset($payments)) { ?>
                                            <?php foreach ($payments as $key => $row) : ?>
                                                <?php $amount = $row->amount; ?>
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
                                                        ->setValue(ucfirst($row->mode))
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
                                                <label class="control-label" style="padding: 0 12px 0 12px;" id = "total_amount"><?php echo $amount ?></label>
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

                <br>
                <div class="row">
                    <div class="col-md-12 col-sm-12 text-center">
                        <div class="btn-group" id="save_group">
                            <?php if($status == 'unpaid' && !$checker) : ?>
                                <a href="<?=MODULE_URL?>edit/<?php echo $voucherno ?>" class = "btn btn-info btn-flat">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class = "col-md-12">&nbsp;</div>                                
                </div>
            </div>
            
        </div>
        <?if(!$show_input && !empty($attachment_filename)):?>
        <div id="Attachment" class="tab-pane">
         <div class="box box-primary">
            <form method = "post" class="form-horizontal" id="case_attachments_form" enctype="multipart/form-data">
               <div class="row">
                  <div class="col-md-12">
                     <div class="table-responsive">
                        <table id="fileTable" class="table table-bordered">
                           <thead>
                              <tr class="info">
                                 <th class="col-md-1">Action</th>
                                 <th class="col-md-5">File Name</th>
                                 <th class="col-md-2">File Type</th>
                             </tr>
                         </thead>
                         <tbody class="files" id="attachment_list">
                          <tr>
                             <td>
                                <button type="button" id="replace_attachment" data-voucherno='<?=$voucherno;?>' name="replace_attachment" class="btn btn-primary">Replace</button>
                            </td>
                            <td><a href="<?=$attachment_url;?>" target='_blank'><?=urldecode($attachment_filename)?></a></td>
                            <td><?=$attachment_filetype?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br/>
</form>
</div>
</div>
<?php endif;?>

<div id="attach_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method = "post" id="attachments_form" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Attach File for <span id="modal-voucher"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="voucherno" id='input_voucherno'>
                        <?php
                        echo $ui->setElement('file')
                        ->setId('files')
                        ->setName('files')
                        ->setAttribute(array('accept' => '.pdf, .jpg, .png'))
                        ->setValidation('required')
                        ->draw();
                        ?>
                    </div>
                    <p class="help-block">The file to be imported shall not exceed the size of <strong>3mb</strong> and must be a <strong>PDF, PNG or JPG</strong> file.</p>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" id="attach_button" disabled>Attach</button>
                        </div>
                        &nbsp;&nbsp;&nbsp;
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm btn-flat" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="attach_success" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title modal-success"><span class="glyphicon glyphicon-ok"></span> Success!</h4>
                </div>
                <div class="modal-body">
                    <p>You have successfully updated the attached file.</p>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function(){
        $('#Attachment').hide();
    })

    $('#nav li a').on('click', function(){
        $('#nav li').removeClass();
        $('#Details').hide();
        $('#Attachment').hide();

        $(this).closest('li').attr('class','active');
        var tab = $('#nav li.active a').attr('href');
        $('#'+tab).show();
    });

    $('#replace_attachment').on('click', function(){
        var voucherno = $(this).data('voucherno');
        $('#modal-voucher').html(voucherno);
        $('#input_voucherno').val(voucherno);
        $('#attach_modal').modal('show');
    });
    
    <?php if($ajax_task != 'ajax_create') : ?>
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
        });
        $('#total_debit').html(addComma(total_debit));
    }

    function sumCredit() {
        var total_credit = 0;
        var credit = 0;
        var curr_val = 0;
        $('.credit').each(function() {
            credit = removeComma($(this).html());
            total_credit += +credit;
        });
        $('#total_credit').html(addComma(total_credit));
    }

    function sumCurrencyAmount() {
        var total_currency = 0;
        var currency = 0;
        $('.currencyamount').each(function() {
            currency = removeComma($(this).html().replace('(','').replace(')',''));
            if(removeComma($(this).closest('tr').find('.credit').html()) != 0){
                total_currency += -currency;
            }else{
                total_currency += +currency;
            }
        });
        $('#total_currency').html(addComma(total_currency));
    }

    $('label[for=files]').css({"display": "inline-block","text-overflow": "ellipsis","overflow": "hidden"});
    
    $(function () {
        'use strict';

        $('#attachments_form').fileupload({
            url: '<?= MODULE_URL ?>ajax/ajax_upload_file',
            maxFileSize: 3000000,
            disableExifThumbnail :true,
            previewThumbnail:false,
            autoUpload:false,
            add: function (e, data) {            
                $("#attach_button").off('click').on('click', function () {
                    data.submit();
                });
            },
            messages: {
               maxFileSize: 'File exceeds maximum allowed size of 3MB'
           }
       });
        $('#attachments_form').addClass('fileupload-processing');
        $.ajax({
            url: $('#attachments_form').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#attachments_form')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {
                result: result
            });
        });

        $('#attachments_form').bind('fileuploadadd', function (e, data) {
            var filename = data.files[0].name;
            $('#attachments_form #files').closest('.input-group').find('.form-control').html(filename);

            // Script to validate selected file
            var $this = $(this);
            var validation = data.process(function(){
                return $this.fileupload('process', data);
            });

            validation.done(function(){
                var form_group = $('#attachments_form #files').closest('.form-group');
                form_group.removeClass('has-error');
                form_group.find('p.help-block.m-none').html('');
                $('#attach_button').prop('disabled', false);
            });
            validation.fail(function(data) {
                var form_group = $('#attachments_form #files').closest('.form-group');
                var maxLimitError = data.files[0].error;
                form_group.addClass('has-error');
                form_group.find('p.help-block.m-none').html(maxLimitError);
                
                $('#attach_button').prop('disabled', true);
            });
        });
        $('#attachments_form').bind('fileuploadsubmit', function (e, data) {
            // var source_no = $('#source_no').val();
            // var task = "create";
            // data.formData = {reference: source_no, task: task};
            var voucher_no = $('#voucherno').html();
            var task = "";
            data.formData = {reference: voucher_no, task: task};
        });
        $('#attachments_form').bind('fileuploadalways', function (e, data) {
            var error = data.result['files'][0]['error'];
            var form_group = $('#attachments_form #files').closest('.form-group');
            var old_filename = "<?php echo $attachment_filename ?>";
            if(!error){
                // var source_no = $('#source_no').val();
                var voucherno =  $('#input_voucherno').val();
                $('#attach_modal').modal('hide');
                // <?php if (!$show_input) { ?>
                $('#attachment_success').modal('show');
                setTimeout(function() {							
                    window.location = '<?=MODULE_URL?>view/'+voucherno;						
                }, 1000)
                // <?php } ?>

                var msg = data.result['files'][0]['name'];
                form_group.removeClass('has-error');
                form_group.find('p.help-block.m-none').html('');
                $('#attachments_form #files').closest('.input-group').find('.form-control').html('');
                $('#attach_button').prop('disabled', false);
                // $('#file').val('').trigger('blur');
                // getList();
            }else{
                var msg = data.result['files'][0]['name'];
                form_group.addClass('has-error');
                form_group.find('p.help-block.m-none').html(msg);
                $('#attach_button').prop('disabled', true);
            }
        });
    });
</script>