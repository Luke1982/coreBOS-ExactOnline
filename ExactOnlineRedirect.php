<?php
// This file has only one purpose: to redirect back to the handling file
// With the GET response code
header('index.php?module=ExactOnline&action=ExactOnlineAjax&file=test&code='.$_GET['code']);
?>