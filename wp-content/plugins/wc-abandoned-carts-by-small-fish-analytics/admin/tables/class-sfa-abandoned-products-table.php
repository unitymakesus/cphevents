<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Products_Table extends SFA_WP_List_Table
{
    private $carts;
    protected $start_date;
    protected $end_date;

    function __construct($start_date, $end_date) {
        parent::__construct(array(
			'ajax' => false
		));

        $this->start_date = $start_date;
        $this->end_date = $end_date;

        include_once('class-sfa-abandoned-carts-product-table-listing.php');
        $this->prepare_data();

        $this->render();
    }

    private function prepare_data() {
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($this->get_columns(), array(), $sortable);

		$data = new SFA_Abandoned_Carts_Table($this->start_date, $this->end_date, false);
		$data->prepare_items();

        $bucketed_data = array();

        foreach ($data->carts as $cart) {
			if (!$cart->get_cart_is_recovered() && (time() - $cart->get_cart_expiry_raw()) > (15 * 60)) {
				foreach ($cart->get_cart_items() as $item) {
					if (!isset($bucketed_data[$item->id])) {
						$link = '<a href="' . $item->link . '">' . $item->description;
						$bucketed_data[$item->id] = new SFA_Abandoned_Carts_Product_Table_Listing($link, $item->price, $item->quantity);
					}
					else
					{
						$bucketed_data[$item->id]->add($item->price, $item->quantity);
					}
				}
			}
		}

        usort($bucketed_data, array(&$this, 'sort'));
        $this->items = $bucketed_data;

		$per_page = 30;
	 	$current_page = $this->get_pagenum();
		$total_items = count($this->items);
		
		$this->items = array_slice($this->items,(($current_page-1)*$per_page),$per_page);
		
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => ceil($total_items/$per_page)
			)
		);
	}

    function sort($a, $b) {
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'amount';
		
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
		
		if ($orderby == 'count') {
			$result = strnatcmp($a->count, $b->count);
		}
        else {
            $result = strnatcmp($a->total, $b->total);
        }
		
		return ($order === 'asc') ? $result : -$result;
	}

    function get_columns() {
		$columns = array(
            'product' => 'Product',
            'count' => 'Count',
            'amount' => 'Total Amount'
		);
		
		return $columns;
	}

    function get_sortable_columns() {
        $columns = array(
            'count' => array('count', false),
            'amount' => array('amount', true)
		);
		
		return $columns;
    }

    function column_product($item) {
		return $item->title;
	}

    function column_count($item) {
		return number_format($item->count);
	}

    function column_amount($item) {
        return wc_price($item->total);
	}

    private function render() {
        ?>
        <div class="sfa_wrap">	
			<div id="sfa_chart_title" style="float: left;">
				<h2>Abandoned Products Data</h2>		
			</div>
			<div id="sfa_date_picker_form" style="float: right; margin-top: 0.75em;">
				<form name="sfa_update_report" action="?page=sfa-abandoned-carts&tab=sfa_products" method="post">
					<label class="sfa_update_report_label">From</label>
					<input class="sfa_update_report_item" type="start_date" id="sfa_report_start_date" name="sfa_report_start_date" value="<?php echo($this->start_date); ?>" />
					<label class="sfa_update_report_label">to</label>
					<input class="sfa_update_report_item" type="end_date" id="sfa_report_end_date" name="sfa_report_end_date" value="<?php echo($this->end_date); ?>" />
					<input value="Refresh" type="submit" id="sfa_refresh_report_button" />
				</form>

				<script type='text/javascript'>
					jQuery(document).ready(function() {
		
						jQuery('#sfa_report_start_date').datepicker({
							dateFormat : 'yy-mm-dd'
						});
		
						jQuery('#sfa_report_end_date').datepicker({
							dateFormat : 'yy-mm-dd'
						});
		
					}); 
				</script>
			</div>
			<div style="clear: both;"></div>
			<br />
			<?php $this->display(); ?>
		</div>
        <?php
    }
}

?>