<?php

class ClickController
{
    public static function actionIndex()
    {
        $title='Клики на промо страницу';
        include PANELDIR.'/views/layouts/header.php';
        $db = Db::getConnection();
        $dbstat = Db::getstatConnection();
        $sql = "SELECT `domen` FROM `ploshadki` WHERE (`status`='1')AND(`id`!='0') ORDER BY `domen` ASC";
        $domens = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
		echo '
		  <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		<div class="table-box">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th style="min-width: 130px;">Просмотр ID</th>
                <th style="min-width: 120px;">Анонс ID</th>
                <th>Виджет</th>
                <th>Referer</th>
                <th>Прочитано</th>
                <th>Оплачено</th>
                <th>Переход</th>
                <th>Ставка</th>
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
                $ploshadka_id= $db->query($sql)->fetch(PDO::FETCH_COLUMN);
                $sql="SELECT `prosmotr_id`,`tizer`,`anon_id`,`url_ref`,`read`,`pay`,`click`,`user-agent`,`timestamp`,`ip` FROM `stat_promo_prosmotr` WHERE `date`='".$data."' AND `ploshadka_id`='".$ploshadka_id."'";
                $clicks = $dbstat->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                foreach($clicks as $value) {
                    $sql = "SELECT n.stavka FROM anons a RIGHT OUTER JOIN anons_index n ON a.promo_id = n.promo_id WHERE a.id='".$value['anon_id']."'";
                    $stavka = $db->query($sql)->fetch(PDO::FETCH_COLUMN);

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

                    echo '
                      <tr>
                          <td style="font-size: 15px;">'.$value['prosmotr_id'].'</td>
                          <td style="font-size: 15px;">'.$value['anon_id'].'</td>
                          <td style="font-size: 14px;">'.$value['tizer'].'</td>
                          <td style="font-size: 11px;">'.$value['url_ref'].'</td>
                          <td style="font-size: 15px;">'.$value['read'].'</td>
                          <td style="font-size: 15px;">'.$value['pay'].'</td>
                          <td style="font-size: 15px;">'.$value['click'].'</td>
                          <td style="font-size: 15px;">'.$stavka.'</td>
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
			
			
                <input type="checkbox" checked="" name="mobile" class="form-radiozag">
                <label style="margin-top:0px !important;" id="zagrecomend" class="w-form-label">
                    <a style="color:#333;" class="link">User Agent</a>
                </label>
            
			
             <input name="useragent" type="checkbox" '; if ($_GET['useragent']=='on')echo 'checked'; echo'/> User Agent        
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
