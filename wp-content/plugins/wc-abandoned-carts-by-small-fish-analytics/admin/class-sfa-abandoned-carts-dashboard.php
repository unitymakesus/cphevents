<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Dashboard
{
	private $data;
	private $start_date;
	private $end_date;
	
	function __construct($start_date, $end_date) {
		$this->start_date = $start_date;
		$this->end_date = $end_date;
	}
	
	public function render() {
		$this->include_files();
		$this->register_scripts();
		$this->prepare_data();

		?>
		<div class="sfa_wrap">	
			<div id="sfa_announcement" style="float: left; margin-top: 1.25em;">
				<?php $json = @file_get_contents('https://smallfishanalytics.com/announcement.json'); 
				$data = json_decode($json, true);

				if (isset($data)) {
					echo($data['content']);
				}
        	?>
			</div>		
			<div id="sfa_date_picker_form" style="float: right; margin-top: 0.75em;">
				<form name="sfa_update_report" action="?page=sfa-abandoned-carts" method="post">
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
			
			<div id="sfa_counter_container">
				
				<div class="counter_widget">
					<?php
						$total_cart_amount_counter = new SFA_Abandoned_Carts_Total_Cart_Amount_Counter();
						$total_cart_amount_counter->calculate_data($this->data->carts);
						$total_cart_amount_counter->render();
					?>
				</div>
				
				<div class="counter_widget"> 
					<?php
						$total_carts_counter = new SFA_Abandoned_Carts_Total_Carts_Counter();
						$total_carts_counter->calculate_data($this->data->carts);
						$total_carts_counter->render();
					?>
				</div>
				

				<div class="counter_widget">
					<?php
						$total_cart_recovered_amount_counter = new SFA_Abandoned_Carts_Total_Cart_Recovered_Amount_Counter();
						$total_cart_recovered_amount_counter->calculate_data($this->data->carts);
						$total_cart_recovered_amount_counter->render();
					?>
				</div>
				
				<div class="counter_widget">
					<?php
						$total_cart_amount_recovered_counter = new SFA_Abandoned_Carts_Total_Carts_Recovered_Counter();
						$total_cart_amount_recovered_counter->calculate_data($this->data->carts);
						$total_cart_amount_recovered_counter->render();
					?>
				</div>

				<div class="counter_widget count_widget_right">
					<?php
						$recovery_rate_counter = new SFA_Abandoned_Carts_Recovery_Rate_Counter();
						$recovery_rate_counter->calculate_data($this->data->carts);
						$recovery_rate_counter->render();
					?>
				</div>
			</div>
			
			<div class="clear"></div>
			
			<div class="sfa_widget">
				<div class="sfa_table_title_container" id="sfa_chart_title_container">
					Abandoned Amount
				</div>
				<?php
					$abandoned_chart = new SFA_Abandoned_Carts_Abandoned_Chart($this->start_date, $this->end_date);
					$abandoned_chart->calculate_data($this->data->carts);
					$abandoned_chart->render();
				?>
			</div>
		<div>		
</div>
		<?php
	}	
	
	private function prepare_data() {
		$this->data = new SFA_Abandoned_Carts_Table($this->start_date, $this->end_date, false);
		$this->data->prepare_items();
	}
	
	private function include_files() {
		include_once('tables/class-sfa-abandoned-carts-table.php');
		include_once('counters/class-sfa-abandoned-carts-counter.php');
		include_once('counters/class-sfa-abandoned-carts-total-carts-counter.php');
		include_once('counters/class-sfa-abandoned-carts-total-cart-amount-counter.php');
		include_once('counters/class-sfa-abandoned-carts-total-carts-recovered-counter.php');
		include_once('counters/class-sfa-abandoned-carts-total-cart-recovered-amount-counter.php');
		include_once('charts/class-sfa-abandoned-carts-chart.php');
		include_once('charts/class-sfa-abandoned-carts-abandoned-chart.php');
		include_once('tables/class-sfa-abandoned-carts-product-table-listing.php');
		include_once('counters/class-sfa-abandoned-carts-recovery-rate-counter.php');
	}
	
	private function register_scripts() {
		wp_register_script('flot', plugins_url('../libraries/flot/jquery.flot.min.js', __FILE__), array('jquery'), '2.6.1');
		wp_enqueue_script('flot');
		wp_register_script('flot-time', plugins_url('../libraries/flot/jquery.flot.time.js', __FILE__), array('jquery'), '2.6.1');
		wp_enqueue_script('flot-time');
		wp_register_script('curved-lines', plugins_url('../libraries/flot/curvedLines.js', __FILE__), array('flot'));
		wp_enqueue_script('curved-lines');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('sfa-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
}