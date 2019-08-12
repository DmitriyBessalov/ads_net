<?php
class ClickController
{
    public static function actionIndex()
    {
        $title='Клики на промо страницу';
        include PANELDIR.'/views/layouts/header.php';

        $sql = "SELECT `domen` FROM `ploshadki` WHERE (`status`='1')AND(`id`!='0') ORDER BY `domen` ASC";
        $domens = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_COLUMN);
		echo '
		  <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		<div class="table-box">
		<div class="div-block-102-table">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th style="min-width: 130px;">Просмотр ID</th>
                <th style="min-width: 120px;">Анонс ID</th>
                <th>Виджет</th>
                <th>Referer</th>
                <th>Чтение</th>
                <th>Дочитывание</th>
                <th>Клик со&nbsp;статьи</th>
                <th>Базовая ставка</th>
                <th>Списано с рекламодателя</th>
                <th>Оплачено площадке</th>
                <th>IP</th>';
		        if ($_GET['useragent']=='on'){
		            echo '<th>User agent</th>';
                };
                echo'<th>Время</th>
              </tr>
            </thead>';
            if (!empty($_GET['domen'])) {
                $data=date('Y-m-d', strtotime($_GET['date']));
                $sql="SELECT `id` FROM `ploshadki` WHERE `domen`='".$_GET['domen']."'";
                $ploshadka_id= $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);
                $sql="SELECT `prosmotr_id`,`tizer`,`anon_id`,`url_ref`,`read`,`pay`,`pay_platform`,`click`,`user-agent`,`timestamp`,`ip` FROM `stat_promo_prosmotr` WHERE `date`='".$data."' AND `ploshadka_id`='".$ploshadka_id."'";
                $clicks = $GLOBALS['dbstat']->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                foreach($clicks as $value) {
                    $sql = "SELECT n.stavka FROM anons a RIGHT OUTER JOIN anons_index n ON a.promo_id = n.promo_id WHERE a.id='".$value['anon_id']."'";
                    $stavka = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

                    switch ($value['tizer']) {
                        case 'r':
                            $value['tizer']='Recomendation';
                            break;
                        case 'e':
                            $value['tizer']='NatPreviev';
                            break;
                        case 's':
                            $value['tizer']='Slider';
                            break;
                    };
                    if ($value['pay']>0){$read=1;}else{$read=0;}

                    echo '
                      <tr>
                          <td style="font-size: 15px;">'.$value['prosmotr_id'].'</td>
                          <td style="font-size: 15px;">'.$value['anon_id'].'</td>
                          <td style="font-size: 14px;">'.$value['tizer'].'</td>
                          <td style="font-size: 11px;">'.$value['url_ref'].'</td>
                          <td style="font-size: 15px;">'.$read.'</td>
                          <td style="font-size: 15px;">'.$value['read'].'</td>
                          <td style="font-size: 15px;">'.$value['click'].'</td>
                          <td style="font-size: 15px;">'.$stavka.'</td>
                          <td style="font-size: 15px;">'.$value['pay_platform'].'</td>
                          <td style="font-size: 15px;">'.$value['pay'].'</td>
                          <td style="font-size: 15px;">'.$value['ip'].'</td>';
                            if ($_GET['useragent']=='on'){
                                echo '<td style="font-size: 10px;">'.$value['user-agent'].'</td>';
                            };
                            echo'
                          <td style="font-size: 15px;">'.$value['timestamp'].'</td>
                      </tr>';
                };
            }else{
                echo '<td colspan="10"><h2>Выберите площадку</h2></td>';
            };
            echo
            '</table>
        </div>
		</div>
		<div class="table-right">
             <div class="html-embed-3 w-embed">
            <form id="email-form" name="email-form" data-name="Email Form" class="form-3">
		   <select name="date" style="border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">';
                for ($i = 0; $i < 7; $i++){
                    $date=date('d.m.Y',strtotime('-'.$i.' day'));
                    echo '<option '; if ($_GET['date']==$date)echo 'selected '; echo'>' . $date . '</option>';
                };
            echo'
            </select>
			
            <select name="domen" style="margin: 20px 0 20px 0; border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">
            <option value="">Выбор площадки</option>';
            foreach ($domens as $value) {
                echo '<option '; if ($_GET['domen']==$value)echo 'selected '; echo'>' . $value . '</option>';
            };
            echo
            '</select>
						
                <input type="checkbox" name="useragent" class="form-radiozag" '; if ($_GET['useragent']=='on')echo 'checked'; echo'/>
                <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                    <a style="color:#333;" class="link">User Agent</a>
                </label>
  
		  <input type="submit" value="Применить" style="left: 0px !important;" class="submit-button-addkey w-button">
		  </form>
			</div>
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
		include PANELDIR . '/views/layouts/footer.php';
		return true;
    }
		
}
