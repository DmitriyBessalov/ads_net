<?php
    class PloshadkiController
        {
            //Выводит все площадки
            public static function actionIndex()
            {
                $title = 'Площадки в системе';
                include PANELDIR . '/views/layouts/header.php';

                if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y', strtotime("-1 month"));}
                if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
                $mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
                $mySQLdateend = date('Y-m-d', strtotime($dateend));

                $db = Db::getConnection();
                $dbstat = Db::getstatConnection();

                $str="AND";
                if ((isset($_GET['type'])) AND ($_GET['type']!='all')){
                    $str.=" p.`type`='".$_GET['type']."' AND";
                }
                if ((isset($_GET['status'])) AND ($_GET['status']!='all')){
                    $str.=" p.`status`='".$_GET['status']."' AND";
                }
                $str=substr($str, 0, -3);

                $sql="SELECT p.`id`, p.`domen`, p.`type`, p.`otchiclen`, u.`email` as `user_email`, p.`date_add`, p.`status`, p.`otchiclen`, p.`recomend_aktiv`, p.`natpre_aktiv`, p.`natpro_aktiv`, p.`slider_aktiv` FROM `ploshadki` p RIGHT OUTER JOIN `users` u ON p.`user_id` = u.`id` WHERE p.`id` != 0 ".$str." ORDER BY p.`domen`";

                $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                echo '
		<div class="form-block w-form">
		<div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
        <script src="https://panel.cortonlab.com/js/jquery-3.3.1.min.js" type="text/javascript"></script> 
        <script src="https://panel.cortonlab.com/js/jquery-ui.min.js" type="text/javascript"></script> 
        <script>
                function balans_spisanie(i){
                    $.get( "https://panel.cortonlab.com/platforms-spisanie?id="+i+"&sum="+$("#sum_spisanie"+i).val(),  function( data) {
                        $("#sum_spisanie"+i).val("0");
	                    $("#sum_spisanie"+i).prop(\'disabled\', true);
	                    $("#status_spisanie"+i).html(data);
	                })
                }
        </script> 
        
		<div class="table-box">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <td>Площадка</td>
                <td>Виджеты</td>
                <td>Визиты</td>
                <td>Уники</td>
                <td>Клики</td>
                <td><div class="tooltipinfo1">Просмотры<span class="tooltiptext1">Целевые/оплаченные просмотры промо-статей и процент от кликов</span></div></td>
                <td style="width: 111px;"><div class="tooltipinfo1">График<span class="tooltiptext1">График по просмотрам за последние 7 дней</span></div></td>
                <td>Доход</td>
                <td style="width: 110px;"></td>
              </tr>
            </thead>';

                foreach ($result as $i) {
                    $sql="SELECT SUM(`r_balans`)+SUM(`e_balans`)+SUM(`s_balans`) as 'dohod', SUM(`r`)+SUM(`e`)+SUM(`s`) as 'prosmotr',SUM(`r_promo_load`)+SUM(`e_promo_load`)+SUM(`s_promo_load`)as 'click' FROM `balans_ploshadki` WHERE `ploshadka_id`='".$i['id']."'  AND `date`>='" . $mySQLdatebegin . "' AND `date`<='" . $mySQLdateend . "'";
                    $platform = $dbstat->query($sql)->fetch(PDO::FETCH_ASSOC);
                    if (is_null($platform['prosmotr'])){$platform['prosmotr']=$platform['click']=$platform['dohod']=0;}
                    $protsent_prochteniy = round(100 / $platform['click'] * $platform['prosmotr'], 2);
                    if ((is_infinite($protsent_prochteniy)) OR (is_nan($protsent_prochteniy))){$protsent_prochteniy='0.00';}

                    $sql="SELECT `date`, SUM(`r`) + SUM(`e`) + SUM(`s`) AS 'prosmotr' FROM `balans_ploshadki` WHERE `ploshadka_id` = '".$i['id']."' AND `date`>=(CURDATE() - 6) GROUP BY `date`";
                    $gragik_arr=$dbstat->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                    for ($k = 6; $k >= 0; $k--) {
                        $date=date('Y-m-d', strtotime('-'.$k.' days'));
                        $gragika[$date]=0;
                    }

                    foreach ($gragik_arr as $k){
                        $gragika[$k['date']]=$k['prosmotr'];
                    }

                    $grafik="";
                    foreach ($gragika as $k){
                        $grafik.='"'.$k.'",';
                    }

                    $grafik.='"0"';
                    echo "<tr>
                      <td style=\"text-align: left; min-width: 336px;\">
					  <div style=\"margin-top: 7px;\">
					      <div class=\"logomini\"'>
					        <img style='margin-top: 8px;' src='https://favicon.yandex.net/favicon/".$i['domen']."'>
                          </div>
					      <div class=\"logominitext\">
						     <a href='http://" . $i['domen'] . "' style='text-decoration: none;'>" . $i['domen'] . "</a>
					         <p style=\"color: #768093; font-size: 12px;\">" . $i['user_email'] . " 
							 <span class=\"nowstatus\">";
                    echo $i['status'] ? 'Активна' : 'Неактивен';
                    echo " </span>
					<span style=\"margin-left: 5px;\" class=\"nowstatus\" id='otchic".$i['id']."'>
					%: ".$i['otchiclen']."
                    </span>
					</p>
						  </div>
					  </div>
					  </td>
                      <td style=\"width: 180px;\">
                       <div class=\"backwig\">
                        <div class=\"recommendation-mini\""; if (!$i['recomend_aktiv']) {echo " style='opacity: 0.3;'";}; echo"><span class=\"tooltiptext1\">Recommendation</span></div>
                        <div class=\"nativepreview-mini\""; if (!$i['natpre_aktiv']) {echo " style='opacity: 0.3;'";}; echo"><span class=\"tooltiptext2\">Native Preview</span></div>
                        <div class=\"nativepro-mini\""; if (!$i['natpro_aktiv']) {echo " style='opacity: 0.3;'";}; echo"><span class=\"tooltiptext3\">Native Pro</span></div>
                        <div class=\"slider-mini\""; if (!$i['slider_aktiv']) {echo " style='opacity: 0.3;'";}; echo"><span class=\"tooltiptext4\">Slider</span></div>
                       </div>
                      </td>
                      <td>0</td>
                      <td>0</td>
                      <td>".$platform['click']."</td>
                      <td class=\"greentext\" style=\"min-width: 140px;\">".$platform['prosmotr']." (".$protsent_prochteniy."%)</td>
					  <td style=\"width: 160px;\" >
                         <canvas id=\"d".$i['id']."\" width=\"92\" height=\"32\"></canvas>
                         <script>
// ChartJS

var dataset_01 = {
    backgroundColor: \"rgba(17,109,214,0.1)\",
    borderColor: \"rgba(17,109,214,1)\",
	pointColor: \"rgba(17,109,214,1)\",
	pointBorderColor: \"rgba(17,109,214,1)\",
	borderWidth: \"2\",
	pointRadius: 2,
	pointBackgroundColor: \"#116dd6\",
    data: [".$grafik."]
};

var data = {
    labels: [\"\", \"\", \"\", \"\", \"\", \"\", \"\"],
    datasets: [dataset_01]
};

var options = {
  title: { display: false},
  legend:{ display:false },
  //maintainAspectRatio : false,
  //responsive: false,
  tooltips: {enabled: false},
  animation: {
      duration : 1800,  
      easing : \"easeOutBack\"
  },
  layout: {
            padding: {
                left: 5,
                right: 5,
                top: 5,
                bottom: 5
            }
		},	
  scales:{
      xAxes: [{ display: false }],
      yAxes: [{ display: false }]
  }
};

var ctx = document.getElementById(\"d".$i['id']."\").getContext(\"2d\");

var myLineChart = new Chart(ctx, {
    type: \"line\",
    data: data,
    options : options
});
                      </script>
					  </td>
					  <td class=\"bluetext\">" . $platform['dohod'] . "</td>
                      <td style=\"width: 111px; text-align: right; padding-right: 20px\";>
						 <a class=\"main-item\" href=\"javascript:void(0);\" tabindex=\"1\"  style=\"font-size: 34px; line-height: 1px; vertical-align: super; text-decoration: none; color: #768093;\">...</a> 
                         <ul class=\"sub-menu\">
                              <a href='platforms-edit?id=" . $i['id'] . "'>Настройка</a><br>
                              <a class='modalclick' id='otchiclen".$i['id']."'>Отчисления</a><br>";
                              if ($i['type']!='demo'){
                                  echo "<a href='platform-stat?id=".$i['id']."'>Статистика</a></br>";
                              }
                              echo "
                              <a href='platforms-del?id=" . $i['id'] . "'>Удалить</a> 
                         </ul>
                         
                         <div class=\"modal otchislen\" id='modalotch".$i['id']."' style=\"left:30%;top:300px;right:30%;display: none;\">
                            <div style=\"min-width: 780px !important;\" class=\"div-block-78 w-clearfix\">
                                <div class=\"div-block-132 modalhide\">
                                    <img src=\"/images/close.png\" alt=\"\" class=\"image-5\">
                                </div>
                                <div class=\"polzunok-container\">
								    <div class=\"text-block-103\" style=\"text-align: left; margin-bottom: 40px;\">Настройка процента отчислений для площадки</div>
                                    <div class=\"polzunok\" id=\"polz".$i['id']."\">
                                    </div>
                                </div>
                            </div>
                         </div>
                                     
                      </td>
                  </tr>";
                };
                echo "</table>\n
							
				<script>
                    $(\".polzunok\").slider({min:0,max:200,range:\"min\",animate:\"slow\",slide:function(event, ui){
                        $('#'+this.id+' span').html(\"<b>&lt;</b>\"+ui.value+\"%<b>&gt;</b>\");
                        $.get(\"https://panel.cortonlab.com/platforms-otchicleniay?id=\"+this.id+\"&otchiclen=\"+ui.value);
                        $('#otchic'+this.id.substr(4)).html(ui.value);
                    }});\n";
                foreach ($result as $i) {
                    echo "$(\"#polz".$i['id']."\").slider(\"value\",".$i['otchiclen'].");$(\"#polz".$i['id']." span\").html(\"<b>&lt;</b>\"+$(\"#polz".$i['id']."\").slider(\"value\")+\"%<b>&gt;</b>\");\n";
                }
                echo '</script>
        </div>';
        echo '
		<div class="table-right">
		    <form id="right-form" action="/platforms" name="email-form" class="form-333">
			<a href="/platforms-add" class="button-add-site w-button">Добавить площадку</a>';
        if ($GLOBALS['role']=='admin')
        echo '
			<a href="/platforms-edit?id=0" class="button-css-edit w-button">Стандартные стили</a>';
        echo '    
			<br><br>
			<p class="filtermenu"><label'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="all"class="form-radio"'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' checked';}  echo'>Все статусы площадок</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='1'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="1"  class="form-radio"'; if ($_GET['status']=='1'){echo ' checked';} echo'>Активные площадки</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='0'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="0"  class="form-radio"'; if ($_GET['status']=='0'){echo ' checked';} echo'>Неактивные площадки</label></p>
            <br>
            <p class="filtermenu"><label'; if ((!isset($_GET['type'])) OR ($_GET['type']=='all')){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="type" value="all"  class="form-radio"'; if ((!isset($_GET['type'])) OR ($_GET['type']=='all')){echo ' checked';} echo'>Все типы площадок</label></p>
			<p class="filtermenu"><label'; if ($_GET['type']=='info'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="type" value="info" class="form-radio"'; if ($_GET['type']=='info'){echo ' checked';} echo'>Информационные</label></p>
			<p class="filtermenu"><label'; if ($_GET['type']=='news'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="type" value="news" class="form-radio"'; if ($_GET['type']=='news'){echo ' checked';} echo'>Новостные</label></p>
			<p class="filtermenu"><label'; if ($_GET['type']=='demo'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="type" value="demo" class="form-radio"'; if ($_GET['type']=='demo'){echo ' checked';} echo'>Демонстрационные</label></p>
            

			<div class="html-embed-3 w-embed" style="margin-top: 40px;">
            <input type="text" name="datebegin" class="tcal tcalInput" value="'.$datebegin.'">
            <div class="text-block-128">-</div>
			<input type="text" name="dateend" class="tcal tcalInput" value="'.$dateend.'">
            <input type="submit" value="Применить" style="left: 0px !important;" class="submit-button-addkey w-button">
            </div>
			</form>
		</div>
		
		</div>
		
        <div class="div-block-98">
          <div>
            <div class="text-block-111">&lt;</div>
          </div>
          <div>
            <div class="text-block-111">1</div>
          </div>
          <div>
            <div class="text-block-111">2</div>
          </div>
          <div>
            <div class="text-block-111">3</div>
          </div>
          <div>
            <div class="text-block-111">4</div>
          </div>
          <div>
            <div class="text-block-111">&gt;</div>
          </div>
        </div>
        <div class="black-fon modalhide"></div>
		';
                include PANELDIR . '/views/layouts/footer.php';
                return true;
            }

            //Обновляет площадку
            public static function actionUpdate()
            {
                $db = Db::getConnection();
                if ($_POST['aktiv']=='on'){$aktiv=1;}else{$aktiv=0;};
                //Создание домена площадки
                if (addslashes($_REQUEST['id'])==""){
                    $sql="INSERT INTO `ploshadki` SET `domen`='".$_POST['domen']."',`status`='".$aktiv."',`user_email`='".$_POST['user']."',`type`='".$_POST['type']."', `categoriya`='".$_POST['categoriya']."', `podcategoriya`='".$_POST['podcategoriya']."', `promo_page`='".$_POST['promo_page']."', `CTR`='".$_POST['CTR']."',`CPM`='".$_POST['CPM']."',`CPG`='".$_POST['CPG']."',`demo-annons`='".$_POST['demo-annons']."', `date_add` = '".date('Y-m-d')."';";
                    $db->query($sql);
                    $id=$db->lastInsertId();
                    WidgetcssController::UpdateCSSfile($id);
                    header('Location: /platforms-edit?id='.$id);exit;
                } else {
                    //Обновление домена площадки
                    $sql="SELECT `domen` FROM `ploshadki` WHERE `id` = ".$_POST['id'];
                    $domen = $db->query($sql)->fetch(PDO::FETCH_COLUMN);
                    if ($domen!=$_POST['domen']) {
                        $domen = str_replace(".", "_", $domen);
                        $domen2 = str_replace(".", "_", $_POST['domen']);
                        rename(PANELDIR . '/style/' . $domen . '.min.css', PANELDIR . '/style/' . $domen2 . '.min.css');
                    }

                    if ($_POST['aktiv']=='on'){$aktiv=1;}else{$aktiv=0;};
                    if ($_POST['zagrecomend']=='on'){$zagrecomend=1;}else{$zagrecomend=0;};
                    if ($_POST['zagnatprev']=='on'){$zagnatprev=1;}else{$zagnatprev=0;};
                    if ($_POST['zagnatpro']=='on'){$zagnatpro=1;}else{$zagnatpro=0;};
                    $sql="
                    UPDATE
                        `ploshadki`
                    SET
                        `domen` = '".$_POST['domen']."',
                        `type` = '".$_POST['type']."',
                        `categoriya` = '".$_POST['categoriya']."',
                        `podcategoriya` ='".$_POST['podcategoriya']."',
                        `promo_page`='".$_POST['promo_page']."',
                        `user_id` = '".$_POST['user_id']."',
                        `status` ='".$aktiv."',
                        `recomend_zag_aktiv` = ".$zagrecomend.",
                        `natpre_zag_aktiv` = '".$zagnatprev."',
                        `natpro_zag_aktiv` = '".$zagnatpro."',
                        `user_id` = '".$_POST['user_id']."',
                        `demo-annons`='".$_POST['demo-annons']."',
                        `CTR`='".$_POST['CTR']."',
                        `CPM`='".$_POST['CPM']."',
                        `CPG`='".$_POST['CPG']."'
                    WHERE `id`='".$_POST['id']."';";

                    $db->query($sql);
                    WidgetcssController::UpdateCSSfile($_POST['id']);
                    header('Location: /platforms');
                }
                return true;
            }

            public static function form()
            {
                $db=Db::getConnection();
                if (addslashes($_REQUEST['id'])!='') {
                    $sql = "SELECT  `domen`, `type`, `categoriya`, `podcategoriya`, `user_id`, `status`, `recomend_aktiv`, `recomend_zag_aktiv`, `natpre_aktiv`, `natpre_zag_aktiv`, `natpro_aktiv`, `natpro_zag_aktiv`, `slider_aktiv`,`demo-annons`,`CTR`,`CPM`,`CPG`,`promo_page` FROM `ploshadki` WHERE `id`='".addslashes($_REQUEST['id'])."';";
                    $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                }else{
                    $result['status']=1;
                };

                if ($result['status']) $status="checked=\"\"";
                if ($result['recomend_zag_aktiv']) $zagrecomendaktiv="checked";
                if ($result['natpre_zag_aktiv']) $zagnatprevaktiv="checked";
                if ($result['natpro_zag_aktiv']) $zagnatproaktiv="checked";
                if ($result['recomend_aktiv']){$recomendaktiv='<div id="recomend" class="text-block-118">Настройки</div>';}else{$recomendaktiv='<div id="recomend" class="text-block-118">Активировать</div>';};
                if ($result['natpre_aktiv']){$natpreaktiv='<div id="natprev" class="text-block-118">Настройки</div>';}else{$natpreaktiv='<div id="natprev" class="text-block-118">Активировать</div>';};
                if ($result['natpro_aktiv']){$natproaktiv='<div id="natpro" class="text-block-118">Настройки</div>';}else{$natproaktiv='<div id="natpro" class="text-block-118">Активировать</div>';};
                if ($result['slider_aktiv']){$slideraktiv='<div id="slider" class="text-block-118">Настройки</div>';}else{$slideraktiv='<div id="slider" class="text-block-118">Активировать</div>';};


                $sql="SELECT `id`,`email` FROM `users` WHERE (aktiv='1' AND role='platform') ORDER BY `email` ASC";
                $user = $db->query($sql)->fetchALL(PDO::FETCH_ASSOC);
                if (($_GET['id']==0)and($_SERVER['REQUEST_URI']!='/platforms-add')) $disabled='disabled';
                echo '
        <div class="section-2">
          <div class="w-form">
            <form method="post" action="/platforms-update" class="form">
              <input type="hidden" name="id" value="'.addslashes($_REQUEST['id']).'">
              <div class="div-block-102">
			  <div class="boxsetting">
                <div class="text-block-103">Общие настройки</div>
                <div class="div-block-130">
                  <input type="text" class="text-field-10 w-input" maxlength="256" name="domen" value="'.$result['domen'].'" placeholder="Домен сайта" id="domen" required="" '.$disabled.'>
                  <div class="div-block-131">
                    <div class="html-embed-21 w-embed"><label>
                     <input type="checkbox" class="ios-switch tinyswitch" '.$status.' name="aktiv" '.$disabled.'><div><div></div></div></label></div>
                    <div class="text-block-138">Отключен / Активен</div>
                  </div> 
                </div>
                <select name="type" required="" class="select-field w-select" '.$disabled.'>
                  <option value="">Тип ресурса</option>
                  <option '; if ($result['type']=='news' ) echo "selected"; echo ' value="news">Новостной</option>
                  <option '; if ($result['type']=='info' ) echo "selected"; echo ' value="info">Информационный</option>';
                    if ($result['type']!='news' and $result['type']!='info'){
                        echo '<option '; if ($result['type']=='demo' ) echo "selected"; echo ' value="demo">Демонстрационный</option>';
                    };
                  echo'
                </select>
                <select name="user_id" required="" class="select-field w-select" '.$disabled.'>
                  <option value="">Владелец площадки</option>';
                foreach($user as $i){
                    echo "<option ";
                    if ($result['user_id']==$i['id'] ) echo "selected ";
                    echo "value=\"".$i['id']."\">".$i['email']."</option>";
                };
                echo '
                </select>
                <select id="categoriya" name="categoriya" required="" class="select-field w-select" '.$disabled.'>
                  <option value="">Категория площадки</option>
                  <option '; if ($result['categoriya']=='Здоровье и медицина' ) echo "selected"; echo ' value="Здоровье и медицина">Здоровье и медицина</option>
                  <option '; if ($result['categoriya']=='Еда' ) echo "selected"; echo ' value="Еда">Еда</option>
                  <option '; if ($result['categoriya']=='Авто' ) echo "selected"; echo ' value="Авто">Авто</option>
                  <option '; if ($result['categoriya']=='Недвижимость' ) echo "selected"; echo ' value="Недвижимость">Недвижимость</option>
                </select>
                <div id="podcategoriyaval" hidden>'.$result['podcategoriya'].'</div>
                <select id="podcategoriya" name="podcategoriya" required="" class="select-field w-select" '.$disabled.'>
                  <option value="">Подкатегория площадки</option>
                </select>
				<div style="width: 1337px; margin-bottom: 60px;"></div>
				
				';
                  if ($result['type']=='demo')
                 echo'
                </div>
                  <input type="text" class="text-field-10 w-input" maxlength="256" style="width: 760px; margin-left: 20px;" name="CTR" value="'.$result['CTR'].'" placeholder="CTR, %" required="">
                  <input type="text" class="text-field-10 w-input" maxlength="256" style="width: 760px; margin-left: 20px;" name="CPM" value="'.$result['CPM'].'" placeholder="CPM, руб." required="">
                  <input type="text" class="text-field-10 w-input" maxlength="256" style="width: 760px; margin-left: 20px;" name="CPG" value="'.$result['CPG'].'" placeholder="CPG, pуб." required="">
                  <input type="text" class="text-field-10 w-input" maxlength="256" style="width: 760px; margin-left: 20px;" name="demo-annons" value="'.$result['demo-annons'].'" placeholder="id анонсов через запятую" required="">
                </div>
                ';echo'
				</div>
              <div class="div-block-102">
			  <div class="boxsetting">
                <div class="text-block-103">Скрипт и стили</div>
                <div class="text-block-115">Для подключения площадки добавьте между тегами head скрипт и файл стилей:</div>
                <div class="html-embed w-embed" id="fileadrres">';
                if (isset($result['domen'])){
                    $domen = str_replace(".", "_", $result['domen']);
                    echo '&lt;link href="https://api.cortonlab.com/css/'.$domen.'.css.gz" rel="stylesheet"&gt;<br><br> &lt;script async src="https://api.cortonlab.com/js/cortonlab.js.gz" charset="UTF-8"&gt;&lt;/script&gt;';
                } else {echo 'Заполните домен';};
                echo '
                </div>
			  </div>
              </div>
              <div class="div-block-102">
			  <div class="boxsetting">
                <div class="text-block-103">Настройка промо-страницы</div>
                <div class="text-block-115">Создайте пустой шаблон страницы на вашем сайте без внешних скриптов и отключить функцию комментариев​. После чего разместите этот код между тегами &lt;body&gt;&lt;/body&gt; на странице:</div>
                <div class="html-embed w-embed">&lt;div id="corton-promo"&gt;&lt;/div&gt;</div>
                <div class="div-block-103"></div><input type="text" maxlength="256" placeholder="url promo страницы или её шаблона, (указывать без протокола)" id="url" name="promo_page" value="'.$result['promo_page'].'" class="text-field-10 w-input" '.$disabled.' required="">';
              if (addslashes($_REQUEST['id'])!='') {
                      echo '<div id="promo" class="text-block-118">Настройка статей</div></div>';
                  }
              else
                {
                    echo '<div id="checktag" class="text-block-118">Проверка установки тегов</div></div>';
                };
              if (addslashes($_REQUEST['id'])!='') {
                    echo '
				</div>
                <div style="padding-left:20px;" class="div-block-102">
                <div class="text-block-103">Настройка виджетов</div>
                <div class="text-block-115">Настройти внешний вид каждого виджета и следуйте инструкциям по установки.</div>
                <div class="div-block-115">
                  <div class="div-block-107">
                    <div class="text-block-117">Recommendation</div>
                    <div class="div-block-105"><img src="/images/vj-recom.png" alt="" class="image-3"></div>
                    <div class="div-block-106">
                      '.$recomendaktiv.'
                      <div class="div-block-113">
                        <div class="div-block-108">
                          <div class="checkbox-field-4 w-checkbox">
                          <input type="checkbox" name="zagrecomend" '.$zagrecomendaktiv.' class="form-radiozag" '.$disabled.'>
                          <label id="zagrecomend" class="w-form-label">
                          <a class="link">Заглушка</a>
                          </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="div-block-107">
                    <div class="text-block-117">Native Preview</div>
                    <div class="div-block-105"><img src="/images/vj-native1.png" alt="" class="image-3"></div>
                    <div class="div-block-106">
                      '.$natpreaktiv.'
                      <div class="div-block-113">
                        <div class="div-block-108">
                          <div class="checkbox-field-4 w-checkbox">
                          <input type="checkbox" name="zagnatprev" '.$zagnatprevaktiv.' class="form-radiozag" '.$disabled.'>
                          <label id="zagnatprev" class="w-form-label">
                          <a class="link">Заглушка</a>
                          </label></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="div-block-115">
                  <div class="div-block-107">
                    <div class="text-block-117">Native Pro</div>
                    <div class="div-block-105"><img src="/images/vj-nativepro.png" alt="" class="image-3"></div>
                    <div class="div-block-106">
                      '.$natproaktiv.'
                      <div class="div-block-113">
                        <div class="div-block-108">
                          <div class="checkbox-field-4 w-checkbox">
                          <input type="checkbox" name="zagnatpro" '.$zagnatproaktiv.' class="form-radiozag" '.$disabled.'>
                          <label id="zagnatpro" class="w-form-label">
                          <a class="link">Заглушка</a>
                          </label></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="div-block-107">
                    <div class="text-block-117">Slider</div>
                    <div class="div-block-105"><img src="/images/vj-slider.png" alt="" class="image-3"></div>
                    <div class="div-block-106">
                      '.$slideraktiv.'
                    </div>
                  </div>
                </div>
              </div>
			  <div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px;"></div>';}
                echo'
			  <input type="submit" value="Сохранить настройки" class="submit-button-6 w-button2" '.$disabled.'>
			  
            </form>
          </div>
        </div>';
		     include PANELDIR . '/views/layouts/footer.php';
             return true;
            }

            //Добавления площадки
            public static function actionAdd()
            {
                $title='Добавление площадки';
                include PANELDIR . '/views/layouts/header.php';

                PloshadkiController::form();

                include PANELDIR . '/views/layouts/footer.php';
                return true;
            }

            //Редактирование  площадки
            public static function actionEdit()
            {
                $db = Db::getConnection();
                $title='Редактирование площадки';
                include PANELDIR.'/views/layouts/header.php';
                PloshadkiController::form();

                $sql="SELECT * FROM `style_promo` WHERE `id`='".addslashes($_REQUEST['id'])."';";$result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($result==false){$sql="SELECT * FROM `style_promo` WHERE `id`='0';"; $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);};

                echo '
<div class="modal promo">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-2">Редактировать стиль статьи</strong></div>
        <div class="div-block-117">
            <div class="div-block-119">
                <div class="w-form">
                    <form method="post" action="/widget-update" class="form-6">
                        <div class="widget-promo">
                            <div class="html-embed-13 w-embed">
                                <input type="hidden" value="style_promo" name="type">
                                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                                <input type="hidden" value="" name="css">
                                <input type="hidden" value="" name="dop-css">
                                <input type="hidden" value="" name="adblock-css">
                                <strong class="bold-text-10">Расположение</strong>                            
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['forcibly']) echo ' checked '; echo 'name="forcibly" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Вывод на месте селектора</a>
                                      </label>
                                </div> 
							                                
                                <label>Селектор</label>
                                <input type="text" value="'.$result['selector'].'" name="selector">
                                <br>
                                <br>
                                <label>Селектор заголовка</label>
                                <input type="text" value="'.$result['selector-title'].'" name="selector-title">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Настройки блока</strong>
                                <label>Единица измерения шрифта</label>
                                <select name="widget-font-unit" class="widget-font-unit">
                                    <option '; if ($result['widget-font-unit']=='px' ) echo "selected"; echo ' value="px">px</option>
                                    <option '; if ($result['widget-font-unit']=='em' ) echo "selected"; echo ' value="em">em</option>
                                </select>
                                <br>
                                <label>Цвет фона</label>
                                <input value="'.$result['widget-background-block'].'" name="widget-background-block" class="jscolor widget-background-block">
                                <br>
                                <label>Ширина блока (%)</label>
                                <input type="text" value="'.$result['widget-width-block'].'" name="widget-width-block">
                                <br>
                                <br>
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['icon']) echo ' checked ';echo 'name="icon" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Марка партнера</a>
                                      </label>
                                </div> 
								
                                <br>
                                <br>
                                <strong class="bold-text-10">Заголовок Н1</strong>
                                <label>Шрифт</label>
                                <select name="widget-h1-font">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-h1-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-h1-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-h1-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-h1-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-h1-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-h1-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-h1-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-h1-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-h1-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-h1-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-h1-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-h1-size">
                                    <option value=""></option>
                                    <option '; if ($result['widget-h1-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-h1-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-h1-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-h1-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-h1-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-h1-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-h1-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['widget-h1-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['widget-h1-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                    <option '; if ($result['widget-h1-size']=="21"){echo 'selected ';} echo 'value="21">21px / 2.1em</option>
                                    <option '; if ($result['widget-h1-size']=="22"){echo 'selected ';} echo 'value="22">22px / 2.2em</option>
                                    <option '; if ($result['widget-h1-size']=="23"){echo 'selected ';} echo 'value="23">23px / 2.3em</option>
                                    <option '; if ($result['widget-h1-size']=="24"){echo 'selected ';} echo 'value="24">24px / 2.4em</option>
                                    <option '; if ($result['widget-h1-size']=="25"){echo 'selected ';} echo 'value="25">25px / 2.5em</option>
                                    <option '; if ($result['widget-h1-size']=="26"){echo 'selected ';} echo 'value="26">26px / 2.6em</option>
                                    <option '; if ($result['widget-h1-size']=="27"){echo 'selected ';} echo 'value="27">27px / 2.7em</option>
                                    <option '; if ($result['widget-h1-size']=="28"){echo 'selected ';} echo 'value="28">28px / 2.8em</option>
                                    <option '; if ($result['widget-h1-size']=="29"){echo 'selected ';} echo 'value="29">29px / 2.9em</option>
                                    <option '; if ($result['widget-h1-size']=="30"){echo 'selected ';} echo 'value="30">30px / 3em</option>
                                    <option '; if ($result['widget-h1-size']=="31"){echo 'selected ';} echo 'value="31">31px / 3.1em</option>
                                    <option '; if ($result['widget-h1-size']=="32"){echo 'selected ';} echo 'value="32">32px / 3.2em</option>
                                    <option '; if ($result['widget-h1-size']=="33"){echo 'selected ';} echo 'value="33">33px / 3.3em</option>
                                    <option '; if ($result['widget-h1-size']=="34"){echo 'selected ';} echo 'value="34">34px / 3.4em</option>
                                    <option '; if ($result['widget-h1-size']=="35"){echo 'selected ';} echo 'value="35">35px / 3.5em</option>
                                    <option '; if ($result['widget-h1-size']=="36"){echo 'selected ';} echo 'value="36">36px / 3.6em</option>
                                    <option '; if ($result['widget-h1-size']=="37"){echo 'selected ';} echo 'value="37">37px / 3.7em</option>
                                    <option '; if ($result['widget-h1-size']=="38"){echo 'selected ';} echo 'value="38">38px / 3.8em</option>
                                    <option '; if ($result['widget-h1-size']=="39"){echo 'selected ';} echo 'value="39">39px / 3.9em</option>
                                    <option '; if ($result['widget-h1-size']=="40"){echo 'selected ';} echo 'value="40">40px / 4em</option>
                                    <option '; if ($result['widget-h1-size']=="41"){echo 'selected ';} echo 'value="41">41px / 4.1em</option>
                                    <option '; if ($result['widget-h1-size']=="42"){echo 'selected ';} echo 'value="42">42px / 4.2em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-h1-color'].'" name="widget-h1-color" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-h1-bold">
									<option value=""></option>
                                    <option '; if ($result['widget-h1-bold']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-h1-bold']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-h1-bold']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-h1-bold']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-h1-bold']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-h1-bold']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-h1-bold']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-h1-bold']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-h1-bold']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-h1-italic']) echo ' checked '; echo'name="widget-h1-italic" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-h1-underline']) echo ' checked '; echo'name="widget-h1-underline" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div>
								
                                <br>
                                <br>
                                <strong class="bold-text-10">Заголовок Н2</strong>
                                <label>Шрифт</label>
                                <select name="widget-h2-font">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-h2-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-h2-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-h2-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-h2-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-h2-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-h2-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-h2-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-h2-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-h2-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-h2-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-h2-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-h2-size">
                                    <option value=""></option>
                                    <option '; if ($result['widget-h2-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-h2-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-h2-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-h2-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-h2-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-h2-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-h2-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['widget-h2-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['widget-h2-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                    <option '; if ($result['widget-h2-size']=="21"){echo 'selected ';} echo 'value="21">21px / 2.1em</option>
                                    <option '; if ($result['widget-h2-size']=="22"){echo 'selected ';} echo 'value="22">22px / 2.2em</option>
                                    <option '; if ($result['widget-h2-size']=="23"){echo 'selected ';} echo 'value="23">23px / 2.3em</option>
                                    <option '; if ($result['widget-h2-size']=="24"){echo 'selected ';} echo 'value="24">24px / 2.4em</option>
                                    <option '; if ($result['widget-h2-size']=="25"){echo 'selected ';} echo 'value="25">25px / 2.5em</option>
                                    <option '; if ($result['widget-h2-size']=="26"){echo 'selected ';} echo 'value="26">26px / 2.6em</option>
                                    <option '; if ($result['widget-h2-size']=="27"){echo 'selected ';} echo 'value="27">27px / 2.7em</option>
                                    <option '; if ($result['widget-h2-size']=="28"){echo 'selected ';} echo 'value="28">28px / 2.8em</option>
                                    <option '; if ($result['widget-h2-size']=="29"){echo 'selected ';} echo 'value="29">29px / 2.9em</option>
                                    <option '; if ($result['widget-h2-size']=="30"){echo 'selected ';} echo 'value="30">30px / 3em</option>
                                    <option '; if ($result['widget-h2-size']=="31"){echo 'selected ';} echo 'value="31">31px / 3.1em</option>
                                    <option '; if ($result['widget-h2-size']=="32"){echo 'selected ';} echo 'value="32">32px / 3.2em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-h2-color'].'" name="widget-h2-color" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-h2-bold">
									<option value=""></option>
                                    <option '; if ($result['widget-h2-bold']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-h2-bold']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-h2-bold']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-h2-bold']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-h2-bold']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-h2-bold']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-h2-bold']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-h2-bold']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-h2-bold']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-h2-italic']) echo ' checked '; echo ' name="widget-h2-italic" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-h2-underline']) echo ' checked '; echo ' name="widget-h2-underline" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div>

                                <br>
                                <br>
                                <strong class="bold-text-10">Текст</strong>
                                <label>Шрифт</label>
                                <select name="widget-text-font">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-text-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-text-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-text-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-text-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-text-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-text-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-text-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-text-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-text-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-text-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-text-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-text-size">
                                    <option value=""></option>
                                    <option '; if ($result['widget-text-size']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-text-size']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-text-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-text-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-text-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-text-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-text-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-text-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-text-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['widget-text-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['widget-text-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-text-color'].'" name="widget-text-color" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-text-bold">
									<option value=""></option>
                                    <option '; if ($result['widget-text-bold']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-text-bold']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-text-bold']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-text-bold']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-text-bold']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-text-bold']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-text-bold']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-text-bold']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-text-bold']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта текста</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-text-italic"']) echo ' checked '; echo ' name="widget-text-italic" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-text-underline']) echo ' checked '; echo ' name="widget-text-underline" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div> 

                                <br>
                                <label>Межстрочный интервал</label>
                                <select name="widget-type-interval-text">
                                    <option '; if ($result['widget-type-interval-text']=="0.9"){echo 'selected ';} echo 'value="0.9">0.9</option>
                                    <option '; if ($result['widget-type-interval-text']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.2"){echo 'selected ';} echo 'value="1.2">1.2</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.3"){echo 'selected ';} echo 'value="1.3">1.3</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.5"){echo 'selected ';} echo 'value="1.5">1.5</option>
                                </select>
                                <br>
                                <label>Цвет ссылки</label>
                                <input value="'.$result['widget-a-color'].'" name="widget-a-color" class="jscolor">
                                <br>
                            </div>
                            <div class="html-embed-14 w-embed">
                                <br>
                                <br>
                                <strong class="bold-text-10">Настройка Формы</strong>
                                <label>Ширина блока, %</label>
                                <input type="text" value="'.$result['form-width'].'" name="form-width">
                                <br>
                                <label>Выравнивание блока</label>
                                <select name="form-blok-aling">
                                    <option '; if ($result['form-blok-aling']=="left"){echo 'selected ';} echo 'value="left">Слева</option>
                                    <option '; if ($result['form-blok-aling']=="center"){echo 'selected ';} echo 'value="center">По центру</option>
                                </select>
                                <br>
                                <label>Выравнивание содержимого блока</label>
                                <select name="form-in-blok-aling">
                                    <option '; if ($result['form-in-blok-aling']=="left"){echo 'selected ';} echo 'value="left">Слева</option>
                                    <option '; if ($result['form-in-blok-aling']=="center"){echo 'selected ';} echo 'value="center">По центру</option>
                                </select>
                                <br>
                                <label>Цвет фона</label>
                                <input value="'.$result['form-palitra-color'].'" name="form-palitra-color" class="jscolor">
                                <br>
                                <label>Толщина обводки</label>
                                <select name="form-border-width">
                                    <option '; if ($result['form-border-width']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-border-width']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-border-width']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-border-width']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-border-width']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-border-width']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-border-width']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-border-width']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-border-width']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-border-width']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-border-width']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Скругление обводки</label>
                                <select name="form-border-radius">
                                    <option '; if ($result['form-border-radius']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-border-radius']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-border-radius']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-border-radius']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-border-radius']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-border-radius']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-border-radius']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-border-radius']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-border-radius']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-border-radius']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-border-radius']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                    <option '; if ($result['form-border-radius']=="11"){echo 'selected ';} echo 'value="11">11 px</option>
                                    <option '; if ($result['form-border-radius']=="12"){echo 'selected ';} echo 'value="12">12 px</option>
                                    <option '; if ($result['form-border-radius']=="13"){echo 'selected ';} echo 'value="13">13 px</option>
                                    <option '; if ($result['form-border-radius']=="14"){echo 'selected ';} echo 'value="14">14 px</option>
                                    <option '; if ($result['form-border-radius']=="15"){echo 'selected ';} echo 'value="15">15 px</option>
                                    <option '; if ($result['form-border-radius']=="16"){echo 'selected ';} echo 'value="16">16 px</option>
                                    <option '; if ($result['form-border-radius']=="17"){echo 'selected ';} echo 'value="17">17 px</option>
                                    <option '; if ($result['form-border-radius']=="18"){echo 'selected ';} echo 'value="18">18 px</option>
                                    <option '; if ($result['form-border-radius']=="19"){echo 'selected ';} echo 'value="19">19 px</option>
                                    <option '; if ($result['form-border-radius']=="20"){echo 'selected ';} echo 'value="20">20 px</option>
                                    <option '; if ($result['form-border-radius']=="21"){echo 'selected ';} echo 'value="21">21 px</option>
                                    <option '; if ($result['form-border-radius']=="22"){echo 'selected ';} echo 'value="22">22 px</option>
                                    <option '; if ($result['form-border-radius']=="23"){echo 'selected ';} echo 'value="23">23 px</option>
                                    <option '; if ($result['form-border-radius']=="24"){echo 'selected ';} echo 'value="24">24 px</option>
                                    <option '; if ($result['form-border-radius']=="25"){echo 'selected ';} echo 'value="25">25 px</option>
                                    <option '; if ($result['form-border-radius']=="26"){echo 'selected ';} echo 'value="26">26 px</option>
                                    <option '; if ($result['form-border-radius']=="27"){echo 'selected ';} echo 'value="27">27 px</option>
                                    <option '; if ($result['form-border-radius']=="28"){echo 'selected ';} echo 'value="28">28 px</option>
                                    <option '; if ($result['form-border-radius']=="29"){echo 'selected ';} echo 'value="29">29 px</option>
                                    <option '; if ($result['form-border-radius']=="30"){echo 'selected ';} echo 'value="30">30 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки</label>
                                <input value="'.$result['form-palitra-border-color'].'" name="form-palitra-border-color" class="jscolor">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Заголовок формы</strong>
                                <label>Шрифт заголовка</label>
                                <select name="form-h2-font">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['form-h2-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['form-h2-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['form-h2-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['form-h2-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['form-h2-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['form-h2-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['form-h2-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['form-h2-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['form-h2-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['form-h2-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['form-h2-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Цвет заголовка</label>
                                <input value="'.$result['form-h2-color'].'" name="form-h2-color" class="jscolor">
                                <br>
                                <label>Размер заголовка</label>
                                <select name="form-h2-size">
                                    <option value=""></option>
                                    <option '; if ($result['form-h2-size']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['form-h2-size']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['form-h2-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['form-h2-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['form-h2-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['form-h2-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['form-h2-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['form-h2-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['form-h2-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['form-h2-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['form-h2-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                    <option '; if ($result['form-h2-size']=="21"){echo 'selected ';} echo 'value="21">21px / 2.1em</option>
                                    <option '; if ($result['form-h2-size']=="22"){echo 'selected ';} echo 'value="22">22px / 2.2em</option>
                                    <option '; if ($result['form-h2-size']=="23"){echo 'selected ';} echo 'value="23">23px / 2.3em</option>
                                    <option '; if ($result['form-h2-size']=="24"){echo 'selected ';} echo 'value="24">24px / 2.4em</option>
                                    <option '; if ($result['form-h2-size']=="25"){echo 'selected ';} echo 'value="25">25px / 2.5em</option>
                                    <option '; if ($result['form-h2-size']=="26"){echo 'selected ';} echo 'value="26">26px / 2.6em</option>
                                    <option '; if ($result['form-h2-size']=="27"){echo 'selected ';} echo 'value="27">27px / 2.7em</option>
                                    <option '; if ($result['form-h2-size']=="28"){echo 'selected ';} echo 'value="28">28px / 2.8em</option>
                                    <option '; if ($result['form-h2-size']=="29"){echo 'selected ';} echo 'value="29">29px / 2.9em</option>
                                    <option '; if ($result['form-h2-size']=="30"){echo 'selected ';} echo 'value="30">30px / 3em</option>
                                    <option '; if ($result['form-h2-size']=="31"){echo 'selected ';} echo 'value="31">31px / 3.1em</option>
                                    <option '; if ($result['form-h2-size']=="32"){echo 'selected ';} echo 'value="32">32px / 3.2em</option>
                                    <option '; if ($result['form-h2-size']=="33"){echo 'selected ';} echo 'value="33">33px / 3.3em</option>
                                    <option '; if ($result['form-h2-size']=="34"){echo 'selected ';} echo 'value="34">34px / 3.4em</option>
                                    <option '; if ($result['form-h2-size']=="35"){echo 'selected ';} echo 'value="35">35px / 3.5em</option>
                                    <option '; if ($result['form-h2-size']=="36"){echo 'selected ';} echo 'value="36">36px / 3.6em</option>
                                </select>
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Поля ввода данных</strong>
                                <label>Фон полей</label>
                                <input value="'.$result['form-input-baground-color'].'" name="form-input-baground-color" class="jscolor">
                                <br>
                                <label>Обводка полей</label>
                                <select name="form-input-text-border-size">
                                    <option '; if ($result['form-input-text-border-size']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-input-text-border-size']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Скругление обводки</label>
                                <select name="form-input-text-border-radius">
                                    <option '; if ($result['form-input-text-border-radius']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="11"){echo 'selected ';} echo 'value="11">11 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="12"){echo 'selected ';} echo 'value="12">12 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="13"){echo 'selected ';} echo 'value="13">13 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="14"){echo 'selected ';} echo 'value="14">14 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="15"){echo 'selected ';} echo 'value="15">15 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="16"){echo 'selected ';} echo 'value="16">16 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="17"){echo 'selected ';} echo 'value="17">17 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="18"){echo 'selected ';} echo 'value="18">18 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="19"){echo 'selected ';} echo 'value="19">19 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="20"){echo 'selected ';} echo 'value="20">20 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="21"){echo 'selected ';} echo 'value="21">21 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="22"){echo 'selected ';} echo 'value="22">22 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="23"){echo 'selected ';} echo 'value="23">23 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="24"){echo 'selected ';} echo 'value="24">24 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="25"){echo 'selected ';} echo 'value="25">25 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="26"){echo 'selected ';} echo 'value="26">26 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="27"){echo 'selected ';} echo 'value="27">27 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="28"){echo 'selected ';} echo 'value="28">28 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="29"){echo 'selected ';} echo 'value="29">29 px</option>
                                    <option '; if ($result['form-input-text-border-radius']=="30"){echo 'selected ';} echo 'value="30">30 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки полей</label>
                                <input value="'.$result['form-input-text-border-color'].'" name="form-input-text-border-color" class="jscolor">
                                <br>
                            </div>
                            <div class="html-embed-15 w-embed">
                                <br>
                                <br>
                                <strong class="bold-text-10">Текст полей ввода данных</strong>
                                <label>Шрифт шаблона полей</label>
                                <select name="form-input-text-font">
									<option value="">Шрифты сайта</option>
                                    <option '; if ($result['form-input-text-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['form-input-text-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['form-input-text-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['form-input-text-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['form-input-text-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['form-input-text-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['form-input-text-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['form-input-text-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['form-input-text-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['form-input-text-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['form-input-text-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Цвет шрифта шаблона полей</label>
                                <input value="'.$result['form-input-text-color'].'" name="form-input-text-color" class="jscolor">
                                <br>
                                <label>Размер шрифта шаблона полей</label>
                                <select name="form-input-text-size">
                                    <option value=""></option>
                                    <option '; if ($result['form-input-text-size']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['form-input-text-size']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['form-input-text-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['form-input-text-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['form-input-text-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['form-input-text-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['form-input-text-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['form-input-text-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['form-input-text-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['form-input-text-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['form-input-text-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                </select>
                                <br>
                                <strong class="bold-text-10">Текст формы</strong>
                                <label>Шрифт описания</label>
                                <select name="form-text-font">
									<option value="">Шрифты сайта</option>
                                    <option '; if ($result['form-text-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['form-text-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['form-text-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['form-text-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['form-text-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['form-text-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['form-text-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['form-text-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['form-text-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['form-text-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['form-text-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Цвет описания</label>
                                <input value="'.$result['form-text-color'].'" name="form-text-color" class="jscolor">
                                <br>
                                <label>Размер шрифта описания</label>
                                <select name="form-text-size">
                                    <option value=""></option>
                                    <option '; if ($result['form-text-font']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['form-text-font']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['form-text-font']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['form-text-font']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['form-text-font']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['form-text-font']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['form-text-font']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['form-text-font']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['form-text-font']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['form-text-font']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['form-text-font']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                </select>
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Кнопка формы</strong>
                                <label>Фон кнопки</label>
                                <input value="'.$result['form-button-background-color'].'" name="form-button-background-color" class="jscolor">
                                <br>
                                <label>Толщина обводки кнопки</label>
                                <select name="form-button-border-size">
                                    <option '; if ($result['form-button-border-size']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-button-border-size']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-button-border-size']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-button-border-size']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-button-border-size']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-button-border-size']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-button-border-size']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-button-border-size']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-button-border-size']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-button-border-size']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-button-border-size']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Скругление обводки</label>
                                <select name="form-button-radius">
                                    <option '; if ($result['form-button-radius']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['form-button-radius']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-button-radius']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-button-radius']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-button-radius']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-button-radius']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-button-radius']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-button-radius']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-button-radius']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-button-radius']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-button-radius']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                    <option '; if ($result['form-button-radius']=="11"){echo 'selected ';} echo 'value="11">11 px</option>
                                    <option '; if ($result['form-button-radius']=="12"){echo 'selected ';} echo 'value="12">12 px</option>
                                    <option '; if ($result['form-button-radius']=="13"){echo 'selected ';} echo 'value="13">13 px</option>
                                    <option '; if ($result['form-button-radius']=="14"){echo 'selected ';} echo 'value="14">14 px</option>
                                    <option '; if ($result['form-button-radius']=="15"){echo 'selected ';} echo 'value="15">15 px</option>
                                    <option '; if ($result['form-button-radius']=="16"){echo 'selected ';} echo 'value="16">16 px</option>
                                    <option '; if ($result['form-button-radius']=="17"){echo 'selected ';} echo 'value="17">17 px</option>
                                    <option '; if ($result['form-button-radius']=="18"){echo 'selected ';} echo 'value="18">18 px</option>
                                    <option '; if ($result['form-button-radius']=="19"){echo 'selected ';} echo 'value="19">19 px</option>
                                    <option '; if ($result['form-button-radius']=="20"){echo 'selected ';} echo 'value="20">20 px</option>
                                    <option '; if ($result['form-button-radius']=="21"){echo 'selected ';} echo 'value="21">21 px</option>
                                    <option '; if ($result['form-button-radius']=="22"){echo 'selected ';} echo 'value="22">22 px</option>
                                    <option '; if ($result['form-button-radius']=="23"){echo 'selected ';} echo 'value="23">23 px</option>
                                    <option '; if ($result['form-button-radius']=="24"){echo 'selected ';} echo 'value="24">24 px</option>
                                    <option '; if ($result['form-button-radius']=="25"){echo 'selected ';} echo 'value="25">25 px</option>
                                    <option '; if ($result['form-button-radius']=="26"){echo 'selected ';} echo 'value="26">26 px</option>
                                    <option '; if ($result['form-button-radius']=="27"){echo 'selected ';} echo 'value="27">27 px</option>
                                    <option '; if ($result['form-button-radius']=="28"){echo 'selected ';} echo 'value="28">28 px</option>
                                    <option '; if ($result['form-button-radius']=="29"){echo 'selected ';} echo 'value="29">29 px</option>
                                    <option '; if ($result['form-button-radius']=="30"){echo 'selected ';} echo 'value="30">30 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки кнопки</label>
                                <input value="'.$result['form-button-border-color'].'" name="form-button-border-color" class="jscolor">
                                <br>
                                <label>Цвет шрифта кнопки</label>
                                <input value="'.$result['form-button-text-color'].'" name="form-button-text-color" class="jscolor">
                                <br>
                                <label>Шрифт кнопки</label>
                                <select name="form-button-text-font">
									<option value="">Шрифты сайта</option>
                                    <option '; if ($result['form-button-text-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['form-button-text-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['form-button-text-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['form-button-text-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['form-button-text-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['form-button-text-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['form-button-text-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['form-button-text-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['form-button-text-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['form-button-text-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['form-button-text-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта кнопки</label>
                                <select name="form-button-text-size">
                                    <option value=""></option>
                                    <option '; if ($result['form-button-text-size']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['form-button-text-size']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['form-button-text-size']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['form-button-text-size']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['form-button-text-size']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['form-button-text-size']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['form-button-text-size']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['form-button-text-size']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['form-button-text-size']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['form-button-text-size']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                    <option '; if ($result['form-button-text-size']=="11"){echo 'selected ';} echo 'value="11">11 px</option>
                                    <option '; if ($result['form-button-text-size']=="12"){echo 'selected ';} echo 'value="12">12 px</option>
                                    <option '; if ($result['form-button-text-size']=="13"){echo 'selected ';} echo 'value="13">13 px</option>
                                    <option '; if ($result['form-button-text-size']=="14"){echo 'selected ';} echo 'value="14">14 px</option>
                                    <option '; if ($result['form-button-text-size']=="15"){echo 'selected ';} echo 'value="15">15 px</option>
                                    <option '; if ($result['form-button-text-size']=="16"){echo 'selected ';} echo 'value="16">16 px</option>
                                    <option '; if ($result['form-button-text-size']=="17"){echo 'selected ';} echo 'value="17">17 px</option>
                                    <option '; if ($result['form-button-text-size']=="18"){echo 'selected ';} echo 'value="18">18 px</option>
                                    <option '; if ($result['form-button-text-size']=="19"){echo 'selected ';} echo 'value="19">19 px</option>
                                    <option '; if ($result['form-button-text-size']=="20"){echo 'selected ';} echo 'value="20">20 px</option>
                                    <option '; if ($result['form-button-text-size']=="21"){echo 'selected ';} echo 'value="21">21 px</option>
                                    <option '; if ($result['form-button-text-size']=="22"){echo 'selected ';} echo 'value="22">22 px</option>
                                    <option '; if ($result['form-button-text-size']=="23"){echo 'selected ';} echo 'value="23">23 px</option>
                                    <option '; if ($result['form-button-text-size']=="24"){echo 'selected ';} echo 'value="24">24 px</option>
                                    <option '; if ($result['form-button-text-size']=="25"){echo 'selected ';} echo 'value="25">25 px</option>
                                    <option '; if ($result['form-button-text-size']=="26"){echo 'selected ';} echo 'value="26">26 px</option>
                                    <option '; if ($result['form-button-text-size']=="27"){echo 'selected ';} echo 'value="27">27 px</option>
                                    <option '; if ($result['form-button-text-size']=="28"){echo 'selected ';} echo 'value="28">28 px</option>
                                    <option '; if ($result['form-button-text-size']=="29"){echo 'selected ';} echo 'value="29">29 px</option>
                                    <option '; if ($result['form-button-text-size']=="30"){echo 'selected ';} echo 'value="30">30 px</option>
                                </select>
                            </div>
                        </div>
                        <input type="submit" value="Сохранить" class="button-7 w-button2">
                    </form>
                </div>
            </div>
			
			<div style="padding:  0px 10px 0 30px; width:480px">
			   <div style="margin-bottom: 20px;"><strong class="bold-text-16">Дополнительные стили:</strong>
                    <br>
               </div>
               <textarea id="textarea-promo" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 380px; resize: vertical;">'.$result['dop-css'].'</textarea>
               <br><br>
               <div style="margin-bottom: 20px;"><strong class="bold-text-16">Блокируемые селекторы:</strong>
                    <br>
               </div>
               <textarea id="textarea-promo2" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 180px; resize: vertical;">'.$result['adblock-css'].'</textarea>

            </div>
            
			
            <div class="div-block-118" style="overflow: auto;">
                <div><strong class="bold-text-12">Код для установки:</strong>
                    <br>
                </div>
                <div class="text-block-131">Установите этот код в нужном месте шаблона</div>
                <div class="html-embed w-embed">&lt;div id="corton-promo"&gt;&lt;/div&gt;</div>
                <div class="div-block-123"></div>
                <div><strong class="bold-text-12">Внешний вид:</strong>
                    <br>
                </div>
                <div class="html-embed-9 w-embed">
                    <div class="promo-script-container">
                    </div>
                    <div class="widget-promo-preview">
                        <div id="corton-promo">
                            <h1>Заголовок Н1</h1>
                            <div style="color: #A5A5A5; text-align:left; padding-bottom:15px;" class="icon-partner">Partner</div>
                            <p>Давно выяснено, что при оценке дизайна и композиции читаемый текст мешает сосредоточиться.</p>
                            <h2>Заголовок Н2</h2>
                            <p>Используют потому, что тот обеспечивает более или менее стандартное заполнение шаблона, а также реальное распределение букв и пробелов в абзацах, которое не получается при простой дубликации. 
                            <a href="#">Здесь ссылка </a></p>
                            <div class="promo-form">
                            <div class="title">Получить пробный курс бесплатно!</div>
                            <div class="text">По промокоду вы можете получить пробный курс.</div>
                                <div class="form">
                                    <form class="form">
                                        <input class="inputtext" maxlength="256" name="name" placeholder="Ваше Имя" required="">
                                        <input class="inputtext" maxlength="256" name="phone" placeholder="Телефон" required="">
                                        <input type="submit" value="Отправить" class="button">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
                $sql="SELECT * FROM `style_recomend` WHERE `id`='".addslashes($_REQUEST['id'])."';";   $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($result==false){$sql="SELECT * FROM `style_recomend` WHERE `id`='0';"; $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);};
                echo '
<div class="modal recomendation">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-3">Редактировать виджет Recomendation</strong></div>
        <div class="div-block-117">
            <div class="div-block-119">
                <div class="w-form">
                    <form name="recomend" method="post" action="/widget-update" class="form-6">
                        <div class="widget-recomendation" style="overflow: auto; height: 650px;">
                            <div class="html-embed-19 w-embed">
                                <input type="hidden" value="style_recomend" name="type">
                                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                                <input type="hidden" value="" name="css">
                                <input type="hidden" value="" name="dop-css">
                                <strong class="bold-text-10">Вывод на устройствах</strong>
                                <br>
								    <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                        <input type="checkbox" '; if ($result['mobile']) echo ' checked '; echo ' name="mobile" class="form-radiozag">
                                        <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                            <a style="color:#333;" class="link">Смартфоны</a>
                                        </label>
                                    </div>
								   
                                    <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                        <input type="checkbox" '; if ($result['tablet']) echo ' checked '; echo ' name="tablet" class="form-radiozag">
                                        <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                            <a style="color:#333;" class="link">Планшет</a>
                                    </label>
                                    </div>
                                
                                    <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                    <input type="checkbox" '; if ($result['desktop']) echo ' checked '; echo ' name="desktop" class="form-radiozag">
                                        <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                            <a style="color:#333;" class="link">ПК</a>
                                        </label>
                                   </div>      
                                <br>
                                <strong class="bold-text-10">Размещение:</strong>
								<p class="grey-color">На действующих площадках выводится только при наличии тега на странице</p>
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='1') echo ' checked '; echo ' name="algorithm-output" value="1" >
                                   <div style="color:#333;" class="radio__text">В месте тега</div>
                                </label>
                                
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='0') echo ' checked '; echo ' name="algorithm-output" value="0" >
                                   <div style="color:#333;" class="radio__text">По родительскому селектору и&nbsp;абзацу</div>
                                </label>
                                              
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='3') echo ' checked '; echo ' name="algorithm-output" value="3" >
                                   <div style="color:#333;" class="radio__text">Пред cелектором</div>
                                </label>
                                
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='4') echo ' checked '; echo ' name="algorithm-output" value="4" >
                                   <div style="color:#333;" class="radio__text">Вместо cелектора</div>
                                </label>
                                
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='5') echo ' checked '; echo ' name="algorithm-output" value="5" >
                                   <div style="color:#333;" class="radio__text">После cелектора</div>
                                </label>
                                
                                <label>Селектор</label>
                                <input type="text" value="'.$result['widget-parent-id'].'"'; if ($result['poz-tag']) echo ' disabled'; echo ' name="widget-parent-id" required '; if ($result['algorithm-output']=='1')echo 'disabled';echo '>
                                <br>
                                <p class="grey-color">Например: .content:nth-of-type(1) или&nbsp;#content</p>

                                <label>Номер абзаца</label>
                                <input type="text" value="'.$result['widget-position-p'].'"'; if ($result['poz-tag']) echo ' disabled'; echo ' name="widget-position-p" required '; if ($result['algorithm-output']=='1')echo 'disabled';echo '>
                                <br>
                                <br>
                                <br>
                                
                                <strong class="bold-text-10">Конструкция:</strong>
                                
                                <label>Блоков по горизонтали</label>
                                <select name="widget-format">
                                    <option '; if ($result['widget-format']=="2"){echo 'selected ';} echo 'value="2">2</option>
                                    <option '; if ($result['widget-format']=="3"){echo 'selected ';} echo 'value="3">3</option>
                                    <option '; if ($result['widget-format']=="4"){echo 'selected ';} echo 'value="4">4</option>
                                </select>
                                <br>
                                <label>Блоков по вертикали</label>
                                <select name="widget-format-1">
                                    <option '; if ($result['widget-format-1']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-format-1']=="2"){echo 'selected ';} echo 'value="2">2</option>
                                    <option '; if ($result['widget-format-1']=="3"){echo 'selected ';} echo 'value="3">3</option>
                                    <option '; if ($result['widget-format-1']=="4"){echo 'selected ';} echo 'value="4">4</option>
                                    <option '; if ($result['widget-format-1']=="5"){echo 'selected ';} echo 'value="5">5</option>
                                    <option '; if ($result['widget-format-1']=="6"){echo 'selected ';} echo 'value="6">6</option>
                                    <option '; if ($result['widget-format-1']=="7"){echo 'selected ';} echo 'value="7">7</option>
                                    <option '; if ($result['widget-format-1']=="8"){echo 'selected ';} echo 'value="8">8</option>
                                    <option '; if ($result['widget-format-1']=="9"){echo 'selected ';} echo 'value="9">9</option>
                                </select>
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид виджета</strong>
                                <label>Единица измерения шрифта</label>
                                <select name="widget-font-unit">
                                    <option '; if ($result['widget-font-unit']=="px"){echo 'selected ';} echo 'value="px">px</option>
                                    <option '; if ($result['widget-font-unit']=="em"){echo 'selected ';} echo 'value="em">em</option>
                                </select>
                                <br>
                                <label>Цвет фона блока</label>
                                <input value="'.$result['widget-background-block'].'" name="widget-background-block" class="jscolor">
                                <br>
                                <label>Обводка блока</label>
                                <select name="widget-border-type">
                                    <option '; if ($result['widget-border-type']=="solid"){echo 'selected ';} echo 'value="solid">Сплошная</option>
                                    <option '; if ($result['widget-border-type']=="dashed"){echo 'selected ';} echo 'value="dashed">Пунктирная</option>
                                    <option '; if ($result['widget-border-type']=="dotted"){echo 'selected ';} echo 'value="dotted">Точки</option>
                                </select>
                                <br>
                                <label>Толщина обводки (px)</label>
                                <select name="widget-border-width">
                                    <option value=""></option>
                                    <option '; if ($result['widget-border-width']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['widget-border-width']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['widget-border-width']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['widget-border-width']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['widget-border-width']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['widget-border-width']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['widget-border-width']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['widget-border-width']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['widget-border-width']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['widget-border-width']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['widget-border-width']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки</label>
                                <input value="'.$result['widget-border-color'].'" name="widget-border-color" class="jscolor">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид заголовка</strong>
                                <label>Текст заголовка</label>
                                <input type="text" value="'.$result['widget-text-title'].'" name="widget-text-title">
                                <br>
                                <label>Шрифт заголовка</label>
                                <select name="widget-font-title">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-title']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-title']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-title']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-title']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-title']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-title']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-title']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта заголовка</label>
                                <select name="widget-size-title">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-title']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-size-title']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-size-title']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-title']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-title']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-size-title']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-size-title']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-size-title']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-size-title']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['widget-size-title']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['widget-size-title']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                    <option '; if ($result['widget-size-title']=="21"){echo 'selected ';} echo 'value="21">21px / 2.1em</option>
                                    <option '; if ($result['widget-size-title']=="22"){echo 'selected ';} echo 'value="22">22px / 2.2em</option>
                                    <option '; if ($result['widget-size-title']=="23"){echo 'selected ';} echo 'value="23">23px / 2.3em</option>
                                    <option '; if ($result['widget-size-title']=="24"){echo 'selected ';} echo 'value="24">24px / 2.4em</option>
                                    <option '; if ($result['widget-size-title']=="25"){echo 'selected ';} echo 'value="25">25px / 2.5em</option>
                                    <option '; if ($result['widget-size-title']=="26"){echo 'selected ';} echo 'value="26">26px / 2.6em</option>
                                    <option '; if ($result['widget-size-title']=="27"){echo 'selected ';} echo 'value="27">27px / 2.7em</option>
                                    <option '; if ($result['widget-size-title']=="28"){echo 'selected ';} echo 'value="28">28px / 2.8em</option>
                                    <option '; if ($result['widget-size-title']=="29"){echo 'selected ';} echo 'value="29">29px / 2.9em</option>
                                    <option '; if ($result['widget-size-title']=="30"){echo 'selected ';} echo 'value="30">30px / 3em</option>
                                    <option '; if ($result['widget-size-title']=="31"){echo 'selected ';} echo 'value="31">31px / 3.1em</option>
                                    <option '; if ($result['widget-size-title']=="32"){echo 'selected ';} echo 'value="32">32px / 3.2em</option>
                                    <option '; if ($result['widget-size-title']=="33"){echo 'selected ';} echo 'value="33">33px / 3.3em</option>
                                    <option '; if ($result['widget-size-title']=="34"){echo 'selected ';} echo 'value="34">34px / 3.4em</option>
                                    <option '; if ($result['widget-size-title']=="35"){echo 'selected ';} echo 'value="35">35px / 3.5em</option>
                                    <option '; if ($result['widget-size-title']=="36"){echo 'selected ';} echo 'value="36">36px / 3.6em</option>
                                    <option '; if ($result['widget-size-title']=="37"){echo 'selected ';} echo 'value="37">37px / 3.7em</option>
                                    <option '; if ($result['widget-size-title']=="38"){echo 'selected ';} echo 'value="38">38px / 3.8em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта заголовка</label>
                                <input value="'.$result['widget-color-title'].'" name="widget-color-title" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-type-bold-title">
									<option value=""></option>
                                    <option '; if ($result['widget-type-bold-title']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-type-bold-title']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-type-bold-title']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-type-bold-title']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-type-bold-title']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-type-bold-title']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-type-bold-title']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-type-bold-title']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-type-bold-title']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта заголовка</label>
								
                                 <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-italic-title']) echo ' checked '; echo ' name="widget-type-italic-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                   </div> 
								   
								   <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-underline-title']) echo ' checked '; echo ' name="widget-type-underline-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                   </div> 
								   
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид анонса</strong>
                                <label>Цвет фона рекомендаций</label>
                                <input value="'.$result['widget-background-tizer'].'" name="widget-background-tizer" class="jscolor">
                                <br>
                                <label>Шрифт</label>
                                <select name="widget-font-text">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-text']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-text']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-text']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-text']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-text']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-text']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-text']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-text']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-text']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-text']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-text']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-size-text">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-text']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-size-text']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-size-text']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-text']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-text']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-size-text']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-size-text']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-size-text']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-size-text']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-color-text'].'" name="widget-color-text" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-type-bold-text">
									<option value=""></option>
                                    <option '; if ($result['widget-type-bold-text']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-type-bold-text']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-type-bold-text']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-type-bold-text']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-type-bold-text']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-type-bold-text']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-type-bold-text']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-type-bold-text']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-type-bold-text']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-italic-text']) echo ' checked ';echo 'name="widget-type-italic-text" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								   
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                    <input type="checkbox" '; if ($result['widget-type-underline-text']) echo ' checked ';echo 'name="widget-type-underline-text" class="form-radiozag">
                                    <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                    </label>
                                </div> 

                                <br>
                                <label>Межстрочный интервал</label>
                                <select name="widget-type-interval-text">
                                    <option '; if ($result['widget-type-interval-text']=="0.9"){echo 'selected ';} echo 'value="0.9">0.9</option>
                                    <option '; if ($result['widget-type-interval-text']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.2"){echo 'selected ';} echo 'value="1.2">1.2</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.3"){echo 'selected ';} echo 'value="1.3">1.3</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.5"){echo 'selected ';} echo 'value="1.5">1.5</option>
                                </select>
                                <br>
                                <label>Выравнивание текста</label>
                                <select name="widget-type-align-text">
                                    <option '; if ($result['widget-type-align-text']=="left"){echo 'selected ';} echo 'value="left">Слева</option>
                                    <option '; if ($result['widget-type-align-text']=="center"){echo 'selected ';} echo 'value="center">По центру</option>
                                </select>
                                <br>
                                
                                <label>Форма изображения</label>
                                <select name="image-shape">
                                    <option '; if ($result['image-shape']=="3"){echo 'selected ';} echo 'value="3">Прямоугольник</option>
                                    <option '; if ($result['image-shape']=="4"){echo 'selected ';} echo 'value="4">Квадрат</option>
                                </select>
                                
                            </div>
                        </div>
                        <input type="submit" value="Сохранить" class="button-7">
                    </form>
                </div>
            </div>
			
			
			<div style="padding:  0px 10px 0 30px; width:480px">
			   <div style="margin-bottom: 20px;"><strong class="bold-text-16">Дополнительные стили:</strong>
                    <br>
               </div>
               <textarea id="textarea-recomendation" name="code" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 600px; resize: vertical;">'.$result['dop-css'].'</textarea>
            </div>
			
            <div class="div-block-118" style="overflow: auto;">
                <div><strong class="bold-text-16">Код для установки:</strong>
                    <br>
                </div>
                <div class="text-block-134">Установите этот код в нужном месте шаблона</div>
                <div class="html-embed w-embed">&lt;div id="corton-recomendation-widget"&gt;&lt;/div&gt;</div>
                <div class="div-block-125"></div>
                <div class="text-block-135"><strong class="bold-text-17">Внешний вид:</strong>
                    <br>
                </div>
                <div class="html-embed-9 w-embed">
                    <div class="recomendation-script-container">
                    </div>
                    <div class="widget-recomendation-preview">
                        <div id="corton-recomendation-widget">
                            <div class="corton-recomendation-wrapper 4x1">
                                <div class="corton-title">Похожие материалы:</div>
                                <div class="corton-recomendation-row">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="holder" style="display: none;">
                        <div class="corton-recomendation-section">
                            <a href="#">
                                <img src="/images/placeholder.jpg" class="recomendationimg" alt="">
                                <p>Пример рекламной рекомендации статьи на вашем сайте!</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
                $sql="SELECT `code` FROM `zag_recomend` WHERE `id`='".addslashes($_REQUEST['id'])."';";
                $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
                $result = str_replace(array("<", ">"), $output = array("&lt;", "&gt;"), $result);
                echo '
