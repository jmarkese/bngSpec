<?php
if ($_GET['randomId'] != "l9OUIrRa8H96760I0SIhNXROAQd_G08EaWOigCaHTHag84ajTkX8KYLTSqh_MMF_") {
    echo "Access Denied";
    exit();
}

// display the HTML code:
echo stripslashes($_POST['wproPreviewHTML']);

?>  
