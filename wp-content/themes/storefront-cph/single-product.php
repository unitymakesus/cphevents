<?php
// Redirect to home page
$redirect = array('event_id' => get_the_ID());
wp_redirect('/?' . http_build_query($redirect));
exit;
