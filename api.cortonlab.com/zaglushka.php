<?php
$_GET = array_map('addslashes', $_GET);
require_once('/var/www/www-root/data/db.php');
$data= $GLOBALS['db']->query("SELECT `code` FROM `zag_".$_GET['tizer']."` WHERE `id`='".$_GET['id']."'")->fetch(PDO::FETCH_COLUMN);
echo
'<!DOCTYPE html>
<html>
<body style="margin: 0px;">
'.$data.'
<script>
    window.onload = function() {
        var url = location.protocol+"//'.$_GET['host'].'";
        var str=\'{"corton_tizer":"'.$_GET['tizer'].'","height":"\'+document.body.scrollHeight+\'"}\';
        parent.postMessage(str,url);
    };
</script>
</body>
</html>';