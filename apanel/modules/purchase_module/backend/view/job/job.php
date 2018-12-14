<section class="content">

    <div class="alert alert-warning alert-dismissable hidden">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <h4><strong>Error!<strong></h4>
        <div id="errmsg"></div>
    </div>

    <div class="box box-primary">

        <div class="box-body">
            <form method="post" id="jobForm" class="form-horizontal">
                
                <div class="col-md-12">&nbsp;</div>
                <div class="row">
                    <?php if($task == 'view') : ?>
                        <?php if($stat == 'cancelled') { ?>
                            <div class="row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-4">
                                    <?php echo '<font size = "4em"><span class="label label-danger">CANCELLED</span></font>'; ?>
                                </div>
                                <div class="col-lg-3"></div>
                            </div>
                            <br>
                        <?php } ?>
                        <?php if($stat == 'closed') { ?>
                            <div class="row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-4">
                                    <?php echo '<font size = "4em"><span class="label label-success">CLOSED</span></font>'; ?>
                                </div>
                                <div class="col-lg-3"></div>
                            </div>
                            <br>
                        <?php } ?>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <?php
                            
                            echo $ui->formField('text')
                                ->setLabel('Job No:')
                                ->setSplit('col-md-4', 'col-md-8')
                                ->setName('job_no')
                                ->setId('job_no')
                                ->setAttribute(array("disabled" => "disabled"))
                                ->setPlaceholder("- auto generate -")
                                ->setValue($job_no)
                                ->draw($show_input);
                        ?>
                        <input type="hidden" name="txtjob" value="<?php echo $job_no?>">
                    </div>
                    <div class = "col-md-6">
                        <?php

                            echo $ui->formField('text')
                                ->setLabel('Transaction Date')
                                ->setSplit('col-md-4', 'col-md-8')
                                ->setName('transaction_date')
                                ->setId('transaction_date')
                                ->setClass('datepicker-input')
                                ->setAttribute(array('readonly' => ''))
                                ->setAddon('calendar')
                                ->setValue($transactiondate)
                                ->setValidation('required')
                                ->draw($show_input);
                        ?>

                    </div>
                </div>
                
                <div class="row">
                    <div class = "col-md-6">
                        <div class="form-group">
                            <label for="btnpreceipt" class="control-label col-md-4">PR Number </label>
                            <div class="col-md-8">
                                <?php
                                if(!$show_input){
                                ?>

                                <button type="button" class="btn btn-block btn-secondary btn-flat" disabled>
                                    <em class="pull-left"><small>Purchase Receipt Selected</small></em>
                                    <strong id="pr_amount" class="pull-right">0</strong>
                                </button>

                                <?php
                                }else{
                                ?>

                                <button type="button" id="btnpreceipt" class="btn btn-block btn-success btn-flat">
                                    <em class="pull-left"><small>Click to view tagged Purchase Receipt</small></em>
                                    <strong id="pr_amount" class="pull-right">0</strong>
                                </button>

                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class = "row">
                    <div class = "col-md-12">
                        <?php
                        echo $ui->formField('textarea')
                        ->setLabel('Notes:')
                        ->setSplit('col-md-2', 'col-md-10')
                        ->setName('remarks')
                        ->setId('remarks')
                        ->setValue($notes)
                        ->draw($show_input);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="item_tableList" class="table table-hover table-striped table-sidepad">
                            <thead>
                                <tr class="info">
                                    <?php
                                        if ($show_input) {
                                    ?>
                                    <th class="col-md-1"><input type="checkbox" class="checkall"></th>
                                    <?php
                                        }
                                    ?>
                                    
                                    <th class="col-md-2">PR No.</th>
                                    <th class="col-md-2">Item</th>
                                    <th class="col-md-4">Description</th>
                                    <th class="col-md-2 text-center">Qty</th>
                                    <th class="col-md-1 text-right">UOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">No Records Found</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                            </tfoot>
                        </table>
                        <div id="item_pagination"></div>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php
                        if($show_input){
                        ?>
                        <div class="btn-group">
                            <button type="button" data-moduleurl="<?=MODULE_URL?>" class="btn btn-primary btn-flat" id="btnSave">Save</button>
                        </div>
                        <?php
                        }
                        ?>
                        &nbsp;&nbsp;&nbsp;
                        <div class="btn-group">
                            <a href="<?=MODULE_URL?>" class="btn btn-default btn-flat" id="btnCancel">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div id="pr_list_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Purchase Receipt List</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-8">
                            <div class="input-group">
                                <input id="pr_table_search" class="form-control pull-right" placeholder="Search" type="text">
                                <div class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table id="pr_tableList" class="table table-hover table-sidepad no-margin-bottom">
                        <thead>
                            <tr class="info">
                                <th><input type="checkbox" class="checkall text-center col-md-1" style="width:100px;"></th>
                                <th class="col-xs-3">PR No.</th>
                                <th class="col-xs-4">IPO No.</th>
                                <th class="col-xs-3">Date</th>
                                <th class="col-xs-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Loading Items</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="pr_pagination"></div>
                </div>
                <div class="modal-footer">
                    <button id="btn_ipo_select" class="btn btn-primary btn-flat">Confirm</button>
                    <button id="btn_modal_close" class="btn btn-default btn-flat">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="item_serial" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Items Serialized</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-8">
                            <div class="input-group">
                                <input id="serial_table_search" class="form-control pull-right" placeholder="Search" type="text">
                                <div class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table id="qty_tableList" class="table table-hover table-sidepad no-margin-bottom">
                        <thead>
                            <tr class="info">
                                <th><input type="checkbox" class="checkall text-center col-md-1" style="width:100px;"></th>
                                <th class="col-xs-3">Serial No.</th>
                                <th class="col-xs-6">Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Loading Items</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="serial_pagination"></div>
                </div>
                <div class="modal-footer">
                    <button id="btn_ipo_select" class="btn btn-primary btn-flat">Confirm</button>
                    <button id="btn_modal_close" class="btn btn-default btn-flat">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</section>

        
<script type="text/javascript">
    var ajax            = {};
    var selected_holder = [];
    var selected  = {'pr_no'    :[],
                    'pr_item'   :[],
                    'itemcode'  :[],
                    'qty'       :[],
                    'linenum'   :[]
                    };
    var task            = "<?=$task?>";
    var job_no          = "<?=$job_no?>";

    function getList() {
        ajax.limit = 5;
        $('#pr_tableList tbody').html(`<tr><td colspan="5" class="text-center">Loading Items</td></tr>`);
        $('#pr_pagination').html('');
        $('#pr_list_modal').modal('show');
        $.post('<?=MODULE_URL?>ajax/ajax_load_pr_list', ajax, function(data) {
            $('#pr_tableList tbody').html(data.table);
            $('#pr_pagination').html(data.pagination);
            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
                
            }
        });
    }
    
    function getItemList(pr ,task, job=""){
        pr          = pr.sort(); 
        ajax.limit  = 5;
        $('#pr_pagination').html('');
        
        $.post('<?=MODULE_URL?>ajax/ajax_load_pr_items', {ajax, pr:pr,task:task,job:job},function (data) {
            $('#item_tableList tbody').html(data.table);
            $('#item_pagination').html(data.pagination);
            console.log(data);
            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
            }
        });
        $('#pr_list_modal').modal('hide');
    }

    // save prev selected checkbox
    function saveSelected(){
        
        $("#item_tableList tbody tr").each(function(){
            pr          = $(this).find("input:checkbox").data("pr");
            itemcode    = $(this).find("input:checkbox").data("itemcode");
            linenum     = $(this).find("input:checkbox").data("linenum");
            qty         = $(this).find(".quantity").val();
            if ($(this).find("input:checkbox").is(":checked")) {
                for(var i=0; i<selected.itemcode.length; i++){
                    if (pr          == selected.pr_item[i] && 
                        itemcode    == selected.itemcode[i] && 
                        linenum     == selected.linenum[i]) 
                    {
                        selected.qty[i] = qty;
                    }
                }
            }
        });
    }

    // check checkbox prev selected 
    function checkExistingPR(){
        $.each($("#pr_tableList tbody tr td input:checkbox"),function(){
            if ($.inArray($(this).data("prno"), selected.pr_no) != -1) {
                $(this).iCheck("check");
            }
        });
    }

    function checkSelectedItems(){
        if (selected.itemcode.length>0) {
            $.each($("#item_tableList tbody tr td input:checkbox"),function(index, value){
                data_pr         = $(this).data("pr");
                data_itemcode   = $(this).data("itemcode");
                data_linenum    = $(this).data("linenum");

                for(var i=0; i<selected.itemcode.length; i++){
                    if (data_itemcode   == selected.itemcode[i] && 
                        data_pr         == selected.pr_item[i] &&
                        data_linenum    == selected.linenum[i]) 
                    {
                        $(this).iCheck("check");
                        $(this).closest("tr").find(".quantity").val(selected.qty[i]);
                    }
                }
            });
        }
    }

    function checkholder(){

        $.each($("#pr_tableList tbody tr td input:checkbox"),function(){
            if ($.inArray($(this).data("prno"), selected_holder) != -1) {
                $(this).iCheck("check");
            }
        });
    }

    function disabledfields(){
        $("input:checkbox").closest("td").remove();
        $(".quantity").prop("disabled", true);
    }

    function viewItemList(){
        $("#item_tableList tbody").empty();
        for(var i=0; i<selected.itemcode.length; i++){
            $("#item_tableList tbody").append("<tr><td>"+ selected.pr_item[i] +"</td><td></td><td>"+ selected.itemcode[i] +"</td><td class='text-right'>"+ selected.qty[i] +"</td></tr>");
        }
        
    }
        
    $(document).ready(function (){

        $("#pr_amount").text(selected.pr_no.length);

        $("#pr_tableList tbody").on("ifChecked", "input:checkbox", function(){
            prno = $(this).data("prno");
            in_array = false;

            for(var i=0; i<selected_holder.length; i++){
                if (selected_holder[i] == prno) {
                    in_array = true;
                }
            }
            if (!in_array) {
                selected_holder.push(prno);
            }
            
        });

        $("#pr_tableList tbody").on("ifUnchecked", "input:checkbox", function(){
            
            index = selected_holder.indexOf($(this).data("prno"));
            selected_holder.splice(index, 1);
        });

        $("#item_tableList tbody").on("ifChecked", "input:checkbox", function(){
            pr          = $(this).data("pr");
            itemcode    = $(this).data("itemcode");
            linenum     = $(this).data("linenum");
            qty         = $(this).closest("tr").find(".quantity").val();
            in_array    =false;

            for(var i=0; i<selected.pr_item.length; i++){
                if (pr          == selected.pr_item[i] && 
                    itemcode    == selected.itemcode[i] && 
                    linenum     == selected.linenum[i]) 
                {
                    in_array = true;
                }
            }

            if (!in_array) {
                    selected.pr_item.push(pr);
                    selected.itemcode.push(itemcode);
                    selected.linenum.push(linenum);
                    selected.qty.push(qty);
            } 
        });

        $("#item_tableList tbody").on("ifUnchecked", "input:checkbox", function(){
            pr          = $(this).data("pr");
            itemcode    = $(this).data("itemcode");
            linenum     = $(this).data("linenum");
            for(var i=0; i<selected.pr_item.length; i++){

                if (pr          == selected.pr_item[i] && 
                    itemcode    == selected.itemcode[i] && 
                    linenum     == selected.linenum[i]) 
                {
                    selected.pr_item.splice(i,1);
                    selected.itemcode.splice(i,1);
                    selected.linenum.splice(i,1);
                    selected.qty.splice(i,1)
                }

            }
        });

        // load IPO in modal
        $('#btnpreceipt').on('click', function() {
            selected_holder = selected.pr_no;
            getList();
        });

        // IPO modal confirm event
        $("#btn_ipo_select").on("click", function(){
            selected.pr_no = selected_holder;
            selected_holder = [];
            saveSelected();
            $("#pr_amount").text(selected.pr_no.length);
            if (task=="update") {
                getItemList(selected.pr_no, task, job_no);
            }
            else
                getItemList(selected.pr_no, task);
        });
        $('#pr_pagination').on('click', 'a', function(e) {
            e.preventDefault();
            ajax.page = $(this).attr('data-page');
            checkholder();
            getList();
        });
        $('#item_pagination').on('click', 'a', function(e) {
            e.preventDefault();
            ajax.page = $(this).attr('data-page');
            saveSelected();
            if (task=="update") {
                getItemList(selected.pr_no, task, job_no);
            }
            else
                getItemList(selected.pr_no, task);
              
        });
        // close modal
        $('#btn_modal_close').on('click', function() {
            $('#pr_list_modal').modal('hide');
        });

        // IPO table search event
        $("#pr_table_search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#pr_tableList tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $("#item_tableList").on("input", ".quantity", function(){
            inputqty = $(this).val();
            maxqty = $(this).data("maxval");
            if (inputqty>maxqty || inputqty =="" || inputqty <1) {
                $(this).closest("div").addClass("has-error");
            }
            else
                $(this).closest("div").removeClass("has-error");
        });


        $('#jobForm #btnSave').on('click', function () {
            no_error        = true;
            error_message   = "";
            

            if ($("#item_tableList tbody input:checked").length<1) {
                no_error        = false;
                error_message   = 'No selected items. Please select items to proceed.';
            }
            else{
                $.each($("#item_tableList tbody tr"),function(index, value){
                    if ($(this).find("input:checkbox").is(":checked")) {
                        if($(this).find("input.quantity").val()=="" || $(this).find("input.quantity").val()<1){
                            no_error        = false;
                            error_message   = 'Please make sure quantity of selected items is greater than 1.';
                        }
                        else if($(this).find(".form-group").hasClass("has-error")) {
                            no_error        = false;
                            error_message   = 'Please make sure quantity of selected items don\'t exceed quantity in Purchase Receipt.';
                        }
                    }
                });
            }
            
            if (no_error) {
                $.each($("#item_tableList tbody tr"),function(index, value){
                    if (!$(this).find("input:checkbox").is(":checked")) {
                        $(this).find("input:hidden").remove();
                        $(this).find("input.quantity").removeAttr("name");
                    }
                });
                if ($('#jobform').find('.form-group.has-error').length == 0) {
                    $.post('<?=MODULE_URL?>ajax/<?=$task?>', $("#jobForm").serialize(),
                        function (data) {
                            console.log(data);
                            if (data.query1 && data.query2 && data.query3 && data.delquery1 && data.delquery2 || data.query1 && data.query2 && data.query3 && task == "save") {
                                $('#delay_modal').modal('show');
                                setTimeout(function () {window.location = "<?=MODULE_URL?>";}, 1000);
                            }
                            
                        });
                }
            }
            else
                $('#warning_modal').modal('show').find('#warning_message').html(error_message);
            console.log(no_error);
            console.log($("#jobForm").serialize());
            
        });
    });
