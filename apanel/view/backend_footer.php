
				<section class="footer-padding"></section>
			</div>
			<div id="modal_div">
			
			</div>
			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>Version</b> 0.9.1
				</div>
				<?php
					$startyear		= 2017;
					$currentyear	= '';
					if ($startyear < date('Y')) {
						$currentyear = ' - ' . date('Y');
					}
				?>
				<strong>Copyright &copy; <?=$startyear?><?=$currentyear?> <a href="http://cid-systems.com">Cid Systems</a>.</strong> All rights reserved.
			</footer>
			<div class="control-sidebar-bg"></div>
		</div>				
		<div id="monthly_datefilter"></div>
		<script src="<?= BASE_URL ?>assets/js/site.js"></script>
	</body>
</html>