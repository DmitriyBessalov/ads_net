<?php

class WordsController
{
    public static function actionIndex()
    {
        $title='Ключевые слова';
        include PANELDIR.'/views/layouts/header.php';

        echo '
            <script>
                document.getElementById("title2").innerHTML="Ключевые слова<br><span class=titlepromo>Площадка: '.$_GET['domen'].'</span>";
            </script>

		<div class="table-box">
		<div class="div-block-102-table">
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>Ключи без окончаний</th>
                <th>Запросы</th>
              </tr>
            </thead>';

            $sql = "SELECT `id` FROM `platforms_domen_memory` WHERE `domen`='".$_GET['domen']."'";
            $platform_id = $GLOBALS['db']->query($sql)->fetch(PDO::FETCH_COLUMN);

            if (!empty($_GET['domen'])) {
                $sql="SELECT `word`,`count` FROM `words` WHERE `platform_id`='".$platform_id."' ORDER BY `words`.`count` DESC";
                $words = $GLOBALS['dbstat']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                foreach($words as $value) {

                    echo '
                          <tr>
                              <td style="font-size: 15px;">'.$value['word'].'_</td>
                              <td style="font-size: 15px;">'.$value['count'].'</td>
                          </tr>';
                };
            }else{
                echo '<td colspan="2"><h2>Выберите площадку</h2></td>';
            };

        echo '
          </table>
        </div>
		</div>
		
		<div class="table-right">
           <div class="html-embed-3 w-embed">
            <form id="email-form" name="email-form" data-name="Email Form" class="form-3">
			
            <select name="domen" style="margin: 20px 0 20px 0; border: 0px; background: #f4f6f9; color: #768093; width: 75px; border: 1px solid #E0E1E5; padding: 4px 8px; border-radius: 4px; width: 193px; height: 34px; cursor: pointer;  -webkit-appearance: none; -moz-appearance: none; appearance: none;">
            <option value="">Выбор площадки</option>';

            $sql = "SELECT `domen` FROM `ploshadki` WHERE (`status`='1')AND(`id`!='0') ORDER BY `domen` ASC";
            $domens = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_COLUMN);

            foreach ($domens as $value) {
                    echo '<option '; if ($_GET['domen']==$value) echo 'selected '; echo'>' . $value . '</option>';
                };

            echo
            '</select>
			
		   <input type="submit" value="Применить" style="left: 0px !important;" class="submit-button-addkey w-button">
		   </form>
		 </div>
		</div>
		
		</div>
        
		';



        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }
}