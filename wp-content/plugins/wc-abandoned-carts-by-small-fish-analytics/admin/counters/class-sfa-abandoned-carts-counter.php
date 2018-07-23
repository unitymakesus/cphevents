<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
	
abstract class SFA_Abandoned_Carts_Counter 
{
	protected $title;
	protected $count;
	protected $id;
	
	function render() {
		?>
		<div id="<?php echo($this->id); ?>" class="sfa_counter_container">
		<div class="sfa_widget_title_container">
		<span class="sfa_counter_title"><?php echo($this->title); ?></span>
		</div>
		<div class="sfa_counter_body">
		<?php echo($this->count); ?>
		</div>
		<div class="sfa_counter_click_explanation">
		Click To Chart Data
		</div>
		</div>
	<?php
	}
	
	abstract function calculate_data($data);
}
?>