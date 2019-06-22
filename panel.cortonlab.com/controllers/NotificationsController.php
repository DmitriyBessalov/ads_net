<?php

class NotificationsController
{
    public static function actionIndex()
    {
        $title='Системные уведомления';
        include PANELDIR.'/views/layouts/header.php';

        $str='1';
        if ((!isset($_GET['status'])) xor ($_GET['status'] != 'all')) {
              if ($_GET['status']) {
                  $str="`status`='1'";
              }else{
                  $str="`status`='0'";
              }
        }

        $sql="SELECT * FROM `notifications` WHERE ".$str." ORDER BY `status`, `id`";
        $result = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		echo '
		  <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		<div style="min-height: 620px;" class="table-box">
		<div class="div-block-102-table">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>ID</th>
                <th>Дата</th>
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
                $i2 = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_ASSOC);
                if ($i['status']){
                    $i['status']='>Обработано';
                }else{
                    $i['status']=' style="color:#60bf52;">Ожидание';
                }

                echo '
                      <tr>
                          <td>'.$i['id'].'</td>
                          <td>'.$i['date'].'</td>
                          <td class="bluetext">'.$i2['domen'].'</td>
                          <td>'.$i2['email'].'</td>
                          <td class="bluetext">'.$i['opisanie'].'</td>
                          <td'.$i['status'].'</td>
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
		</div>
		<div class="table-right">
            <div class="html-embed-3 w-embed">
            <form id="right-form" class="form-333">
		    <a href="/platforms-add" class="button-add-site w-button">Добавить площадку</a>
			<p class="filtermenu"><label'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="all" class="form-radio"'; if ((!isset($_GET['status'])) OR ($_GET['status']=='all')){echo ' checked';}  echo'>Все уведомления</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='0'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="0" class="form-radio"'; if ($_GET['status']=='0'){echo ' checked';} echo'>В ожидание</label></p>
            <p class="filtermenu"><label'; if ($_GET['status']=='1'){echo ' style="font-weight: 600;"';}echo'><input type="radio" name="status" value="1"  class="form-radio"'; if ($_GET['status']=='1'){echo ' checked';} echo'>Обработано</label></p>
		    </form>
			</div>
		</div>

		';
		include PANELDIR . '/views/layouts/footer.php';
		return true;
    }

    public static function addNotification($platform_id, $opisanie)
    {

        $date=date('Y-m-d');

        $sql= "SELECT `domen` FROM `ploshadki` WHERE `id`='".$platform_id."'";
        $domen = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

        $sql= "INSERT INTO `notifications`( `platform_id`, `opisanie`,`date`) VALUES ('".$platform_id."', '".$opisanie."', '".$date."')";
        $GLOBALS['db']->query($sql);

        mail('support@cortonlab.com', 'Уведомление по '.$domen, $opisanie, "Content-Type: text/html; charset=UTF-8\r\n");
        return true;
    }

    public static function actionObrabotano()
    {

        $sql="UPDATE `notifications` SET `status`='1' WHERE `id` = '".$_GET['id']."';";
        $GLOBALS['db']->query($sql);
        NotificationsController::actionIndex();
        return true;
    }

    public static function actionDel()
    {
        $sql="DELETE FROM `notifications` WHERE `id` = '".$_GET['id']."';";
        $GLOBALS['db']->query($sql);
        NotificationsController::actionIndex();
        return true;
    }
}