</script>
<?php
    
    if($task=="update"){
        echo "<script>";
        
        foreach ($pr as $key => $value) {
            echo '
                in_array = false;
                for(var i=0; i<selected.pr_no.length; i++){
                    if (selected.pr_no[i]=="'.$pr[$key].'"){
                        in_array = true;
                    }
                }
                if(!in_array){
                    selected.pr_no.push("'.$pr[$key].'");
                }
                selected.pr_item.push("' . $pr[$key] . '");
                selected.itemcode.push("' . $item[$key] . '");
                selected.linenum.push("' . $linenum[$key] . '");
                selected.qty.push("' . $qty[$key] . '");';
        }
        echo '  getItemList(selected.pr_no, task, job_no);
            </script>';
    }
    elseif($task=="view"){
        
        echo "<script>";
        
        foreach ($pr as $key => $value) {
            echo '
                in_array = false;
                for(var i=0; i<selected.pr_no.length; i++){
                    if (selected.pr_no[i]=="'.$pr[$key].'"){
                        in_array = true;
                    }
                }
                if(!in_array){
                    selected.pr_no.push("'.$pr[$key].'");
                }
                selected.pr_item.push("' . $pr[$key] . '");
                selected.itemcode.push("' . $item[$key] . '");
                selected.linenum.push("' . $linenum[$key] . '");
                selected.qty.push("' . $qty[$key] . '");';
        }
        echo '  viewItemList();
                
            </script>';

    }
?>