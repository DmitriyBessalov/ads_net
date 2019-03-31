<?php

class WidgetcssController
{
    //Обновляет файл стилей площадки
    public static function actionUpdate(){
        $db = Db::getConnection();

        if (($_POST['type']=="zag_recomend")or($_POST['type']=="zag_natpre")or($_POST['type']=="zag_natpro")){
        }else{;
            //Деактивация виджета
            if ((!isset($_POST['mobile'])) AND (!isset($_POST['tablet'])) AND (!isset($_POST['desktop']))){
                switch ($_POST['type']){
                    case 'style_recomend':$sql="UPDATE `ploshadki` SET `recomend_aktiv`='0' WHERE `id`='".$_POST['id']."'";break;
                    case 'style_natpre':$sql="UPDATE `ploshadki` SET `natpre_aktiv`='0' WHERE `id`='".$_POST['id']."'";break;
                    case 'style_slider':$sql="UPDATE `ploshadki` SET `slider_aktiv`='0' WHERE `id`='".$_POST['id']."'";
                }
                $db->query($sql);
            }
        }

        $sql="REPLACE INTO `".addslashes($_POST['type'])."` SET ";

        foreach ($_POST as $key=>$value){
            if (($key!='type') and ($value!='')) {
                if ($value=="on")$value=1;
                $sql .= "`" . $key . "`='" . addslashes($value) . "',";
            }
        };

        $sql = substr($sql,0,-1).";";
        $db->query($sql);

        WidgetcssController::UpdateCSSfile(addslashes($_POST['id']));
        header('Location: /platforms-edit?id='.$_POST['id']);
        return true;
    }

    //Собирает файл стилей для площадки
    public static function UpdateCSSfile($id){
        $db = Db::getConnection();
        $sql="SELECT `status`,`type`,`promo_page`,`recomend_aktiv`,`natpre_aktiv`,`natpro_aktiv`,`slider_aktiv` FROM `ploshadki` WHERE `id`='".$id."'";
        $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

        if ($result['status']){
            $sql="SELECT `forcibly`,`selector`,`selector-title`,`css`,`adblock-css` FROM `style_promo` WHERE `id`='".$id."'";
            $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if ($css2==false){
                $sql="SELECT `forcibly`,`selector`,`selector-title`,`css`,`adblock-css` FROM `style_promo` WHERE `id`='0';";
                $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
            }
            $css='@charset "utf-8"; '.$css2['css'];
            $block = preg_replace("/[\r\n][\r\n]/",",",$css2['adblock-css']);
            $css.="#corton-promo{--adblock:".$block.";}";

            $css.="body{--forcibly:".$css2['forcibly'].";--selector:".$css2['selector'].";--selectortitle:".$css2['selector-title'].";--promo:".$result['promo_page'].";--promo_template:".$result['promo_template'].";}";

            if ($result['recomend_aktiv']) {
                $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`,`widget-text-title`,`mobile`, `tablet`, `desktop` FROM `style_recomend` WHERE `id`='".$id."'";
                $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`,`widget-text-title`,`mobile`, `tablet`, `desktop` FROM `style_recomend` WHERE `id`='0';";
                    $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-recomendation-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";--titletext:".$css2['widget-text-title'].";--widgetpositionp:".$css2['widget-position-p'].";--widgetparentid:".$css2['widget-parent-id'].";}";
                $css.="body{--recomend-algorithm-output:".$css2['algorithm-output'].";}";
            }

            if ($result['natpre_aktiv']) {
                $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`, `button-text`,`mobile`, `tablet`, `desktop` FROM `style_natpre` WHERE `id`='".$id."'";
                $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`,`widget-position-p`, `widget-parent-id`, `algorithm-output`, `button-text`,`mobile`, `tablet`, `desktop` FROM `style_natpre` WHERE `id`='0'";
                    $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-nativepreview-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";--widgetpositionp:".$css2['widget-position-p'].";--widgetparentid:".$css2['widget-parent-id'].";--buttontext:".$css2['button-text'].";}";
                $css.="body{--natpre-algorithm-output:".$css2['algorithm-output'].";}";
            }

            if ($result['slider_aktiv']) {
                $sql="SELECT `css`, `mobile`, `tablet`, `desktop` FROM `style_slider` WHERE `id`='".$id."'";
                $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($css2==false){
                    $sql="SELECT `css`, `mobile`, `tablet`, `desktop` FROM `style_slider` WHERE `id`='0'";
                    $css2=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                }
                $css.=$css2['css'];
                $css.="#corton-slider-widget{--mobile:".$css2['mobile'].";--tablet:".$css2['tablet'].";--desktop:".$css2['desktop'].";}";
                if ($result['type']=='demo'){$css.="body{--slider-algorithm-output:0;}";}else{$css.="body{--slider-algorithm-output:1;}";}
            }

        }else{$css="";}

        $sql="SELECT `domen` FROM `ploshadki` WHERE `id`='".$id."'";
        $domen=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
        $domen = str_replace(".", "_", $domen);

        if ($domen!='default style') {
            if (($domen == 'sustavzdorov_ru') OR ($domen == 'spinet_ru') OR ($domen == 'artrozamnet_ru')) {
                file_put_contents(APIDIR . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $domen.".min.css", $css);
            };

            $gzdata = gzencode($css, 9);
            unlink(      APIDIR . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $domen . ".css.gz");
            $fp = fopen( APIDIR . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . $domen . ".css.gz", "w") or die("не удалось создать файл");
            fwrite($fp, $gzdata);
            fclose($fp);
        }
        return true;
    }

    public static function actionAktiv(){
        $db = Db::getConnection();
        $sql="UPDATE `ploshadki` SET `".addslashes($_GET['widget'])."_aktiv`='1' WHERE `id`='".addslashes($_GET['id'])."';";
        $db->query($sql);
        WidgetcssController::UpdateCSSfile(addslashes($_GET['id']));
        return true;
    }
}