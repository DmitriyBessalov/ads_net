<?php
if ($_SERVER['REQUEST_URI']=='/robots.txt'){echo "User-agent: *\nDisallow: /";exit;}
$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
if (isset($_GET['site'])){
    $parsed=parse_url($_GET['site']);
    setcookie("host", $parsed['host'],time()+72000,'/', ".corton.io");
    setcookie("scheme", $parsed['scheme'],time()+72000,'/' ,'.corton.io');
    $sql="SELECT `CTR`,`CPM`,`CPG` FROM `ploshadki` WHERE `domen`='".$parsed['host']."'";
    $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    echo '
<html>
    <head>
        <title>'.$_GET['site'].'</title>
    </head>
    <body style="overflow:hidden;margin: 0px;">
        <div style="height: 70px;overflow: hidden;">
            <div style="float: left; "><a href="https://corton.io/"><img style="margin: 10px;" src="https://panel.corton.io/images/logo-corton.png"></a></div>
            <div style="float: left;margin: 24px;">CTR '.$result['CTR'].' %</div>
            <div style="float: left;margin: 24px;">CPM '.$result['CPM'].' руб.</div>
            <div style="float: left;margin: 24px;">CPG '.$result['CPG'].' руб.</div>
            <a style="background-color: #116dd6;color: #fff;float: right; margin:15px;padding: 8px;">Виджет Recomendation</a>
            <a style="background-color: #116dd6;color: #fff;float: right; margin:15px;padding: 8px;">Виджет Native Preview</a>
        </div>
        <iframe id="frame" style="width: 100%;" src="iframe.php?url='.$_GET['site'].'">
        </iframe>
    </body>
    <script>
        elem=document.getElementById("frame");
        elem.style.height=document.body.scrollHeight-71+"px";
    </script>
</html>';
}else{
    if (strpos($_SERVER['REQUEST_URI'],'iframe.php?url=')) {
        $url=$URI=$_GET['url'];
    }else{
        $url=$URI=$_COOKIE['scheme'].'://'.$_COOKIE['host'].$_SERVER['REQUEST_URI'];
    }

    $sql="SELECT `promo_page` FROM `ploshadki` WHERE `domen`='".$_COOKIE['host']."'";
    $result = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

    if ($_SERVER['REDIRECT_URL']=='/promo'){
        $URI=$_COOKIE['scheme'].'://'.$result;
    }

    $outsite=$_COOKIE['scheme'].'://'.$_SERVER['HTTP_HOST'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URI);
    curl_setopt($ch, CURLOPT_ENCODING ,"UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $page=curl_exec($ch);
    curl_close($ch);

    //Замена ссылок
    $page= preg_replace('/<base.*?>/', '<base href="'.$outsite.'">', $page);
    $page = str_replace($_COOKIE['scheme'].'://'.$_COOKIE['host'], $outsite, $page);

    //Подключение скрипта
    $host=str_replace('.','_',$_COOKIE['host']);
    $page = str_replace('</head>', '<link href="https://api.corton.io/css/'.$host.'.css.gz" rel="stylesheet"><script async src="https://api.corton.io/js/corton.js" charset="UTF-8"></script></head>', $page);

    preg_match_all("/<meta.*?>/", $page, $phones);
    foreach ($phones[0] as $phone) {
        $phone=mb_strtolower($phone);
        if ((strpos($phone, 'content-type') !== false) and (strpos($phone, 'windows-1251') !== false)) {
            header('Content-Type: text/html; windows-1251; charset=windows-1251');
        }
    }

    $url=str_replace('http://','',$url);
    $url=str_replace('https://','',$url);
    $page = str_replace('<head>', '<head><script>var corton_url="'.$url.'";</script>', $page);
    echo $page;
}
