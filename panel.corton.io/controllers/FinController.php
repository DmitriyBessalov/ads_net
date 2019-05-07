<?php

class FinController
{

    public static function actionPlatform()
    {
        $title='Статистика кабинета';
        include PANELDIR.'/views/layouts/header.php';

        if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y', strtotime("-1 month"));}
        if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
        $mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
        $mySQLdateend = date('Y-m-d', strtotime($dateend));

        echo '
<div class="table-box">
    <div class="table w-embed">
        <table>
            <thead>
                <tr class="trtop">
                    <td style="min-width: 230px;">Виджет</td>
                    <td style="min-width: 210px;">Целевые просмотры
                        <div class="tooltipinfo2" style="font-size: 14px;">?<span class="tooltiptext1">Целевые / оплаченные просмотры партнерских материалов</span></div>
                    </td>
                    <td style="min-width: 110px;">CTR
                    </td>
                    <td style="min-width: 120px;">eCPM
                        <div class="tooltipinfo2" style="font-size: 14px;">?<span class="tooltiptext1">Доход на 1000 показов анонсов</span></div>
                    </td>
                    <td style="min-width: 130px;">Доход</td>
					<td style="min-width: 140px;">Код виджета</td>
                </tr>
            </thead>';
        if ((strtotime($datebegin)<=strtotime($dateend)) AND (strtotime($datebegin)<=strtotime(date('d.m.Y')))) {
            $db = Db::getConnection();
            $dbstat = Db::getstatConnection();
            $sql = "SELECT p.`id`, p.`domen`, u.`email`, p.`user_id` FROM `ploshadki` p JOIN `users` u ON p.`user_id`=u.`id` WHERE `phpsession`='" . $_COOKIE['PHPSESSID'] . "'";
            $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $balansall = 0;
            foreach ($result as $i) {
                $arrplatform[] = $i['id'];
                $domen = $i['domen'];
            };

            if (count($result) != 1) $domen = "";

            $strplatform = implode("','", $arrplatform);

            $sql="SELECT `balans` FROM `balans_user` WHERE `user_id`='".$result[0]['user_id']."' AND `date`=(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id`='".$result[0]['user_id']."')";
            $balans=$db->query($sql)->fetch(PDO::FETCH_COLUMN);


            if (is_null($balans))$balans=0;

            if (isset($_GET['platform'])) {
                if ($_GET['platform'] != 'all') {
                    if (in_array($_GET['platform'], $arrplatform)) {
                        $strplatform = $_GET['platform'];
                        $sql = "SELECT `domen` FROM `ploshadki` WHERE id='" . $_GET['platform'] . "'";
                        $domen = $db->query($sql)->fetch(PDO::FETCH_COLUMN);
                    } else exit;
                }
            }

            $sql = "SELECT SUM(`recomend_aktiv`) as `recomend_aktiv`, SUM(`natpre_aktiv`) as `natpre_aktiv`, SUM(`slider_aktiv`) as `slider_aktiv` FROM `ploshadki` WHERE `id` in ('" . $strplatform . "')";
            $aktiv = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

            if (is_null($strplatform)){
                $aktiv['recomend_aktiv']=$aktiv['natpre_aktiv']=$aktiv['slider_aktiv']=false;
            }

            $sql = "SELECT SUM(`r_balans`) as `r_balans`,SUM(`e_balans`) as `e_balans`, SUM(`s_balans`) as `s_balans`, SUM(`r`) as `r`, SUM(`e`) as `e`, SUM(`s`) as `s`, SUM(`r_show_anons`) as 'r_show_anons', SUM(`e_show_anons`) as 'e_show_anons', SUM(`s_show_anons`) as 's_show_anons', SUM(`r_promo_load`) as 'r_promo_load', SUM(`e_promo_load`) as 'e_promo_load', SUM(`s_promo_load`) as 's_promo_load' FROM `balans_ploshadki` WHERE `ploshadka_id` in ('" . $strplatform . "') AND `date`>='" . $mySQLdatebegin . "' AND `date`<='" . $mySQLdateend . "'";
            $balansperiod = $dbstat->query($sql)->fetch(PDO::FETCH_ASSOC);

            if ((strtotime($datebegin) <= strtotime(date('d.m.Y'))) AND (strtotime($dateend) >= strtotime(date('d.m.Y')))) {
                $today = true;
                $redis = new Redis();
                $redis->connect('185.75.90.54', 6379);
                $redis->select(3);

                $platforms=explode("','",$strplatform);

                foreach ($platforms as $i){
                    $ch=$redis->get(date('d').':'.$i.':r');
                    if($ch)$balansperiod['r_show_anons']+=$ch;
                    $ch=$redis->get(date('d').':'.$i.':e');
                    if($ch)$balansperiod['e_show_anons']+=$ch;
                    $ch=$redis->get(date('d').':'.$i.':s');
                    if($ch)$balansperiod['s_show_anons']+=$ch;
                };
            }

            $r_CTR = $balansperiod['r_promo_load'] / $balansperiod['r_show_anons'];
            $e_CTR = $balansperiod['e_promo_load'] / $balansperiod['e_show_anons'];
            $s_CTR = $balansperiod['s_promo_load'] / $balansperiod['s_show_anons'];

            if (is_nan($r_CTR)) {
                $r_CTR = '0.00';
            } else {
                $r_CTR = round($r_CTR * 100);
                if (is_infinite($r_CTR)) $r_CTR = '0.00';
            };
            if (is_nan($e_CTR)) {
                $e_CTR = '0.00';
            } else {
                $e_CTR = round($e_CTR * 100);
                if (is_infinite($e_CTR)) $e_CTR = '0.00';
            };
            if (is_nan($s_CTR)) {
                $s_CTR = '0.00';
            } else {
                $s_CTR = round($s_CTR * 100);
                if (is_infinite($s_CTR)) $s_CTR = '0.00';
            };

            if (is_null($balansperiod['r'])) {
                $balansperiod['r'] = $balansperiod['e'] = $balansperiod['s'] = 0;
                $balansperiod['r_balans'] = $balansperiod['e_balans'] = $balansperiod['s_balans'] = '0.00';
            };
			
            echo '
    <tbody>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="recommendation-mini-site"></div>
                    <div class="logominitext">
                        Recommendation
                        <p style="color: #768093; font-size: 12px;">Статус:
                            <span class="nowstatus">';
            if ($aktiv['recomend_aktiv']) {
                echo 'Активен';
            } else {
                echo 'Неактивен';
            }
            echo '</span>
                        </p>
                    </div>
                </div>
            </td>
            <td>' . $balansperiod['r'] . '</td>
            <td>' . $r_CTR . ' %</td>
            <td>';
            if (($aktiv['recomend_aktiv'])AND($balansperiod['r']!=0)) {
                $val= round($balansperiod['r_balans']/$balansperiod['r_show_anons']*1100,2);
                if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                echo $val;
            } else {
                echo '0.00';
            }
            echo ' р.</td>
            <td class="bluetext">' . $balansperiod['r_balans'] . ' р.</td>
			<td>
		        <a class="main-itemcode" href="javascript:void(0);" tabindex="1" style="font-size: 16px; text-decoration: none; color: #333333;">
                    <div class="codeblock">
                       <xmp style="margin: 0 !important;"><code></xmp>
                    </div>
                </a>
                <ul class="sub-menucode">
				    <div style="padding: 7px 0px;">
					    <span style="color: #333;">Установите код в конце статьи:</span>
                        <xmp style="margin: 14px 0px 0px 0px;"><div id="corton-recomendation-widget"></div></xmp>
                    </div>
                </ul>
			</td>
        </tr>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="nativepreview-mini-site"></div>
                    <div class="logominitext">
                        Native Preview
                        <p style="color: #768093; font-size: 12px;">Статус:
                            <span class="nowstatus">';
            if ($aktiv['natpre_aktiv']) {
                echo 'Активен';
            } else {
                echo 'Неактивен';
            }
            echo '</span>
                        </p>
                    </div>
                </div>
            </td>
            <td>' . $balansperiod['e'] . '</td>
            <td>' . $e_CTR . ' %</td>
            <td>';
            if (($aktiv['natpre_aktiv'])AND($balansperiod['e']!=0)) {
                $val= round($balansperiod['e_balans']/$balansperiod['e_show_anons']*1100,2);
                if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                echo $val;
            } else {
                echo '0.00';
            }
            echo ' р.</td>
            <td class="bluetext">' . $balansperiod['e_balans'] . ' р.</td>
			<td>
			   <a class="main-itemcode2" href="javascript:void(0);" tabindex="1" style="font-size: 16px; text-decoration: none; color: #333333;">
                    <div class="codeblock">
                       <xmp style="margin: 0 !important;"><code></xmp>
                    </div>
                </a>
                <ul class="sub-menucode2">
				    <div style="padding: 7px 0px;">
					    <span style="color: #333;">Установите код в теле статьи:</span>
                        <xmp style="margin: 14px 0px 0px 0px;"><div id="corton-nativepreview-widget"></div></xmp>
                    </div>
                </ul>
			</td>
        </tr>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="slider-mini-site"></div>
                    <div class="logominitext">
                        Slider
                        <p style="color: #768093; font-size: 12px;">Статус:
                            <span class="nowstatus">';
            if ($aktiv['slider_aktiv']) {
                echo 'Активен';
            } else {
                echo 'Неактивен';
            }
            echo '</span>
                        </p>
                    </div>
                </div>
            </td>
            <td>' . $balansperiod['s'] . '</td>
            <td>' . $s_CTR . ' %</td>
            <td>';
            if (($aktiv['slider_aktiv'])AND($balansperiod['s']!=0)) {
                $val= round($balansperiod['s_balans']/$balansperiod['s_show_anons']*1100,2);
                if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                echo $val;
            } else {
                echo '0.00';
            }
            echo ' р.</td>
            <td class="bluetext">' . $balansperiod['s_balans'] . ' р.</td>
			<td>
			   <a class="main-itemcode3" href="javascript:void(0);" tabindex="1" style="font-size: 16px; text-decoration: none; color: #333333;">
                    <div class="codeblock">
                       <xmp style="margin: 0 !important;"><code></xmp>
                    </div>
                </a>
                <ul class="sub-menucode3">
				    <div style="padding: 7px 0px;">
					    <span style="color: #333;">Установите код в любом месте страницы:</span>
                        <xmp style="margin: 14px 0px 0px 0px;"><div id="corton-slider-widget"></div></xmp>
                    </div>
                </ul>
			</td>
        </tr>
        <!--tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="nativepro-mini-site"></div>
                    <div class="logominitext">
                        <a href="http://demoblog.tw1.ru" style="text-decoration: none;">Native Pro</a>
                        <p style="color: #768093; font-size: 12px;">demoblog.tw1.ru
                            <span class="nowstatus">Не активен</span>
                        </p>
                    </div>
                </div>
            </td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td class="bluetext">0</td>
        </tr-->
    </tbody>';
        }else{echo '<tr><td colspan="5">Некоректные даты фильтра</td></tr>';}
        echo'
</table>

                </div>
                    <div class="table-right">
				 <form id="right-form" name="email-form" class="form-333">
			         <a href="/finance#openaddsite" class="button-add-site w-button">Добавить площадку</a>'.'
					 <p class="filtermenu"><label '; if ((!isset($_GET['platform'])) OR ($_GET['platform']=='all')){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="platform" value="all" id="" class="form-radio">Показать все</label></p>';

        foreach ($result as $platform){
            echo                    '<p class="filtermenu"><label style="width: 200px;'; if ($_GET['platform']==$platform['id']){echo 'text-decoration: underline;';}echo'"><input type="radio" name="platform" value="'.$platform['id'].'" id="" class="form-radio">Показать '.$platform['domen'].'</label></p>';
        };
        echo '              <div class="html-embed-3 w-embed" style="margin-top: 40px;">
                        <input type="text" name="datebegin" class="tcal tcalInput" value="'.$datebegin.'">
                        <div class="text-block-128">-</div>
                        <input type="text" name="dateend" class="tcal tcalInput" value="'.$dateend.'">
                        </div>
                        <input type="submit" value="Применить" class="submit-button-addkey w-button">
                    </div>
		        </form>
		    </div>
		
    <script>
        function AjaxFormRequest(result_id,formMain,url) {
            jQuery.ajax({
                url:     url,
                type:     "POST",
                dataType: "html",
                data: jQuery("#"+formMain).serialize(),
                success: function(response) {
                    document.getElementById(result_id).innerHTML = response;
                },
                error: function(response) {
                    document.getElementById(result_id).innerHTML = "<p>Сообщение отправлено, скоро с Вами свяжется менежер.</p>";
                }
            });
            $(\':input\',\'#formMain\') .prop(\'disabled\',true)
        }
    </script>
<div id="openaddsite" class="modalDialog2">
    <div>
        <a href="/finance#close" title="Закрыть" class="close">
            <img src="https://uploads-ssl.webflow.com/5bd6e3ad10ba2a79417b499a/5c25ef6677f283ad129ce5fe_close.png">
        </a>
        <div>
            <div class="text-block-82-copy" style="">Добавить новую площадку</div>
			<p style="margin-top: 10px; color: #768093; font-weight: 400 !important; font-size: 16px; line-height: 20px; font-family: \'Myriadpro Regular\';">Укажите URL площадки которую хотите добавить в систему, после чего личный менеджер свяжется с вами.</p>
            <div class="w-form">
                <form method="post" id="formMain" action="https://corton.io/contact-form2.php" class="form-3" style="margin-right: 0px !important;">
                    <input type="text" name="host" maxlength="50" placeholder="URL площадки" required="" style="background: #f4f6f9; color: #768093; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 260px; height: 34px; margin-right: 20px;">
                    <input type="hidden" name="email" value="'.$platform['email'].'">
                    <input style="margin-top: 4px !important; cursor: pointer; color: #fff; border: 0px;" type="submit" value="Добавить площадку" onclick="AjaxFormRequest(\'messegeResult\', \'formMain\', \'https://corton.io/contact-form2.php\')" class="button-add-site">
                 </form>
                <div id="messegeResult"></div>
            </div>
        </div>
    </div>
</div>

<style>
	.modalDialog,
    .modalDialog2  {
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0,0,0,0.8);
		z-index: 99999;
		-webkit-transition: opacity 400ms ease-in;
		-moz-transition: opacity 400ms ease-in;
		transition: opacity 400ms ease-in;
		display: none;
		pointer-events: none;
	}
	.modalDialog:target,
    .modalDialog2:target {
		display: block;
		pointer-events: auto;
	}
	.modalDialog > div,
    .modalDialog2 > div {
		width: 524px;
		position: relative;
		margin: 15% auto;
		padding: 20px 20px 20px 20px;
		background: #fff;
        border-radius: 4px;
	}
	.close {
		color: #FFFFFF;
		line-height: 34px;
		position: absolute;
		right: -33px;
		text-align: center;
		top: -33px;
		width: 34px;
		text-decoration: none;
		font-weight: bold;
		-webkit-border-radius: 34px;
		-moz-border-radius: 34px;
		border-radius: 34px;
		background: none !important;
	}
	.close:hover { background: #116dd6; }
</style>
			
			';
        include PANELDIR . '/views/layouts/footer.php';
        if (isset($today)) {$redis->close();}
        if ($balans==false)$balans='0.00';
        echo '<script>$(".text-block-balans").html("'.$balans.' р.");</script>';
        return true;
    }
	    	

    public static function actionAdmin()
    {
        $title='Статистика кабинета';
        include PANELDIR.'/views/layouts/header.php';

        if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y');}
        if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
        //$mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
        //$mySQLdateend = date('Y-m-d', strtotime($dateend));

        echo '
<div class="form-block w-form">
    <div class="w-form-done"></div>
    <div class="w-form-fail"></div>
</div>

<div class="table-box">
<div class="div-block-95 w-clearfix">
    <div style="width:876px;" class="div-block-94">
        <div class="text-block-103">Внешние метрики</div>
		<div style="display: flex;">
		<div style="width:33%;">
           <div class="text-block-104">Колличество визитов</div>
           <div style="font-size: 46px;" class="text-block-105">128 315</div>
		</div>  
        <div style="border-width: 0 0 0 1px; border-style: solid; color:#E0E1E5; padding: 20px; margin-left: 40px;"></div>		
		<div style="width:33%;">   
		   <div class="text-block-104">Колличество показов анонсов</div>
           <div style="font-size: 46px;" class="text-block-105">42 130</div>
		</div>
		<div style="border-width: 0 0 0 1px; border-style: solid; color:#E0E1E5; padding: 20px; margin-left: 40px;"></div>	
		<div style="width:33%;">   
		   <div class="text-block-104">Процент показа анонсов</div>
           <div style="font-size: 46px;" class="text-block-105">36 %</div>
		</div>
		</div><img src="/images/stat-2.png" alt="" class="image-8" style="width: 838px;">
    </div>
    <div class="div-block-94">
        <div class="text-block-103">Доход площадок</div>
        <div class="text-block-104">Объём заработанных средств площадками</div>
        <div class="text-block-105">16 780</div>
		<div id="containergr" style="width:356px; height:102px;">
           <canvas id="c" width="356" height="102"></canvas>
        </div>
    </div>
    <div class="div-block-94">
        <div class="text-block-103">Клики</div>
        <div class="text-block-104">Клики за период</div>
        <div class="text-block-105">310</div>
		<div id="containergr" style="width:356px; height:102px;">
           <canvas id="b" width="356" height="102"></canvas>
        </div>
    </div>
    <div class="div-block-94">
        <div class="text-block-103">Оплаченные просмотры</div>
        <div class="text-block-104">Оплаченные просмотры за период</div>
        <div class="text-block-105">249</div>
		<div id="containergr" style="width:356px; height:102px;">
           <canvas id="a" width="356" height="102"></canvas>
        </div>
    </div>
	<div class="div-block-94">
        <div class="text-block-103">ТОП 5 площадок и ключей</div>
        <div class="text-block-104">Популярные площадки и ключи</div>
		<div style="padding-bottom:22px;"></div>
		<div style="display: flex;">
		   <div>
              <span class="topwords"><span style="color:#768093;">1. </span>Cardio.com</span><br>
		      <span class="topwords"><span style="color:#768093;">2. </span> Diabethelp.org</span><br>
		      <span class="topwords"><span style="color:#768093;">3. </span> damskf.ru</span><br>
		      <span class="topwords"><span style="color:#768093;">4. </span> fb.ru</span><br>
		      <span class="topwords"><span style="color:#768093;">5. </span> like.com</span>
		   </div>
		   <div style="border-width: 0 0 0 1px; border-style: solid; color:#E0E1E5; margin-left: 40px;"></div>	
		   <div style="padding-left: 40px;">
              <span class="topwords"><span style="color:#768093;">1. </span> Артроз</span><br>
		      <span class="topwords"><span style="color:#768093;">2. </span> Давление</span><br>
		      <span class="topwords"><span style="color:#768093;">3. </span> Кардио</span><br>
		      <span class="topwords"><span style="color:#768093;">4. </span> Инсулин</span><br>
		      <span class="topwords"><span style="color:#768093;">5. </span> Диабет</span>
	    </div>
		</div>
    </div>
</div>

<script>
// ChartJS C
var dataset_01 = {
    label: "Значение",
    backgroundColor: "rgba(17,109,214,0.2)",
    borderColor: "rgba(17,109,214,1)",
    pointBorderColor: "rgba(1,149,114,1)",
	borderWidth: "2",
    data: [140, 159, 140, 211, 156, 130, 140, 50]
};

var data = {
    labels: ["1 день", "2 день", "3 день", "4 день", "5 день", "6 день", "Сегодня"],
    datasets: [dataset_01]
};

var options = {
    title: { display: false },
    legend: { display: false },
    //maintainAspectRatio : false,
    //responsive: false,
    animation: {
        duration: 1800,
        easing: "easeOutBack"
    },
    scales: {
        xAxes: [{ display: false }],
        yAxes: [{ display: false }]
    },
	borderWidth: {
   top: 1,
   right: 0,
   bottom: 0,
   left: 0
}	
};

var ctx = document.getElementById("c").getContext("2d");

var myLineChart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: options
});
</script>

<script>
// ChartJS B
var dataset_02 = {
    label: "Значение",
    backgroundColor: "rgba(17,109,214,0.2)",
    borderColor: "rgba(17,109,214,1)",
    pointBorderColor: "rgba(1,149,114,1)",
	borderWidth: "2",
    data: [140, 159, 140, 211, 156, 130, 310, 0]
};

var data = {
    labels: ["1 день", "2 день", "3 день", "4 день", "5 день", "6 день", "Сегодня"],
    datasets: [dataset_02]
};

var options = {
    title: { display: false },
    legend: { display: false },
    //maintainAspectRatio : false,
    //responsive: false,
    animation: {
        duration: 1800,
        easing: "easeOutBack"
    },
    scales: {
        xAxes: [{ display: false }],
        yAxes: [{ display: false }]
    },
	borderWidth: {
   top: 1,
   right: 0,
   bottom: 0,
   left: 0
}	
};

var ctx = document.getElementById("b").getContext("2d");

var myLineChart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: options
});
</script>

