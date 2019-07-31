<?php

class WidgetcssController
{
    //Обновляет файл стилей площадки
    public static function actionUpdate(){

        if (($_POST['type']=="zag_recomend")or($_POST['type']=="zag_natpre")or($_POST['type']=="zag_natpro")){
        }else{;
            //Деактивация виджета
            if ((!isset($_POST['mobile'])) AND (!isset($_POST['tablet'])) AND (!isset($_POST['desktop']))){
                switch ($_POST['type']){
                    case 'style_recomend':$sql="UPDATE `ploshadki` SET `recomend_aktiv`='0' WHERE `id`='".$_POST['id']."'";break;
                    case 'style_natpre':$sql="UPDATE `ploshadki` SET `natpre_aktiv`='0' WHERE `id`='".$_POST['id']."'";break;
                    case 'style_slider':$sql="UPDATE `ploshadki` SET `slider_aktiv`='0' WHERE `id`='".$_POST['id']."'";
                }
                $GLOBALS['db']->query($sql);
            }
        }

        $sql="REPLACE INTO `".$_POST['type']."` SET ";

        foreach ($_POST as $key=>$value){
            if (($key!='type') and ($value!='')) {
                if ($value=="on")$value=1;
                $sql .= "`" . $key . "`='" . $value . "',";
            }
        };

        $sql = substr($sql,0,-1).";";
        $GLOBALS['db']->query($sql);

        WidgetcssController::UpdateCSSfile($_POST['id']);
        header('Location: /platforms-edit?id='.$_POST['id']);
        return true;
    }

    //Собирает файл стилей для площадки
    public static function UpdateCSSfile($id){

        $sql="SELECT `status`,`type`,`promo_page`,`recomend_aktiv`,`natpre_aktiv`,`natpro_aktiv`,`slider_aktiv` FROM `ploshadki` WHERE `id`='".$id."'";
        $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

        if ($result['status']){
            $sql="SELECT `forcibly`,`selector`,`selector-title`,`css`,`adblock-css` FROM `style_promo` WHERE `id`='".$id."'";
            $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
            if ($css2==false){
                $sql="SELECT `forcibly`,`selector`,`selector-title`,`css`,`adblock-css` FROM `style_promo` WHERE `id`='0';";
                $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
            }
            $css='@charset "utf-8"; '.$css2['css'];
            $block = preg_replace("/[\r\n][\r\n]/",",",$css2['adblock-css']);
            $css.="#corton-promo{--adblock:".$block.";}";

            $sql='SELECT `id_categoriya`,`type_search`,`regex` FROM `plarforms_regex_categorii` WHERE `id_platform`='.$id.';';
            $category=$GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_ASSOC);

            $categoryjson=json_encode($category, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $categoryjson=str_replace('/','[/]',$categoryjson);

            $css.="body{--forcibly:".$css2['forcibly'].";--selector:".$css2['selector'].";--selectortitle:".$css2['selector-title'].";--promo:".$result['promo_page'].";--promo_template:".$result['promo_template'].";--category:".$categoryjson.";}";

            if ($result['recomend_aktiv']) {
                $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`,`widget-text-title`,`image-shape`,`mobile`, `tablet`, `desktop` FROM `style_recomend` WHERE `id`='".$id."'";
                $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`,`widget-text-title`,`image-shape`,`mobile`, `tablet`, `desktop` FROM `style_recomend` WHERE `id`='0';";
                    $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-recomendation-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";--titletext:".$css2['widget-text-title'].";--widgetpositionp:".$css2['widget-position-p'].";--widgetparentid:".$css2['widget-parent-id'].";--image_shape:".$css2['image-shape'].";}";
                $css.="body{--recomend-algorithm-output:".$css2['algorithm-output'].";}";
            }

            if ($result['natpre_aktiv']) {
                $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`, `button-text`,`image-shape`,`mobile`, `tablet`, `desktop` FROM `style_natpre` WHERE `id`='".$id."'";
                $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`, `button-text`,`image-shape`,`mobile`, `tablet`, `desktop` FROM `style_natpre` WHERE `id`='0'";
                    $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-nativepreview-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";--widgetpositionp:".$css2['widget-position-p'].";--widgetparentid:".$css2['widget-parent-id'].";--buttontext:".$css2['button-text'].";--image_shape:".$css2['image-shape'].";}";
                $css.="body{--natpre-algorithm-output:".$css2['algorithm-output'].";}";
            }

            if ($result['slider_aktiv']) {
                $sql="SELECT `css`, `mobile`, `tablet`, `desktop` FROM `style_slider` WHERE `id`='".$id."'";
                $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`, `mobile`, `tablet`, `desktop` FROM `style_slider` WHERE `id`='0'";
                    $css2=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-slider-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";}";
                if ($result['type']=='demo'){$css.="body{--slider-algorithm-output:0;}";}else{$css.="body{--slider-algorithm-output:1;}";}
            }

        }else{$css="";}

        $sql="SELECT `domen` FROM `ploshadki` WHERE `id`='".$id."'";
        $domen=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        $domen = str_replace(".", "_", $domen);

        if ($domen!='default style') {
            $gzdata = gzencode($css, 9);
            unlink(      APIDIR . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $domen . ".css.gz");
            $fp = fopen( APIDIR . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $domen . ".css.gz", "w") or die("не удалось создать файл");
            fwrite($fp, $gzdata);
            fclose($fp);
        }

        $redis = new Redis();
        $redis->pconnect('185.75.90.54', 6379);
        $redis->select(0);
        $cache=$redis->get('cdn_cache_update');
        if ($cache) $cache_arr=json_decode($cache, true);
        $cache_arr['paths'][]='/css/'.$domen.'.css.gz';
        $cache_arr['paths']=array_unique($cache_arr['paths']);
        $cache=json_encode($cache_arr);
        $redis->set('cdn_cache_update',$cache, 1296000);
        $redis->close();

        exec('wget -q https://api2.cortonlab.com/update_cache_cdn.php -O - >/dev/null 2>&1');
        return true;
    }

    public static function actionAktiv(){
        $sql="UPDATE `ploshadki` SET `".$_GET['widget']."_aktiv`='1' WHERE `id`='".$_GET['id']."';";
        $GLOBALS['db']->query($sql);
        WidgetcssController::UpdateCSSfile($_GET['id']);
        return true;
    }
}
