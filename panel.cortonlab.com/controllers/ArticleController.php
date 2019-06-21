<?php

class ArticleController
{
    public static function actionIndex()
    {
        $title='Управление статьями';
        include PANELDIR.'/views/layouts/header.php';

        echo '
		<div class="table-box">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>ID</th>
                <th>Заголовок</th>
                <th><div class="tooltipinfo1">Расход<span class="tooltiptext1">Израсходованные средства с балансов</span></div></th>
                <th><div class="tooltipinfo1">Показы<span class="tooltiptext1">Количество показов анонсов</span></div></th>
                <th><div class="tooltipinfo1">Клики<span class="tooltiptext1">Клики на промо статью</span></div></th>
				<th style="width: 120px;"><div class="tooltipinfo1">Просмотры<span class="tooltiptext1">Целевые/оплаченные просмотры промо-статей</span></div></th>
                <th style="width: 140px;"><div class="tooltipinfo1">Дочитываний<span class="tooltiptext1">Кол-во пользователей дочитавших промо-статью</span></div></th>
                <th style="width: 132px;"><div class="tooltipinfo1">Переходы<span class="tooltiptext1">Клики с промо статьи и процент от оплаченых просмотров</span></div></th>
                <th style="width: 127px;"><div class="tooltipinfo1">CTR<span class="tooltiptext1">CTR от количества показов анонсов</span></div></th>
                <th style="width: 110px;"></th>
              </tr>
            </thead>';

        if($GLOBALS['role']=='admin'){
            $str="1";
        }else{
            $str="`user_id`='".$GLOBALS['user']."'";
        }

        switch ($_GET['active']){
            case '0': break;
            default: {$str.=" AND `active`='1'";$_GET['active']='1';}
        }

        if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y');}
        if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
        if ((strtotime($datebegin)<=strtotime($dateend)) AND (strtotime($datebegin)<=strtotime(date('d.m.Y')))) {
            $mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
            $mySQLdateend = date('Y-m-d', strtotime($dateend));

            if ((strtotime($datebegin) <= strtotime(date('d.m.Y'))) AND (strtotime($dateend) >= strtotime(date('d.m.Y')))) {
                $today = true;
                $redis = new Redis();
                $redis->pconnect('185.75.90.54', 6379);
                $redis->select(1);
            }

            $sql = "SELECT `main_promo_id`,SUM(`active`) as `active` FROM `promo` WHERE ".$str." GROUP BY `main_promo_id` ORDER BY `main_promo_id` DESC";
            $main_promo_id = $GLOBALS['db']->query($sql)->fetchall(PDO::FETCH_ASSOC);

            foreach ($main_promo_id as $i) {
                $begin = true;
                if ($_GET['active'] == '0') {
                    if ($i['active'])
                        $begin = false;
                }

                if ($begin) {
                    $sql = "SELECT `title`,`namebrand`  FROM `promo` WHERE `id`='" . $i['main_promo_id'] . "'";
                    $promo = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

                    $sql = "SELECT `anons_ids`, `stavka` FROM `anons_index` WHERE `promo_id`='" . $i['main_promo_id'] . "';";
                    $anons_index = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

                    $anons = str_replace(",", "','", $anons_index['anons_ids']);
                    $sql = "SELECT SUM(`reading`) as doread, SUM(`pay`) as pay, SUM(`clicking`) as perehod, SUM(`st`) as st, SUM(`perehod`) as clicking FROM `stat_promo_day_count` WHERE `anons_id` IN ('" . $anons . "')  AND `data`>='" . $mySQLdatebegin . "' AND `data`<='" . $mySQLdateend . "'";
                    $promosum = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_ASSOC);

                    if (is_null($promosum['doread'])) {
                        $promosum['doread'] = $promosum['pay'] = $promosum['perehod'] = $promosum['st'] = $promosum['clicking'] = 0;
                    }

                    $sql = "SELECT SUM(`ch`) FROM `stat_anons_day_show` WHERE `anons_id` IN ('" . $anons . "') AND `date`>='" . $mySQLdatebegin . "' AND `date`<='" . $mySQLdateend . "'";
                    $pokaz = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_COLUMN);

                    if (is_null($pokaz)) {
                        $pokaz = 0;
                    }

                    if (isset($today)) {
                        $anon = explode(',', $anons_index['anons_ids']);
                        foreach ($anon as $y) {
                            $ch = $redis->get(date('d') . ':' . $y);
                            if ($ch) {
                                $pokaz += $ch;
                            }
                        }
                    }

                    $CRT = $promosum['clicking'] / $pokaz;

                    $protsentperehodov = round(100 / $promosum['st'] * $promosum['perehod'], 2);
                    if (is_nan($protsentperehodov)) {
                        $protsentperehodov = 0;
                    }

                    if (is_nan($CRT)) {
                        $CRT = '--';
                    } else {
                        if (is_infinite($CRT)) {
                            $CRT = '--';
                        } else {
                            $CRT = round($CRT * 100, 2) . ' %';
                        }
                    };

                    $protsentst = 100 / $promosum['clicking'] * $promosum['st'];
                    if (is_nan($protsentst)) {
                        $protsentst = 0;
                    }

                    $doread = round(100 / $promosum['clicking'] * $promosum['doread'], 2);
                    if (is_nan($doread) or is_infinite($doread)) $doread = 0;

                    echo '
                                <tr>
                                  <td>' . $i['main_promo_id'] . '</td>
                                  <td style="min-width: 280px; padding-top: 14px; padding-bottom: 12px;">
								     <div class="titleform2"><a style="color: #333333; outline: none; text-decoration: none;" href="/article-edit-content?id=' . $i['main_promo_id'] . '">' . $promo['title'] . '</a></div>
								     <div class="miniinfo"> 
								        <div class="blockminiinfo">
										   <input type="checkbox" ';
                    if ($_GET['active']) echo 'checked="checked "';
                    echo ' class="flipswitch all"/>
                                           <span></span>
										</div>
										<div class="blockminiinfo"><span style="color: #768093;">Бренд: </span>' . $promo['namebrand'] . '</div>
										<div class="blockminiinfo"><span style="color: #768093;">Ставка:</span> ' . $anons_index['stavka'] . '</div>
								     </div>
								  </td>
                                  <td style="color: #116dd6;">' . sprintf("%.2f", $promosum['pay']) . '</td>
                                  <td>' . $pokaz . '</td>
                                  <td>' . $promosum['clicking'] . '</td>
								  <td  style="width:140px;" class="greentext">' . $promosum['st'] . ' (' . sprintf("%.2f", $protsentst) . '%)</td>
                                  <td>' . $promosum['doread'] . ' (' . $doread . '%)</td>
                                  <td>' . $promosum['perehod'] . ' (' . $protsentperehodov . '%)</td>
                                  <td style="min-width: 96px;">' . $CRT . '</td>
                                  <td style="width: 111px; text-align: right; padding-right: 20px;">
								  <a class="main-item" href="javascript:void(0);" tabindex="1"  style="font-size: 34px; line-height: 1px; vertical-align: super; text-decoration: none; color: #768093;">...</a> 
                                  <ul class="sub-menu">
								     <a href="article-edit-content?id=' . $i['main_promo_id'] . '">Отредактировать</a><br>
									 <a href="article-edit-anons?id=' . $i['main_promo_id'] . '">Управление анонсами</a><br>
									 <a style="color: #ff0303;" href="article-del?id=' . $i['main_promo_id'] . '">Удалить</a> 
									 <div style="height:1px; width:100%; background:#E0E1E5; margin: 6px 0;"></div>
									 <a href="article-stat?id=' . $i['main_promo_id'] . '">Расширенная статистика</a><br>
								     <a href="article-a/b?id=' . $i['main_promo_id'] . '">A/B анализ</a><br>
									 <a href="article-stat-url?id=' . $i['main_promo_id'] . '">Анализ ссылок</a><br>
									 <div style="height:1px; width:100%; background:#E0E1E5; margin: 6px 0;"></div>
									 <a href="article-edit-target?id=' . $i['main_promo_id'] . '">Таргетинги</a><br>
									 <a href="article-edit-form?id=' . $i['main_promo_id'] . '">Лид форма</a><br>
                                  </ul>
                                  </td>
                                </tr>
                               ';
                }
            }

