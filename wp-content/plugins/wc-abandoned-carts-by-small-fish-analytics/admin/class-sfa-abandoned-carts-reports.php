<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Reports 
{
	function __construct($start_date, $end_date) {
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('sfa-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
	
	public function render() {
		include_once('tables/class-sfa-abandoned-carts-table.php');
	
		$table = new SFA_Abandoned_Carts_Table($this->start_date, $this->end_date, false);
		$table->prepare_items();
		?>
		<div class="sfa_wrap">			
			<div id="sfa_chart_title" style="float: left;">
				<h2>Abandoned Carts Data</h2>		
			</div>
			<div id="sfa_date_picker_form" style="float: right; margin-top: 0.75em;">
				<form name="sfa_update_report" action="?page=sfa-abandoned-carts&tab=sfa_data" method="post">
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
			<?php $table->display(); ?>
		</div>
		
		<?php
	}
}