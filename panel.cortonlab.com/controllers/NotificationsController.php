<?php

class NotificationsController
{
    public static function actionIndex()
    {
        $title='Системные уведомления';
        include PANELDIR.'/views/layouts/header.php';
        $db = Db::getConnection();

        if (isset($_GET['status'])) {
            if ($_GET['status'] != 'all') {
                if (in_array($_GET['platform'], $arrplatform)) {
                    $strplatform = $_GET['platform'];
                    $sql = "SELECT `domen` FROM `ploshadki` WHERE id='" . $_GET['platform'] . "'";
                    $domen = $db->query($sql)->fetch(PDO::FETCH_COLUMN);
                } else exit;
            }
        }

        $sql="SELECT * FROM `notifications` WHERE 1";
        $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
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
                <th>Дата и время</th>
                <th>Площадка</th>
                <th>Владелец площадки</th>
                <th>Описание</th>
                <th>Статус</th>
                <th></th>
				</tr>
            </thead>
            ';

            foreach ($result as $i) {
                $i['date']=date('d.m.Y',strtotime($i['date']));
                $sql="SELECT p.`domen`,u.`email` FROM `ploshadki`p RIGHT OUTER JOIN `users` u ON p.`user_id`=u.`id` WHERE  p.`id`='".$i['platform_id']."'";
                $i2 = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($i['status']){
                    $i['status']='Обработано';
                }else{
                    $i['status']='Ожидание';
                }

                echo '
                      <tr>
                          <td>'.$i['id'].'</td>
                          <td>'.$i['date'].'</td>
                          <td>'.$i2['domen'].'</td>
                          <td>'.$i2['email'].'</td>
                          <td>'.$i['opisanie'].'</td>
                          <td>'.$i['status'].'</td>
                          <td style="width: 111px; text-align: right; padding-right: 20px;">
						 <a class="main-item" href="javascript:void(0);" tabindex="1" style="font-size: 34px; line-height: 1px; vertical-align: super; text-decoration: none; color: #768093;">...</a> 
                         <ul class="sub-menu">
                              <a href="notifikations-obrabotano?id='.$i['id'].'">Обработано</a><br>
                              <a href="notifikations-del?id='.$i['id'].'">Удалить</a> 
                         </ul>       
                      </td>
                      </tr>';
            }

            echo '
            </table>
        </div>
		<div class="table-right">
            <div class="html-embed-3 w-embed">
            <form id="right-form" class="form-333">
		    <a href="/platforms-add" class="button-add-site w-button">Добавить площадку</a>
			<p class="filtermenu"><label'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="status" value="all" class="form-radio"'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' checked';}  echo'>Все уведомления</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='0'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="status" value="0" class="form-radio"'; if ($_GET['status']=='0'){echo ' checked';} echo'>В ожидание</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='1'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="status" value="1"  class="form-radio"'; if ($_GET['status']=='1'){echo ' checked';} echo'>Обработано</label></p>
		    </form>
			</div>
		</div>

		';
		include PANELDIR . '/views/layouts/footer.php';
		return true;
    }

    public static function actionObrabotano()
    {
        $db = Db::getConnection();
        $sql="UPDATE `notifications` SET `status`='1' WHERE `id` = '".$_GET['id']."';";
        $db->query($sql);
        NotificationsController::actionIndex();
        return true;
    }

    public static function actionDel()
    {
        $db = Db::getConnection();
        $sql="DELETE FROM `notifications` WHERE `id` = '".$_GET['id']."';";
        $db->query($sql);
        NotificationsController::actionIndex();
        return true;
    }
}