<div class="modal zagrecom">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-4">Код заглушки виджета Recommendation</strong></div>
        <div class="w-form">
            <form method="post" action="/widget-update" class="form-5">
                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                <input type="hidden" value="zag_recomend" name="type">
                <textarea id="js" placeholder="html код включая javascript" maxlength="5000" name="code" class="textarea-5 w-input">'.$result.'</textarea>
                <input type="submit" value="Сохранить" style="width:120px" class="button-7">
            </form>
        </div>
    </div>
</div>';
                $sql="SELECT * FROM `style_natpre` WHERE `id`='".addslashes($_REQUEST['id'])."';";   $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($result==false){$sql="SELECT * FROM `style_natpre` WHERE `id`='0';"; $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);};
                echo '
<div class="modal nativepreview">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-5">Редактировать виджет Native Preview</strong></div>
        <div class="div-block-117">
            <div class="div-block-119">
                <div class="w-form">
                    <form method="post" action="/widget-update" class="form-6">
                        <div class="widget-nativepre" style="overflow: auto; height: 650px;">
                            <div class="html-embed-16 w-embed">
                                <input type="hidden" value="style_natpre" name="type">
                                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                                <input type="hidden" value="" name="css">
                                <input type="hidden" value="" name="dop-css">
                                <strong class="bold-text-10">Вывод на устройствах</strong>
                                <br>
								   <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['mobile']) echo ' checked '; echo ' name="mobile" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Смартфоны</a>
                                      </label>
                                   </div>
								   
								   <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['tablet']) echo ' checked '; echo ' name="tablet" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Планшет</a>
                                      </label>
                                   </div>
								   
								   <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['desktop']) echo ' checked '; echo ' name="desktop" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">ПК</a>
                                      </label>
                                   </div>      
                                <br>
                                <strong class="bold-text-10">Размещение:</strong>
								<p class="grey-color">На действующих площадках выводится только при наличии тега на странице</p>
								
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='1') echo ' checked '; echo ' name="algorithm-output" value="1" >
                                   <div style="color:#333;" class="radio__text">В месте тега</div>
                                </label>
                                
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='0') echo ' checked '; echo ' name="algorithm-output" value="0" >
                                   <div style="color:#333;" class="radio__text">По родительскому селектору и&nbsp;абзацу</div>
                                </label>
                                              
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='3') echo ' checked '; echo ' name="algorithm-output" value="3" >
                                   <div style="color:#333;" class="radio__text">Пред cелектором</div>
                                </label>

                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='4') echo ' checked '; echo ' name="algorithm-output" value="4" >
                                   <div style="color:#333;" class="radio__text">Вместо cелектора</div>
                                </label>
                                
                                <label class="radio">
                                   <input type="radio" '; if ($result['algorithm-output']=='5') echo ' checked '; echo ' name="algorithm-output" value="5" >
                                   <div style="color:#333;" class="radio__text">После cелектора</div>
                                </label>
                                
                                <label>Селектор</label>
                                <input type="text" value="'.$result['widget-parent-id'].'"'; if ($result['poz-tag']) echo ' disabled'; echo ' name="widget-parent-id" required '; if ($result['algorithm-output']=='1')echo 'disabled';echo '>
                                <br>
                                <p class="grey-color">Например: .content:nth-of-type(1) или&nbsp;#content</p>

                                <label>Номер абзаца</label>
                                <input type="text" value="'.$result['widget-position-p'].'"'; if ($result['poz-tag']) echo ' disabled'; echo ' name="widget-position-p" required '; if ($result['algorithm-output']=='1')echo 'disabled';echo '>
                                <br>
                                <br>
                                <br>
                                                                
                                <strong class="bold-text-10">Общий вид</strong>
                                <label>Единица измерения шрифта</label>
                                <select name="widget-font-unit">
                                    <option '; if ($result['widget-font-unit']=="px"){echo 'selected ';} echo 'value="px">px</option>
                                    <option '; if ($result['widget-font-unit']=="em"){echo 'selected ';} echo 'value="em">em</option>
                                </select>
                                <br>
                                <label>Ширина блока, %</label>
                                <input type="text" value="'.$result['widget-width-block'].'" name="widget-width-block">
                                <br>
                                <label>Цвет фона блока</label>
                                <input value="'.$result['widget-background-block'].'" name="widget-background-block" class="jscolor">
                                <br>
                                <label>Обводка блока</label>
                                <select name="widget-border-type">
                                    <option '; if ($result['widget-border-type']=="solid"){echo 'selected ';} echo 'value="solid">Сплошная</option>
                                    <option '; if ($result['widget-border-type']=="dashed"){echo 'selected ';} echo 'value="dashed">Пунктирная</option>
                                    <option '; if ($result['widget-border-type']=="dotted"){echo 'selected ';} echo 'value="dotted">Точки</option>
                                </select>
                                <br>
                                <label>Толщина обводки (px)</label>
                                <select name="widget-border-width">
                                    <option '; if ($result['widget-border-width']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['widget-border-width']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['widget-border-width']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['widget-border-width']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['widget-border-width']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['widget-border-width']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['widget-border-width']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['widget-border-width']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['widget-border-width']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['widget-border-width']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['widget-border-width']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки</label>
                                <input value="'.$result['widget-border-color'].'" name="widget-border-color" class="jscolor">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид заголовка</strong>
                                <label>Шрифт</label>
                                <select name="widget-font-title">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-title']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-title']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-title']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-title']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-title']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-title']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-title']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-size-title">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-title']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-title']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-title']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-size-title']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-size-title']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-size-title']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-size-title']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['widget-size-title']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['widget-size-title']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                    <option '; if ($result['widget-size-title']=="21"){echo 'selected ';} echo 'value="21">21px / 2.1em</option>
                                    <option '; if ($result['widget-size-title']=="22"){echo 'selected ';} echo 'value="22">22px / 2.2em</option>
                                    <option '; if ($result['widget-size-title']=="23"){echo 'selected ';} echo 'value="23">23px / 2.3em</option>
                                    <option '; if ($result['widget-size-title']=="24"){echo 'selected ';} echo 'value="24">24px / 2.4em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-color-title'].'" name="widget-color-title" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-type-bold-title">
									<option value=""></option>
                                    <option '; if ($result['widget-type-bold-title']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-type-bold-title']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-type-bold-title']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-type-bold-title']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-type-bold-title']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-type-bold-title']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-type-bold-title']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-type-bold-title']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-type-bold-title']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
								
                                <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-italic-title']) echo ' checked '; echo ' name="widget-type-italic-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                   </div> 
								   
								   <div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-underline-title']) echo ' checked '; echo ' name="widget-type-underline-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                   </div> 
                               
                                <br>
                                <label>Межстрочный интервал</label>
                                <select name="widget-type-interval-title">
                                    <option '; if ($result['widget-type-interval-title']=="0.9"){echo 'selected ';} echo 'value="0.9">0.9</option>
                                    <option '; if ($result['widget-type-interval-title']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.2"){echo 'selected ';} echo 'value="1.2">1.2</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.3"){echo 'selected ';} echo 'value="1.3">1.3</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.5"){echo 'selected ';} echo 'value="1.5">1.5</option>
                                </select>
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид текста</strong>
                                <label>Шрифт</label>
                                <select name="widget-font-text">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-text']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-text']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-text']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-text']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-text']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-text']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-text']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-text']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-text']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-text']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-text']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-size-text">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-text']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-size-text']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-size-text']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-text']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-text']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-size-text']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-size-text']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-size-text']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-size-text']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-color-text'].'" name="widget-color-text" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-type-bold-text">
									<option value=""></option>
                                    <option '; if ($result['widget-color-text']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-color-text']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-color-text']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-color-text']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-color-text']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-color-text']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-color-text']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-color-text']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-color-text']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['icon']) echo ' checked '; echo ' name="widget-type-italic-text" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['icon']) echo ' checked '; echo ' name="widget-type-underline-text" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div>
            
                                <br>
                                <label>Межстрочный интервал</label>
                                <select name="widget-type-interval-text">
                                    <option '; if ($result['widget-type-interval-text']=="0.9"){echo 'selected ';} echo 'value="0.9">0.9</option>
                                    <option '; if ($result['widget-type-interval-text']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.2"){echo 'selected ';} echo 'value="1.2">1.2</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.3"){echo 'selected ';} echo 'value="1.3">1.3</option>
                                    <option '; if ($result['widget-type-interval-text']=="1.5"){echo 'selected ';} echo 'value="1.5">1.5</option>
                                </select>
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид кнопки</strong>
                                <label>Цвет</label>
                                <input value="'.$result['button-background-color'].'" name="button-background-color" class="jscolor">
                                <br>
                                <label>Шрифт</label>
                                <select name="button-font">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['button-font']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['button-font']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['button-font']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['button-font']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['button-font']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['button-font']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['button-font']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['button-font']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['button-font']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['button-font']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['button-font']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="button-font-size">
                                    <option value=""></option>
                                    <option '; if ($result['button-font-size']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['button-font-size']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['button-font-size']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['button-font-size']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['button-font-size']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['button-font-size']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['button-font-size']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['button-font-size']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['button-font-size']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                    <option '; if ($result['button-font-size']=="19"){echo 'selected ';} echo 'value="19">19px / 1.9em</option>
                                    <option '; if ($result['button-font-size']=="20"){echo 'selected ';} echo 'value="20">20px / 2em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['button-text-color'].'" name="button-text-color" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="button-type-bold">
									<option value=""></option>
                                    <option '; if ($result['button-text-color']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['button-text-color']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['button-text-color']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['button-text-color']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['button-text-color']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['button-text-color']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['button-text-color']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['button-text-color']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['button-text-color']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['button-type-italic']) echo ' checked '; echo ' name="button-type-italic" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['button-type-underline']) echo ' checked '; echo ' name="button-type-underline" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div> 

                                <br>
                                <label>Обводка</label>
                                <select name="button-border-type">
                                    <option '; if ($result['button-border-type']=="solid"){echo 'selected ';} echo 'value="solid">Сплошная</option>
                                    <option '; if ($result['button-border-type']=="dashed"){echo 'selected ';} echo 'value="dashed">Пунктирная</option>
                                    <option '; if ($result['button-border-type']=="dotted"){echo 'selected ';} echo 'value="dotted">Точки</option>
                                </select>
                                <br>
                                <label>Толщина обводки</label>
                                <select name="button-border-width">
                                    <option value=""></option>
                                    <option '; if ($result['button-border-width']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['button-border-width']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['button-border-width']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['button-border-width']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                    <option '; if ($result['button-border-width']=="5"){echo 'selected ';} echo 'value="5">5 px</option>
                                    <option '; if ($result['button-border-width']=="6"){echo 'selected ';} echo 'value="6">6 px</option>
                                    <option '; if ($result['button-border-width']=="7"){echo 'selected ';} echo 'value="7">7 px</option>
                                    <option '; if ($result['button-border-width']=="8"){echo 'selected ';} echo 'value="8">8 px</option>
                                    <option '; if ($result['button-border-width']=="9"){echo 'selected ';} echo 'value="9">9 px</option>
                                    <option '; if ($result['button-border-width']=="10"){echo 'selected ';} echo 'value="10">10 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки кнопки</label>
                                <input value="'.$result['button-border-color'].'" name="button-border-color" class="jscolor">
                                <br>
                                <label>Текст кнопки</label>
                                <select name="button-text">
                                    <option '; if ($result['button-text']=="Подробнее"){echo 'selected ';} echo 'value="Подробнее">Подробнее</option>
                                    <option '; if ($result['button-text']=="Читать далее"){echo 'selected ';} echo 'value="Читать далее">Читать далее</option>
                                </select>
                                
                                <label>Форма изображения</label>
                                <select name="image-shape">
                                    <option '; if ($result['image-shape']=="3"){echo 'selected ';} echo 'value="3">Прямоугольник</option>
                                    <option '; if ($result['image-shape']=="4"){echo 'selected ';} echo 'value="4">Квадрат</option>
                                </select>
                                
                            </div>
                        </div>
                        <input type="submit" value="Сохранить" class="button-7">
                        </form>
                </div>
            </div>
			
			<div style="padding:  0px 10px 0 30px; width:480px">
			   <div style="margin-bottom: 20px;"><strong class="bold-text-16">Дополнительные стили:</strong>
                    <br>
               </div>
               <textarea id="textarea-nativepreview" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 600px; resize: vertical;">'.$result['dop-css'].'</textarea>
            </div>
			
            <div class="div-block-118" style="overflow: auto;">
                <div class="text-block-133"><strong class="bold-text-15">Код для установки:</strong>
                    <br>
                </div>
                <div class="text-block-132">Установите этот код в нужном месте шаблона</div>
                <div class="html-embed w-embed">&lt;div id="corton-nativepreview-widget"&gt;&lt;/div&gt;</div>
                <div class="div-block-122"></div>
                <div><strong class="bold-text-14">Внешний вид:</strong>
                    <br>
                </div>
                <div class="html-embed-9 w-embed">
                    <div class="nativepre-script-container"></div>
                    <div class="#">
                        <div id="corton-nativepreview-widget" class="#">
                            <div class="corton-left"> <img src="/images/placeholder.jpg" class="natpreimg" height="180"> </div>
                            <div class="corton-right">
                                <div class="corton-title">Пример рекламного анонса на вашем сайте!</div>
                                <p class="corton-content">Пример рекламного анонса на вашем сайте!», «Нейронные сети? Да! Биологический нейрон – это специальная клетка, которая структурно состоит из ядра, тела клетки и отростков.</p> <a class="corton-link" href="#">Подробнее</a> </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
                $sql="SELECT `code` FROM `zag_natpre` WHERE `id`='".addslashes($_REQUEST['id'])."';";
                $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
                $result = str_replace(array("<", ">"), $output = array("&lt;", "&gt;"), $result);
                echo '