            if (isset($today)) {
                $redis->close();
            }
        }else{ echo '<tr><td colspan="12">Некоректные даты фильтра</td></tr>';}
        echo '
          </table>
        </div>
		
		<div class="table-right">
		    <form id="right-form" class="form-333"><br>';
            if ($GLOBALS['role']!='admin') echo '<a href="/article-edit" class="button-add-site w-button">Создать статью</a>';
			echo '
			<p class="filtermenu"><label '; if ((!isset($_GET['active'])) OR ($_GET['active']=='all')){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="active" value="all" class="form-radio"'; if ($_GET['active']=='all'){echo ' checked';} echo'>Все статьи</label></p>
			<p class="filtermenu"><label '; if ($_GET['active']=='1'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="active" value="1" class="form-radio"'; if ($_GET['active']==1){echo ' checked';} echo'>Активные статьи</label></p>
			<p class="filtermenu"><label '; if ($_GET['active']=='0'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="active" value="0" class="form-radio"'; if ($_GET['active']==0){echo ' checked';} echo'>Статьи на паузе</label></p>
			
            <div class="html-embed-3 w-embed" style="margin-top: 40px;">
             <input type="text" name="datebegin" class="tcal tcalInput" autocomplete="off"  value="'.$datebegin.'">
             <div class="text-block-128">-</div>
			 <input type="text" name="dateend" class="tcal tcalInput" autocomplete="off" value="'.$dateend.'">
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
		';
        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }


    public static function actionStat()
    {
        $title='Статистика по анонсам';
        include PANELDIR.'/views/layouts/article_header.php';

        echo '
		<div class="table-box">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>ID</th>
                <td>Превью</td>
                <th>Заголовок</th>
                <th><div class="tooltipinfo1">Расход<span class="tooltiptext1">Израсходованные средства с балансов</span></div></th>
                <th><div class="tooltipinfo1">Показы<span class="tooltiptext1">Количество показов анонсов</span></div></th>
                <th><div class="tooltipinfo1">Клики<span class="tooltiptext1">Клики на промо статью</span></div></th>
				<th><div class="tooltipinfo1">Просмотры<span class="tooltiptext1">Целевые/оплаченные просмотры промо-статей</span></div></th>
                <th><div class="tooltipinfo1">Дочитываний<span class="tooltiptext1">Кол-во пользователей дочитавших промо-статью</span></div></th>
                <th><div class="tooltipinfo1">Переходы<span class="tooltiptext1">Клики с промо статьи и процент от оплаченых просмотров</span></div></th>
                <th><div class="tooltipinfo1">CTR<span class="tooltiptext1">CTR от кол-ва кликов / CTR от кол-ва целевых просмотров</span></div></th>
                <th><div class="tooltipinfo1">PCL<span class="tooltiptext1">Цена за переход по URL</span></div></th>
                <th></th>
              </tr>
            </thead>';

        if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y');}
        if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
        if ((strtotime($datebegin)<=strtotime($dateend)) AND (strtotime($datebegin)<=strtotime(date('d.m.Y')))) {
            $mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
            $mySQLdateend = date('Y-m-d', strtotime($dateend));

            if ((strtotime($datebegin) <= strtotime(date('d.m.Y'))) AND (strtotime($dateend) >= strtotime(date('d.m.Y')))) {
                $today = true;
                $redis = new Redis();
                $redis->pconnect('185.75.90.54', 6379);
                $redis->select(1);
                $ch=0;
            }

            $sql = "SELECT GROUP_CONCAT(`id`) as `id` FROM `anons` WHERE `promo_id`='" . $_GET['id'] . "'";
            $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            $sql = "SELECT `title` FROM  `promo` WHERE `id`='" . $_GET['id'] . "'";
            $title = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            echo '
            <script>
                document.getElementById("title2").innerHTML="Статистика по статье<br><span class=titlepromo>Статья: '.$title.'</span>";
            </script>';
            $anon[] = str_replace(",", "','", $result);
            $anons2 = explode(',', $result);

            foreach ($anons2 as $i) {
                $anon[] = $i;
                if ($today){
                    $pokazann[$i]=$redis->get(date('d') . ':' . $i);
                    $ch+= $redis->get(date('d') . ':' . $i);
                }
            }
            $ch2 = -1;
            foreach ($anon as $anons) {
                $sql = "SELECT SUM(`reading`) as doread, SUM(`pay`) as pay, SUM(`clicking`) as perehod, SUM(`st`) as st, SUM(`perehod`) as clicking FROM `stat_promo_day_count` WHERE `anons_id` IN ('" . $anons . "') AND `data`>='" . $mySQLdatebegin . "' AND `data`<='" . $mySQLdateend . "'";
                $promosum = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_ASSOC);

                if (is_null($promosum['doread'])) {
                    $promosum['doread'] = $promosum['pay'] = $promosum['perehod'] = $promosum['st'] = $promosum['clicking'] = 0;
                }

                $sql = "SELECT SUM(`ch`) FROM `stat_anons_day_show` WHERE `anons_id` IN ('" . $anons . "')AND `date`>='" . $mySQLdatebegin . "' AND `date`<='" . $mySQLdateend . "'";
                $pokaz = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_COLUMN);
                if (is_null($pokaz)) {$pokaz = 0;}

                $protsentperehodov = round(100 / $promosum['st'] * $promosum['perehod'], 2);
                if ((is_infinite($protsentperehodov)) OR (is_nan($protsentperehodov))){$protsentperehodov=0;}

                if(isset($today)) {
                    if ($ch2 != -1) {
                        $pokaz +=$pokazann[$anons];
                    }else{
                        $pokaz += $ch;
                    }
                }

                $CRT = $promosum['clicking'] / $pokaz;

                if (is_nan($CRT)) {
                    $CRT = '--';
                } else {
                    if (is_infinite($CRT)) {
                        $CRT = '--';
                    } else {
                        $CRT = round($CRT * 100, 2) . '%';
                    }
                };
                $anons = str_replace("','", ",", $anons);
                $PCL=$promosum['pay']/$promosum['perehod'];
                if ((is_infinite($PCL)) OR (is_nan($PCL))) $PCL = '0';

                $protsentst=100/$promosum['clicking']*$promosum['st'];
                if (is_nan($protsentst) or is_infinite($protsentst))$protsentst=0;

                echo '
             <tr>';
                if ($ch2 != -1) {
                    $sql = "SELECT `user_id`,`img_290x180`,`title`,`active` FROM `anons` WHERE `id`='" . $anons . "'";
                    $img = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                    echo '<td>' . $anons . '</td>
                          <td><a class="screenshot" style="text-decoration:none;" rel="https://api.cortonlab.com/img/' . $img['user_id'] . '/a/' . $img['img_290x180'] . '" ><img style="max-width: 70px !important; border-radius: 2px;" src="https://api.cortonlab.com/img/' . $img['user_id'] . '/a/' . $img['img_290x180'] . '"></a></td>';
                    echo '<td style="width: 180px !important;"><div class=titleform>' . $img['title'] . '</div></td>';
                } else {
                    echo '<td>' . $_GET['id'] . '</td>';
                    echo '<td></td><td></td>';
                }

                $doread=round(100/$promosum['clicking']*$promosum['doread'],2);
                if (is_nan($doread) or is_infinite($doread))$doread=0;

                echo '
               <td style="color: #116dd6;">' . sprintf("%.2f", $promosum['pay']) . '</td>
               <td>'.$pokaz.'</td>
               <td>' . $promosum['clicking'] . '</td>
			   <td class="greentext" style="width:140px;">'.$promosum['st'].' ('.sprintf("%.2f", $protsentst).'%)</td>
               <td>' . $promosum['doread'] . ' ( '.$doread.'%)</td> 
               <td>' . $promosum['perehod'] . ' (' . $protsentperehodov . '%)</td>
               <td style="min-width:90px;">' . $CRT . '</td>
               <td>' . sprintf("%.2f", $PCL) . '</td>
               
               <td style="width: 20px !important;">';
               if ($ch2 != -1) {echo'<input type="checkbox" '; if ($img['active']) echo 'checked="checked" '; echo 'class="flipswitch anons">';}
               echo '    
               </td>
              </tr>
             ';
                $ch2++;
            }
        }else{echo '<tr><td colspan="13">Некоректные даты фильтра</td></tr>';}


        echo '
          </table>
        </div>
		
		<div class="table-right">
		    <form id="right-form" class="form-333">
			
            <div class="html-embed-3 w-embed" style="margin-top: 40px;">
             <input type="hidden" name="id" value="'.$_GET['id'].'">
             <input type="text" name="datebegin" class="tcal tcalInput" autocomplete="off" value="'.$datebegin.'">
             <div class="text-block-128">-</div>
			 <input type="text" name="dateend" class="tcal tcalInput" autocomplete="off" value="'.$dateend.'">
             
             <input type="submit" value="Применить" style="left: 0px !important;" class="submit-button-addkey w-button">
			
            </div>
			</form>
		</div>
		
		</div>
        
		';
        include PANELDIR . '/views/layouts/footer.php';
        return true;
    }

    public static function actionUpdate()
    {

        switch ($_POST['tab']){
            case 'статья' :{
                $data_add = date('Y-m-d');
                if ($_POST['id'] == "new"){$id='-';}else{$id=$_POST['id'];};
                $role=UsersController::checkRole();
                if (($role=='admin') AND ($id!="")){
                    $sql="SELECT `user_id` FROM `promo` WHERE `id`=".$id;
                    $user_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                }else{
                    $user_id = UsersController::getUserId();
                }
                $_POST['formtext']=stripcslashes ($_POST['formtext']);
                preg_match_all("/src=\"data:image\/(jpeg|jpg|gif|png);base64,(.*?)\">/", $_POST['formtext'],$out);
                mkdir(APIDIR.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'promo'.DIRECTORY_SEPARATOR.$id, 0755);
                $i=0;
                while ($i<count($out[0])){
                    $hash=hash('crc32', $out[2][$i]);
                    $ifp = fopen(APIDIR.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'promo'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$hash.'.'.$out[1][$i], "wb");
                    fwrite($ifp, base64_decode($out[2][$i]));
                    fclose($ifp);
                    $replase=' src="https://api.cortonlab.com/img/promo/'.$id.'/'.$hash.'.'.$out[1][$i].'">';
                    $_POST['formtext']=str_replace($out[0][$i], $replase, $_POST['formtext']);
                    $i++;
                }

                $sql="UPDATE `promo` SET  `user_id`='".$user_id."', `title`='".$_POST['title']."',`text`='".$_POST['formtext']."',`data_add`='".$data_add."' WHERE  `id`='".$id."'";
                if (!$GLOBALS['db']->exec($sql)){
                    $sql = "INSERT INTO `promo` SET `user_id`='".$user_id."', `title`='".$_POST['title']."',`text`='".$_POST['formtext']."',`data_add`='".$data_add."';";
                    $GLOBALS['db']->query($sql);
                    $id=$GLOBALS['db']->lastInsertId();
                    $sql = "UPDATE `promo` SET  `main_promo_id`='".$id."' WHERE  `id`='".$id."';";
                    $sql .= "INSERT INTO `anons_index` SET `promo_id`='".$id."';";
                    $GLOBALS['db']->query($sql);
                    header('Location: /article-edit-anons?id='.$id);
                    exit;
                }
                break;
            }case 'настройка' :{
            $strtolow=mb_strtolower($_POST['words'], 'UTF-8');
            $words = array_unique(explode(",", $strtolow));
            asort($words);
            $strtolow=implode(',', $words);
            $sql="SELECT `active`,`words` FROM `promo` WHERE `id`='".$_POST['id']."';";
            $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
            $wordsold=explode(",", $result['words']);

            if (!$result['active']){array_splice($words, 0);}

            $words=ArticleController::miniword($words);
            $wordsold=ArticleController::miniword($wordsold);

            $wordsall = array_unique(array_merge($words, $wordsold));
            foreach($wordsall as $i){
                if ($i!="")
                    if (in_array($i, $words)){
                        if (!in_array($i, $wordsold)){
                            $sql="SELECT `promo_ids` FROM `words_index` WHERE `word`='".$i."';";
                            $promo_ids = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                            if ($promo_ids){
                                $promo_id=explode(',', $promo_ids);
                                $promo_id[]=$_POST['id'];
                                asort($promo_id);
                                $promo_id=array_unique($promo_id);
                                $promo_ids=implode(',', $promo_id);
                            }else{
                                $promo_ids=$_POST['id'];
                            }
                            $sql="REPLACE INTO `words_index` SET `word`='".$i."' , `promo_ids`='".$promo_ids."';";
                            $GLOBALS['db']->query($sql);
                        }
                    }else{
                        if (in_array($i, $wordsold)){
                            $sql="SELECT `promo_ids` FROM `words_index` WHERE `word`='".$i."';";
                            $promo_ids = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

                            //Удаляет
                            $ids=explode(",", $promo_ids);
                            unset($ids[array_search($_POST['id'],$ids)]);
                            $promo_ids = implode(",", $ids);
                            if ($promo_ids==''){
                                $sql="DELETE FROM `words_index` WHERE `word` ='".$i."'";
                                $GLOBALS['db']->query($sql);
                            }else{
                                $sql="REPLACE INTO `words_index` SET `word`='".$i."' , `promo_ids`='".$promo_ids."';";
                                $GLOBALS['db']->query($sql);
                            }
                        }
                    }
            }

            $sql="UPDATE `promo` SET `words`='".$strtolow."', `namebrand`='".$_POST['namebrand']."' WHERE `id`='".$_POST['id']."';";
            $GLOBALS['db']->query($sql);
            $sql="UPDATE `anons_index` SET `stavka`='".$_POST['stavka']."' WHERE `promo_id`='".$_POST['id']."';";
            $GLOBALS['db']->query($sql);

            break;
        }case 'анонсы':{
            //Список старых анонсов
            $sql="SELECT `anons_ids` FROM `anons_index` WHERE `promo_id`='".$_POST['id']."';";
            $anonsold=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            if ($anonsold!=""){
                $anonsold2= explode(",", $anonsold);
            }else {
                $anonsold2 = array();
            };

            //Список старых файлов картинок
            $role= UsersController::getUserRole();
            if ($role=='advertiser'){
                $user_id= UsersController::getUserId();
            }else {
                $sql ="SELECT `user_id` FROM `promo` WHERE `id` = '".$_POST['id']."';";
                $user_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            }
            $uploaddir = '/var/www/www-root/data/www/api.cortonlab.com'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$user_id.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR;

            //загрузка файлов
            mkdir('/var/www/www-root/data/www/api.cortonlab.com'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$user_id, 0755);
            mkdir('/var/www/www-root/data/www/api.cortonlab.com'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$user_id.DIRECTORY_SEPARATOR.'a', 0755);

            $count=count ($_POST['anons_ids']);

            foreach($anonsold2 as $i) {
                $sql="SELECT `img_290x180`,`img_180x180` FROM `anons` WHERE `id`='".$_POST['anons_ids'][$i]."'";
                $imgdb = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                $imageold[]=$imgdb['img_290x180'];
                $imageold[]=$imgdb['img_180x180'];
            };

            for ($i = 0; $i < $count; $i++){
                if (($_FILES['image290']['type'][$i] == 'image/gif' || $_FILES['image290']['type'][$i] == 'image/jpeg' || $_FILES['image290']['type'][$i] == 'image/png') && ($_FILES['image290']['size'][$i] != 0 and $_FILES['image290']['size'][$i] <= 1024000)) {
                    $hash290 = md5_file($_FILES['image290']['tmp_name'][$i]);
                    $extension290 = substr($_FILES['image290']['type'][$i], 6, 4);
                    $uploadfile = $uploaddir . $hash290 . '.' . $extension290;
                    move_uploaded_file($_FILES['image290']['tmp_name'][$i], $uploadfile);
                    $filename290[$i]=", `img_290x180`='" . $hash290 . '.' . $extension290 . "'";
                }
                if (($_FILES['image180']['type'][$i] == 'image/gif' || $_FILES['image180']['type'][$i] == 'image/jpeg' || $_FILES['image180']['type'][$i] == 'image/png') && ($_FILES['image180']['size'][$i] != 0 and $_FILES['image180']['size'][$i] <= 1024000)) {
                    $hash180 = md5_file($_FILES['image180']['tmp_name'][$i]);
                    $extension180 = substr($_FILES['image180']['type'][$i], 6, 4);
                    $uploadfile = $uploaddir . $hash180 . '.' . $extension180;
                    move_uploaded_file($_FILES['image180']['tmp_name'][$i], $uploadfile);
                    $filename180[$i]=", `img_180x180`='" . $hash180 . '.' . $extension180 . "'";
                }
                if($_POST['anons_ids'][$i]=="new"){
                    $sql = "SELECT MAX(`id`) FROM `anons`";
                    $maxid = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                    $maxid+=random_int ( 1 , 105 );
                    $sql = "INSERT INTO `anons` SET `id`=".$maxid.", `promo_id`=".$_POST['id'].", `user_id`='".$user_id."' ,`title`='" . $_POST['title'][$i] . "',`snippet`='" . $_POST['opisanie'][$i] . "'".$filename290[$i].$filename180[$i];
                    $GLOBALS['db']->query($sql);
                    $anon[]=$GLOBALS['db']->lastInsertId();
                    $img[]=$hash290.'.'.$extension290;
                    $img[]=$hash180.'.'.$extension180;
                }else if (in_array($_POST['anons_ids'][$i], $anonsold2)) {
                    $sql = "UPDATE `anons` SET `promo_id`=".$_POST['id'].", `title`='" . $_POST['title'][$i] . "',`snippet`='" . $_POST['opisanie'][$i] . "'".$filename290[$i].$filename180[$i]." WHERE `id`='" . $_POST['anons_ids'][$i] . "'";
                    $GLOBALS['db']->query($sql);
                    $anon[]=$_POST['anons_ids'][$i];
                    $sql="SELECT `img_290x180`,`img_180x180` FROM `anons` WHERE `id`='".$_POST['anons_ids'][$i]."'";
                    $imgdb = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                    $img[]=$imgdb['img_290x180'];
                    $img[]=$imgdb['img_180x180'];
                }
            }

            /* Удаляет старые картинки
            foreach($imageold as $i) {
                if (!in_array($i, $img)){
                     unlink($uploaddir.$i);
                }
            };*/

            //Обновляет индексы анонсов к статье
            $anons=implode(",", $anon);
            $sql="UPDATE `anons_index` SET `anons_ids`='".$anons."' WHERE `promo_id`='".$_POST['id']."'";
            $GLOBALS['db']->query($sql);
            foreach($anonsold2 as $i) {
                if (!in_array($i, $anon)){
                    $sql = "DELETE FROM `anons` WHERE `id`='" . $i . "';";
                    $GLOBALS['db']->query($sql);
                }
            };
            break;
        }case 'форма_заказа' :{
            $sql="UPDATE `promo` SET `form_title`='".$_POST['form-title']."',`form_text`='".$_POST['form-text']."',`form_button`='".$_POST['form-button']."' WHERE `id`='".$_POST['id']."'";
            $GLOBALS['db']->query($sql);
        }
        };
        header('Location: https://panel.cortonlab.com/articles?active=1');
        exit;
    }

    public static function actionA_b()
    {
        $title='А/B тестирование';
        include PANELDIR.'/views/layouts/article_header.php';

        echo'
        <div class="table-box">
            <div class="table w-embed">
                <table>
                    <thead>
                        <tr class="trtop">
                            <td style="min-width: 118px;">Версия (ID)</td>
                            <td>Дата запуска</td>
                            <td>Заголовок</td>
                            <td>Расходы</td>
                            <td>Клики</td>
                            <td>Просмотры</td>
                            <td>Дочитывания</td>
                            <td>Переходы</td>
                        </tr>
                    </thead>
                    <tbody>';
        if (isset($_GET['datebegin'])){$datebegin=$_GET['datebegin'];}else{$datebegin=date('d.m.Y');}
        if (isset($_GET['dateend'])){$dateend=$_GET['dateend'];}else{$dateend=date('d.m.Y');};
        if ((strtotime($datebegin)<=strtotime($dateend)) AND (strtotime($datebegin)<=strtotime(date('d.m.Y')))) {
            $mySQLdatebegin = date('Y-m-d', strtotime($datebegin));
            $mySQLdateend = date('Y-m-d', strtotime($dateend));

            if ((strtotime($datebegin) <= strtotime(date('d.m.Y'))) AND (strtotime($dateend) >= strtotime(date('d.m.Y')))) {
                $today = true;
                $redis = new Redis();
                $redis->pconnect('185.75.90.54', 6379);
                $redis->select(1);
            }

            $sql = "SELECT `id`,`data_add`,`title`,`active` FROM `promo` WHERE `main_promo_id`='".$_GET['id']."';";
            $result = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT `anons_ids` FROM `anons_index` WHERE `promo_id`=".$result[0]['id'];
            $anons= $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            $anons = str_replace(",", "','", $anons);

            $ch = 65;
            foreach ($result as $i) {

                $sql = "SELECT SUM(`reading`) as doread, SUM(`pay`) as pay, SUM(`clicking`) as perehod, SUM(`st`) as st, SUM(`perehod`) as clicking FROM `stat_promo_day_count` WHERE `anons_id` IN ('" . $anons . "') AND `promo_variant`='".$i['id']."' AND `data`>='" . $mySQLdatebegin . "' AND `data`<='" . $mySQLdateend . "'";
                $promosum = $GLOBALS['dbstat']->query($sql)->fetch(PDO::FETCH_ASSOC);

                if (is_null($promosum['doread'])){$promosum['doread']=$promosum['pay']=$promosum['perehod']=$promosum['st']=$promosum['clicking']=0;}

                $protsentperehodov = round(100 / $promosum['st'] * $promosum['perehod'], 2);
                if (is_nan($protsentperehodov)){$protsentperehodov=0;}

                $protsentst=100/$promosum['clicking']*$promosum['st'];
                if (is_nan($protsentst)){$protsentst=0;}

                $doread=round(100/$promosum['clicking']*$promosum['doread'],2);
                if (is_nan($doread) or is_infinite($doread))$doread=0;

                echo '      <tr>
                                <td>' . chr($ch++) . ' (' . $i['id'] . ')</td>
                                <td>'.date('d.m.Y', strtotime($i['data_add'])).'</td>
                                
                                <td style="min-width: 280px; padding-top: 14px; padding-bottom: 12px;">
                                         <div class="titleform2"><a style="color: #333333; outline: none; text-decoration: none;" href="/article-edit-content?id=' . $i['id'] . '">' . $i['title'] . '</a></div>
                                         <div class="miniinfo"> 
                                            <div class="blockminiinfo">
                                               <input type="checkbox" ';
                if ($i['active']) echo 'checked="checked "';
                echo 'class="flipswitch one"/>
                                               <span></span>
                                            </div>
                                         </div>
                                </td>
                                <td style="color: #116dd6;">' . sprintf("%.2f", $promosum['pay']) . '</td>
                                <td>' . $promosum['clicking'] . '</td>
								<td  style="width:140px;" class="greentext">' . $promosum['st'] . ' ('.sprintf("%.2f", $protsentst).'%)</td>
                                <td>' . $promosum['doread'] . ' ('.$doread.'%)</td>
                                <td>' . $promosum['perehod'] . ' (' . $protsentperehodov . '%)</td>
                            </tr>';
            }
        }else{ echo '<tr><td colspan="10">Некоректные даты фильтра</td></tr>';}
        echo '        </tbody>
                </table>
            </div>
            
            <div class="table-right">
		    <form id="right-form" class="form-333"><br>
                <div class="html-embed-3 w-embed" style="margin-top: 40px;">
                 <input type="hidden" name="id" value="'.$_GET['id'].'">
                 <input type="text" name="datebegin" class="tcal tcalInput" autocomplete="off"  value="'.$datebegin.'">
                 <div class="text-block-128">-</div>
                 <input type="text" name="dateend" class="tcal tcalInput" autocomplete="off" value="'.$dateend.'">
                 <input type="submit" value="Применить" style="left: 0px !important;" class="submit-button-addkey w-button">
                </div>
			</form>
		</div>
            
        </div>';
        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionContent()
    {
        $title='Редактирование статьи';
        $id=$_GET['id'];
        $sql="SELECT * FROM `promo` WHERE `id`='".$id."'";
        $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

        include PANELDIR.'/views/layouts/article_header.php';
        echo'
        <script type="text/javascript" src="https://panel.cortonlab.com/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="https://panel.cortonlab.com/js/quill.js"></script>
        <link rel="stylesheet" href="https://panel.cortonlab.com/css/quill.snow.css">
        
        <div style="margin-top: 40px; padding-left: 20px;">';

        $sql="SELECT `id` FROM `promo` WHERE `main_promo_id`='".$result['main_promo_id']."'";
        $result2 = $GLOBALS['db']->query($sql)->fetchALL(PDO::FETCH_COLUMN);

        switch (count($result2)){
            case 1:
                echo '<a class="aticlevariant btnarticle'; if ($id!=$result2[0]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[0].'">Вариант А</a>            
                      <a class="btnarticlegr" style="width: 50px;float:left;margin-right: 12px;" id="add_variat_promo">+</a>'; break;
            case 2:
                echo '<a class="aticlevariant btnarticle'; if ($id!=$result2[0]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[0].'">Вариант А</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[1]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[1].'">Вариант B</a>            
                      <a class="btnarticlegr" style="width: 50px;float:left;margin-right: 12px;" id="add_variat_promo">+</a>'; break;
            case 3:
                echo '<a class="aticlevariant btnarticle'; if ($id!=$result2[0]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[0].'">Вариант А</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[1]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[1].'">Вариант B</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[2]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[2].'">Вариант C</a>            
                      <a class="btnarticlegr" style="width: 50px;float:left;margin-right: 12px;" id="add_variat_promo">+</a>'; break;
            case 4:
                echo '<a class="aticlevariant btnarticle'; if ($id!=$result2[0]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[0].'">Вариант А</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[1]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[1].'">Вариант B</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[2]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[2].'">Вариант C</a>            
                      <a class="aticlevariant btnarticle'; if ($id!=$result2[3]) echo 'gr'; echo '" style="width: 120px;float:left;margin-right: 12px;" href="https://panel.cortonlab.com/article-edit-content?id='.$result2[3].'">Вариант D</a>';
        };

    echo
        '</div>
        <form method="post" id="formtextsend" action="/article-update" class="form-2">
                    <input type="hidden" name="tab" value="статья">
                    <input type="hidden" name="id" value="'.$id.'" class="w-checkbox-input">
                    <div class="div-block-97" style="width: 1337px">
                        <div style=" width: 1337px;">
                            <input type="text" class="text-field-4 w-input" style=" width: 760px;" maxlength="256" name="title" value="'.$result['title'].'" placeholder="Заголовок" id="title" required="">
                            <input name="formtext" type="hidden">
                            <div id="toolbar_position"></div>
                            <div id="editor-container">
                        '.$result['text'].'
                        </div>
                        </div>
						<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-top: 60px;"></div>
                            <button class="submit-button-6 w-button" type="submit">'; if($title=='Редактирование статьи'){echo 'Сохранить статью';}else{echo 'Далее';}; echo'</button>
                    </div>
        </form>
        <script>
            var quill = new Quill(\'#editor-container\', {
                modules: {
                    toolbar: [
                        [{ header: \'2\' }, "bold", "italic", "underline", { list: \'ordered\' }, { list: \'bullet\' }, "image", "video", "blockquote", "link", "clean"]
                    ]
                },
                scrollingContainer: "#scrolling-container",
                placeholder: "Написать что-то ценное...",
                theme: "snow"
            });
            
            var form = document.querySelector(\'#formtextsend\');
            form.onsubmit = function() {
                var about = document.querySelector(\'input[name=formtext]\');
                var textt = document.querySelector(\'.ql-editor\');
                about.value = textt.innerHTML;
                return true;
            }
        </script>';

        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionAnons()
    {
        $title='Анонсы статей';

        if ($_GET['id']==''){
            $id='new';
        }else{
            $id=$_GET['id'];
        };
        include PANELDIR.'/views/layouts/article_header.php';
        echo '<form method="post" action="/article-update" class="form-2" enctype="multipart/form-data">
                    <input type="hidden" name="tab" value="анонсы">
                    <input type="hidden" name="id" value="'.$id.'">
                    <div id="anonses">';
            $sql="SELECT `anons_ids` FROM `anons_index` WHERE `promo_id`='".$id."'";
            $anons_ids = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            if ($anons_ids!='') {
                $anons = explode(",", $anons_ids);
                foreach ($anons as $i) {
                    $sql = "SELECT * FROM `anons` WHERE `id`='" . $i . "'";
                    $anon = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

                    $sql = "SELECT `user_id` FROM `promo` WHERE `id`='" . $id . "'";
                    $dir = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                    $imgdir = '//api.cortonlab.com/img/'.$dir.'/a/';
                    echo '
                                <div class="div-block-97-copy">
								<div class="text-block-103">Настройка анонса</div>
                                    <input type="hidden" name="anons_ids[]" value="'.$anon['id'].'">
                                    <div class="div-block-142">
                                        <div class="div-block-145">
                                            <input type="text" value="'.$anon['title'].'" class="text-field-6 _1 w-input" maxlength="55" name="title[]" placeholder="Заголовок анонса статьи до 55 символов" id="title-3" required="">
                                            <textarea name="opisanie[]" placeholder="Описание от 90 до 130 символов" maxlength="130" class="textarea-7 w-input">' . $anon['snippet'] . '</textarea>
                                        </div>
                                    </div>
                                    <div class="div-block-142">
                                        <div class="div-block-148">
                                            <div class="image-preview" style="background-image:url(' . $imgdir . $anon['img_290x180'] . ');background-position:center center;background-repeat:no-repeat;background-size:cover;">
                                                <label for="image-upload290" style="background-color:#e1e2e8" class="image-label">Обновить изображение 290x180px</label>
                                                <input type="file" name="image290[]" class="image-upload290" accept=".png,.jpeg,.jpg,.gif" />
                                            </div>
                                        </div>
                                        <div class="div-block-147"></div>
                                        <div class="div-block-148">
                                            <div class="image-preview _180" style="background-image:url(' . $imgdir . $anon['img_180x180'] . ');background-position:center center;background-repeat:no-repeat;background-size:cover;">
                                                <label for="image-upload290" style="background-color:#e1e2e8" class="image-label">Обновить изображение 180x180px</label>
                                                <input type="file" name="image180[]" class="image-upload180" accept=".png,.jpeg,.jpg,.gif" />
                                            </div>
                                        </div>
                                    </div>
                                    <a class="button-10 w-button delanons">Удалить анонс</a>
                                </div>';
                };
            };
            echo '
                    </div>
                    <input type="hidden" name="del_id" value="">
					<div style="border-top: 0 solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px;"></div>
                    <div class="submit-button-6" id="addanons" style="margin-right: 20px;">Добавить анонс</div>
                     <input type="submit" value="Сохранить" class="submit-button-6">
                 </form>';
        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionAnons_start()
    {
        $sql="UPDATE `anons` SET `active`='1' WHERE `id`='".$_GET['id']."'";
        $GLOBALS['db']->query($sql);

        $sql="SELECT `promo_id` FROM `anons` WHERE `id`='".$_GET['id']."'";
        $promo_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

        $sql="UPDATE `anons_index` SET `anons_ids`=(SELECT GROUP_CONCAT(`id`) as `id` FROM `anons` WHERE `promo_id`='" . $promo_id . "' AND `active`='1') WHERE `promo_id`='".$promo_id."'";
        $GLOBALS['db']->query($sql);

        return true;
    }

    public static function actionAnons_stop()
    {
        $sql="UPDATE `anons` SET `active`='0' WHERE `id`='".$_GET['id']."'";
        $GLOBALS['db']->query($sql);

        $sql="SELECT `promo_id` FROM `anons` WHERE `id`='".$_GET['id']."'";
        $promo_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

        $sql="UPDATE `anons_index` SET `anons_ids`=(SELECT GROUP_CONCAT(`id`) as `id` FROM `anons` WHERE `promo_id`='" . $promo_id . "' AND `active`='1') WHERE `promo_id`='".$promo_id."'";
        $GLOBALS['db']->query($sql);

        return true;
    }


    public static function actionTarget()
    {
        $title='Таргетинг';
        include PANELDIR.'/views/layouts/article_header.php';
        if ($_GET['id']==''){
            $id='new';
        }else{
            $id=$_GET['id'];
            $sql="SELECT * FROM `promo` WHERE `id`='".$id."'";
            $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
        };
        echo '
        <form method="post" action="/article-update" class="form-2">
                    <div class="div-block-97" style="padding: 30px 0;">
                        <input type="hidden" name="tab" value="настройка">
                        <input type="hidden" name="id" value="'.$id.'">
                        <input type="hidden" name="words" value="">
						<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-left: -20px; margin-top: -20px;"></div>
						<div class="text-block-103">Ключевые слова</div>
                        <div class="div-block-81">
                            <div>
                                <div class="div-block-82">
                                    <input type="text" class="text-field-2 w-input" maxlength="256" placeholder="Ключ" id="addkey-2">
                                    <div class="text-block-141">+</div>
                                </div>
                            </div>
                        </div>
                        <div class="div-block-84">';
            if ($result['words']!=""){
                $word=explode(",", $result['words']);
                foreach($word as $i) {
                    echo'
                                        <div class="div-block-86" >
                                            <div class="text-block-114" >'.$i.'</div >
                                            <div class="text-block-98" > Удалить</div >
                                        </div>';
                };
            };
            echo'
                        </div>
                        <div class="text-block-110">Можно добавить до 50-ти ключей. Без пробелов. Минимальное кол-во символов - 4.</div>
                    </div>
					<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-left: -20px; margin-top: -20px;"></div>
					<div class="div-block-97" style="display: flex;padding: 20px 0 0 0;">					
                        <div style="flex-direction: column;">
                            <div>
                            <div class="text-block-103">Выберите регион:</div>
                                <div class="div-block-84" style="width: 500px">
                                    <div>
                                        <select name="select2" class="text-field-geo" style="width: 400px;">
                                            <option selected="selected">Все страны</option>
                                            <option>Россия</option>
                                            <option>Армения</option>
                                            <option>Азербайджан</option>
                                            <option>Белоруссия</option>
                                            <option>Грузия</option>
                                            <option>Латвия</option>
                                            <option>Литва</option>
                                            <option>Монголия</option>
                                            <option>Казахстан</option>
                                            <option>Норвегия</option>
                                            <option>Польша</option>
                                            <option>Украина</option>
                                            <option>Финляндия</option>
                                            <option>Эстония</option>
                                            <option>Швеция</option>
                                        </select>
                                    </div>
                                    <div>
                                        <select name="select2" class="text-field-geo" style="width: 400px;">
                                            <option selected="selected">Все регионы</option>
                                            <option>Чебурашка</option>
                                        </select>                                        
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div>
                            <div class="submit-button-6" style="margin:60px 50px 0px 50px">>></div>
                        </div>
                        <div>
                            <div class="text-block-103">Выбранные регионы:</div>
                            <div class="div-block-84" style="width: 700px">
                            <div class="div-block-86">
                                <div class="text-block-114">Москва </div>
                                <div class="text-block-98"> Удалить</div>
                            </div>
                            <div class="div-block-86">
                                <div class="text-block-114">Московская обл.</div>
                                <div class="text-block-98"> Удалить</div>
                            </div>
                            </div>
                        </div>
                    </div>
					<div class="text-block-103" style="padding: 35px 0 0 0;">Ставка</div>
                    <div class="div-block-85">
                        <div>';
                            $sql="SELECT `stavka` FROM `anons_index` WHERE `promo_id`='".$id."'";
                            $stavka = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                            echo'
                            <input type="text" class="text-field-9 w-input" maxlength="256" name="stavka" placeholder="0.00" id="stavka" value="'.$stavka.'" required>
                        </div>
                        <div>
                            <div class="text-block-96">₽ за CPG</div>
                        </div>
                    </div>
                    										
					<div class="text-block-103" style="padding: 35px 0 0 0;">Бренд</div>
                    <div class="div-block-85"></div>
                    <input type="text" class="text-field-9 w-input" maxlength="256" name="namebrand" placeholder="Название бренда" id="stavka" value="'.$result['namebrand'].'">
					<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-top: 60px;"></div>
					<input type="submit" value="Сохранить изменения" class="submit-button-6">
					
                </form>';

        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionPromo_form()
    {
        $title='Контактная форма в статье';
        include PANELDIR.'/views/layouts/article_header.php';
        if ($_GET['id']==''){
            $id='new';
        }else{
            $id=$_GET['id'];
            $sql="SELECT * FROM `promo` WHERE `id`='".$id."'";
            $result = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
        };
        echo '
                <form method="post" action="/article-update" class="form-2">
                    <input type="hidden" name="tab" value="форма_заказа">
                    <input type="hidden" name="id" value="'.$id.'">
                    <div class="div-block-97">
                        <input type="text" value="'.$result['form_title'].'" class="text-field-13 w-input" maxlength="46" name="form-title" placeholder="Заголовок формы" id="form-title">
                        <input type="text" value="'.$result['form_text'].'" class="text-field-14 w-input" maxlength="78" name="form-text" placeholder="Текст формы" id="form-text">
                        <select required="" class="select-field-2 w-select" name="form-button">
                            <option value="">Текст кнопки</option>
                            <option '; if ($result['form_button']=='Отправить'){echo 'selected ';}; echo 'value="Отправить">Отправить</option>
                            <option '; if ($result['form_button']=='Заказать') {echo 'selected ';}; echo 'value="Заказать">Заказать</option>
                            <option '; if ($result['form_button']=='Оформить') {echo 'selected ';}; echo 'value="Оформить">Оформить</option>
                            <option '; if ($result['form_button']=='Получить') {echo 'selected ';}; echo 'value="Получить">Получить</option>
                        </select>
						<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-top: 60px;"></div>
                        <input type="submit" value="Сохранить" class="submit-button-6">
                    </div>
                </form>';
        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionStat_url()
    {
        $title='Анализ ссылок';
        include PANELDIR.'/views/layouts/article_header.php';

        echo'
        <div class="table-box">
            <div class="table w-embed">
                <table>
                    <thead>
                        <tr class="trtop">
                            <td>Анкор</td>
                            <td>URL</td>
                            <td>Переходы</td>
                            <td>% Переходов</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>текст</td>
                            <td>https://example.com</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>    
                </table>
            </div>
        </div>';
        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }

    public static function actionEdit()
    {
        $title='Создание новой статьи';
        $id='new';
        include PANELDIR.'/views/layouts/header.php';
        echo '
<script type="text/javascript" src="https://panel.cortonlab.com/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="https://panel.cortonlab.com/js/quill.js"></script>
<link rel="stylesheet" href="https://panel.cortonlab.com/css/quill.snow.css">
    <div class="w-tab-content">
            <div class="form-block-2 w-form">
                <form method="post" id="formtextsend" action="/article-update" class="form-2">
                    <input type="hidden" name="tab" value="статья">
                    <input type="hidden" name="id" value="'.$id.'" class="w-checkbox-input">
                    <div class="div-block-97" style="width: 1337px">
					<div class="text-block-103">Контент статьи</div>
                        <div style="width: 1337px;">
                            <input type="text" class="text-field-4 w-input" style=" width: 760px;" maxlength="256" name="title" value="'.$result['title'].'" placeholder="Заголовок" id="title" required="">
                            <input name="formtext" type="hidden">
                            <div id="toolbar_position"></div>
                            <div id="editor-container">
                                '.$result['text'].'
                            </div>
                        </div>
						<div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px; margin-top: 60px;"></div>
                            <button class="submit-button-6 w-button" type="submit">Далее</button>
                    </div>
                </form>
            </div>
    </div>
<script>
    var quill = new Quill(\'#editor-container\', {
      modules: {
        toolbar: [
          [{ header: \'2\' }, "bold", "italic", "underline", { list: \'ordered\' }, { list: \'bullet\' }, "image", "video", "blockquote", "link", "clean"]
        ]
      },
      scrollingContainer: "#scrolling-container",
      placeholder: "Написать что-то ценное...",
      theme: "snow"
    });
    
    var form = document.querySelector(\'#formtextsend\');
    form.onsubmit = function() {
      var about = document.querySelector(\'input[name=formtext]\');
      var textt = document.querySelector(\'.ql-editor\');
      about.value = textt.innerHTML;
      return true;
    }
</script>
';
        include PANELDIR . '/views/layouts/footer.php';
        return true;
    }

    //Добавление индексов слов
    public static function startword($id){

        $sql ="SELECT `words` FROM `promo` WHERE `id` = '".$id."';";
        $word = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        if ($word=="") {echo 'word'; exit;}
        $words=explode(',',$word);
        $words=ArticleController::miniword($words);
        foreach ($words as $word){
            $sql="SELECT `promo_ids` FROM `words_index` WHERE `word`='".$word."'";
            $promo_id=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            $promo_ids=explode(',', $promo_id);
            $promo_ids[]=$id;
            $promo_ids=array_unique($promo_ids);
            asort($promo_ids);
            $promo_id=implode(',',$promo_ids);
            $sql="UPDATE `words_index` SET `promo_ids`='".$promo_id."' WHERE `word`='".$word."'";
            if (!$GLOBALS['db']->exec($sql)){
                $sql="INSERT INTO `words_index` SET `promo_ids`='".$id."', `word`='".$word."'";
                $GLOBALS['db']->query($sql);
            }
        }
        return true;
    }

    //Очистка индексов слов
    public static function stopword($id){

        $sql ="SELECT `words` FROM `promo` WHERE `id` = '".$id."';";
        $word = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
        $words=explode(',',$word);
        $words=ArticleController::miniword($words);
        foreach ($words as $word){
            $sql="SELECT `promo_ids` FROM `words_index` WHERE `word`='".$word."'";
            $promo_id=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            $promo_ids=explode(',', $promo_id);
            $key=array_search($id, $promo_ids);
            if (false !== $key)
                unset( $promo_ids[$key]);
            $promo_id=implode(',',$promo_ids);
            if ($promo_id==''){
                $sql = "DELETE FROM `words_index` WHERE `word`='" . $word . "'";
            }else{
                $sql = "UPDATE `words_index` SET `promo_ids`='" . $promo_id . "' WHERE `word`='" . $word . "'";
            }
            $GLOBALS['db']->query($sql);
        }
        return true;
    }

    //Остановка показа статей
    public static function actionStop_all(){
        {


            ArticleController::stopword($_GET['id']);

            $sql ="UPDATE `promo` SET `active`='0' WHERE `main_promo_id`= '".$_GET['id']."';";
            $GLOBALS['db']->query($sql);
            return true;
        }
    }


    //Активация показа статей
    public static function actionStart_all(){
        {

            //Проверка на анонсы
            $sql ="SELECT `anons_ids` FROM `anons_index` WHERE `promo_id` = '".$_GET['id']."';";
            $anons = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            if ($anons=="") {echo 'anon'; exit;}

            ArticleController::startword($_GET['id']);

            $sql ="UPDATE `promo` SET `active`='1' WHERE `main_promo_id`= '".$_GET['id']."';";
            $GLOBALS['db']->query($sql);

            $sql ="SELECT COUNT(*) FROM `promo` WHERE `main_promo_id`='".$_GET['id']."';";
            $count=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            if ($count!=1){
                echo $count;
            }else{
                echo 'true';
            }

            return true;
        }
    }

    //Активация показа статей
    public static function actionStart(){
        {


            $id=preg_replace('/[ABCD ()]/', '', $_GET['id']);

            $sql ="SELECT `main_promo_id` FROM `promo` WHERE `id`= '".$id."';";
            $main_promo_id=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            //Проверка на анонсы
            $sql ="SELECT `anons_ids` FROM `anons_index` WHERE `promo_id` = '".$main_promo_id."';";
            $anons = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
            if ($anons=="") {echo 'anon'; exit;}

            $sql ="SELECT COUNT(*)  FROM `promo` WHERE `main_promo_id`= '".$main_promo_id."' AND `active`='1';";
            $count=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            if (!$count) {
                ArticleController::startword($main_promo_id);
            }

            $sql ="UPDATE `promo` SET `active`='1' WHERE `id`= '".$id."';";
            $GLOBALS['db']->query($sql);
            echo 'true';
            return true;
        }
    }

    //Остановка показа статей
    public static function actionStop(){
        {


            $id=preg_replace('/[ABCD ()]/', '', $_GET['id']);

            $sql ="UPDATE `promo` SET `active`='0' WHERE `id`= '".$id."';";
            $GLOBALS['db']->query($sql);

            $sql ="SELECT `main_promo_id` FROM `promo` WHERE `id`= '".$id."';";
            $main_promo_id=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            $sql ="SELECT COUNT(*)  FROM `promo` WHERE `main_promo_id`= '".$main_promo_id."' AND `active`='1';";
            $count=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            if (!$count){
                ArticleController::stopword($main_promo_id);
            }
            echo 'true';
            return true;
        }
    }

    //Функция обрезает окончания слов
    public static function miniword($words)
    {
        $count = count($words);
        for ($i = 0; $i < $count; $i++) {
            $words[$i] = preg_replace('/ья$|яя$|ая$|ия$|я$/', "", $words[$i]);
            $words[$i] = preg_replace('/ое$|ее$|ие$|ые$|е$/', "", $words[$i]);
            $words[$i] = preg_replace('/а$/', "", $words[$i]);
            $words[$i] = preg_replace('/иями$|ями$|ьми$|еми$|ами$|ии$|и$/', "", $words[$i]);
            $words[$i] = preg_replace('/ь$/', "", $words[$i]);
            $words[$i] = preg_replace('/его$|ого$|о$/', "", $words[$i]);
            $words[$i] = preg_replace('/ий$|ей$|ый$|ой$|й$/', "", $words[$i]);
            $words[$i] = preg_replace('/иям$|им$|ем$|ом$|ям$|ам$/', "", $words[$i]);
            $words[$i] = preg_replace('/ы$/', "", $words[$i]);
            $words[$i] = preg_replace('/ию$|ью$|ею$|ою$|ю$/', "", $words[$i]);
            $words[$i] = preg_replace('/иях$|ях$|их$|ах$/', "", $words[$i]);
            $words[$i] = preg_replace('/ев$|ов$/', "", $words[$i]);
            $words[$i] = preg_replace('/у$/', "", $words[$i]);
        }
        return array_unique($words);
    }

    //Удаление промо статьи
    public static function actionDel(){
        {

            ArticleController::actionStop();

            //Написать функцию очистки от лишних анонсов
            $sql ="DELETE FROM `promo` WHERE `id` = '".$_GET['id']."';";
            $sql.="DELETE FROM `anons_index` WHERE `anons_index`.`promo_id` = '".$_GET['id']."'";

            $GLOBALS['db']->query($sql);
            ArticleController::actionIndex();
            return true;
        }
    }

    //Копирование промо статьи на основе текущей
    public static function actionClone(){
        {

            $sql ="SELECT * FROM `promo` WHERE `id` = '".$_GET['id']."';";
            $promo=$GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);

            $sql ="INSERT INTO `promo` SET `title`='".$promo['title']."', `text`='".$promo['text']."', `main_promo_id`=".$promo['main_promo_id'].", `data_add`=CURDATE();";
            $GLOBALS['db']->query($sql);
            $id=$GLOBALS['db']->lastInsertId();

            echo $id;
            return true;
        }
    }
}