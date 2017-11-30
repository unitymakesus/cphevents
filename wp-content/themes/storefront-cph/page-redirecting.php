<?php
$UPAY_SITE_ID = $_GET['UPAY_SITE_ID'];
$BILL_EMAIL_ADDRESS = $_GET['BILL_EMAIL_ADDRESS'];
$BILL_NAME = $_GET['BILL_NAME'];
$BILL_STREET1 = $_GET['BILL_STREET1'];
$BILL_STREET2 = $_GET['BILL_STREET2'];
$BILL_CITY = $_GET['BILL_CITY'];
$BILL_STATE = $_GET['BILL_STATE'];
$BILL_POSTAL_CODE = $_GET['BILL_POSTAL_CODE'];
$BILL_COUNTRY = $_GET['BILL_COUNTRY'];
$AMT = $_GET['AMT'];
$EXT_TRANS_ID = $_GET['EXT_TRANS_ID'];
$VALIDATION_KEY = $_GET['VALIDATION_KEY'];
?>
<form method="post" action="https://secure.touchnet.com:8443/C21551test_upay/web/index.jsp" name="patron_form">
  <input value="<?php echo $UPAY_SITE_ID; ?>" name="UPAY_SITE_ID" type="hidden"></input>
  <input value="<?php echo $BILL_EMAIL_ADDRESS; ?>" name="BILL_EMAIL_ADDRESS" type="hidden"></input>
  <input value="<?php echo $BILL_NAME; ?>" name="BILL_NAME" type="hidden"></input>
  <input value="<?php echo $BILL_STREET1; ?>" name="BILL_STREET1" type="hidden"></input>
  <input value="<?php echo $BILL_STREET2; ?>" name="BILL_STREET1" type="hidden"></input>
  <input value="<?php echo $BILL_CITY; ?>" name="BILL_CITY" type="hidden"></input>
  <input value="<?php echo $BILL_STATE; ?>" name="BILL_STATE" type="hidden"></input>
  <input value="<?php echo $BILL_POSTAL_CODE; ?>" name="BILL_POSTAL_CODE" type="hidden"></input>
  <input value="<?php echo $BILL_COUNTRY; ?>" name="BILL_COUNTRY" type="hidden"></input>
  <input value="<?php echo $AMT; ?>" name="AMT" type="hidden"></input>
  <input value="<?php echo $EXT_TRANS_ID; ?>" name="EXT_TRANS_ID" type="hidden"></input>
  <input value="<?php echo $VALIDATION_KEY; ?>" name="VALIDATION_KEY" type="hidden"></input>
  <input value="Click here if the site is taking too long to redirect" class="art-button" type="submit"></input>
</form>