<div class="modal zagnativepreview">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-6">Код заглушки виджета Native Preview</strong></div>
        <div class="w-form">
            <form method="post" action="/widget-update" class="form-5">
                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                <input type="hidden" value="zag_natpre" name="type">
                <textarea placeholder="html код включая javascript" maxlength="5000" name="code" class="textarea-5 w-input">'.$result.'</textarea>
                <input type="submit" value="Сохранить" style="width:120px" class="button-7">
            </form>
        </div>
    </div>
</div>';
                $sql="SELECT * FROM `style_natpro` WHERE `id`='".addslashes($_REQUEST['id'])."';";   $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($result==false){$sql="SELECT * FROM `style_natpro` WHERE `id`='0';"; $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);};
                echo '
<div class="modal nativepro">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-5">Редактировать виджет Native Pro</strong></div>
        <div class="div-block-117">
            <div class="div-block-119">
                <div class="w-form">
                    <form method="post" action="/widget-update" class="form-6">
                        <div class="html-embed-10 w-embed">
                            <input type="hidden" value="style_natpro" name="type">
                            <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                            <input type="hidden" value="" name="css">
                            <br>
                            <strong class="bold-text-10">Внешний вид блока</strong>
                            <br>
                            <label>Единица измерения</label>
                            <select name="widget-font-unit">
                                <option '; if ($result['widget-font-unit']=="px"){echo 'selected ';} echo 'value="px">%</option>
                                <option '; if ($result['widget-font-unit']=="em"){echo 'selected ';} echo 'value="em">px</option>
                            </select>
                            <br>
                            <label>Ширина блока</label>
                            <input type="text" value="'.$result['widget-width-block'].'" name="widget-width-block">
                            <br>
                            <br>
                            <label>Единица измерения</label>
                            <select name="widget-font-unit">
                                <option '; if ($result['widget-font-unit']=="px"){echo 'selected ';} echo 'value="px">%</option>
                                <option '; if ($result['widget-font-unit']=="em"){echo 'selected ';} echo 'value="em">px</option>
                            </select>
                            <br>
                            <label>Высота блока</label>
                            <input type="text" value="'.$result['widget-width-block'].'" name="widget-width-block">
                        </div>
                        <div class="div-block-122"></div>
                        <div class="text-block-137">Инструкция:
                            <br>
                        </div>
                        <div class="text-block-136">Используйте следующие шаблоны для кода:
                            <br><strong>{image}</strong> - картинка
                            <br><strong>{title}</strong> - заголовок
                            <br><strong>{body}</strong> - тело
                            <br>‍<strong>{link}</strong> - ссылка (пример использования: Читать далее)
                            <br><strong>{logo} </strong>- лого Partner</div>
                    </form>
                </div>
            </div>
			
			<div style="padding:  0px 10px 0 30px; width:480px">
			   <div style="margin-bottom: 20px;"><strong class="bold-text-16">Дополнительные стили:</strong>
                    <br>
               </div>
               <textarea id="textarea-nativepro" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 600px; resize: vertical;">'.$result['dop-css'].'</textarea>
            </div>
			
            <div class="div-block-118" style="overflow: auto;">
                <div class="text-block-133"><strong class="bold-text-15">Код для установки:</strong>
                    <br>
                </div>
                <div class="text-block-132">Установите этот код в нужном месте шаблона</div>
                <div class="html-embed w-embed">&lt;div id="corton-nativePro-widget"&gt;&lt;/div&gt;</div>
                <div class="div-block-122"></div>
                <div class="w-form">
                    <form class="form-7">
                        <textarea id="codenativepro" name="codenativepro" placeholder="Ваш код" maxlength="5000" required="" class="textarea-6 w-input"></textarea>
                        <input type="submit" value="Сохранить" style="width:120px" class="button-7 w-button" disabled>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>';
                $sql="SELECT `code` FROM `zag_natpro` WHERE `id`='".addslashes($_REQUEST['id'])."';";
                $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
                $result = str_replace(array("<", ">"), $output = array("&lt;", "&gt;"), $result);
                echo '
