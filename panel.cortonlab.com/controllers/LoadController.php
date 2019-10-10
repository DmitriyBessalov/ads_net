<?php

class LoadController
{
    public static function actionIndex()
    {
        $title='Анализ категорий ';
        include PANELDIR.'/views/layouts/header.php';

        $sql = "SELECT `id`,`domen` FROM `ploshadki` WHERE (`status`='1')AND(`id`!='0') ORDER BY `domen` ASC";
        $domens = $GLOBALS['db']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $GLOBALS['postgre'] = new PDO('pgsql:host=185.75.90.54;dbname=corton', 'corton', 'Qwe!23');
        $sql = "
        select
            c.idx,
            c.category,
            CASE WHEN count(*) = 1
                    THEN 0
                    ELSE count(*)
            END
        from
            tb_platform_stat_request p
        right OUTER join
            tb_category c
        on
            p.category_id_list[1]=c.idx
        group by 1
        order by 3 desc, 1 asc";
        $category = $GLOBALS['postgre']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
        select 
            tb1.category_id_list[1],
            sum (CASE when tb2.recomend > 0  THEN 1  ELSE 0 end) as preview_recomend_widget,
            sum (tb2.recomend) as preview_recomend_anons,
            sum (CASE when tb2.native   ='t' THEN 1  ELSE 0 end) as preview_native_widget
        from
            tb_platform_stat_request tb1,
            tb_platform_stat_request tb2 
        where
            tb1.view_id=tb2.view_id and
            tb1.is_load_widget and
            tb2.is_show_preview 
        group by 1";
        $widget_pokaz = $GLOBALS['postgre']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
        select tb1.category_id_list[1], count(*) 
        from 
            tb_platform_stat_request tb1,
            tb_platform_stat_request tb2 
        where 
            tb1.view_id=tb2.view_id and
            tb1.is_load_widget and
            tb2.is_read_post and
            tb2.is_baned is null
        group by 1
        order by 1 asc";
        $promo_pokaz = $GLOBALS['postgre']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
        select 
            category_id_list[1], count(*)
        from
            tb_platform_stat_request 
        where
            is_load_widget and
            promo_id_list[1] is null
        group by 1
        order by 1";
        $not_pokaz = $GLOBALS['postgre']->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($widget_pokaz as $i) {
            $preview_widget_arr[$i['category_id_list']]=$i['preview_recomend_widget']+$i['preview_native_widget'];
            $preview_anons_arr[$i['category_id_list']]=$i['preview_recomend_anons']+$i['preview_native_widget'];
        };

        foreach ($not_pokaz as $i) {
            $not_pokaz_arr[$i['category_id_list']]=$i['count'];
        };
        foreach ($promo_pokaz as $i) {
            $promo_pokaz_arr[$i['category_id_list']]=$i['count'];
        };

        echo '
        <div class="bodys">
	  	  <div class="form-block w-form">
          <div class="w-form-done"></div>
          <div class="w-form-fail"></div>
        </div>
		<div class="table-box">
		<div class="div-block-102-table">
        <div class="table w-embed">';
              if (!isset($_GET['platform_id'])) {
                  echo '
                  <table>
                        <thead>
                          <tr class="trtop">
                            <th>Категория</th>
                            <th>Трафик</th>
                            <th>Показы виджетов</th>
                            <th>Не показы виджетов</th>
                            <th>Показы анонсов</th>
                            <th>Прочтений статей</th>
                          </tr>
                        </thead>
                        <tbody style="font-size: 15px;">';
                  foreach ($category as $i){
                      $i['category']=mb_convert_case(substr($i['category'],0,2), MB_CASE_TITLE, "UTF-8").substr($i['category'],2);

                      if (!isset($preview_widget_arr[$i['idx']])){$preview_widget_arr[$i['idx']]=0;};
                      if (!isset($not_pokaz_arr[$i['idx']])){$not_pokaz_arr[$i['idx']]=0;};
                      if (!isset($preview_anons_arr[$i['idx']])){$preview_anons_arr[$i['idx']]=0;};
                      if (!isset($promo_pokaz_arr[$i['idx']])){$promo_pokaz_arr[$i['idx']]=0;};

                      echo '
                         <tr>
                          <td>'.$i['idx'].'. '.lcfirst($i['category']).'</td>
                          <td>'.$i['count'].'</td>
                          <td>'.$preview_widget_arr[$i['idx']].'</td>
                          <td>'.$not_pokaz_arr[$i['idx']].'</td>
                          <td>'.$preview_anons_arr[$i['idx']].'</td>
                          <td>'.$promo_pokaz_arr[$i['idx']].'</td>
                         </tr>';
                         }
                        echo'
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
            <option value="all">Все площадки</option>';
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
		</div>';

        include PANELDIR.'/views/layouts/footer.php';
        return true;
    }
}