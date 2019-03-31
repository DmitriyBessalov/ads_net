<?php

class TicketController
{
    public static function actionIndex()
    {
        $title='Тикеты';
		include PANELDIR.'/views/layouts/header.php';
	echo '
	<div class="form-block w-form">
          <form id="email-form" name="email-form" class="form-3" method="post">
            <div class="html-embed-2 w-embed"><select style="min-width:200px; background-color:#fff;
 height: 35px; padding: 5px 5px 5px 10px; border:1px solid #E1E2E8; border-radius:4px; color:#768093;">
<option>Все площадки</option>
<option>Информационные</option>
<option>Новостные</option>
</select></div>
            <div class="div-block-121">
              <div class="html-embed-3 w-embed"><input style="height: 35px; margin-left: 20px; border:1px solid #E1E2E8; border-radius:4px; color:#768093;" type="date" name="from"></div>
              <div class="text-block-128">-</div>
              <div class="html-embed-3 w-embed"><input style="height: 35px; border:1px solid #E1E2E8; border-radius:4px; color:#768093;" type="date" name="to"></div>
            </div>
			<input type="submit" value="Применить" class="submit-button-addkey w-button"></form>
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
        <div class="table w-embed">
          <table>
            <thead>
              <tr class="trtop">
                <th>ID</th>
                <th>Тема</th>
                <th>Отдел</th>
                <th>Дата создания</th>
                <th>Дата изменения</th>
                <th>Статус</th>
              </tr>
            </thead>
            <tr>
              <td>77</td>
              <td>Как решить вопрос?</td>
              <td>Технический</td>
              <td>22.08.2018</td>
              <td>23.08.2018</td>
              <td>Решен</td>
            </tr>
            <tr>
              <td>76</td>
              <td>Как решить вопрос?</td>
              <td>Технический</td>
              <td>22.08.2018</td>
              <td>23.08.2018</td>
              <td>Ждёт ответа</td>
              </tr><tr>
                <td>78</td>
                <td>Как решить вопрос?</td>
                <td>Технический</td>
                <td>22.08.2018</td>
                <td>23.08.2018</td>
                <td>Ответ получен</td>
              </tr>
          </table>
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
        </div>';
		
		include PANELDIR . '/views/layouts/footer.php';
        return true;
    }
}
