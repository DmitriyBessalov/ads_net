<?php
if ($_SERVER['REQUEST_URI']=='/robots.txt'){echo "User-agent: *\nDisallow: /";exit;}
$db = new PDO("mysql:host=185.75.90.54;dbname=corton", 'www-root', 'Do5aemub0e7893', array(PDO::ATTR_PERSISTENT => true));
if (isset($_GET['site'])){
    $parsed=parse_url($_GET['site']);
    setcookie("host", $parsed['host'],time()+72000,'/', ".corton.io");
    setcookie("scheme", $parsed['scheme'],time()+72000,'/' ,'.corton.io');
    $sql="SELECT `CTR`,`CPM`,`CPG`,`recomend_aktiv`,`natpre_aktiv` FROM `ploshadki` WHERE `domen`='".$parsed['host']."'";
    $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    echo '
<html style="scroll-behavior: smooth;">
    <head>
        <title>'.$_GET['site'].'</title>
		<link href="https://corton.io/css/corton-lp3.webflow.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="/scroll.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="js/jquery.fancy-scroll.js"></script>
    </head>
    <body style="overflow:hidden;margin: 0px;">
        <div style="height: 80px; overflow: hidden; min-width: 1020px; padding: 0px 30px; border-bottom: 1px solid #E0E1E5; background: #F4F6F9;">
            <div style="float: left; margin-right: 14px;"><a href="https://corton.io/">
			<a href="https://corton.io/platforms" target="_blank"><img style="margin: 15px;" src="https://panel.corton.io/images/logo-corton.png"></a></div>
            <div style="float: left;margin: 29px; font-family: Roboto; color: #116dd6; font-size: 18px;"><span style="color: 768093;">CTR: </span><span style="font-weight: 500;">'.$result['CTR'].' %</span></div>
            <div style="float: left;margin: 29px; font-family: Roboto; color: #116dd6; font-size: 18px;"><span style="color: 768093;">eCPM: </span><span style="font-weight: 500;">'.$result['CPM'].' руб.</span></div>
            <div style="float: left;margin: 29px; font-family: Roboto; color: #116dd6; font-size: 18px;"><span style="color: 768093;">CPG: </span><span style="font-weight: 500;">'.$result['CPG'].' руб.</span></div>';
            if ($result['natpre_aktiv']){echo '<a id="message_e" style="font-family: Roboto; cursor: pointer; background-color: #116dd6;color: #fff;float: right; margin:20px;padding: 8px 20px; font-size: 14px; border-radius: 4px; text-decoration: none;">Виджет NativePreview</a>';}
            if ($result['recomend_aktiv']){echo '<a id="message_r" style="font-family: Roboto; cursor: pointer; background-color: #116dd6;color: #fff;float: right; margin:20px;padding: 8px 20px; font-size: 14px; border-radius: 4px; text-decoration: none;">Виджет Recomendation</a>';}
            echo '
    </div>
        <iframe id="frame" style="width: 100%; border: none;" src="iframe.php?url='.$_GET['site'].'">
        </iframe>
    </body>
    <script>
        //Высота фрейма
        iframe=document.getElementById("frame");
        iframe.style.height=document.body.scrollHeight-71+"px";
        
        //Отправка события в iframe на поиск виджетов
        function bind(eventHandler,idr) {
            element= document.getElementById(idr);
            if (element.addEventListener){
                element.addEventListener(\'click\', eventHandler, false);
            }
        }
        bind(function(e){iframe.contentWindow.postMessage(\'corton-recomendation-widget\',\'*\');},\'message_r\');
        bind(function(e){iframe.contentWindow.postMessage(\'corton-nativepreview-widget\',\'*\');},\'message_e\');
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
    curl_setopt($ch,CURLOPT_HEADER,true);

    $response=curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);

    $header = strtolower($header);

    preg_match('/content-type(.*)\r\n/', $header,$ContentType);

    $result = strpos ($ContentType[1], 'html');

    if ($result){  //содержимое html код

        $body = substr($response, $header_size);
        curl_close($ch);

        //Замена ссылок
        $body= preg_replace('/<base.*?>/', '<base href="'.$outsite.'">', $body);
        $body = str_replace($_COOKIE['scheme'].'://'.$_COOKIE['host'], $outsite, $body);

        //Подключение скрипта
        $host=str_replace('.','_',$_COOKIE['host']);
        $body = str_replace('</head>', '<link href="https://api.corton.io/css/'.$host.'.css.gz" rel="stylesheet"><script async src="https://api.corton.io/js/corton.js" charset="UTF-8"></script></head>', $body);

        preg_match_all("/<meta.*?>/", $body, $phones);
        foreach ($phones[0] as $phone) {
            $phone=mb_strtolower($phone);
            if ((strpos($phone, 'content-type') !== false) and (strpos($phone, 'windows-1251') !== false)) {
                header('Content-Type: text/html; windows-1251; charset=windows-1251');
            }
        }

        $url=str_replace('http://','',$url);
        $url=str_replace('https://','',$url);
        $script="<script>
var corton_url='".$url."';
function bindEvent(eventHandler){if(window.addEventListener){window.addEventListener('message',eventHandler,false);}}
bindEvent(function (e) {
    if(e.data.indexOf('corton') !== -1) {
        if (document.readyState === \"complete\") {        
            let element=document.getElementById(e.data);
            if (element){
                let scrolo=element.getBoundingClientRect().top;
                window.scrollBy({top: scrolo-200, behavior: 'smooth'});
                let ch=1;let ch2=0;
                function draw() {
                    if (ch<=1 && ch>=0){
                        element.style.opacity=ch;
                        ch=ch-0.05;
                        setTimeout(draw,20);
                    }else{
                        if (ch2<1){
                            element.style.opacity=ch2;
                            ch2=ch2+0.05;
                            setTimeout(draw,50);
                        }
                    }
                }
                function wait() {
                    if (scrolo!=element.getBoundingClientRect().top){
                            scrolo=element.getBoundingClientRect().top
                            setTimeout(wait,150);
                    }else{
                        draw();
                    }
                }
                setTimeout(wait,150);
            }else{
                alert('Виджета на данной странице нету');
            }
        }else{
                alert('Страница ещё не загрузилась');
        }
    }
});
        </script>";
        $body = str_replace('<head>', '<head>'.$script, $body);
        echo $body;
    }else{//содержимое не html код
        header('Location: '.$url);
        exit;
    }


}