<script>
// ChartJS A
var dataset_03 = {
    label: "Значение",
    backgroundColor: "rgba(17,109,214,0.2)",
    borderColor: "rgba(17,109,214,1)",
    pointBorderColor: "rgba(1,149,114,1)",
	borderWidth: "2",
    data: [140, 159, 140, 211, 156, 130, 710, 0]
};

var data = {
    labels: ["1 день", "2 день", "3 день", "4 день", "5 день", "6 день", "Сегодня"],
    datasets: [dataset_03]
};

var options = {
    title: { display: false },
    legend: { display: false },
    //maintainAspectRatio : false,
    //responsive: false,
    animation: {
        duration: 1800,
        easing: "easeOutBack"
    },
    scales: {
        xAxes: [{ display: false }],
        yAxes: [{ display: false }]
    },
	borderWidth: {
   top: 1,
   right: 0,
   bottom: 0,
   left: 0
}	
};

var ctx = document.getElementById("a").getContext("2d");

var myLineChart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: options
});
</script>

<div class="table-right">
					   <form id="email-form" name="email-form" class="form-333">
			             <a href="/platforms-add" class="button-add-site w-button">Добавить площадку</a>
						 <p class="filtermenu"><input type="radio" name="platform" value="all" id="radio-one" class="form-radio"><label for="radio-one">Все площадки</label></p>
						 <p class="filtermenu"><input type="radio" name="platform" value="all" id="radio-two" class="form-radio"><label for="radio-two">Информационные</label></p>
						 <p class="filtermenu"><input type="radio" name="platform" value="all" id="radio-three" class="form-radio"><label for="radio-three">Новостные</label></p>
					   </form>
                       
						
			    <div class="html-embed-3 w-embed" style="margin-top: 40px;">
                            <input type="text" name="datebegin" class="tcal tcalInput" value="'.$datebegin.'">
                            <div class="text-block-128">-</div>
                            <input type="text" name="dateend" class="tcal tcalInput" value="'.$dateend.'">
                        </div>
                        <input type="submit" value="Применить" class="submit-button-addkey w-button">
                    </div>
		        </form>
		    </div>

';
        include PANELDIR . '/views/layouts/footer.php';
        return true;
    }
}