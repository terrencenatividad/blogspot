<section class="content">
    <div class="box box-primary">
        <div class="box-header pb-none">
            <div class="row">
                <div class="col-md-3">
                    <?php
                                echo $ui->formField('text')
                                ->setName('daterangefilter')
                                ->setId('daterangefilter')
                                ->setAttribute(array('data-daterangefilter' => 'month'))
                                ->setAddon('calendar')
                                ->setValue($datefilter)
                                ->setValidation('required')
                                ->draw(true);
                            ?>
                </div>
                <div class="col-md-3">
                    <?php
							echo $ui->formField('dropdown')
								->setPlaceholder('Filter Job Number')
								->setName('job_number')
								->setId('job_number')
								->setList($job_list)
								->setNone('Filter: All')
								->draw();
						?>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input id="account_search" name="account_search" class="form-control pull-right" placeholder="Search Account Code / Name"
                            type="text">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-1">
                    <a href="" id="bt_close_job" class="btn btn-primary" data-toggle="modal" data-target="#close-job-modal"><span
                            class="glyphicon glyphicon-export"></span>
                        Close job</a>
                </div>
                <div class="col-md-1">
                    <a href="" id="export_csv" download="jobReport.csv" class="btn btn-primary"><span class="glyphicon glyphicon-export"></span>
                        Export</a>
                </div>
            </div>
        </div>
        <div class="box-body table-responsive no-padding" id="report_content">
            <table id="tableList" class="table table-hover table-striped table-sidepad">
                <thead>
                    <?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('Job Number',array('class'=>'col-md-2'),'sort','j.job_no')
							->addHeader('Account Code', array('class'=>'col-md-1'),'sort','cc.segment5')
							->addHeader('Account Name',array('class'=>'col-md-2'),'sort','cc.accountname')
							->addHeader('Amount',array('class'=>'col-md-1 text-right'))
							->addHeader('Status',array('class'=>'col-md-1 text-right'),'sort','j.stat')
							->draw();
				?>
                </thead>

                <form method="post">
                    <tbody id="list_container">
                    </tbody>
                </form>

                <tfoot>
                    <tr class="info">
                        <td style="border-top:1px solid #DDDDDD;"></td>
                        <td style="border-top:1px solid #DDDDDD;"></td>
                        <td style="border-top:1px solid #DDDDDD;" class="bold text-right">Total</td>
                        <td style="border-top:1px solid #DDDDDD;" class="bold text-right">
                            <label class="total_job"> </label>
                        </td>
                        <td style="border-top:1px solid #DDDDDD;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div id="pagination"></div>
</section>

<!-- Processing Fee Modal -->
<div class="modal fade" id="process-fee-modal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Processing Fee</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" autocomplete="off">
                    <div class="box-body table-responsive no-padding" id="close-job-content">
                        <table id="tb_process_fee" class="table table-hover table-striped table-sidepad">
                            <thead>
                                <?php
					echo $ui->loadElement('table')
							->setHeaderClass('info')
							->addHeader('Reference',array('class'=>'col-md-1'),'sort','referenceList')
							->addHeader('Date', array('class'=>'col-md-2'),'sort','main.entereddate')
							->addHeader('Debit',array('class'=>'col-md-1'),'sort','main.debit')
							->addHeader('Credit',array('class'=>'col-md-1 text-right'),'sort','main.credit')
							->draw();
				?>
                            </thead>
                            <tbody id="tb_process_body">

                            </tbody>
                            <tfoot>
                                <tr id="total">
                                    <td style="border-top:1px solid #DDDDDD;" class="<?=$toggle_wtax?>">&nbsp;</td>

                                    <td class="right" style="border-top:1px solid #DDDDDD;">
                                        <label class="control-label col-md-2">Total</label>
                                    </td>
                                    <td style="border-top:1px solid #DDDDDD;">
                                        <?php 
													echo $ui->formField('text')
															->setName('total_debit')
															->setId('total_debit')
															->setClass("input_label bold text-right")
															->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
															->draw(true);
												?>
                                    </td>
                                    <td style="border-top:1px solid #DDDDDD;">
                                        <?php 
													echo $ui->formField('text')
															->setName('total_credit')
															->setId('total_credit')
															->setClass("input_label bold font-weight-bold text-right")
															->setAttribute(array("maxlength" => "40", "readonly" => "readonly"))
															->draw(true);
												?>
                                    </td>

                                </tr>
                            </tfoot>
                        </table>
                        </div>
                        <div id="pagination2"></div>
                    </div>

                    <div class="modal-footer">
                        <div class="row row-dense">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Processing Fee Modal -->



