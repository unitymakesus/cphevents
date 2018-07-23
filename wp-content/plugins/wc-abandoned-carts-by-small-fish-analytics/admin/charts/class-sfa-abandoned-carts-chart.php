<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
	
abstract class SFA_Abandoned_Carts_Chart
{
	protected $title;
	protected $chart_div;
	protected $abandoned_data;
	protected $recovered_data;
	protected $abandoned_count_data;
	protected $recovered_count_data;
	protected $recovery_rate_data;
	
	function render() { 
		?>
		<script type="text/javascript">

			jQuery(function() {

				var abandoned = <?php echo($this->abandoned_data); ?>;
				var recovered = <?php echo($this->recovered_data); ?>;
				var abandoned_count = <?php echo($this->abandoned_count_data); ?>;
				var recovered_count = <?php echo($this->recovered_count_data); ?>;
				var recovery_rate = <?php echo($this->recovery_rate_data); ?>;
				 
				jQuery("#sfa_total_abandoned_amount").click(function() {
					prefix = '<?php echo(get_woocommerce_currency_symbol()); ?>';
					suffix = '';
					hover = 'abandoned on';
					jQuery("#sfa_chart_title_container").text("Abandoned Amount");
					plot(abandoned, "#5a3bb3", prefix, suffix, hover);
				});
				
				jQuery("#sfa_total_recovered_amount").click(function() {
					prefix = '<?php echo(get_woocommerce_currency_symbol()); ?>';
					suffix = '';
					hover = 'recovered on';
					jQuery("#sfa_chart_title_container").text("Recovered Amount");
					plot(recovered, "#0192a5", prefix, suffix, hover);
				});
				
				jQuery("#sfa_total_abandoned_count").click(function() {
					prefix = '';
					suffix = '';
					hover = 'abandoned on';
					jQuery("#sfa_chart_title_container").text("Abandoned Carts");
					plot(abandoned_count, "#01a300", prefix, suffix, hover);
				});
				
				jQuery("#sfa_total_recovered_count").click(function() {
					prefix = '';
					suffix = '';
					hover = 'recovered on';
					jQuery("#sfa_chart_title_container").text("Recovered Carts");
					plot(recovered_count, "#cd4c26", prefix, suffix, hover);
				});
				
				jQuery("#sfa_total_recovery_rate").click(function() {
					prefix = '';
					suffix = '%';
					hover = 'recovery rate on'
					jQuery("#sfa_chart_title_container").text("Recovery Rate");
					plot(recovery_rate, "#ea8906", prefix, suffix, hover, 100);
				});
				
				var prefix = '<?php echo(get_woocommerce_currency_symbol()); ?>';
				var suffix = '';
				var hover = 'abandoned on';
				jQuery("#sfa_chart_title_container").text("Abandoned Amount");
				plot(abandoned, "#5a3bb3", prefix, suffix);
				
				jQuery("<div id='tooltip'></div>").css({
					position: "absolute",
					display: "none",
					border: "1px solid rgba(37,90,140,1)",
					padding: "10px",
					"background-color": "rgba(54,151,220,0.9)"
				}).appendTo("body");
		
				jQuery(<?php echo($this->chart_div); ?>).bind("plothover", function (event, pos, item) {
					if (item) {
						var x = item.datapoint[0];
						var y = item.datapoint[1].toFixed(2);
						
						if (suffix == "%") {
							y = item.datapoint[1].toFixed(1);
						}
						else if (prefix != "$") {
							y = Math.round(y);
						}

						var date = new Date(x);
						var dateString = date.getUTCFullYear() + "-" + (date.getUTCMonth() + 1)+ "-" + date.getUTCDate();
						
						jQuery("#tooltip").html(prefix + y + suffix + " " + hover + " " + dateString)
							.css({top: item.pageY-100, left: item.pageX-75})
							.fadeIn(50);
					} else {
						jQuery("#tooltip").hide();
					}
				});

				
			});
			
			function plot(dataSet, color, prefix, suffix, hover, ylimit) {
				jQuery.plot(<?php echo($this->chart_div); ?>, [dataSet],
				{
					series: {
			           	lines: { 
							show: true, 
							fill: true
						},
						points: {
							show: true
						}
			        },
					colors: [color],
			        grid: { 
						hoverable: true,
						borderWidth: 0,
						labelMargin: 20,
						margin: 15
					},
			        xaxis: { 
						mode: 'time',
						timeformat: '%y/%m/%d',
						show: false,
						tickLength: 0,
						font: {
							color: 'black',
							size: 15,
						},
						tickFormatter: function(val, axis) {
							var d = new Date(val);
							return d.getFullYear() + '-' + d.getDay() + '-' + d.getMonth();
						}
			        },
			        yaxis: { 
						show: true,
						tickLength: 0,
						color: '#fff',
						min: 0,
						max: ylimit,
						font: {
							color: 'black',
							size: 15
						},
						tickFormatter: function(val, axis) {
							return prefix + val.toFixed(0).toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",") + suffix;
						}
			        }
				});
			}

			</script>
			<div id="<?php echo($this->chart_div); ?>" class="sfa_chart"></div>
	<?php
	}
	
	abstract function calculate_data($data = array());
}
?>