<?php
/*
 * pagination.php
 * Used on pages with just the $page passed in the URL
 */
class paginator {
	/**
	 * Purpose: set the number of items per page.
	 * @var numeric
	*/
	private $_perPage;

	/**
	 * Purpose: set get parameter for fetching the page number
	 * @var string
	*/
	private $_instance;

	/**
	 * Purpose: sets the page number.
	 * @var numeric
	*/
	private $_page;

	/**
	 * Purpose: set the limit for the data source
	 * @var string
	*/
	private $_limit;

	/**
	 * Purpose: set the total number of records/items.
	 * @var numeric
	*/
	private $_totalRows = 0;

	/**
	 * Function: __construct
	 * Purpose: pass values when class is istantiated
	 *
	 * @param numeric  $_perPage  sets the number of iteems per page
	 * @param numeric  $_instance sets the instance for the GET parameter
	 */
	public function __construct($perPage,$instance) {
		$this->_instance = $instance;
		$this->_perPage = $perPage;
		$this->set_instance();
	}

	/**
	 * Function: get_start
	 * Purpose: creates the starting point for limiting the dataset
	 *
	 * @return numeric
	*/
	private function get_start() {
		return ($this->_page * $this->_perPage) - $this->_perPage;
	}

	/**
	 * Function: set_instance
	 * Purpose: sets the instance parameter, if numeric value is 0 then set to 1
	 *
	 * @var numeric
	*/
	private function set_instance() {
		$this->_page = (int) (!isset($_GET[$this->_instance]) ? 1 : $_GET[$this->_instance]);
		$this->_page = ($this->_page == 0 ? 1 : $this->_page);
	}

	/**
	 * Function: set_total
	 * Purpose: collect a numeric value and assigns it to the totalRows
	 *
	 * @var numeric
	*/
	public function set_total($_totalRows) {
		$this->_totalRows = $_totalRows;
	}

	/**
	 * Function: get_limit
	 * Purpose: returns the limit for the data source, calling the get_start method and passing in the number of items per page
	 *
	 * @return string
	*/
	public function get_limit() {
		return "LIMIT ".$this->get_start().",$this->_perPage";
	}

	/**
	 * Function: page_links
	 * Purpose: create the html links for navigating through the dataset
	 *
	 * @var sting $path optionally set the path for the link
	 * @var sting $ext optionally pass in extra parameters to the GET
	 * @return string returns the html menu
	*/
	public function page_links($path='&', $ext=null) {
		$adjacents = "2";
		$prev = $this->_page - 1;
		$next = $this->_page + 1;
		$lastpage = ceil($this->_totalRows/$this->_perPage);
		$lpm1 = $lastpage - 1;
		
		// Localization Globals
		global $previousLink;
		global $nextLink;

		$pagination = "";

		if($lastpage > 1) {
			$pagination .= "<div class='pagination'>";
		}

		if ($this->_page > 1) {
			$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$prev"."$ext'><i class='fa fa-angle-double-left'></i> ".$previousLink."</a>";
		} else {
			$pagination.= "<span class='disabled'><i class='fa fa-angle-double-left'></i>  ".$previousLink."</span>";
		}

		if ($lastpage < 7 + ($adjacents * 2)) {
			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $this->_page) {
					$pagination.= "<span class='current'>$counter</span>";
				} else {
					$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$counter"."$ext'>$counter</a>";
				}
			}
		} else if ($lastpage > 5 + ($adjacents * 2)) {
			if ($this->_page < 1 + ($adjacents * 2)) {
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if ($counter == $this->_page) {
						$pagination.= "<span class='current'>$counter</span>";
					} else {
						$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$counter"."$ext'>$counter</a>";
					}
				}
				$pagination.= "&hellip;";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$lpm1"."$ext'>$lpm1</a>";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$lastpage"."$ext'>$lastpage</a>";
			} else if ($lastpage - ($adjacents * 2) > $this->_page && $this->_page > ($adjacents * 2)) {
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=1"."$ext'>1</a>";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=2"."$ext'>2</a>";
				$pagination.= "&hellip;";
				for ($counter = $this->_page - $adjacents; $counter <= $this->_page + $adjacents; $counter++) {
					if ($counter == $this->_page) {
						$pagination.= "<span class='current'>$counter</span>";
					} else {
						$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$counter"."$ext'>$counter</a>";
					}
				}
				$pagination.= "&hellip;";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$lpm1"."$ext'>$lpm1</a>";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$lastpage"."$ext'>$lastpage</a>";
			} else {
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=1"."$ext'>1</a>";
				$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=2"."$ext'>2</a>";
				$pagination.= "&hellip;";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $this->_page) {
						$pagination.= "<span class='current'>$counter</span>";
					} else {
						$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$counter"."$ext'>$counter</a>";
					}
				}
			}
		}

		if ($this->_page < $counter - 1) {
			$pagination.= "<a href='index.php?page=".$_GET['page'].$path."$this->_instance=$next"."$ext'>".$nextLink." <i class='fa fa-angle-double-right'></i></a>";
			$pagination.= "</div>\n";
		} else {
			$pagination.= "<span class='disabled'>".$nextLink." <i class='fa fa-angle-double-right'></i></span>";
			$pagination.= "</div>\n";
		}
		return $pagination;
	}
}
?>