<!-- Close Job Modal -->
<div class="modal fade" id="close-job-modal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Close Job </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" autocomplete="off" id="closedjobForm">
                    <div class="alert alert-warning alert-dismissable hidden" id="closedjobAlert">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p>&nbsp;</p>
                    </div>


                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                                echo $ui->formField('dropdown')
                                    ->setLabel('Select Job No.')
                                    ->setSplit('col-md-3', 'col-md-9')
                                    ->setPlaceholder('Select Job number')
                                    ->setName('close_job_number')
                                    ->setId('close_job_number')
									->setList($job_list2)
                                    ->draw(true);
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="box-body table-responsive no-padding" id="close-job-content">
                                <table id="tb_closejob" class="table table-hover table-striped table-sidepad">
                                    <thead>
                                        <?php
									echo $ui->loadElement('table')
											->setHeaderClass('info')
											->addHeader('Reference',array('class'=>'col-md-2'),'sort','main.voucherno')
											->addHeader('Account', array('class'=>'col-md-2'),'sort','main.accountname')
											->addHeader('Debit',array('class'=>'col-md-2 text-right'),'sort','main.debit')
											->addHeader('Credit',array('class'=>'col-md-2 text-right'),'sort','main.credit')
											->draw();
									?>
                                    </thead>
                                    <tbody id="closedjob_listing">


                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td style="border-top:1px solid #DDDDDD;">
                                            </td>
                                            <td style="border-top:1px solid #DDDDDD;" class="bold text-right">
                                                Total
                                            </td>
                                            <td style="border-top:1px solid #DDDDDD;" class="bold text-right">
                                                <label class="closed_job_total_debit"> </label>
                                            </td>
                                            <td style="border-top:1px solid #DDDDDD;" class="bold text-right">
                                                <label class="closed_job_total_credit"> </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="border-top:1px solid #DDDDDD;" class="bold text-right">
                                                Total Importation Cost
                                            </td>
                                            <td style="border-top:1px solid #DDDDDD;">
                                            </td>
                                            <td style="border-top:1px solid #DDDDDD;" class="bold text-right">
                                                <label class="closed_final_total"> </label>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div id="pagination3"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="row row-dense">
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-flat" id="jobClose">Close Job</button>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<script>
    var ajax = filterFromURL();
    var ajax_call = '';
    ajaxToFilter(ajax, {
        search: '#account_search',
        job_number: '#job_number',
        daterangefilter: '#daterangefilter'
    });

    /** -- FOR SEARCH -- **/
    $('#account_search').keyup(function () {
        ajax.page = 1;
        ajax.account_search = $(this).val();
        console.log(ajax.account_search);
        showList();
    });
    /** -- FOR SEARCH -- end **/

    /** -- FOR PAGINATION **/
    $('#pagination').on('click', 'a', function (e) {
        e.preventDefault();
        var li = $(this).closest('li');
        if (li.not('.active').length && li.not('.disabled').length) {
            ajax.page = $(this).attr('data-page');
            showList();
        }
    });

     $('#pagination2').on('click', 'a', function (e) {
        e.preventDefault();
        var li = $(this).closest('li');
        if (li.not('.active').length && li.not('.disabled').length) {
            ajax.page = $(this).attr('data-page');
            showProcessFee();
        }
    });

    $('#pagination3').on('click', 'a', function (e) {
        e.preventDefault();
        var li = $(this).closest('li');
        if (li.not('.active').length && li.not('.disabled').length) {
            ajax.page = $(this).attr('data-page');
            closejobList();
        }
    });
    /** -- FOR PAGINATION -- end **/

    // Sorting Script
    tableSort('#tableList', function (value) {
        ajax.sort = value;
        ajax.page = 1;
        showList();
    });

    tableSort('#tb_process_fee', function (value) {
        ajax.sort = value;
        ajax.page = 1;
        showProcessFee();
    });

    tableSort('#tb_closejob', function (value) {
        ajax.sort = value;
        ajax.page = 1;
        closejobList();
    });

    /** -- FOR DATE -- **/
    $('#daterangefilter').on('change', function () {
        ajax.daterangefilter = $(this).val();
        ajax.page = 1;
        console.log(ajax.daterangefilter);
        showList();
    });
    /** -- FOR DATE -- end **/

    /** -- FOR PROCESSING FEE -- **/
    var debits = 0;
    var ftotal_debit = 0;
    var credits = 0;
    var ftotal_credit = 0;

    $('#tableList').on('click', '.amount', function () {
        var account_code = $(this).attr('data-id');
        ajax.account_code = $(this).attr('data-id');
        showProcessFee();
        $('#process-fee-modal').modal('show');
        console.log(account_code);
    });

    function showProcessFee() {
        var ftotal_debit = 0;
        var ftotal_credit = 0;
        $.post('<?=MODULE_URL?>ajax/processing_fee_listing', ajax, function (data) {
            $('#tb_process_fee #tb_process_body').html(data.table);
            $('#pagination2').html(data.pagination);

            $('#tb_process_fee tbody tr td.tobesum').each(function () {
                debits = removeComma($(this).html());
                ftotal_debit += +debits;
                $('#total_debit').val(addComma(ftotal_debit));
            });

            $('#tb_process_fee tbody tr td.tobesum2').each(function () {
                credits = removeComma($(this).html());
                ftotal_credit += +credits;
                $('#total_credit').val(addComma(ftotal_credit));
            });

            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
                showProcessFee();
            }
        });
    }
    /** -- FOR PROCESSING FEE end -- **/

    /** -- FOR close_job_number -- **/
    // button 
    // $('#bt_close_job').on('click', function() {
    //     $('#close_job_number').val('').trigger('change');
    // });

    // modal show
    var tdebits = 0;
    var tcredits = 0;
    var closed_ftotal_debit = 0;
    var closed_ftotal_credit = 0;
    var closed_finaltotal = 0;
    $('#close-job-modal').on('hidden.bs.modal', function () {
        $('#close_job_number').val('').trigger('change');
        $('.closed_job_total_debit').html('');
        $('.closed_job_total_credit').html('');
        $('.closed_final_total').html('');
        $('.closed_final_total').val('');
        tdebits = 0;
        tcredits = 0;
        closed_ftotal_debit = 0;
        closed_ftotal_credit = 0;
        closed_finaltotal = 0;
    });

    // dropdown
    $('#close_job_number').on('change', function () {
        var close_job_number = $(this).val();
        ajax.close_job_number = close_job_number;
        console.log(close_job_number);
        tdebits = 0;
        tcredits = 0;
        closed_ftotal_debit = 0;
        closed_ftotal_credit = 0;
        closed_finaltotal = 0;
        $('.closed_job_total_debit').html('');
        $('.closed_job_total_credit').html('');
        $('.closed_final_total').html('');
        closejobList();
    });

    function closejobList() {
        $.post('<?=MODULE_URL?>ajax/close_job_listing', ajax, function (data) {
            tdebits = 0;
            tcredits = 0;
            closed_ftotal_debit = 0;
            closed_ftotal_credit = 0;
            closed_finaltotal = 0;
            $('#tb_closejob #closedjob_listing').html(data.table);
            $('#pagination').html(data.pagination);

            $('#tb_closejob tbody tr td.closed_debit').each(function () {
                tdebits = removeComma($(this).html());
                closed_ftotal_debit += +tdebits;
                $('.closed_job_total_debit').html(addComma(closed_ftotal_debit));
            });

            $('#tb_closejob tbody tr td.closed_credit').each(function () {
                tcredits = removeComma($(this).html());
                closed_ftotal_credit += +tcredits;
                $('.closed_job_total_credit').html(addComma(closed_ftotal_credit));
            });
            closed_finaltotal = closed_ftotal_debit - closed_ftotal_credit;
            $('.closed_final_total').html(addComma(closed_finaltotal));

        });
    }
    var error_message 	=	'';	
	var form_group	 	= 	$('#closedjobForm').closest('.form-group');

    // closed job finally
    $('#closedjobForm #jobClose').on('click', function () {
        if ($('#closedjobForm').find('.form-group.has-error').length == 0) {
            var closedjob = $('#close_job_number').val();
            ajax.closedjob = closedjob;
            console.log(ajax.closedjob);
            if(closedjob != "") {
                form_group.removeClass('has-error').find('p.help-block').html('');
                $.post('<?=MODULE_URL?>ajax/ajax_get_closing', ajax, function (data) {
                if (data.msg == 'success') {
                    $('#close-job-modal').modal('hide');
                    $('#delay_modal').modal('show');
                    setTimeout(function () {
                        window.location = '<?=MODULE_URL?>';
                    }, 1000)

                    }
                });
            }
            else {
                error_message 	=	"<b>Please select job no first!</b>";
			    $('#closedjobForm').closest('.form-group').addClass("has-error").find('p.help-block').html(error_message);
            }
            
        }
    });
    /** -- FOR close_job_number -- end **/

    /** -- FOR JOB NUMBER **/
    $('#job_number').on('change', function (e) {
        ajax.job_number = $(this).val();
        ajax.page = 1;
        console.log(ajax.job_number);
        showList();
    });
    /** -- FOR JOB NUMBER -- end **/

    var sum = 0;
    var fsum = 0;
    /** -- FOR DISPLAY -- **/
    function showList() {
        filterToURL();
        if (ajax_call != '') {
            ajax_call.abort();
        }
        ajax_call = $.post('<?=MODULE_URL?>ajax/jobreport_listing', ajax, function (data) {
            $('#tableList #list_container').html(data.table);
            $('#pagination').html(data.pagination);
            $("#export_csv").attr('href', 'data:text/csv;filename=jobReport.csv;charset=utf-8,' +
                encodeURIComponent(data.csv));
            fsum = 0;
            $('#tableList tbody tr td').find('.amount').each(function () {
                sum = removeComma($(this).html());
                fsum += +sum;
                $('.total_job').html(addComma(fsum));
            });

            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
                showList();
            }
        });
    };

    showList();

    function show_error(msg) {
        $(".delete-modal").modal("hide");
        $(".alert-warning").removeClass("hidden");
        $("#errmsg").html(msg);
    }
    /** -- FOR DISPLAY -- end **/
</script>