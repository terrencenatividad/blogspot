<?php
class pagination {

	private $page		= 0;
	private $page_limit	= 0;

	public function __construct() {
		$this->page = isset($_POST['page']) ? $_POST['page'] : '';
	}

	public function setPageLimit($page_limit) {
		$this->page_limit = $page_limit;
	}

	public function setPage($page) {
		$this->page = $page;
	}

	public function draw() {
		$inner_page				= 2;
		$inner_counter			= 0;
		$inner_counter_limit	= 5;
		$pagination = '';

		if ($this->page_limit > 1) {
			$pagination .= '  <ul class="pagination">
								<li>
									<a href="#">
										<span aria-hidden="true">&laquo;</span>
									</a>
								</li>
								<li><a href="#">1</a></li>';

			for (; $inner_page < $this->page_limit && $inner_counter < $inner_counter_limit; $inner_page++, $inner_counter++) {
				$pagination .= '<li><a href="#">' . $inner_page . '</a></li>';
			}

			if ($inner_page != $this->page_limit) {
				$pagination .= '<li><a>...</a></li>';
			}

			$pagination .= '
								<li><a href="#">' . $this->page_limit . '</a></li>
								<li>
									<a href="#">
										<span aria-hidden="true">&raquo;</span>
									</a>
								</li>
							</ul>';
		}

		return $pagination;
	}

}