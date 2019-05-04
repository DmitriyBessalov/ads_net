<?php

class NotificationsController
{
    public static function actionIndex()
    {
        $title='Системные уведомления';
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
                <th>ID</th>
                <th style="width: 140px;">Дата и время</th>
                <th>Площадка</th>
                <th>Владелец</th>
                <th>Описание</th>
				<th>Статус</th>
                <th style="width: 110px;"></th>
              </tr>
            </thead>
                      <tr>
                          <td style="font-size: 15px;">'.$value['anon_id'].'</td>
                          <td style="font-size: 14px;">'.$value['tizer'].'</td>
                          <td style="font-size: 11px;">'.$value['url_ref'].'</td>
                          <td style="font-size: 15px;">'.$value['pay'].'</td>
                          <td style="font-size: 15px;">'.$value['click'].'</td>
                          <td style="font-size: 15px;">'.$stavka.'</td>
                          <td style="font-size: 15px;">'.$value['ip'].'</td>
                      </tr>
            </table>
        </div>
		<div class="table-right">
             <div class="html-embed-3 w-embed">
            <form id="email-form" name="email-form" data-name="Email Form" class="form-3">
		   <select name="date" style="border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">    
            </select>
			
            <select name="domen" style="margin: 20px 0 20px 0; border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">
            <option value="">Выбор площадки</option>
            </select>
  
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
