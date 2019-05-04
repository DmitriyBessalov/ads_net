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
		<div style="min-height: 620px;" class="table-box">
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
                          <td>'.$value['anon_id'].'</td>
                          <td>'.$value['anon_id'].'</td>
                          <td>'.$value['anon_id'].'</td>
						  <td>'.$value['anon_id'].'</td>
                          <td style="color:#116DD6;">'.$value['anon_id'].'</td>
                          <td>'.$value['anon_id'].'</td>
                          <td">'.$value['anon_id'].'</td>
                          <td style="width: 110px; text-align: right; padding-right: 20px">
						  <a class="main-item" href="javascript:void(0);" tabindex="1"  style="font-size: 34px; line-height: 1px; vertical-align: super; text-decoration: none; color: #768093;">...</a> 
                                  <ul class="sub-menu"> 
                                     <a href="article-edit?id=' . $i['promo_id'] . '">Обработано</a><br>
									 <a href="article-stat?id=' . $i['promo_id'] . '">Удалить</a><br>
                                  </ul>
						  </td>
                      </tr>
            </table>
        </div>
		<div class="table-right">
            <div class="html-embed-3 w-embed">
            <form id="right-form" class="form-333">
		    <a href="/platforms-add" class="button-add-site w-button">Добавить площадку</a>
			<p class="filtermenu"><label'; if ((!isset($_GET['role'])) OR ($_GET['role']=='all')){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="all" class="form-radio"'; if ((!isset($_GET['role'])) OR ($_GET['role']=='all')){echo ' checked';}  echo'>Все уведомления</label></p>
            <p class="filtermenu"><label'; if ($_GET['role']=='platform'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="platform"  class="form-radio"'; if ($_GET['role']=='platform'){echo ' checked';} echo'>В ожидание</label></p>
            <p class="filtermenu"><label'; if ($_GET['role']=='advertiser'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="advertiser"  class="form-radio"'; if ($_GET['role']=='advertiser'){echo ' checked';} echo'>Обработано</label></p>
		    </form>
			</div>
		</div>

		';
		include PANELDIR . '/views/layouts/footer.php';
		return true;
    }
		
}
