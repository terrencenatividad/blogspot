<section class="content">
    <div class="box box-primary">

        <div class = "pageheader">
		    <span class = 'pagetitle'> Customer Listing </span>
		</div>

        <div class = "col-md-12">&nbsp;</div>
        
        <div class="box-header">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input name="table_search" class="form-control pull-right" placeholder="Search" type="text">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>

                <div class = "col-md-1 pull-right">
                    <input id = "deletelistBtn" type = "submit" name = "delete" value = "Delete" class="btn btn-danger btn-sm btn-flat width100">
                </div>

            </div>
        </div>

       <div class="box-body">
            <?php var_dump($list);?>
       </div>     

    </div>
</section>
