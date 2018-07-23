<div class="sfa_wrap">
	<h3>Thank You!</h3>
	<p>
		Thanks for downloading and using the plugin. 
	</p>
	<p>
		If you need any help, have feature requests or bug reports please
		shoot me an email <a href="mailto:mike@smallfishanalytics.com">mike@smallfishanalytics.com</a>.
	</p>
	<br />
	<br />
	<br />
	<p>
		<div style="min-width: 200px; margin-top: 2em">
		<p style="margin-bottom: -1em; font-weight: bold; color: Red;">Want to remove all the plugin data? Warning this can't be undone! </p>
		<form name="sfa_delete_carts" action="?page=sfa-abandoned-carts" method="post">
		<input type="hidden" name="sfa_delete_carts" />
		<div class="submit"><input id="sfa_delete_carts" type="submit" value="Permanently Delete All Abandoned Cart Data"/></div>
		<?php wp_nonce_field('sfa_delete_carts'); ?>
	</p>
</div>