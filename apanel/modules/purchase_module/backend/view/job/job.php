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
                            <label for="btnIPO" class="control-label col-md-4">IPO Number </label>
                            <div class="col-md-8">
                                <?php
                                if(!$show_input){
                                ?>

                                <button type="button" class="btn btn-block btn-secondary btn-flat" disabled>
                                    <em class="pull-left"><small>Import Purchase Order Selected</small></em>
                                    <strong id="ipo_amount" class="pull-right">0</strong>
                                </button>

                                <?php
                                }else{
                                ?>

                                <button type="button" id="btnIPO" class="btn btn-block btn-success btn-flat">
                                    <em class="pull-left"><small>Click to view tagged Import Purchase Order</small></em>
                                    <strong id="ipo_amount" class="pull-right">0</strong>
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
                        ->setMaxLength(300)
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
                                    
                                    <th class="col-md-2">IPO No.</th>
                                    <th class="col-md-2">Item</th>
                                    <th class="col-md-2">Description</th>
                                    <th class="col-md-2 text-center">Qty Left</th>
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
                            echo $ui->drawSubmitDropdown($show_input); 
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

    <div id="ipo_list_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Import Purchase Order List</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-8">
                            <div class="input-group">
                                <input id="ipo_table_search" class="form-control pull-right" placeholder="Search" type="text">
                                <div class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table id="ipo_tableList" class="table table-hover table-sidepad no-margin-bottom">
                        <thead>
                            <tr class="info">
                                <th><input type="checkbox" class="checkall text-center col-md-1" style="width:100px;"></th>
                                <th class="col-xs-5">IPO No.</th>
                                <th class="col-xs-6">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Loading Items</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="ipo_pagination"></div>
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
    var selected  = {'ipo_no'           :[],
                    'ipo_item'          :[],
                    'itemcode'          :[],
                    'qty'               :[],
                    'qty_left'          :[],
                    'linenum'           :[],
                    'uom'               :[],
                    'detailparticular'  :[]
                    };
    var task            = "<?=$task?>";
    var job_no          = "<?=$job_no?>";

    function getList() {
        ajax.limit = 5;
        $('#ipo_tableList tbody').html(`<tr><td colspan="5" class="text-center">Loading Items</td></tr>`);
        $('#ipo_pagination').html('');
        $('#ipo_list_modal').modal('show');
        $.post('<?=MODULE_URL?>ajax/ajax_load_ipo_list', ajax, function(data) {
            $('#ipo_tableList tbody').html(data.table);
            $('#ipo_pagination').html(data.pagination);
            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
                
            }
        });
    }
    
    function getItemList(ipo ,task, job=""){
        ipo          = ipo.sort(); 
        ajax.limit  = 5;
        $('#item_pagination').html('');
        
        $.post('<?=MODULE_URL?>ajax/ajax_load_ipo_items', {ajax, ipo:ipo,task:task,job:job},function (data) {
            $('#item_tableList tbody').html(data.table);
            $('#item_pagination').html(data.pagination);
            
            if (ajax.page > data.page_limit && data.page_limit > 0) {
                ajax.page = data.page_limit;
            }
        });
        $('#ipo_list_modal').modal('hide');
    }

    // save prev selected checkbox
    function saveSelected(){
        
        $("#item_tableList tbody tr").each(function(){
            ipo         = $(this).find("input:checkbox").data("ipo");
            itemcode    = $(this).find("input:checkbox").data("itemcode");
            linenum     = $(this).find("input:checkbox").data("linenum");
            qty         = $(this).find(".quantity").val();
            if ($(this).find("input:checkbox").is(":checked")) {
                for(var i=0; i<selected.itemcode.length; i++){
                    if (ipo          == selected.ipo_item[i] && 
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
    function checkExistingIPO(){
        $.each($("#ipo_tableList tbody tr td input:checkbox"),function(){
            if ($.inArray($(this).data("ipono"), selected.ipo_no) != -1) {
                $(this).iCheck("check");
            }
        });
    }

    function checkSelectedItems(){
        if (selected.itemcode.length>0) {
            $.each($("#item_tableList tbody tr td input:checkbox"),function(index, value){
                data_ipo         = $(this).data("ipo");
                data_itemcode   = $(this).data("itemcode");
                data_linenum    = $(this).data("linenum");

                for(var i=0; i<selected.itemcode.length; i++){
                    if (data_itemcode   == selected.itemcode[i] && 
                        data_ipo        == selected.ipo_item[i] &&
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

        $.each($("#ipo_tableList tbody tr td input:checkbox"),function(){
            if ($.inArray($(this).data("ipono"), selected_holder) != -1) {
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
        console.log(selected);
        for(var i=0; i<selected.itemcode.length; i++){
            $("#item_tableList tbody").append("<tr><td>"+ selected.ipo_item[i] +"</td><td>"+ selected.itemcode[i] +"</td><td>"+ selected.detailparticular[i] +"</td><td class='text-right'>"+ selected.qty_left[i] +"</td><td class='text-right'>"+ selected.qty[i] +"</td><td>"+ selected.uom[i] +"</tr>");
        }
        
    }
        
    $(document).ready(function (){

        $("#ipo_amount").text(selected.ipo_no.length);

        $("#ipo_tableList tbody").on("ifChecked", "input:checkbox", function(){
            ipono = $(this).data("ipono");
            in_array = false;

            for(var i=0; i<selected_holder.length; i++){
                if (selected_holder[i] == ipono) {
                    in_array = true;
                }
            }
            if (!in_array) {
                selected_holder.push(ipono);
            }
            
        });

        $("#ipo_tableList tbody").on("ifUnchecked", "input:checkbox", function(){
            
            index = selected_holder.indexOf($(this).data("ipono"));
            selected_holder.splice(index, 1);
        });

        $("#item_tableList tbody").on("ifChecked", "input:checkbox", function(){
            ipo          = $(this).data("ipo");
            itemcode    = $(this).data("itemcode");
            linenum     = $(this).data("linenum");
            qty         = $(this).closest("tr").find(".quantity").val();
            in_array    =false;

            for(var i=0; i<selected.ipo_item.length; i++){
                if (ipo          == selected.ipo_item[i] && 
                    itemcode    == selected.itemcode[i] && 
                    linenum     == selected.linenum[i]) 
                {
                    in_array = true;
                }
            }

            if (!in_array) {
                    selected.ipo_item.push(ipo);
                    selected.itemcode.push(itemcode);
                    selected.linenum.push(linenum);
                    selected.qty.push(qty);
            } 
        });

        $("#item_tableList tbody").on("ifUnchecked", "input:checkbox", function(){
            ipo          = $(this).data("ipo");
            itemcode    = $(this).data("itemcode");
            linenum     = $(this).data("linenum");
            for(var i=0; i<selected.ipo_item.length; i++){

                if (ipo          == selected.ipo_item[i] && 
                    itemcode    == selected.itemcode[i] && 
                    linenum     == selected.linenum[i]) 
                {
                    selected.ipo_item.splice(i,1);
                    selected.itemcode.splice(i,1);
                    selected.linenum.splice(i,1);
                    selected.qty.splice(i,1)
                }

            }
        });

        // load IPO in modal
        $('#btnIPO').on('click', function() {
            selected_holder = selected.ipo_no;
            getList();
        });

        // IPO modal confirm event
        $("#btn_ipo_select").on("click", function(){
            selected.ipo_no = selected_holder;
            selected_holder = [];
            saveSelected();
            $("#ipo_amount").text(selected.ipo_no.length);
            if (task=="update") {
                getItemList(selected.ipo_no, task, job_no);
            }
            else
                getItemList(selected.ipo_no, task);
        });
        $('#ipo_pagination').on('click', 'a', function(e) {
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
                getItemList(selected.ipo_no, task, job_no);
            }
            else
                getItemList(selected.ipo_no, task);
              
        });
        // close modal
        $('#btn_modal_close').on('click', function() {
            $('#ipo_list_modal').modal('hide');
        });

        // IPO table search event
        $("#ipo_table_search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#ipo_tableList tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $("#item_tableList").on("change", ".quantity", function(){
            inputqty = $(this).val();
            maxqty = $(this).data("maxval");
            if (inputqty>maxqty) {
                $(this).val(maxqty);
            }
            if (inputqty =="" || inputqty <1) {
                $(this).closest("div").addClass("has-error");
            }
            else
                $(this).closest("div").removeClass("has-error");
        });


        $('#jobForm').on('click', '[type="submit"]', function (e) {
            e.preventDefault();
            var submit_data = '&' + $(this).attr('name') + '=' + $(this).val();
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
                            error_message   = 'Please make sure quantity of selected items don\'t exceed quantity in Import Purchase Order.';
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
                if ($('#jobform').find('.form-group.has-error').length == 0) {console.log('sample');
                            
                    $.post('<?=MODULE_URL?>ajax/<?=$task?>', $("#jobForm").serialize() + submit_data,
                        function (data) {
                            if (data.query1 && data.query2 && data.query3 && data.delquery1 && data.delquery2 || data.query1 && data.query2 && data.query3 && task == "save") {
                                $('#delay_modal').modal('show');
                                setTimeout(function () {window.location = data.redirect;}, 1000);
                            }
                            
                        });
                }
            }
            else
                $('#warning_modal').modal('show').find('#warning_message').html(error_message);
            console.log(submit_data);
            console.log($("#jobForm").serialize());
            
        });
    });
</script>
<?php
    
    if($task=="update"){
        echo "<script>";
        
        foreach ($ipo as $key => $value) {
            echo '
                in_array = false;
                for(var i=0; i<selected.ipo_no.length; i++){
                    if (selected.ipo_no[i]=="'.$ipo[$key].'"){
                        in_array = true;
                    }
                }
                if(!in_array){
                    selected.ipo_no.push("'.$ipo[$key].'");
                }
                selected.ipo_item.push("' . $ipo[$key] . '");
                selected.itemcode.push("' . $item[$key] . '");
                selected.linenum.push("' . $linenum[$key] . '");
                selected.qty.push("' . $qty[$key] . '");';

        }
        echo '  getItemList(selected.ipo_no, task, job_no);
            </script>';
    }
    elseif($task=="view"){
        
        echo "<script>";
        
        foreach ($ipo as $key => $value) {
            echo '
                in_array = false;
                for(var i=0; i<selected.ipo_no.length; i++){
                    if (selected.ipo_no[i]=="'.$ipo[$key].'"){
                        in_array = true;
                    }
                }
                if(!in_array){
                    selected.ipo_no.push("'.$ipo[$key].'");
                }
                selected.ipo_item.push("' . $ipo[$key] . '");
                selected.itemcode.push("' . $item[$key] . '");
                selected.linenum.push("' . $linenum[$key] . '");
                selected.qty_left.push("' . $qty_left[$key] . '");
                selected.qty.push("' . $qty[$key] . '");
                selected.uom.push("' . $uom[$key] . '");
                selected.detailparticular.push("' . $detailparticular[$key] . '");';
        }
        echo '  viewItemList();
                
            </script>';

    }
?>