<?php
class UsersController
{
	//Список пользователей в системе
	public static function actionIndex(){
        $title='Управление пользователями';
        include PANELDIR.'/views/layouts/header.php';
        $db = Db::getConnection();
        if (isset($_POST['email'])){
            if ($_POST['aktiv']=="on"){$_POST['aktiv']=1;}else{$_POST['aktiv']=0;};
            if ($_POST['id']!=""){
                $id=$_POST['id'];
                $sql="UPDATE `users` SET `email`='".$_POST['email']."', `password_md5`='".md5($_POST['password'])."',`fio`='".$_POST['fio']."',`role`='".$_POST['role']."',`aktiv`='".$_POST['aktiv']."' WHERE `id`='".$id."';";
                $db->query($sql);
            }else{
                $data=date('Y-m-d');
                $phpsession= substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 26);
                $sql="INSERT INTO `users` SET `data_add`='".$data."', `email`='".$_POST['email']."', `password_md5`='".md5($_POST['password'])."',`fio`='".$_POST['fio']."',`role`='".$_POST['role']."',`phpsession`='".$phpsession."',`aktiv`='".$_POST['aktiv']."';";
                $db->query($sql);
                if ($_POST['role']=='advertiser'){
                    $id=$db->lastInsertId();
                    $dirName=PANELDIR.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$id;
                    mkdir($dirName);
                    mkdir($dirName.DIRECTORY_SEPARATOR.'a');
                }
            }
        };

        if ((isset($_GET['role'])) AND ($_GET['role']!='all')){
            $str=" WHERE u.`role`='".$_GET['role']."'";
        }

        $sql="SELECT u.id, u.email, u.fio, u.role, u.last_ip , u.data_add, GROUP_CONCAT(`p`.`domen` SEPARATOR '<br>') AS `domen` FROM ploshadki p RIGHT OUTER JOIN users u ON p.user_id = u.id".$str." GROUP BY u.email";