<div class="modal zag-nativepro">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-8">Код заглушки виджета Native Pro</strong></div>
        <div class="w-form">
            <form method="post" action="/widget-update" class="form-5">
                <input type="hidden" value="'.$_REQUEST['id'].'" name="id">
                <input type="hidden" value="zag_natpro" name="type">
                <textarea placeholder="html код включая javascript" maxlength="5000" name="code" class="textarea-5 w-input">'.$result.'</textarea>                <input type="submit" value="Сохранить" style="width:120px" class="button-7 w-button">
            </form>
        </div>
    </div>
</div>';
                $sql="SELECT * FROM `style_slider` WHERE `id`='".addslashes($_REQUEST['id'])."';";
                $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($result==false){$sql="SELECT * FROM `style_slider` WHERE `id`='0';"; $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);};
                echo '
<div class="modal slider">
    <div class="div-block-78 w-clearfix">
        <div class="div-block-132 modalhide"><img src="/images/close.png" alt="" class="image-5"></div>
        <div class="text-block-120"><strong class="bold-text-9">Редактировать стиль виджета Slider </strong></div>
        <div class="div-block-117">
            <div class="div-block-119">
                <div class="w-form">
                    <form method="post" action="/widget-update" class="form-6">
                        <div class="widget-slider" style="overflow: auto; height: 650px;">
                            <div class="html-embed-10 w-embed">
                                <input type="hidden" value="style_slider" name="type">
                                <input type="hidden" value="'.addslashes($_REQUEST['id']).'" name="id">
                                <input type="hidden" value="" name="css">
                                <input type="hidden" value="" name="dop-css">
                                <br>
                                <strong class="bold-text-10">Внешний вид блока</strong>
                                <br>
                                <label style="margin-bottom: 10px;">Выводить на устройствах:</label>
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['mobile']) echo ' checked '; echo ' name="mobile" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Смартфоны</a>
                                      </label>
                                </div>
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['tablet']) echo ' checked '; echo ' name="tablet" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Планшет</a>
                                      </label>
                                </div>
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['desktop']) echo ' checked '; echo ' name="desktop" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">ПК</a>
                                      </label>
                                </div>
								   
                                <label>Единица измерения шрифта</label>
                                <select name="widget-font-unit">
                                    <option '; if ($result['widget-font-unit']=="px"){echo 'selected ';} echo 'value="px">px</option>
                                    <option '; if ($result['widget-font-unit']=="em"){echo 'selected ';} echo 'value="em">em</option>
                                </select>
                                <br>
                                <label>Цвет фона</label>
                                <input value="'.$result['widget-background-block'].'" name="widget-background-block" class="jscolor">
                                <br>
                                <label>Обводка блока</label>
                                <select name="widget-border-type">
                                    <option '; if ($result['widget-background-block']=="solid"){echo 'selected ';} echo 'value="solid">Сплошная</option>
                                    <option '; if ($result['widget-background-block']=="dashed"){echo 'selected ';} echo 'value="dashed">Пунктирная</option>
                                    <option '; if ($result['widget-background-block']=="dotted"){echo 'selected ';} echo 'value="dotted">Точки</option>
                                </select>
                                <br>
                                <label>Толщина обводки (px)</label>
                                <select name="widget-border-width">
                                    <option value=""></option>
                                    <option '; if ($result['widget-border-width']=="0"){echo 'selected ';} echo 'value="0">0 px</option>
                                    <option '; if ($result['widget-border-width']=="1"){echo 'selected ';} echo 'value="1">1 px</option>
                                    <option '; if ($result['widget-border-width']=="2"){echo 'selected ';} echo 'value="2">2 px</option>
                                    <option '; if ($result['widget-border-width']=="3"){echo 'selected ';} echo 'value="3">3 px</option>
                                    <option '; if ($result['widget-border-width']=="4"){echo 'selected ';} echo 'value="4">4 px</option>
                                </select>
                                <br>
                                <label>Цвет обводки</label>
                                <input value="'.$result['widget-border-color'].'" name="widget-border-color" class="jscolor">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид подписи</strong>
                                <label>Шрифт</label>
                                <select name="widget-font-sign">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-sign']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-sign']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-sign']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-sign']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-sign']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-sign']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-sign']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-sign']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-sign']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-sign']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-sign']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-size-sign">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-sign']=="8"){echo 'selected ';} echo 'value="8">8px / 0.8em</option>
                                    <option '; if ($result['widget-size-sign']=="9"){echo 'selected ';} echo 'value="9">9px / 0.9em</option>
                                    <option '; if ($result['widget-size-sign']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-size-sign']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-size-sign']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-sign']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-sign']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта</label>
                                <input value="'.$result['widget-color-sign'].'" name="widget-color-sign" class="jscolor">
                                <br>
                                <br>
                                <br>
                                <strong class="bold-text-10">Внешний вид заголовка</strong>
                                <label>Шрифт</label>
                                <select name="widget-font-title">
                                    <option value="">Шрифты сайта</option>
                                    <option '; if ($result['widget-font-title']=="Arial, Helvetica"){echo 'selected ';} echo 'value="Arial, Helvetica">Arial, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Arial Black, Gadget"){echo 'selected ';} echo 'value="Arial Black, Gadget">Arial Black, Gadget</option>
                                    <option '; if ($result['widget-font-title']=="Impact, Charcoal"){echo 'selected ';} echo 'value="Impact, Charcoal">Impact, Charcoal</option>
                                    <option '; if ($result['widget-font-title']=="Tahoma, Geneva"){echo 'selected ';} echo 'value="Tahoma, Geneva">Tahoma, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Trebuchet MS, Helvetica"){echo 'selected ';} echo 'value="Trebuchet MS, Helvetica">Trebuchet MS, Helvetica</option>
                                    <option '; if ($result['widget-font-title']=="Verdana, Geneva"){echo 'selected ';} echo 'value="Verdana, Geneva">Verdana, Geneva</option>
                                    <option '; if ($result['widget-font-title']=="Georgia"){echo 'selected ';} echo 'value="Georgia">Georgia</option>
                                    <option '; if ($result['widget-font-title']=="Palatino"){echo 'selected ';} echo 'value="Palatino">Palatino</option>
                                    <option '; if ($result['widget-font-title']=="Times New Roman"){echo 'selected ';} echo 'value="Times New Roman">Times New Roman</option>
                                    <option '; if ($result['widget-font-title']=="Open Sans"){echo 'selected ';} echo 'value="Open Sans">Open Sans</option>
                                    <option '; if ($result['widget-font-title']=="Roboto"){echo 'selected ';} echo 'value="Roboto">Roboto</option>
                                </select>
                                <br>
                                <label>Размер шрифта</label>
                                <select name="widget-size-title">
                                    <option value=""></option>
                                    <option '; if ($result['widget-size-title']=="10"){echo 'selected ';} echo 'value="10">10px / 1em</option>
                                    <option '; if ($result['widget-size-title']=="11"){echo 'selected ';} echo 'value="11">11px / 1.1em</option>
                                    <option '; if ($result['widget-size-title']=="12"){echo 'selected ';} echo 'value="12">12px / 1.2em</option>
                                    <option '; if ($result['widget-size-title']=="13"){echo 'selected ';} echo 'value="13">13px / 1.3em</option>
                                    <option '; if ($result['widget-size-title']=="14"){echo 'selected ';} echo 'value="14">14px / 1.4em</option>
                                    <option '; if ($result['widget-size-title']=="15"){echo 'selected ';} echo 'value="15">15px / 1.5em</option>
                                    <option '; if ($result['widget-size-title']=="16"){echo 'selected ';} echo 'value="16">16px / 1.6em</option>
                                    <option '; if ($result['widget-size-title']=="17"){echo 'selected ';} echo 'value="17">17px / 1.7em</option>
                                    <option '; if ($result['widget-size-title']=="18"){echo 'selected ';} echo 'value="18">18px / 1.8em</option>
                                </select>
                                <br>
                                <label>Цвет шрифта заголовка</label>
                                <input value="'.$result['widget-color-title'].'" name="widget-color-title" class="jscolor">
                                <br>
                                <label>Насыщенность</label>
                                <select name="widget-type-bold-title">
									<option value=""></option>
                                    <option '; if ($result['widget-type-bold-title']=="100"){echo 'selected ';} echo 'value="100">thin-100</option>
                                    <option '; if ($result['widget-type-bold-title']=="200"){echo 'selected ';} echo 'value="200">200</option>
                                    <option '; if ($result['widget-type-bold-title']=="300"){echo 'selected ';} echo 'value="300">light-300</option>
                                    <option '; if ($result['widget-type-bold-title']=="400"){echo 'selected ';} echo 'value="400">normal-400</option>
                                    <option '; if ($result['widget-type-bold-title']=="500"){echo 'selected ';} echo 'value="500">medium-500</option>
                                    <option '; if ($result['widget-type-bold-title']=="600"){echo 'selected ';} echo 'value="600">semi-bold-600</option>
                                    <option '; if ($result['widget-type-bold-title']=="700"){echo 'selected ';} echo 'value="700">bold-700</option>
                                    <option '; if ($result['widget-type-bold-title']=="800"){echo 'selected ';} echo 'value="800">extra-bold-800</option>
                                    <option '; if ($result['widget-type-bold-title']=="900"){echo 'selected ';} echo 'value="900">ultra-bold-900</option>
                                </select>
                                <br>
                                <label>Тип шрифта</label>
                                
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-italic-title']) echo ' checked '; echo ' name="widget-type-italic-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Курсив</a>
                                      </label>
                                </div> 
								
								<div style="margin-top: 12px;" class="checkbox-field-4 w-checkbox">
                                      <input type="checkbox" '; if ($result['widget-type-underline-title']) echo ' checked '; echo ' name="widget-type-underline-title" class="form-radiozag">
                                      <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                                         <a style="color:#333;" class="link">Подчеркнутый</a>
                                      </label>
                                </div> 
								
                                <br>
                                <label>Межстрочный интервал</label>
                                <select name="widget-type-interval-title">
                                    <option '; if ($result['widget-type-interval-title']=="0.9"){echo 'selected ';} echo 'value="0.9">0.9</option>
                                    <option '; if ($result['widget-type-interval-title']=="1"){echo 'selected ';} echo 'value="1">1</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.2"){echo 'selected ';} echo 'value="1.2">1.2</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.3"){echo 'selected ';} echo 'value="1.3">1.3</option>
                                    <option '; if ($result['widget-type-interval-title']=="1.5"){echo 'selected ';} echo 'value="1.5">1.5</option>
                                </select>
                            </div>
                        </div>
                        <input type="submit" value="Сохранить" class="button-7">
                    </form>
                </div>
            </div>
			
			<div style="padding:  0px 10px 0 30px; width:480px">
			   <div style="margin-bottom: 20px;"><strong class="bold-text-16">Дополнительные стили:</strong>
                    <br>
               </div>
               <textarea id="textarea-slider" style="width:440px; font-size: 14px; padding: 10px; border-radius: 4px; border: 1px solid #E0E1E5; height: 600px; resize: vertical;">'.$result['dop-css'].'</textarea>
            </div>
			
            <div class="div-block-118" style="overflow: auto;">
                <div><strong class="bold-text-10">Код для установки:</strong>
                    <br>
                </div>
                <div class="text-block-129">Установите этот код в нужном месте шаблона</div>
                <div class="html-embed w-embed">&lt;div id="corton-slider-widget"&gt;&lt;/div&gt;</div>
                <div class="div-block-124"></div>
                <div class="text-block-130"><strong class="bold-text-11">Внешний вид:</strong>
                    <br>
                </div>
                <div class="html-embed-9 w-embed">
                    <div class="slider-script-container"></div>
                    <div class="widget-slider-preview">
                        <div id="corton-slider-widget" class="widget-slider">
                            <div class="corton-left"> <img src="/images/placeholder.jpg" width="180" height="180"></div>
                            <div class="corton-right"> <span class="corton-sign">Рекомендовано для Вас:</span>
                                <div class="corton-title">Пример рекламного анонса на вашем сайте!</div>
                            </div><span class="close-widget"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="black-fon modalhide"></div>';
             include PANELDIR . '/views/layouts/footer.php';
             return true;
        }

        //Меняет коэфициент отчислений для площадки
        public static function actionOtchicleniay()
        {
            $db = Db::getConnection();
            $dbstat = Db::getstatConnection();
            $_GET['id']= substr($_GET['id'], 4);
            $sql="UPDATE `ploshadki` SET `otchiclen`='".$_GET['otchiclen']."' WHERE `id` = '".$_GET['id']."';";
            $db->query($sql);
            return true;
        }

        public static function actionSpisanie()
        {
            $db = Db::getConnection();
            $sql="SELECT `balans`, `spisanie`,`date` FROM `balans_user` WHERE `user_id`='".$_GET['id']."' AND `date`=(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id`='".$_GET['id']."')";
            $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if (($result) ){
                if (floatval($result['balans'])>=floatval($_GET['sum'])+floatval($result['spisanie'])){
                    if ($result['date']==date('Y-m-d')){
                        $sql = "UPDATE `balans_user` SET `balans` = `balans` - ".$_GET['sum'].",`spisanie` = `spisanie` + ".$_GET['sum']."  WHERE `user_id`='".$_GET['id']."' AND `date`=CURDATE()";
                    }else{
                        $balans=floatval($result['balans'])-floatval($_GET['sum']);
                        $sql = "INSERT INTO `balans_user` SET `user_id`='".$_GET['id']."', `date`=CURDATE(), `balans` = '".$balans."', `spisanie` = '".$_GET['sum']."';";
                    }
                    $db->query($sql);
                    echo "Операция выполнена, списано с баланса";
                    return true;
                }
                echo "Ошибка, неправильная сумма";
                return true;
            }else{
                echo "Ошибка, неудалось списать";
                return true;
            }
        }


        //Меняет коэфициент отчислений для площадки
        public static function actionStat()
        {
            $title='Статистика площадки';
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
                    <td><div class="tooltipinfo1">Показы<span class="tooltiptext1">Показы анонсов</span></div></td>
                    <td style="min-width: 210px;">Клики</td>
                    <td>CTR</td>
                    <td style="min-width: 110px;"><div class="tooltipinfo1">Просмотры<span class="tooltiptext1">Оплаченные просмотры (%&nbsp;кликов)</span></div></td>
                    <td><div class="tooltipinfo1">eCPM<span class="tooltiptext1">Доход на 1000 показов анонсов</span></div></td>
                    <td style="min-width: 120px;"><div class="tooltipinfo1">Переходы<span class="tooltiptext1">Переходы со статей по URL (%&nbsp;просмотр&nbsp;оплаченных)</span></div></td>
                    <td style="min-width: 130px;">Доход</td>
                </tr>
            </thead>';
            if ((strtotime($datebegin)<=strtotime($dateend)) AND (strtotime($datebegin)<=strtotime(date('d.m.Y')))) {
                $db = Db::getConnection();
                $dbstat = Db::getstatConnection();

                $sql = "SELECT SUM(`recomend_aktiv`) as `recomend_aktiv`, SUM(`natpre_aktiv`) as `natpre_aktiv`, SUM(`slider_aktiv`) as `slider_aktiv`, `domen` FROM `ploshadki` WHERE `id`='".$_GET['id']."'";
                $aktiv = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

                $sql = "SELECT SUM(`r_balans`) as `r_balans`,SUM(`e_balans`) as `e_balans`, SUM(`s_balans`) as `s_balans`, SUM(`r`) as `r`, SUM(`e`) as `e`, SUM(`s`) as `s`, SUM(`r_show_anons`) as 'r_show_anons', SUM(`e_show_anons`) as 'e_show_anons', SUM(`s_show_anons`) as 's_show_anons', SUM(`r_promo_load`) as 'r_promo_load', SUM(`e_promo_load`) as 'e_promo_load', SUM(`s_promo_load`) as 's_promo_load', SUM(`r_promo_click`) as 'r_promo_click', SUM(`e_promo_click`) as 'e_promo_click', SUM(`s_promo_click`) as 's_promo_click' FROM `balans_ploshadki` WHERE `ploshadka_id`='".$_GET['id']."' AND `date`>='" . $mySQLdatebegin . "' AND `date`<='" . $mySQLdateend . "'";
                $balansperiod = $dbstat->query($sql)->fetch(PDO::FETCH_ASSOC);

                if ((strtotime($datebegin) <= strtotime(date('d.m.Y'))) AND (strtotime($dateend) >= strtotime(date('d.m.Y')))) {
                    $today = true;
                    $redis = new Redis();
                    $redis->pconnect('185.75.90.54', 6379);
                    $redis->select(3);

                    $ch=$redis->get(date('d').':'.$_GET['id'].':r');
                    if($ch)$balansperiod['r_show_anons']+=$ch;
                    $ch=$redis->get(date('d').':'.$_GET['id'].':e');
                    if($ch)$balansperiod['e_show_anons']+=$ch;
                    $ch=$redis->get(date('d').':'.$_GET['id'].':s');
                    if($ch)$balansperiod['s_show_anons']+=$ch;

                }

                if (is_null($balansperiod['r'])) {
                    $balansperiod['r'] = $balansperiod['e'] = $balansperiod['s'] = 0;
                    $balansperiod['r_balans'] = $balansperiod['e_balans'] = $balansperiod['s_balans'] = '0.00';
                };

                $rCTR=round($balansperiod['r_promo_load']/$balansperiod['r_show_anons']*100);
                $eCTR=round($balansperiod['e_promo_load']/$balansperiod['e_show_anons']*100);
                $sCTR=round($balansperiod['s_promo_load']/$balansperiod['s_show_anons']*100);
                if ((is_nan($rCTR)) or (is_infinite($rCTR))) {$rCTR = '0';}
                if ((is_nan($eCTR)) or (is_infinite($eCTR))) {$eCTR = '0';}
                if ((is_nan($sCTR)) or (is_infinite($sCTR))) {$sCTR = '0';}

                echo '
        <tbody>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="recommendation-mini-site"></div>
                    <div class="logominitext"> </div>
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
            <td>'.$balansperiod['r_show_anons'].'</td>
            <td>'.$balansperiod['r_promo_load'].'</td>
            <td>'.$rCTR.' %</td>
            <td>'.$balansperiod['r'];$val=round(100/$balansperiod['r_promo_load']*$balansperiod['r'],2);if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}echo' ('.$val.'%)</td>
            <td>';
            if (($aktiv['recomend_aktiv'])AND($balansperiod['r']!=0)) {
                $val= round($balansperiod['r_balans']/$balansperiod['r_show_anons']*1100,2);
                if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                echo $val;
            } else {
                echo '0.00';
            }
            echo'&nbsp;p</td>
            <td>'.$balansperiod['r_promo_click'].'</td>
            <td class="bluetext">' . $balansperiod['r_balans'] . '&nbsp;р.</td>
        </tr>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="nativepreview-mini-site"></div>
                    <div class="logominitext"></div>
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
            
            <td>'.$balansperiod['e_show_anons'].'</td>
            <td>'.$balansperiod['e_promo_load'].'</td>
            <td>'.$eCTR.' %</td>
            <td>'.$balansperiod['e'];$val=round(100/$balansperiod['e_promo_load']*$balansperiod['e'],2);if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}echo' ('.$val.'%)</td>
            <td>';
                if (($aktiv['natpre_aktiv'])AND($balansperiod['e']!=0)) {
                    $val= round($balansperiod['e_balans']/$balansperiod['e_show_anons']*1100,2);
                    if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                    echo $val;
                } else {
                    echo '0.00';
                }
                echo'&nbsp;p</td>
            <td>'.$balansperiod['e_promo_click'].'</td>
            <td class="bluetext">' . $balansperiod['e_balans'] . '&nbsp;р.</td>
        </tr>
        <tr>
            <td style="text-align: left; width: 260px;">
                <div style="margin-top: 7px;">
                    <div class="slider-mini-site"></div>
                    <div class="logominitext"></div>
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
            <td>'.$balansperiod['s_show_anons'].'</td>
            <td>'.$balansperiod['s_promo_load'].'</td>
            <td>'.$sCTR.' %</td>  
            <td>'.$balansperiod['s'];$val=round(100/$balansperiod['s_promo_load']*$balansperiod['s'],2);if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}echo' ('.$val.'%)</td>
            <td>';
                if (($aktiv['slider_aktiv'])AND($balansperiod['s']!=0)) {
                    $val= round($balansperiod['s_balans']/$balansperiod['s_show_anons']*1100,2);
                    if ((is_nan($val)) or (is_infinite($val))) {$val = '0.00';}
                    echo $val;
                } else {
                    echo '0.00';
                }
                echo'&nbsp;p</td>
            <td>'.$balansperiod['s_promo_click'].'</td>
            <td class="bluetext">' . $balansperiod['s_balans'] . '&nbsp;р.</td>
        </tr>
    <tbody>';
            }else{echo '<tr><td colspan="7">Некоректные даты фильтра</td></tr>';}
            echo'
        </table>
    </div>
    <div class="table-right">
				 <form id="right-form" name="email-form" class="form-333">
                    <div class="html-embed-3 w-embed" style="margin-top: 40px;">
                         <input type="hidden" name="id" value="'.$_GET['id'].'" id="" class="form-radio">
                        <input type="text" name="datebegin" class="tcal tcalInput" value="'.$datebegin.'">
                        <div class="text-block-128">-</div>
                        <input type="text" name="dateend" class="tcal tcalInput" value="'.$dateend.'">
                        <input type="submit" value="Применить" class="submit-button-addkey w-button">
                    </div>
		        </form>
	</div>
	

    <script>
        document.getElementById("title2").innerHTML="Статистика по площадки:<span class=titlepromo>' . $aktiv['domen'] . '</span>";
    </script>     
</div>';

            include PANELDIR . '/views/layouts/footer.php';
            return true;
        }


        //Удаляет площадку
        public static function actionDel()
        {
            $db = Db::getConnection();
            $sql="SELECT `domen` FROM `ploshadki` WHERE `id` = ".$_GET['id'];
            $domen = $db->query($sql)->fetch(PDO::FETCH_COLUMN);
            $domen = str_replace(".", "_", $domen);
            unlink(PANELDIR.'/style/'.$domen.'.css.gz');
            $sql="DELETE FROM `ploshadki` WHERE `ploshadki`.`id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `style_natpre` WHERE `id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `style_natpro` WHERE `id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `style_promo` WHERE `id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `style_recomend` WHERE `id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `style_slider` WHERE `id` = '".$_GET['id']."';";
            $db->query($sql);
            PloshadkiController::actionIndex();
            return true;
        }
    };
