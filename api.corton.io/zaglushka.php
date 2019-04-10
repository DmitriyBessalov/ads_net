<?php
$_GET = array_map('addslashes', $_GET);
$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
$data= $db->query("SELECT `code` FROM `zag_".$_GET['tizer']."` WHERE `id`='".$_GET['id']."'")->fetch(PDO::FETCH_COLUMN);
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