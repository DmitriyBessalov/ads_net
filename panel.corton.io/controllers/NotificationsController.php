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