        $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		echo '
        <script src="https://panel.cortonlab.com/js/jquery-3.3.1.min.js" type="text/javascript"></script> 
        <script>
                function balans_spisanie(i){
                    $.get( "https://panel.cortonlab.com/platforms-spisanie?id="+i+"&sum="+$("#sum_spisanie"+i).val(),  function(data) {
                        $("#sum_spisanie"+i).val("0");
	                    $("#sum_spisanie"+i).prop(\'disabled\', true);
	                    $("#status_spisanie"+i).html(data);
	                })
                }
        </script>
        <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		 <div class="table-box">
		 <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>Email</th>
                <th>ФИО</th>
                <th>Группа</th>
                <th>IP авторизации</th>
                <th>Дата создания</th> 
                <th>Площадки</th>
                <th>Баланс</th>
                <th style="width:110px"></th>
              </tr>
            </thead>';
             foreach($result as $i){
                 switch ($i['role']) {
                     case "admin": $i['role'] = "Администраторы"; break;
                     case "advertiser": $i['role'] = "Рекламодатели"; break;
                     case "platform": $i['role'] = "Площадки"; break;
                     case "manager": $i['role'] = "Менеджер";
                 };

                 $sql="SELECT `balans` FROM `balans_user` WHERE `user_id`='".$i['id']."' AND `date`=(SELECT MAX(`date`) FROM `balans_user` WHERE `user_id`='".$i['id']."')";
                 $balans=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
                 if (!$balans){$balans='0.00';}
                 echo "
            <tr>
              <td style=\"color:#116DD6\">".$i['email']."</td>
              <td>".$i['fio']."</td>
              <td>".$i['role']."</td>
              <td>".$i['last_ip']."</td>
              <td style=\"width: 200px;\">".$i['data_add']."</td>
              <td>".$i['domen']."</td>
              <td style=\"width: 154px; color: #116DD6;\">".$balans."</td>              
              <td style=\"width:90px; text-align: right; padding-right: 20px;\">
			      <a class=\"main-item\" href=\"javascript:void(0);\" tabindex=\"1\"  style=\"font-size: 34px; line-height: 1px; vertical-align: super; text-decoration: none; color: #768093;\">...</a> 
                  <ul class=\"sub-menu\"> 
                      <a href=\"user-edit?id=".$i['id']."\">Редактировать</a><br>
                      <a href=\"user-enter?id=".$i['id']."\">Войти</a><br>
                      <a class='modalclickb' id='balans".$i['id']."'>Вывод баланса</a><br>
                      <a href=\"user-del?id=".$i['id']."\">Удалить</a> 	 
                   </ul>
              </td>
                        <div class=\"modal otchislen\" id='spisanie".$i['id']."' style=\"left:30%;top:300px;right:30%;display: none;\">
                            <div style=\"min-width: 400px !important;\" class=\"div-block-78 w-clearfix\">
                                <div class=\"div-block-132 modalhide\">
                                    <img src=\"/images/close.png\" alt=\"\" class=\"image-5\">
                                </div>
                                <div style='text-align: left;'>
                                    <br>
                                    <br>
                                    <br>
                                    Площадка: ".$i['domen']."<br>
                                    Email: ".$i['email']." <br><br>
                                    <input id='sum_spisanie".$i['id']."' type=\"number\" step=\"0.01\" min=\"0\" max=\"".$balans."\" placeholder='Сумма' value='0.00'> руб.<br><br>
                                    <p id='status_spisanie".$i['id']."'>Максимальная сумма к выводу ".$balans." руб.</p>
                                    <a id='button_spisanie".$i['id']."' onclick=\"balans_spisanie(".$i['id'].");\" class=\"button-add-site w-button\">Списать с баланса</a>
                                </div>
                            </div>
                         </div>
            </tr>";
        };
        echo '
          </table>
          <div class="black-fon modalhide" style="display: none;"></div>
		  </div>
		  <div class="table-right">
		    <form id="right-form" class="form-333">
			<a href="/user-edit" class="button-add-site w-button">Новый пользователь</a>			
            <p class="filtermenu"><label'; if ((!isset($_GET['role'])) OR ($_GET['role']=='all')){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="all" class="form-radio"'; if ((!isset($_GET['role'])) OR ($_GET['role']=='all')){echo ' checked';}  echo'>Все пользователи</label></p>
            <p class="filtermenu"><label'; if ($_GET['role']=='platform'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="platform"  class="form-radio"'; if ($_GET['role']=='platform'){echo ' checked';} echo'>Площадки</label></p>
            <p class="filtermenu"><label'; if ($_GET['role']=='advertiser'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="advertiser"  class="form-radio"'; if ($_GET['role']=='advertiser'){echo ' checked';} echo'>Рекламодатели</label></p>
            <p class="filtermenu"><label'; if ($_GET['role']=='admin'){echo ' style="text-decoration: underline;"';}echo'><input type="radio" name="role" value="admin"  class="form-radio"'; if ($_GET['role']=='admin'){echo ' checked';} echo'>Техподдержка</label></p>
           
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

    public static function getUser()
    {
        $db = Db::getConnection();
        $sql="SELECT `id`,`role`,`email`,`aktiv` FROM `users` WHERE `phpsession`='".$_COOKIE['PHPSESSID']."' LIMIT 1;";
        $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($result['aktiv']) {
            $GLOBALS['user'] = $result['id'];
            $GLOBALS['role'] = $result['role'];
            $GLOBALS['email'] = $result['email'];
        }else{
            header('Location: https://cortonlab.com/');
            exit;
        }
    }


    //Возвращает роль пользователя
    public static function checkRole()
    {
        $db = Db::getConnection();
        $sql="SELECT `role`,`aktiv` FROM `users` WHERE `phpsession`='".$_COOKIE['PHPSESSID']."' LIMIT 1;";
        $result=$db->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($result['aktiv']) return $result['role'];
        // Иначе выдаём форму авторизации
        header('Location: https://cortonlab.com/');
        exit;
    }

    //Возвращает id пользователя
    public static function getUserId()
    {
        $db = Db::getConnection();
        $sql="SELECT `id` FROM `users` WHERE `phpsession`='".$_COOKIE['PHPSESSID']."' LIMIT 1;";
        return $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    //Возвращает role пользователя
    public static function getUserRole()
    {
        $db = Db::getConnection();
        $sql="SELECT `role` FROM `users` WHERE `phpsession`='".$_COOKIE['PHPSESSID']."' LIMIT 1;";

        return $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    public static function getUserEmail()
    {
        $db = Db::getConnection();
        $sql="SELECT `email` FROM `users` WHERE `phpsession`='".$_COOKIE['PHPSESSID']."' LIMIT 1;";
        return $result=$db->query($sql)->fetch(PDO::FETCH_COLUMN);
    }

    //Добавление (создание) пользователей
    public static function actionEdit(){
	    if (isset($_GET['id'])){
            $title='Редактирование пользователя';
            $db = Db::getConnection();
            $sql="SELECT `email`,`fio`,`role`,`aktiv` FROM `users` WHERE `id`='".$_GET['id']."' LIMIT 1;";
            $result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
        }else{
            $title='Добавление пользователя';
        };
        include PANELDIR.'/views/layouts/header.php';
        echo '
        <div class="section-2">
          <div class="w-form">
            <form method="post" action="/users" class="form">
              <div style="padding-left:20px;" class="div-block-102">
                <div class="text-block-103">Настройка</div>
                <div class="div-block-116">
					<input type="text" class="text-field-10 w-input" maxlength="256" name="fio" value="'.$result['fio'].'" placeholder="Имя" required="">
				</div>
				<input type="hidden" name="id" value="'.$_GET['id'].'">
				<input class="text-field-10 w-input" maxlength="256" name="email" value="'.$result['email'].'" placeholder="Email" required="">
				<input type="password" class="text-field-10 w-input" maxlength="256" name="password" value="" placeholder="Пароль" required="">
				<select name="role" required="" class="select-field w-select">
					<option '; if ($result['role']=="platform") echo 'selected '; echo 'value="platform">Площадки</option>
					<option '; if ($result['role']=="advertiser") echo 'selected '; echo 'value="advertiser">Рекламодатели</option>
                    <option '; if ($result['role']=="manager") echo 'selected '; echo 'value="manager">Менеджер</option>
					<option '; if ($result['role']=="admin") echo 'selected '; echo 'value="admin">Админы</option>
				</select>
                <div class="div-block-127">
                  <div class="html-embed-7 w-embed">
				    <label>
					  <input type="checkbox" class="ios-switch tinyswitch" '; if ($result['aktiv']) echo 'checked="" '; echo ' name="aktiv"><div><div></div></div>
					</label>
				   </div>
                  <div class="text-block-138">Заблокирован / Активен</div>
                </div>
              </div>
			  <div style="border-top: 1px solid #E0E1E5 !important; width: 1337px; margin-bottom: 60px;"></div>
			  <input type="submit" value="Сохранить настройки" class="submit-button-6"></form>
            <div class="w-form-done"></div>
            <div class="w-form-fail"></div>
          </div>
        </div>
        ';
        include PANELDIR . '/views/layouts/footer.php';
        return true;
    }

    //Авторизация
    public static function actionLogin(){
        $session=substr(sha1(rand()), 0, 26);
        setcookie ( 'PHPSESSID', $session, time () + 10000000 , '/');
        $db = Db::getConnection();
        $email=$_POST['login'];
        $password=md5($_POST['password']);
        $ip=$_SERVER['REMOTE_ADDR'];
        $sql="SELECT `password_md5`, `role` FROM `users` WHERE (`email`='".$email."') AND (`aktiv`='1') LIMIT 1;";
        $user = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($user['password_md5'] === $password) {
             $sql = "UPDATE `users` SET `phpsession` = '" . $session . "',`last_ip`='" . $ip . "' WHERE `email`='" . $email . "';";
             $db->query($sql);
             UsersController::panelStartPage($user['role']);
        }else{
             SiteController::actionLoginform();
        };
        return true;
    }

    //Удаление пользователя
    public static function actionDel(){
        $db = Db::getConnection();
        $sql="DELETE FROM `users` WHERE `users`.`id` = ".$_GET['id'];
        $db->query($sql);

        /* Удаление каталога c содержимым только под linux
           $dirName=PANELDIR.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$id;
           //Добавить проверку на существование файла
           exec("rm -rf $dirName");
           rmdir($dirName);
        */
        UsersController::actionIndex();
        return true;
    }

    //Вход в пользователя из под админа
    public static function actionEnter(){
        $db = Db::getConnection();
        $sql="SELECT `phpsession`,`role` FROM `users` WHERE `users`.`id` = ".$_GET['id'];
        $user = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
        setcookie ( 'PHPSESSID', $user['phpsession'], time () + 10000000, '/');
        UsersController::panelStartPage($user['role']);
        exit;
    }

    //Стартовая страница при входе в админку
    public static function panelstartPage($role){
        switch ($role) {
            case 'advertiser':
                header('Location: /articles?active=1');
                break;
            case 'manager':
                header('Location: /platforms?status=1');
                break;
            default:
                header('Location: /finance');
        }
        exit;
    }

    //Выход из панели
    public static function actionLogout(){
        setcookie ( 'PHPSESSID', "", time () - 10000000 );
        header('Location: https://cortonlab.com/');
        return true;
    }


}