<?php

class LoadController
{
    public static function actionIndex()
    {
        $title='Нагрузка с площадок';
        include PANELDIR.'/views/layouts/header.php';

        $sql = "SELECT `id`,`domen` FROM `ploshadki` WHERE (`status`='1')AND(`id`!='0') ORDER BY `domen` ASC";
        $domens = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        echo '
        <div class="bodys">
	  	  <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		<div class="table-box">
		<div class="div-block-102-table">
        <div class="table w-embed">';
              if (!isset($_GET['platform_id'])){
                  echo '
                  <table>
                        <thead>
                          <tr class="trtop">
                            <th style="min-width: 130px;">Площадка</th>
                            <th style="min-width: 120px;">Сегодня,<br>Нагрузка / Показы анонсов (%)</th>
                            <th>Вчера,<br>Нагрузка / Показы анонсов (%)</th>
                            <th>Позаврера,<br>Нагрузка / Показы анонсов (%)</th>
                          </tr>
                        </thead>
                        <tbody>
                         <tr>
                          <td style="font-size: 15px;">Платформа №1</td>
                          <td style="font-size: 15px;">1/1(50%)</td>
                          <td style="font-size: 14px;">2/1(50%)</td>
                          <td style="font-size: 15px;">3/1(50%)</td>
                         </tr>
                         <tr>
                          <td style="font-size: 15px;">Платформа №2</td>
                          <td style="font-size: 15px;">1/1(50%)</td>
                          <td style="font-size: 14px;">2/1(50%)</td>
                          <td style="font-size: 15px;">3/1(50%)</td>
                         </tr>
                        </tbody>
                      </table>
                  ';
              } else {
                  echo '<h2>Выберите площадку</h2>';
              }
          echo '
        </div>
		</div>
		<div class="table-right">
            <div class="html-embed-3 w-embed">
            <form id="email-form" name="email-form" data-name="Email Form" class="form-3">
            <select name="platform_id" style="margin: 20px 0 20px 0; border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">
            <option value="">Выбор площадки</option>';
            foreach ($domens as $value) {
                echo '<option value="'.$value['id'].'" '; if ($_GET['platform_id']==$value['id'])echo 'selected '; echo'>' . $value['domen'] . '</option>';
            };
            echo
            '</select>						
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
		</div>';

        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }
}