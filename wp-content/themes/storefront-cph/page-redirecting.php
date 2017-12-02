<?php
/**
* This is the page that redirects to TouchNet
*
* It takes the submitted data from the checkout process to POST data, which is
* the only way TouchNet allows data to be set.
*/

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
?><!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>

    <style type="text/css">
      html, body {
        height: 100%;
      }

      #redirect {
        text-align: center;
        position: relative;
      }

      #redirect .loader {
        margin: 100px auto;
        font-size: 25px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: relative;
        text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
      }

      @-webkit-keyframes load5 {
        0%,
        100% { box-shadow: 0em -2.6em 0em 0em #13294B, 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.5), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.7); }
        12.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.7), 1.8em -1.8em 0 0em #13294B, 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.5); }
        25% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.5), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.7), 2.5em 0em 0 0em #13294B, 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        37.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.5), 2.5em 0em 0 0em rgba(19, 41, 75, 0.7), 1.75em 1.75em 0 0em #13294B, 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        50% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.5), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.7), 0em 2.5em 0 0em #13294B, -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        62.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.5), 0em 2.5em 0 0em rgba(19, 41, 75, 0.7), -1.8em 1.8em 0 0em #13294B, -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        75% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.5), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.7), -2.6em 0em 0 0em #13294B, -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        87.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.5), -2.6em 0em 0 0em rgba(19, 41, 75, 0.7), -1.8em -1.8em 0 0em #13294B; }
      }
      @keyframes load5 {
        0%,
        100% { box-shadow: 0em -2.6em 0em 0em #13294B, 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.5), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.7); }
        12.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.7), 1.8em -1.8em 0 0em #13294B, 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.5); }
        25% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.5), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.7), 2.5em 0em 0 0em #13294B, 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        37.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.5), 2.5em 0em 0 0em rgba(19, 41, 75, 0.7), 1.75em 1.75em 0 0em #13294B, 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        50% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.5), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.7), 0em 2.5em 0 0em #13294B, -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.2), -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        62.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.5), 0em 2.5em 0 0em rgba(19, 41, 75, 0.7), -1.8em 1.8em 0 0em #13294B, -2.6em 0em 0 0em rgba(19, 41, 75, 0.2), -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        75% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.5), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.7), -2.6em 0em 0 0em #13294B, -1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2); }
        87.5% { box-shadow: 0em -2.6em 0em 0em rgba(19, 41, 75, 0.2), 1.8em -1.8em 0 0em rgba(19, 41, 75, 0.2), 2.5em 0em 0 0em rgba(19, 41, 75, 0.2), 1.75em 1.75em 0 0em rgba(19, 41, 75, 0.2), 0em 2.5em 0 0em rgba(19, 41, 75, 0.2), -1.8em 1.8em 0 0em rgba(19, 41, 75, 0.5), -2.6em 0em 0 0em rgba(19, 41, 75, 0.7), -1.8em -1.8em 0 0em #13294B; }
      }
    </style>

    <script type="text/javascript">
    window.onload = function(){
      document.forms['redirect'].submit();
    }
    </script>
  </head>

  <body <?php body_class(); ?>>

    <form id="redirect" method="post" action="https://secure.touchnet.com:8443/C21551test_upay/web/index.jsp" name="patron_form">
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
      <div class="loader"></div>
      <input value="Click here if the site is taking too long to redirect" class="art-button" type="submit"></input>
    </form>

    <?php wp_footer(); ?>

  </body>
</html>
