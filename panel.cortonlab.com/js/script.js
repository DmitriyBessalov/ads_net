$(document).ready(function(){
//Получение get параметров
    function $_GET(key) {
        var p = window.location.search;
        p = p.match(new RegExp(key + '=([^&=]+)'));
        return p ? p[1] : false;
    }

//Подгрузка подкатегорий при выборе категорий на странице редактирования площадок
    $("#categoriya").change(function(){
        selec();
    });
    selecswitch();
    function selecswitch() {
        if($("#podcategoriyaval").length) {
            var podcategor=document.getElementById('podcategoriyaval').innerHTML;
            if (podcategor!=""){
                selec();
                $('#podcategoriya option[value="'+podcategor+'"]').attr("selected", "selected");
            }
        }
    }
    function selec() {
        var categoriyaval = $("#categoriya option:selected").val();
        switch (categoriyaval){
            case 'Авто':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option> <option value="Общее">Общее</option><option value="Обзоры авто">Обзоры авто</option>';
                break;
            case 'Беременность и роды':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Дом и сад':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Домашние животные':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Женская общая':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Заработок и Финансы':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option><option value="Общее">Бизнес</option><option value="Общее">IT</option>';
                break;
            case 'Здоровье и медицина':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option><option value="Суставы">Суставы</option><option value="Диабет">Диабет</option><option value="Похудание">Похудание</option><option value="Кардио">Кардио</option>';
                break;
            case 'Фитнес и диеты':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Знаменитости':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Игры':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Искусство':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Книги и журналы':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Комиксы и анимация':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Красота':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Мобильные технологии':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Мужская':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Музыка':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Общее':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Покупки':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Психология/Отношения':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Развлекательный':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Рецепты':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Семья и воспитание':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Спорт':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option><option value="Общее">Футбол</option><option value="Общее">Хокей</option>';
                break;
            case 'Стиль и мода':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Строительство и ремонт':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Учеба':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Фильмы/Сериалы/Телешоу':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option>';
                break;
            case 'Хобби и интересы':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option><option value="Здоровое питание">Здоровое питание</option><option value="Рецепты">Рецепты</option><option value="Диеты">Диеты</option>';
                break;
            case 'Недвижимость':
                document.getElementById('podcategoriya').innerHTML='<option value="">Подкатегория площадки</option><option value="Общее">Общее</option><option value="Покупка квартир">Покупка квартир</option>';
        }
    }

//Формирует теги с ссылками для подключения площадки
    $('input[name="domen"]').change(function() {
        var  domen= $('input[name="domen"]').val();
        domen = domen.replace(/\./gi, '_');
        document.getElementById('fileadrres').innerHTML='&lt;link href="https://api.cortonlab.com/css/'+domen+'.css.gz" rel="stylesheet"&gt;<br><br>&lt;script async src="https://api.cortonlab.com/js/cortonlab.js.gz"&gt;&lt;/script&gt;';
    });
	
//Функция для чекбокса для всех вариантов статей
    $('.flipswitch.all').click(function() {
        if ($(this).is(':checked')){
            var id=$(this).parents('tr:first').children('td')[0].innerHTML;
            var checkbox=$(this);
            $.post("https://panel.cortonlab.com/article-start-all?id="+id,function(data) {
                switch (data) {
                    case 'anon':{checkbox.next().text('Неактивна, отсутсвуют анонсы'); checkbox.prop('checked', false); console.log('a'); break;}
                    case 'word':{checkbox.next().text('Неактивна, отсутсвуют ключевые слова'); checkbox.prop('checked', false); console.log('w'); break;}
                    case 'true':{checkbox.next().text('Активна'); break;}
                    default:    {checkbox.next().text('Активна'); alert('Внимание активированно '+data+' варианта статьи');}
                }
            });
        } else {
            $(this).next().text('На паузе');
            var id=$(this).parents('tr:first').children('td')[0].innerHTML;
            $.post("https://panel.cortonlab.com/article-stop-all?id="+id)
        }
    });

    //Функция для чекбокса для одного вариантов статей
    $('.flipswitch.one').click(function() {
        if ($(this).is(':checked')){
            var id=$(this).parents('tr:first').children('td')[0].innerHTML;
            var checkbox=$(this);
            $.post("https://panel.cortonlab.com/article-start?id="+id,function(data) {
                switch (data) {
                    case 'anon':{checkbox.next().text('Неактивна, отсутсвуют анонсы'); checkbox.prop('checked', false); console.log('a'); break;}
                    case 'word':{checkbox.next().text('Неактивна, отсутсвуют ключевые слова'); checkbox.prop('checked', false); console.log('w'); break;}
                    case 'true':{checkbox.next().text('Активна');}
                }
            });
        } else {
            $(this).next().text('На паузе');
            var id=$(this).parents('tr:first').children('td')[0].innerHTML;
            $.post("https://panel.cortonlab.com/article-stop?id="+id)
        }
    });

    //Функция для чекбокса для остановки показа анонсов
    $('.flipswitch.anons').click(function() {
        var id=$(this).parents('tr:first').children('td')[0].innerHTML;
        if ($(this).is(':checked')){
            $.post("https://panel.cortonlab.com/article-anons-start?id="+id);
        } else {
            $.post("https://panel.cortonlab.com/article-anons-stop?id="+id)
        }
    });


//Отрытие модальных окон на странице площадок
    $("#promo").click(function(){
        $('.modal.promo').css("display", "block");
        $('.black-fon').css("display", "block");
    });

    $("#recomend").click(function(){
        var variable = $('#recomend').html();
        if (variable=='Активировать'){
            var idploshadka= $('input[name=id]').val();
            $.get( "widget-aktiv?id="+idploshadka+"&widget=recomend");
            $("#recomend").html("Настройки");
        }else{
            $('.recomendation').css("display", "block");
            $('.black-fon').css("display", "block");
        }
    });

    $("#zagrecomend").click(function(){
        $('.modal.zagrecom').css("display", "block");
        $('.black-fon').css("display", "block");
    });

    $("#natprev").click(function(){
        var variable = $('#natprev').html();
        if (variable=='Активировать'){
            var idploshadka= $('input[name=id]').val();
            $.get( "widget-aktiv?id="+idploshadka+"&widget=natpre");
            $("#natprev").html("Настройки");
        }else{
            $('.modal.nativepreview').css("display", "block");
            $('.black-fon').css("display", "block");
        }
    });
    $("#zagnatprev").click(function(){
        $('.modal.zagnativepreview').css("display", "block");
        $('.black-fon').css("display", "block");
    });

    $("#natpro").click(function(){
        var variable = $('#natpro').html();
        if (variable=='Активировать'){
            var idploshadka= $('input[name=id]').val();
            $.get( "widget-aktiv?id="+idploshadka+"&widget=natpro");
            $("#natpro").html("Настройки");
        }else{
            $('.modal.nativepro').css("display", "block");
            $('.black-fon').css("display", "block");
        }
    });

    $("#zagnatpro").click(function(){
        $('.modal.zag-nativepro').css("display", "block");
        $('.black-fon').css("display", "block");
    });

    $("#slider").click(function(){
        var variable = $('#slider').html();
        if (variable=='Активировать'){
            var idploshadka= $('input[name=id]').val();
            $.get( "widget-aktiv?id="+idploshadka+"&widget=slider");
            $("#slider").html("Настройки");
        }else{
            $('.modal.slider').css("display", "block");
            $('.black-fon').css("display", "block");
        }
    });
//Скрытие модальных окон
    $(".modalhide").click(function(){
        $('.modal').css("display", "none");
        $('.black-fon').css("display", "none");
    });
//Открытие модальных окон на странице со статистикой площадок
    $(".modalclick").click(function(){
        $('.black-fon').css("display", "block");
        $('#modalotch'+this.id.substr(9)).css("display", "block");
    });

    $(".modalclickb").click(function(){
        $('.black-fon').css("display", "block");
        $('#spisanie'+this.id.substr(6)).css("display", "block");
    });
//Открытие модального окона в кабинете площадок при запросе вывода средств
    $("#vivod").click(function(){
        $('.modal').css("display", "block");
        $('.black-fon').css("display", "block");
    });

// При изменении виджета Promo
    $('.widget-promo input, .widget-promo select').change(function(){
        widget_promo();
    });
    widget_promo();

// Визуализация виджета Promo-статья
    function widget_promo(){

        var widgetfontunit=$('.widget-promo [name=widget-font-unit]').val();

        var style = "#corton-widget{width: 100%; padding:10px;} " +
            "#corton-promo .icon-partner{display: inline-block;}";

        if($('.widget-promo .icon').prop("checked")) style += "#corton-widget .icon-partner{display: block;}";

        var background_block = $('.widget-promo [name=widget-background-block]').val();
        if (background_block != "") style += "#corton-promo{background: #" + background_block + ";}";

        var width_block = $('.widget-promo [name=widget-width-block]').val();
        if (width_block != "") style += "#corton-promo{width: " + width_block + "%;}";

        style += "#corton-promo h1{font-weight: 400;}";
        
         style += "#corton-promo p{text-indent: 0px !important; margin-bottom: 20px !important;}";

        var h1_font = $('.widget-promo [name=widget-h1-font]').val();
        if (h1_font != "") style += "#corton-promo h1{font-family: " + h1_font + ";}";

        var h1_size = $('.widget-promo [name=widget-h1-size]').val();
        if (h1_size != ""){
            if (widgetfontunit=='px') {
                style += "#corton-promo h1{font-size: " + h1_size + "px;}";
            } else {
                style += "#corton-promo h1{font-size: " + h1_size/10 + "em;}";
            };
        };

        var h1_color = $('.widget-promo [name=widget-h1-color]').val();
        if (h1_color != "") style += "#corton-promo h1{color: #" + h1_color + ";}";

        var variable = $('.widget-promo [name=widget-h1-bold]').val();
        if (variable != "") style += "#corton-promo h1{font-weight: " + variable + ";}";

        if($('.widget-promo [name=widget-h1-italic]').prop("checked")) style += "#corton-promo h1{font-style: italic;}";

        if($('.widget-promo [name=widget-h1-underline]').prop("checked")) style += "#corton-promo h1{text-decoration: underline;}";

        var h2_color = $('.widget-promo [name=widget-h2-color]').val();
        if (h2_color != "") style += "#corton-promo h2{color: #" + h2_color + ";}";

        style += "#corton-promo h2{font-weight: 400;}";
        style += "#corton-promo img{max-width: 99%;}";

        var h2_font = $('.widget-promo [name=widget-h2-font]').val();
        if (h2_font != "") style += "#corton-promo h2{font-family: " + h2_font + ";}";

        var h2_size = $('.widget-promo [name=widget-h2-size]').val();
        if (h2_size != "") {
            if (widgetfontunit=='px') {
                style += "#corton-promo h2{font-size: " + h2_size + "px;}";
            } else {
                style += "#corton-promo h2{font-size: " + h2_size/10 + "em;}";
            };
        };

        var variable = $('.widget-promo [name=widget-h2-bold]').val();
        if (variable != "") style += "#corton-promo h2{font-weight: " + variable + ";}";

        if($('.widget-promo [name=widget-h2-italic]').prop("checked")) style += "#corton-promo h2{font-style: italic;}";

        if($('.widget-promo [name=widget-h2-underline]').prop("checked")) style += "#corton-promo h2{text-decoration: underline;}";

        var text_color = $('.widget-promo [name=widget-text-color]').val();
        if (text_color != "") style += "#corton-promo p{color: #" + text_color + ";}";

        var a_color = $('.widget-promo [name=widget-a-color]').val();
        if (a_color != "") style += "#corton-promo a{color: #" + a_color + ";}";

        var text_font = $('.widget-promo [name=widget-text-font]').val();
        if (text_font != "") style += "#corton-promo p{font-family: " + text_font + ";}";

        var text_size = $('.widget-promo [name=widget-text-size]').val();
        if (text_size != "") {
            if (widgetfontunit=='px') {
                style += "#corton-promo p{font-size: " + text_size + "px;}";
            } else {
                style += "#corton-promo p{font-size: " + text_size/10 + "em;}";
            };
        };

        var variable = $('.widget-promo [name=widget-text-bold]').val();
        if (variable != "") style += "#corton-promo p{font-weight: " + variable + ";}";

        var variable = $('.widget-promo [name=widget-type-interval-text]').val();
        if (variable != "") style += "#corton-promo p{line-height: " + variable + ";}";

        if($('.widget-promo [name=widget-text-italic]').prop("checked")) style += "#corton-promo p{font-style: italic;}";

        if($('.widget-promo [name=widget-text-underline]').prop("checked")) style += "#corton-promo p{text-decoration: underline;}";

        //форма
        style += ".promo-form {padding: 20px; margin-bottom: 30px; width: auto !important; margin-top: 20px; }";
        style += ".promo-form input.inputtext {padding: 7px; width: 30%; min-width: 160px; }";
        style += ".promo-form input.button {padding: 6px 20px 6px 20px; width: 30%; min-width: 160px; }";
        style += ".promo-form div.title {padding-bottom: 5px; margin-bottom: 0px !important; }";
        style += ".promo-form div.text {padding-bottom: 10px; }";
        
        style += " @media (max-width: 720px) and (min-width: 260px) { .promo-form input.inputtext{padding: 5px; margin-bottom: 10px !important; width: 100%; }}";
        style += " @media (max-width: 720px) and (min-width: 260px) { .promo-form input.button{padding: 6px 20px 6px 20px; width: 100%; }}";
        style += " @media (max-width: 992px) and (min-width: 770px) { .promo-form input.button{padding: 6px 20px 6px 20px; width: 30%; }}";
         
        var variable = $('.widget-promo [name=form-width]').val();
        if (variable != "") style += ".promo-form {width: " + variable + "%;}";

        var variable = $('.widget-promo [name=form-blok-aling]').val();
        if (variable == "center") style += ".promo-form {margin-left: auto;}"; style += ".promo-form {margin-right: auto;}";

        var variable = $('.widget-promo [name=form-in-blok-aling]').val();
        if (variable != "") style += ".promo-form {text-align: " + variable + ";}";

        var variable = $('.widget-promo [name=form-palitra-color]').val();
        if (variable != "") style += ".promo-form {background: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-border-width]').val();
        var variable2 = $('.widget-promo [name=form-palitra-border-color]').val();
        if(variable != "") style += ".promo-form{border: solid " + variable + "px #" + variable2 + ";}";

        var variable = $('.widget-promo [name=form-border-radius]').val();
        if(variable != "") style += ".promo-form{border-radius:" + variable + "px;}";

        var variable = $('.widget-promo [name=form-h2-font]').val();
        if(variable != "") style += ".promo-form div.title{font-family: " + variable + ";}";

        var variable = $('.widget-promo [name=form-h2-color]').val();
        if(variable != "") style += ".promo-form div.title{color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-h2-size]').val();
        if(variable != "") {
            if (widgetfontunit=='px') {
                style += ".promo-form div.title{font-size: " + variable + "px;}";
            } else {
                style += ".promo-form div.title{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-promo [name=form-input-baground-color]').val();
        if(variable != "") style += ".promo-form input.inputtext{background-color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-input-text-border-size]').val();
        var variable2 = $('.widget-promo [name=form-input-text-border-color]').val();
        if(variable != "") style += ".promo-form input.inputtext{border: solid " + variable + "px #" + variable2 + ";}";

        var variable = $('.widget-promo [name=form-input-text-border-radius]').val();
        if(variable != "") style += ".promo-form input.inputtext{border-radius:" + variable + "px;}";

        var variable = $('.widget-promo [name=form-input-text-font]').val();
        if(variable != "") style += ".promo-form input.inputtext{font-family: " + variable + ";}";

        var variable = $('.widget-promo [name=form-input-text-color]').val();
        if(variable != "") style += ".promo-form input.inputtext{color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-input-text-size]').val();
        if(variable != "") {
            if (widgetfontunit=='px') {
                style += ".promo-form input.inputtext{font-size: " + variable + "px;}";
            } else {
                style += ".promo-form input.inputtext{font-size: " + variable/10 + "em;}";
            };
        };

        style += ".promo-form input.inputtext{margin: 0px 2% 0px 0px;}";
        style += ".promo-form div.form{margin-top: 10px;}";

        var variable = $('.widget-promo [name=form-text-font]').val();
        if(variable != "") style += ".promo-form div.text{font-family: " + variable + ";}";

        var variable = $('.widget-promo [name=form-text-color]').val();
        if(variable != "") style += ".promo-form div.text{color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-text-size]').val();
        if(variable != "") {
            if (widgetfontunit=='px') {
                style += ".promo-form div.text{font-size: " + variable + "px;}";
            } else {
                style += ".promo-form div.text{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-promo [name=form-button-background-color]').val();
        if(variable != "") style += ".promo-form input.button{background-color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-button-border-size]').val();
        var variable2 = $('.widget-promo [name=form-button-border-color]').val();
        if(variable != "") style += ".promo-form input.button{border: solid " + variable + "px #" + variable2 + ";}";

        var variable = $('.widget-promo [name=form-button-radius]').val();
        if(variable != "") style += ".promo-form input.button{border-radius:" + variable + "px;}";

        var variable = $('.widget-promo [name=form-button-text-color]').val();
        if(variable != "") style += ".promo-form input.button{color: #" + variable + ";}";

        var variable = $('.widget-promo [name=form-button-text-font]').val();
        if(variable != "") style += ".promo-form input.button{font-family: " + variable + ";}";

        var variable = $('.widget-promo [name=form-button-text-size]').val();
        if(variable != "") style += ".promo-form input.button{font-size: " + variable + "px;}";

        var variable = $("#textarea-promo").val();
        if(variable != "") style += variable;

        var variable2 = $("#textarea-promo2").val();

        $('.promo-script-container').html('<style>' + style + '</style>');
        $('.widget-promo [name=css]').val(style);
        $('.widget-promo [name=dop-css]').val(variable);
        $('.widget-promo [name=adblock-css]').val(variable2);

    }


// При изменении виджета рекомендаций
    $('.widget-recomendation input, .widget-recomendation select').change(function(){
        widget_recomendation();
    });
    widget_recomendation();

// Визуализация виджета рекомендаций
    function widget_recomendation(){
        $('.holder').css("display", "none");
        var width = $('[name=widget-format]').val();
        var height = $('[name=widget-format-1]').val();
        var count = parseInt(width) * parseInt(height);

        $('.corton-recomendation-row').html('');
        var html = $('.holder').html();
        for (i=0; i<count; i++) {
            $(html).appendTo('.corton-recomendation-row')
        }

        var style = "#corton-recomendation-widget{--hsize: "+height+"; --wsize: "+width+";}";

        style += ".corton-recomendation-wrapper{display: inline-block; padding:0px;}";

        if ($('[name=widget-format]').val() == 2) style += ".corton-recomendation-section{width: 50%;}";
        if ($('[name=widget-format]').val() == 3) style += ".corton-recomendation-section{width: 33.33%;}";
        if ($('[name=widget-format]').val() == 4) style += ".corton-recomendation-section{width: 25%;}";
		

        style += " @media (max-width: 1980px) and (min-width: 640px) { .corton-recomendation-section img{width: 100%; margin: 0 10px 0 0; }}";
        style += " @media (max-width: 1980px) and (min-width: 640px) { .corton-recomendation-section p{margin-bottom: 0px; }}";
        
        style += " @media (max-width: 640px) and (min-width: 260px) { .corton-recomendation-section{display: inline-block; width: 100%; }}";
        style += " @media (max-width: 640px) and (min-width: 260px) { .corton-recomendation-section img{width: 35%;  float: left; margin: 0 10px 0 0 !important; float: left; }}";
        style += " @media (max-width: 640px) and (min-width: 260px) { .corton-recomendation-section p{margin-bottom: 0px; }}";

        style += ".corton-recomendation-row{display: flex; flex-wrap: wrap; border-spacing: 5px; }";
        style += ".corton-recomendation-section{display: block; padding: 0px;}";
        style += ".corton-recomendation-section img{width: 100%; height: auto; margin-bottom:5px;}";

        var widgetfontunit=$('.widget-recomendation [name=widget-font-unit]').val();

        var bg1 = $('.widget-recomendation [name=widget-background-block]').val();
        if (bg1 != "") style += ".corton-recomendation-wrapper{background: #" + bg1 + ";}";

        var border_type = $('.widget-recomendation [name=widget-border-type]').val();
        var border_width = $('.widget-recomendation [name=widget-border-width]').val();
        var border_color = $('.widget-recomendation [name=widget-border-color]').val();
        if (border_width != "") 
        style += ".corton-recomendation-wrapper{border: "+border_width+"px #"+border_color+" " + border_type + ";}\n";

        var text_title = $('.widget-recomendation [name=widget-text-title]').val();
        $('#corton-recomendation-widget .corton-title').text(text_title);

        if(text_title != "") style += "#corton-recomendation-widget .corton-title{padding:10px 0 5px 8px;}";

        var font_title = $('.widget-recomendation [name=widget-font-title]').val();
        if(font_title != "") style += "#corton-recomendation-widget .corton-title{font-family: " + font_title + ";}";

        var size_title = $('.widget-recomendation [name=widget-size-title]').val();
        if(size_title != "") {
            if (widgetfontunit=='px') {
                style += "#corton-recomendation-widget .corton-title{font-size: " + size_title + "px;}";
            } else {
                style += "#corton-recomendation-widget .corton-title{font-size: " + size_title/10 + "em;}";
            };
        };

        var color_title = $('.widget-recomendation [name=widget-color-title]').val();
        if(color_title != "") style += "#corton-recomendation-widget .corton-title{color: #" + color_title + ";}";

        var bold = $('.widget-recomendation [name=widget-type-bold-title]').val();
        if(bold != "") style += "#corton-recomendation-widget .corton-title{font-weight: " + bold + ";}";

        if($('.widget-recomendation [name=widget-type-italic-title]').prop("checked")) style += "#corton-recomendation-widget .corton-title{font-style: italic;}";

        if($('.widget-recomendation [name=widget-type-underline-title]').prop("checked")) style += "#corton-recomendation-widget .corton-title{text-decoration: underline;}";

        style += ".corton-recomendation-section a{display: block; box-sizing: border-box; width: 100%; padding: 10px; text-decoration: none;}";

        var background_tizer = $('.widget-recomendation [name=widget-background-tizer]').val();
        if(background_tizer != "") style += ".corton-recomendation-section a{background: #" + background_tizer + ";}";

        var color_text = $('.widget-recomendation [name=widget-color-text]').val();
        if(color_text != "") style += ".corton-recomendation-section a{color: #" + color_text + ";}";

        var font_text = $('.widget-recomendation [name=widget-font-text]').val();
        if(font_text != "") style += ".corton-recomendation-section a{font-family: " + font_text + ";}";

        var size_text = $('.widget-recomendation [name=widget-size-text]').val();
        if(size_text != "") {
            if (widgetfontunit=='px') {
                style += ".corton-recomendation-section a{font-size: " + size_text + "px;}";
            } else {
                style += ".corton-recomendation-section a{font-size: " + size_text/10 + "em;}";
            };
        };

        var size_text = $('.widget-recomendation [name=widget-type-bold-text]').val();
        if(size_text != "") style += ".corton-recomendation-section a{font-weight: " + size_text + ";}";

        var size_text = $('.widget-recomendation [name=widget-type-interval-text]').val();
        if(size_text != "") style += ".corton-recomendation-section a p{line-height: " + size_text + "; text-indent: 0px !important;}";

        var size_text = $('.widget-recomendation [name=widget-type-align-text]').val();
        if(size_text != "") style += ".corton-recomendation-section p{text-align: " + size_text + ";}";

        if($('.widget-recomendation [name=widget-type-italic-text]').prop("checked")) style += ".corton-recomendation-section a{font-style: italic;}";

        if($('.widget-recomendation [name=widget-type-underline-text]').prop("checked")) style += ".corton-recomendation-section a{text-decoration: underline;}";

        var variable = $("#textarea-recomendation").val();
        if(variable != "") style += variable;

        $('.widget-recomendation [name=dop-css]').val(variable);
        $('.widget-recomendation [name=css]').val(style);

        $('.recomendation-script-container').html('<style>' + style + '</style>');

        var variable = $('.widget-recomendation [name=image-shape]').val();
        let n = $('.recomendationimg').length;
        for (i = 0; i < n; i++) {
            if (variable == 3){
                $('.recomendationimg')[i].style.width= "270px";
                $('.recomendationimg')[i].style.height= "180px";
            }else {
                $('.recomendationimg')[i].style.width= "180px";
                $('.recomendationimg')[i].style.height= "180px";
            }
        }
    }

// При изменении виджета nativepre
    $('.widget-nativepre input, .widget-nativepre select').change(function(){
        widget_nativepre();
    });
    widget_nativepre();
// Визуализация виджета nativepre-статья
    function widget_nativepre(){
        var style = "#corton-nativepreview-widget{width: 100%; padding: 18px 0 18px 0; display: table !important; box-sizing: border-box; margin-bottom: 20px; margin-top: 20px;}\n";

        style += "#corton-nativepreview-widget .corton-left{display: table-cell !important; vertical-align: middle; width: 290px;}\n";

        style += "#corton-nativepreview-widget .corton-left img{width: 100%; max-width: 290px; vertical-align:top; object-fit: cover;}\n";

        style += "#corton-nativepreview-widget .corton-right{display: table-cell !important; vertical-align: middle; padding-left: 15px;}\n";
        style += "#corton-nativepreview-widget .corton-title{width: 100%; padding-bottom: 10px !important; font-weight: 400;}\n";

        style += "#corton-nativepreview-widget .corton-link{display: inline-block !important; padding: 2px 10px !important;}\n";

        style += " @media (max-width: 600px) and (min-width: 200px) { #corton-nativepreview-widget .corton-left{ float: left; }}";
        style += " @media (max-width: 600px) and (min-width: 200px) { #corton-nativepreview-widget .corton-right{ padding-left: 0px; padding-top: 10px; }}";
		style += " @media (max-width: 640px) and (min-width: 600px) { #corton-nativepreview-widget .corton-left{ width: 230px; }}";
        style += " @media (max-width: 600px) and (min-width: 200px) { #corton-nativepreview-widget .corton-left{ width: 100%; }}";
		style += " @media (max-width: 440px) and (min-width: 360px) { #corton-nativepreview-widget .corton-left img{ width: 100%; max-width: 440px; height: 168px; object-fit: cover; }}";
        style += " @media (max-width: 360px) and (min-width: 200px) { #corton-nativepreview-widget .corton-left img{ width: 100%; max-width: 360px; }}";
        var widgetfontunit=$('.widget-nativepre [name=widget-font-unit]').val();

        var variable = $('.widget-nativepre  [name=widget-background-block]').val();
        if (variable != "") style += "#corton-nativepreview-widget{background: #" + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-width-block]').val();
        if ((variable != "")&& (variable!=0))style += "#corton-nativepreview-widget{width: " + variable + "%;}\n";

        var variable = $('.widget-nativepre  [name=widget-border-type]').val();
        var variable2 = $('.widget-nativepre  [name=widget-border-width]').val();
        var variable3 = $('.widget-nativepre  [name=widget-border-color]').val();
        if (variable == "left") style += "#corton-nativepreview-widget{border-left: "+variable2+"px #" + variable3 + " " + variable + ";}\n";
        else if (variable == "right") style += "#corton-nativepreview-widget{border-right: "+variable2+"px #" + variable3 + " " + variable + ";}\n";
        else if (variable != "") style += "#corton-nativepreview-widget{border-top: "+variable2+"px #" + variable3 + " " + variable + "; border-bottom: "+variable2+"px #" + variable3 + " " + variable + "; }\n";

        var variable = $('.widget-nativepre  [name=widget-font-title]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-title{font-family: " + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-size-title]').val();
        if (variable != "") {
            if (widgetfontunit=='px') {
                style += "#corton-nativepreview-widget .corton-title{font-size: " + variable + "px;}";
            } else {
                style += "#corton-nativepreview-widget .corton-title{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-nativepre  [name=widget-color-title]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-title{color: #" + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-type-bold-title]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-title{font-weight: " + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-type-interval-title]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-title{line-height: " + variable + ";}\n";

        if ($('.widget-nativepre  [name=widget-type-italic-title]').prop("checked")) style += "#corton-nativepreview-widget .corton-title{font-style: italic;}\n";

        if ($('.widget-nativepre  [name=widget-type-underline-title]').prop("checked")) style += "#corton-nativepreview-widget .corton-title{text-decoration: underline;}\n";

        var variable = $('.widget-nativepre  [name=widget-font-text]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-content{font-family: " + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-size-text]').val();
        if (variable != "") {
            if (widgetfontunit=='px') {
                style += "#corton-nativepreview-widget .corton-content{font-size: " + variable + "px;}";
            } else {
                style += "#corton-nativepreview-widget .corton-content{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-nativepre  [name=widget-color-text]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-content{color: #" + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-type-bold-text]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-content{font-weight: " + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=widget-type-interval-text]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-content{line-height: " + variable + ";}\n";

        if ($('.widget-nativepre  [name=widget-type-italic-text]').prop("checked")) style += "#corton-nativepreview-widget .corton-content{font-style: italic;}\n";

        if ($('.widget-nativepre  [name=widget-type-underline-text]').prop("checked")) style += "#corton-nativepreview-widget .corton-content{text-decoration: underline;}\n";

        var variable = $('.widget-nativepre  [name=button-background-color]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-link{background: #" + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=button-text-color]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-link{color: #" + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=button-font]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-link{font-family: " + variable + ";}\n";

        var variable = $('.widget-nativepre  [name=button-font-size]').val();
        if (variable != "") {
            if (widgetfontunit=='px') {
                style += "#corton-nativepreview-widget .corton-link{font-size: " + variable + "px;}";
            } else {
                style += "#corton-nativepreview-widget .corton-link{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-nativepre  [name=button-type-bold]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-link{font-weight: " + variable + ";}\n";

        if ($('.widget-nativepre  [name=button-type-italic]').prop("checked")) style += "#corton-nativepreview-widget .corton-link{font-style: italic;}\n";

        if ($('.widget-nativepre  [name=button-type-underline]').prop("checked")) style += "#corton-nativepreview-widget .corton-link{text-decoration: underline;}\n";

        var variable = $('.widget-nativepre  [name=button-border-width]').val();
        var variable2 = $('.widget-nativepre  [name=button-border-color]').val();
        var variable3 = $('.widget-nativepre  [name=button-border-type]').val();
        if (variable != "") style += "#corton-nativepreview-widget .corton-link{border: " + variable + "px #" + variable2 + " " + variable3 + ";}\n";

        var text_title = $('.widget-nativepre [name=button-text]').val();
        $('#corton-nativepreview-widget .corton-link').text(text_title);

        var variable = $("#textarea-nativepreview").val();
        if(variable != "") {
            style += variable;
            $('.widget-nativepre [name=dop-css]').val(variable);
        }

        $('.widget-nativepre [name=css]').val(style);
        $('.nativepre-script-container').html('<style>' + style + '</style>');

        var variable = $('.widget-nativepre [name=image-shape]').val();
        if (variable == 3){
            $('.natpreimg')[0].style.width= "270px";
            $('.corton-left')[0].style.width= "270px";
        }
        if (variable == 4){
            $('.natpreimg')[0].style.width= "180px";
            $('.corton-left')[0].style.width= "180px";
        }
    }

// При изменении виджета slider
    $('.widget-slider input, .widget-slider select').change(function(){
        widget_slider();
    });
    widget_slider();
// Визуализация виджета slider
    function widget_slider(){
        var style = "#corton-slider-widget{width: 360px; height: 110px; display: table; position: relative; box-sizing: border-box; bottom: 0px; right: 0px; z-index: 99999;}\n";
        style += ".close-widget{display: block; position: absolute; top:0px; right: 0px; width: 15px; height: 15px;}";

        style += ".close-widget:after{content: ''; position: absolute; left: -4px; bottom: -6px; border: 7px solid transparent;  border-top: 7px solid #cccccc; cursor: pointer;}";
        style += ".widget-slider .corton-left{display: table-cell; vertical-align: middle; width: 110px; height: 110px;}\n";
        style += ".widget-slider .corton-left img{width: 110px; height: 110px; float: left;}\n";
        style += ".widget-slider .corton-right{display: table-cell; vertical-align: middle; padding: 0 10px 10px 10px;}\n";
        style += ".widget-slider .corton-title{width: 100%; margin-top: 5px;}\n";
        style += ".widget-slider .corton-sign{font-size: 12px;}\n";

        style += " @media (max-width: 460px) and (min-width: 220px) { #corton-slider-widget{ width: 100% !important; height: 95px !important; }}";
        style += " @media (max-width: 460px) and (min-width: 220px) { .widget-slider .corton-left img{ width: 95px; height: 95px; }}";

        var widgetfontunit=$('.widget-slider [name=widget-font-unit]').val();

        var variable = $('.widget-slider  [name=widget-background-block]').val();
        if (variable != "") style += "#corton-slider-widget{background: #" + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-border-type]').val();
        if (variable != "") style += "#corton-slider-widget{border: 1px #ccc " + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-border-width]').val();
        if (variable != "") style += "#corton-slider-widget{border-width: " + variable + "px;}\n";

        var variable = $('.widget-slider  [name=widget-border-color]').val();
        if (variable != "") style += "#corton-slider-widget{border-color: #" + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-font-sign]').val();
        if (variable != "") style += ".widget-slider  .corton-sign{font-family: " + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-size-sign]').val();
        if (variable != "") {
            if (widgetfontunit=='px') {
                style += ".widget-slider  .corton-sign{font-size: " + variable + "px;}";
            } else {
                style += ".widget-slider  .corton-sign{font-size: " + variable/10 + "em;}";
            };
        };

        var variable = $('.widget-slider  [name=widget-color-sign]').val();
        if (variable != "") style += ".widget-slider  .corton-sign{color: #" + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-font-title]').val();
        if (variable != "") style += ".widget-slider  .corton-title{font-family: " + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-size-title]').val();
        if (variable != "") {
            if (widgetfontunit=='px') {
                style += ".widget-slider .corton-title{font-size: " + variable + "px;}";
            } else {
                style += ".widget-slider .corton-title{font-size: " + variable/10 + "em;}";
            }
        }

        var variable = $('.widget-slider  [name=widget-color-title]').val();
        if (variable != "") style += ".widget-slider .corton-title{color: #" + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-type-bold-title]').val();
        if (variable != "") style += ".widget-slider .corton-title{font-weight: " + variable + ";}\n";

        var variable = $('.widget-slider  [name=widget-type-interval-title]').val();
        if (variable != "") style += ".widget-slider  .corton-title{line-height: " + variable + ";}\n";

        if ($('.widget-slider [name=widget-type-italic-title]').prop("checked")) style += ".widget-slider  .corton-title{font-style: italic;}\n";

        if ($('.widget-slider [name=widget-type-underline-title]').prop("checked")) style += ".widget-slider  .corton-title{text-decoration: underline;}\n";

        var variable = $("#textarea-slider").val();
        if(variable != "") {
            style += variable;
            $('.widget-slider [name=dop-css]').val(variable);
        }
        $('.widget-slider [name=css]').val(style);
        $('.slider-script-container').html('<style>' + style + '</style>');
    }

    //переключение табов при редактирование статьи
    $( "#tab1" ).on( "click", function() {
        $(".w-tab-pane").removeClass("w--tab-active");
        $(".w-tab-link").removeClass("w--current");
        $("#tab1").addClass("w--current");
        $("#tab1block").addClass("w--tab-active");
    });
    $( "#tab2" ).on( "click", function() {
        $(".w-tab-pane").removeClass("w--tab-active");
        $(".w-tab-link").removeClass("w--current");
        $("#tab2").addClass("w--current");
        $("#tab2block").addClass("w--tab-active");
    });
    $( "#tab3" ).on( "click", function() {
        $(".w-tab-pane").removeClass("w--tab-active");
        $(".w-tab-link").removeClass("w--current");
        $("#tab3").addClass("w--current");
        $("#tab3block").addClass("w--tab-active");
        let toolbar=document.querySelector('div.ql-toolbar.ql-snow');
        if (toolbar)  toolbar.style.position = 'relative';
    });
    $( "#tab4" ).on( "click", function() {
        $(".w-tab-pane").removeClass("w--tab-active");
        $(".w-tab-link").removeClass("w--current");
        $("#tab4").addClass("w--current");
        $("#tab4block").addClass("w--tab-active");
    });

    //Подключение слов в форму ключи
    function words() {
        let variable=$('.div-block-84.word').text();
        variable=variable.replace(/Удалить/g,',');
        variable=variable.replace(/ /g,'');
        variable=variable.replace(/\n/g,'');
        variable=variable.slice(0, -1);
        $('[name=words]').val(variable);
    }
    words();
    //Добавление ключевых слов при редактировании статьи
    $( ".text-block-141" ).on( "click", function() {
        var variable = $('#addkey-2').val();
        if (variable.length > 3) {
            $('.div-block-84.word').append('<div class="div-block-86"><div class="text-block-114">' + variable + '</div><div class="text-block-98">Удалить</div></div>');
            $('#addkey-2').val("");
            words();
        }else{
            alert('Введите ключевое слово длинее 4 символов');
        }
    });
    //Удаление ключевых слов при редактировании статьи
    $(document).on('click','.text-block-98',function(){
        $(this).parent('div').toggle('slow');
        var $this=this;
        setTimeout(function (){
            $($this).parent('div').remove();
            words();geo();
        }, 300);
    });

    //Добавление формы анонса
    $('#addanons').click(function() {
        $('#anonses').append(
            '<div class="div-block-97-copy">' +
                '<input type="hidden" name="anons_ids[]" value="new">' +
                '<div class="div-block-142">' +
                    '<div class="div-block-145">' +
                        '<input type="text" class="text-field-6 _1 w-input" maxlength="55" name="title[]" placeholder="Заголовок анонса статьи до 55 символов" id="title-3" required="">' +
                        '<textarea name="opisanie[]" placeholder="Описание от 90 до 130 символов" maxlength="130" class="textarea-7 w-input"></textarea>' +
                    '</div>' +
                '</div>' +
                '<div class="div-block-142">' +
                    '<div class="div-block-148">' +
                        '<div class="image-preview">' +
                            '<label for="image-upload290" class="image-label">Загрузить изображение 290x180px</label>' +
                            '<input type="file" name="image290[]" class="image-upload290" accept=".png,.jpeg,.jpg,.gif" required="" />' +
                        '</div>' +
                    '</div>' +
                    '<div class="div-block-147"></div>' +
                    '<div class="div-block-148">' +
                        '<div class="image-preview _180">' +
                            '<label for="image-upload180" class="image-label">Загрузить изображение 180x180px</label>' +
                            '<input type="file" name="image180[]" class="image-upload180"  accept=".png,.jpeg,.jpg,.gif" required="" />' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<a class="button-10 w-button delanons">Удалить анонс</a>' +
            '</div>'
        );
    });
    //Удаление формы анонса
    $(document).on('click','.delanons',function(){
        $(this).parent('div').toggle('slow');
        $this=this;
        setTimeout(function (){
             $($this).parent('div').remove();
        }, 400);
    });
    //Подгрузка картинки в форму 290x180px
    $(document).on('change','.image-upload290',function(){
        var files = this.files;
        var file = files[0];
        var reader = new FileReader();
        var diver=this;
        reader.addEventListener("load",function(event) {
            var loadedFile = event.target;
            if (file.type.match('image')) {
                // Image
                $(diver).parent('div').css("background-image", "url("+loadedFile.result+")");
                $(diver).parent('div').css("background-position", "center center");
                $(diver).parent('div').css("background-repeat", "no-repeat");
                $(diver).parent('div').css("background-size", "cover");
                $(diver).parent('div').children('label').css("background-color", "#e1e2e8");
                $(diver).parent('div').children('label').html("Обновить изображение 290x180px");
            } else {
                alert("This file type is not supported yet.");
            }
        });
        reader.readAsDataURL(file);
    });
    //Подгрузка картинки в форму 180x180px
    $(document).on('change','.image-upload180',function(){
        var files = this.files;
        var file = files[0];
        var reader = new FileReader();
        var diver=this;
        reader.addEventListener("load",function(event) {
            var loadedFile = event.target;
             if (file.type.match('image')) {
                $(diver).parent('div').css("background-image", "url("+loadedFile.result+")");
                $(diver).parent('div').css("background-position", "center center");
                $(diver).parent('div').css("background-repeat", "no-repeat");
                $(diver).parent('div').css("background-size", "cover");
                $(diver).parent('div').children('label').css("background-color", "#e1e2e8");
                $(diver).parent('div').children('label').html("Обновить изображение 180x180px");
            } else {
                alert("This file type is not supported yet.");
            }
        });
        reader.readAsDataURL(file);
    });

    //Добавление стилей
    $('#textarea-promo').keyup(function(){widget_promo();});
    $('#textarea-promo2').keyup(function(){widget_promo();});
    $('#textarea-recomendation').keyup(function(){widget_recomendation();});
    $('#textarea-nativepreview').keyup(function(){widget_nativepre();});
    $('#textarea-slider').keyup(function(){widget_slider();});

    //Переключатель смены алгоритма вывода виджетов Recomendation и Natpre
    function radioch(widget){
        var v=$(widget+'input[name=algorithm-output]:checked').val();
        switch(v)
        {
            case '1':
                $(widget+'input[name=widget-parent-id]').prop('disabled', true);
                $(widget+'input[name=widget-position-p]').prop('disabled', true);
                break;
            case '0':
                $(widget+'input[name=widget-parent-id]').prop('disabled', false);
                $(widget+'input[name=widget-position-p]').prop('disabled', false);
                break;
            default:
                $(widget+'input[name=widget-parent-id]').prop('disabled', false);
                $(widget+'input[name=widget-position-p]').prop('disabled', true);
        }
    }
    $('.widget-recomendation  input[name=algorithm-output]').click( function() {
        radioch('.widget-recomendation ');
    });
    $('.widget-nativepre  input[name=algorithm-output]').click( function() {
        radioch('.widget-nativepre ');
    });
    radioch('.widget-recomendation ');
    radioch('.widget-nativepre ');

    //События radiobutton в правом меню панели с отправкой формы
    $('label').click(function(){
        $(this).find(':radio').attr('checked','checked');
        $("#right-form").submit();
    });

    //Вывод балансов
    $('#button_vivod').click(function(){
        var variable = $('[name=summa]').val();

        //alert(variable);
        $.post("https://panel.cortonlab.com/finance-vivod?summa="+variable,function(data) {
            switch (data) {
                case 'summa':{$('#status_vivod').text('Неправильная сумма к выводу'); break;}
                case 'date':{$('#status_vivod').text('Ошибка запрашивать вывод можно, не чаше 1 раза в месяц'); break;}
                case 'true':{$('#status_vivod').text('Запрос принят');}
            }
        });
    });

    //Скрытие панели левого меню
    $('#panel_hide').click(function(){
        var str = $(this).text();
        if (str==='<'){
            $(this).text('>');
            document.getElementsByClassName('left-menu')[0].style.display = 'none';
            document.getElementsByClassName('div-block-88')[0].style.marginLeft = '0px';
            document.getElementById('panel_hide').style.marginLeft = '0px';
        } else {
            $(this).text('<');
            document.getElementsByClassName('left-menu')[0].style.display = 'block';
            document.getElementsByClassName('div-block-88')[0].style.marginLeft = '246px';
            document.getElementById('panel_hide').style.marginLeft = '246px';
        }
    });

    //Выпадающий список при поиске региона
    let countries=["Россия","Армения","Азербайджан","Белоруссия","Грузия","Казахстан","Латвия","Литва","Монголия","Норвегия","Польша","Украина","Финляндия","Швеция","Эстония","Арагацотнская область, Армения","Араратская область, Армения","Армавирская область, Армения","г. Ереван, Армения","Гехаркуникская область, Армения","Котайкская область, Армения","Лорийская область, Армения","Ширакская область, Армения","Сюникская область, Армения","Тавушская область, Армения","Вайоцдзорская область, Армения","Абшеронский район, Азербайджан","Акстафинский район, Азербайджан","Агджабединский район, Азербайджан","Агдамский район, Азербайджан","Агдашский район, Азербайджан","Ахсуйский район, Азербайджан","Астаринский район, Азербайджан","Бейлаганский район, Азербайджан","Бардинский район, Азербайджан","Белоканский район, Азербайджан","Билясуварский район, Азербайджан","Джебраильский район, Азербайджан","Джалилабадский район, Азербайджан","Дашкесанский район, Азербайджан","Физулинский район, Азербайджан","Кедабекский район, Азербайджан","Геранбойский район, Азербайджан","Геокчайский район, Азербайджан","Гёйгёльский район, Азербайджан","Аджикабулский район, Азербайджан","Имишлинский район, Азербайджан","Исмаиллинский район, Азербайджан","Кельбаджарский район, Азербайджан","Кюрдамирский район, Азербайджан","Лачинский район, Азербайджан","Ленкоранский район, Азербайджан","Лерикский район, Азербайджан","Масаллинский район, Азербайджан","Нефтечалинский район, Азербайджан","Огузский район, Азербайджан","Габалинский район, Азербайджан","Кахский район, Азербайджан","Казахский район, Азербайджан","Губинский район, Азербайджан","Кубатлинский район, Азербайджан","Гобустанский район, Азербайджан","Кусарский район, Азербайджан","Сабирабадский район, Азербайджан","Шекинский район, Азербайджан","Сальянский район, Азербайджан","Саатлинский район, Азербайджан","Шабранский район, Азербайджан","Сиазанский район, Азербайджан","Шамкирский район, Азербайджан","Шемахинский район, Азербайджан","Самухский район, Азербайджан","Шушинский район, Азербайджан","Тертерский район, Азербайджан","Товузский район, Азербайджан","Уджарский район, Азербайджан","Хачмазский район, Азербайджан","Ходжалинский район, Азербайджан","Хызынский район, Азербайджан","Ходжаведский район, Азербайджан","Ярдымлинский район, Азербайджан","Евлахский район, Азербайджан","Зангиланский район, Азербайджан","Закаталинский район, Азербайджан","Зардабский район, Азербайджан","Брестская область, Белорусия","г. Минск, Белорусия","Гомельская область, Белорусия","Гродненская область, Белорусия","Могилёвская область, Белорусия","Минская область, Белорусия","Витебская область, Белорусия","Харьюмаа уезд, Эстония","Хийумаа уезд, Эстония","Ида-Вирумаа уезд, Эстония","Йыгевамаа уезд, Эстония","Ярвамаа уезд, Эстония","Ляэнемаа уезд, Эстония","Ляэне-Вирумаа уезд, Эстония","Пылвамаа уезд, Эстония","Пярнумаа уезд, Эстония","Рапламаа уезд, Эстония","Сааремаа уезд, Эстония","Тартумаа уезд, Эстония","Валгамаа уезд, Эстония","Вильяндимаа уезд, Эстония","Вырумаа уезд, Эстония","Аландские острова область, Финляндия","Южная Карелия область, Финляндия","Южная Остроботния область, Финляндия","Южное Саво область, Финляндия","Кайнуу область, Финляндия","Канта-Хяме область, Финляндия","Центральная Остроботния область, Финляндия","Центральная Финляндия область, Финляндия","Кюменлааксо область, Финляндия","Лапландия область, Финляндия","Пирканмаа область, Финляндия","Похьянмаа область, Финляндия","Северная Карелия область, Финляндия","Северная Похьянмаа область, Финляндия","Северное Саво область, Финляндия","Пяйят-Хяме область, Финляндия","Сатакунта область, Финляндия","Уусимаа область, Финляндия","Исконная Финляндия область, Финляндия","Абхазская автономная республика, Грузия","Аджария автономная республика, Грузия","Гурия край, Грузия","Имеретия край, Грузия","Кахетия край, Грузия","Квемо-Картли край, Грузия","Мцхета-Мтианети край, Грузия","Рача-Лечхуми и Квемо-Сванети край, Грузия","Самцхе-Джавахетия край, Грузия","Шида-Картли край, Грузия","Самегрело и Земо-Сванети край, Грузия","г. Тбилиси, Грузия","Акмолинская область, Казахстан","Актюбинская область, Казахстан","г. Алма-Ата, Казахстан","Алматинская область, Казахстан","г. Астана, Казахстан","Атырауская область, Казахстан","г. Байконур, Казахстан","Карагандинская область, Казахстан","Костанайская область, Казахстан","Кызылординская область, Казахстан","Мангистауская область, Казахстан","Павлодарская область, Казахстан","Северо-Казахстанская область, Казахстан","г. Шымкент, Казахстан","Восточно-Казахстанская область, Казахстан","Туркестанская область, Казахстан","Западно-Казахстанская область, Казахстан","Жамбылская область, Казахстан","Алитусский уезд, Литва","Клайпедский уезд, Литва","Каунасский уезд, Литва","Мариямпольский уезд, Литва","Паневежский уезд, Литва","Шяуляйский уезд, Литва","Таурагский уезд, Литва","Тельшяйский уезд, Литва","Утенский уезд, Литва","Вильнюсский уезд, Литва","Аглонский край, Латвия","Айзкраукльский край, Латвия","Айзпутский край, Латвия","Акнистский край, Латвия","Алойский край, Латвия","Алсунгский край, Латвия","Алуксненский край, Латвия","Аматский край, Латвия","Апский край, Латвия","Ауцский край, Латвия","Адажский край, Латвия","Бабитский край, Латвия","Балдонский край, Латвия","Балтинавский край, Латвия","Балвский край, Латвия","Бауский край, Латвия","Беверинский край, Латвия","Броценский край, Латвия","Буртниекский край, Латвия","Царникавский край, Латвия","Цесвайнский край, Латвия","Цесисский край, Латвия","Циблский край, Латвия","Дагдский край, Латвия","Даугавпилсский край, Латвия","Добельский край, Латвия","Дундагский край, Латвия","Дурбский край, Латвия","Энгурский край, Латвия","Эргльский край, Латвия","Гаркалнский край, Латвия","Гробинский край, Латвия","Гулбенский край, Латвия","Иецавский край, Латвия","Икшкильский край, Латвия","Илукстский край, Латвия","Инчукалнсский край, Латвия","Яунелгавский край, Латвия","Яунпиебалгский край, Латвия","Яунпилсский край, Латвия","Елгавский край край, Латвия","Екабпилсский край, Латвия","Кандавский край, Латвия","Карсавский край, Латвия","Коценский край, Латвия","Кокнесский край, Латвия","Краславский край, Латвия","Кримулдский край, Латвия","Крустпилсский край, Латвия","Кулдигский край, Латвия","Кегумский край, Латвия","Кекавский край, Латвия","Лиелвардский край, Латвия","Лимбажский край, Латвия","Лигатненский край, Латвия","Ливанский край, Латвия","Лубанский край, Латвия","Лудзенский край, Латвия","Мадонский край, Латвия","Мазсалацский край, Латвия","Малпилсский край, Латвия","Марупский край, Латвия","Мерсрагский край, Латвия","Наукшенский край, Латвия","Неретский край, Латвия","Ницский край, Латвия","Огрский край, Латвия","Олайнский край, Латвия","Озолниекский край, Латвия","Паргауйский край, Латвия","Павилостский край, Латвия","Плявинский край, Латвия","Прейльский край, Латвия","Приекульский край, Латвия","Приекульский край, Латвия","Раунский край, Латвия","Резекненский край, Латвия","Риебинский край, Латвия","Ройский край, Латвия","Ропажский край, Латвия","Руцавский край, Латвия","Ругайский край, Латвия","Рундальский край, Латвия","Руйиенский край, Латвия","Салский край, Латвия","Салацгривский край, Латвия","Саласпилсский край, Латвия","Салдусскийs край, Латвия","Саулкрастский край, Латвия","Сейский край, Латвия","Сигулдский край, Латвия","Скриверский край, Латвия","Скрундский край, Латвия","Смилтенский край, Латвия","Стопинский край, Латвия","Стренчский край, Латвия","Талсинский край, Латвия","Терветский край, Латвия","Тукумский край, Латвия","Вайнёдский край, Латвия","Валкский край, Латвия","Вараклянский край, Латвия","Варкавский край, Латвия","Вецпиебалгский край, Латвия","Вецумниекский край, Латвия","Вентспилсский край, Латвия","Виеситский край, Латвия","Вилякский край, Латвия","Вилянский край, Латвия","Зилупский край, Латвия","г. Даугавпилс, Латвия","г. Елгава, Латвия","г. Екабпилс, Латвия","г. Юрмала, Латвия","г. Лиепая, Латвия","г. Резекне, Латвия","г. Рига, Латвия","г. Вентспилс, Латвия","г. Валмиера, Латвия","Орхон аймак, Монголия","Дархан-Уул аймак, Монголия","Хэнтий аймак, Монголия","Хувсгел аймак, Монголия","Ховд аймак, Монголия","Увс аймак, Монголия","Туве аймак, Монголия","Сэлэнгэ аймак, Монголия","Сухэ-Батор аймак, Монголия","Умнеговь аймак, Монголия","Уверхангай аймак, Монголия","Завхан аймак, Монголия","Дундговь аймак, Монголия","Дорнод аймак, Монголия","Дорноговь аймак, Монголия","Говь-Сумбэр аймак, Монголия","Говь-Алтай аймак, Монголия","Булган аймак, Монголия","Баянхонгор аймак, Монголия","Баян-Улгий аймак, Монголия","Архангай аймак, Монголия","г. Улан-Батор, Монголия","Эстфолл фюльке, Норвегия","Акерсхус фюльке, Норвегия","Осло фюльке, Норвегия","Хедмарк фюльке, Норвегия","Оппланн фюльке, Норвегия","Бускеруд фюльке, Норвегия","Вестфолл фюльке, Норвегия","Телемарк фюльке, Норвегия","Эуст-Агдер фюльке, Норвегия","Вест-Агдер фюльке, Норвегия","Ругаланн фюльке, Норвегия","Хордаланн фюльке, Норвегия","Согн-ог-Фьюране фюльке, Норвегия","Мёре-ог-Ромсдал фюльке, Норвегия","Сёр-Трёнделаг фюльке, Норвегия","Нур-Трёнделаг фюльке, Норвегия","Нурланн фюльке, Норвегия","Тромс фюльке, Норвегия","Финнмарк фюльке, Норвегия","Шпицберген архипелаг, Норвегия","Ян-Майен остров, Норвегия","Нижнесилезское воеводство, Польша","Куявско-Поморское воеводство, Польша","Любушское воеводство, Польша","Лодзинское воеводство, Польша","Люблинское воеводство, Польша","Малопольское воеводство, Польша","Мазовецкое воеводство, Польша","Опольское воеводство, Польша","Подляское воеводство, Польша","Подкарпатское воеводство, Польша","Поморское воеводство, Польша","Свентокшиское воеводство, Польша","Силезское воеводство, Польша","Варминьско-Мазурское воеводство, Польша","Великопольское воеводство, Польша","Западно-Поморское воеводство, Польша","Адыгея республика, Россия","Республика Алтай, Россия","Амурская область, Россия","Архангельская область, Россия","Астраханская область, Россия","Башкортостан республика, Россия","Белгородская область, Россия","Брянская область, Россия","Бурятия республика, Россия","Чечня республика, Россия","Челябинская область, Россия","Чукотский автономный округ, Россия","Крым, Россия","Чувашия республика, Россия","Дагестан республика, Россия","Ингушетия республика, Россия","Иркутская область, Россия","Ивановская область, Россия","Камчатский край, Россия","Кабардино-Балкария республика, Россия","Карачаево-Черкесия республика, Россия","Краснодарский край, Россия","Кемеровская область, Россия","Калининградская область, Россия","Курганская область, Россия","Хабаровский край, Россия","Ханты-Мансийский автономный округ — Югра, Россия","Кировская область, Россия","Хакасия республика, Россия","Калмыкия республика, Россия","Калужская область, Россия","Республика Коми, Россия","Костромская область, Россия","Карелия республика, Россия","Курская область, Россия","Красноярский край, Россия","Ленинградская область, Россия","Липецкая область, Россия","Магаданская область, Россия","Марий Эл республика, Россия","Мордовия республика, Россия","Московская область, Россия","г. Москва, Россия","Мурманская область, Россия","Ненецкий автономный округ, Россия","Новгородская область, Россия","Нижегородская область, Россия","Новосибирская область, Россия","Омская область, Россия","Оренбургская область, Россия","Орловская область, Россия","Пермский край, Россия","Пензенская область, Россия","Приморский край, Россия","Псковская область, Россия","Ростовская область, Россия","Рязанская область, Россия","Республика Саха, Россия","Сахалинская область, Россия","Самарская область, Россия","Саратовская область, Россия","Северная Осетия республика, Россия","Смоленская область, Россия","г. Санкт-Петербург, Россия","Ставропольский край, Россия","Свердловская область, Россия","Татарстан республика, Россия","Тамбовская область, Россия","Томская область, Россия","Тульская область, Россия","Тверская область, Россия","Тыва республика, Россия","Тюменская область, Россия","Удмуртия республика, Россия","Ульяновская область, Россия","Волгоградская область, Россия","Владимирская область, Россия","Вологодская область, Россия","Воронежская область, Россия","Ямало-Ненецкий автономный округ, Россия","Ярославская область, Россия","Еврейская автономная область, Россия","Забайкальский край, Россия","Стокгольм лен, Швеция","Вестерботтен лен, Швеция","Норрботтен лен, Швеция","Уппсала лен, Швеция","Сёдерманланд лен, Швеция","Эстергётланд лен, Швеция","Йёнчёпинг лен, Швеция","Крунуберг лен, Швеция","Кальмар лен, Швеция","Готланд лен, Швеция","Блекинге лен, Швеция","Сконе лен, Швеция","Халланд лен, Швеция","Вестра-Гёталанд лен, Швеция","Вермланд лен, Швеция","Эребру лен, Швеция","Вестманланд лен, Швеция","Даларна лен, Швеция","Евлеборг лен, Швеция","Вестерноррланд лен, Швеция","Емтланд лен, Швеция","Винницкая область, Украина","Волынская область, Украина","Луганская область, Украина","Днепропетровская область, Украина","Донецкая область, Украина","Житомирская область, Украина","Закарпатская область, Украина","Запорожская область, Украина","Ивано-Франковская область, Украина","г. Киев, Украина","Киевская область, Украина","Кировоградская область, Украина","г. Севастополь, Украина","Львовская область, Украина","Николаевская область, Украина","Одесская область, Украина","Полтавская область, Украина","Ровненская область, Украина","Сумская область, Украина","Тернопольская область, Украина","Харьковская область, Украина","Херсонская область, Украина","Хмельницкая область, Украина","Черкасская область, Украина","Черниговская область, Украина","Черновицкая область, Украина"];
    $('input[name=searchgeo]').keyup(function(){
        const value=$(this).val().toLowerCase();
        let str='';
        let count=0;
        if (value.length){

            countries.forEach(function(item, index) {
                if (count<15){
                    let itm=item.toLowerCase();
                    if(itm.indexOf(value) + 1) {
                        str=str+'<li>'+item+'</li>';
                        count++;
                    };
                }
            });

            $('#geolist').html('<ul id="geo_list">'+str+'</ul>');
            $('#geolist').show();
        }else {
            $('#geolist').html('');
            $('#geolist').hide();
        }
    });

    //Подключение слов в форму регионы
    /*function geo() {
        let variable=$('.div-block-84.geo').text();
        variable=variable.replace(/Удалить/g,',');
        variable=variable.replace(/ /g,'_');
        variable=variable.replace(/\n/g,'');
        variable=variable.slice(0, -1);
        $('[name=geo]').val(variable);
    }
    geo();

    //Добавление в список регионов
    $(document).on('click','li',function(){
        let region=$(this).children('a')[0].text;
        region=region.substr(0,region.length - 9)
        $('.div-block-84.geo').append('<div class="div-block-86"><div class="text-block-114">' + region + '</div><div class="text-block-98">Удалить</div></div>');
        geo();
        $('.typeahead__list').remove();
        $('.js-typeahead').val('');
    });*/

    //Создание статьи на основе текущей
    $('#add_variat_promo').click(function(){
        let variant = document.getElementsByClassName('aticlevariant');
        id=$_GET('id');
        switch (variant.length) {
            case 1:{
                $.post("https://panel.cortonlab.com/article-clone?id="+id, function(data) {
                    $('#add_variat_promo').before("<a href='https://panel.cortonlab.com/article-edit-content?id="+ data +"' class=\"btnarticlegr aticlevariant\" style=\"width: 120px;float:left;margin-right: 12px;\">Вариант B</a>");
                });
                break;
            }
            case 2:{
                $.post("https://panel.cortonlab.com/article-clone?id="+id, function(data) {
                    $('#add_variat_promo').before("<a href='https://panel.cortonlab.com/article-edit-content?id="+ data +"' class=\"btnarticlegr aticlevariant\" style=\"width: 120px;float:left;margin-right: 12px;\">Вариант C</a>");
                });
                break;
            }
            case 3:{
                $.post("https://panel.cortonlab.com/article-clone?id="+id, function(data) {
                    $('#add_variat_promo').before("<a href='https://panel.cortonlab.com/article-edit-content?id="+ data +"' class=\"btnarticlegr aticlevariant\" style=\"width: 120px;float:left;margin-right: 12px;\">Вариант D</a>");
                    $('#add_variat_promo').remove();
                });
            }
        }
    });
});

//Блок с информацией о обновлениях для площадок
$(function(){
    function get_cookie(cookie_name){
        var results = document.cookie.match ('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
        if (results)
            return (unescape (results[2]));
        else
            return null;
    }

    function check_cookie(){
        var textCookie = get_cookie('messagetext'),
            currentText = $('.message-box p').text();
        if (textCookie == currentText) {
            $('.message-box').hide(0);
        }
    }
    check_cookie();
    
    $('.close-button').click(function(){
        var currentText = $('.message-box p').text(),
        date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
		document.cookie = "messagetext="+currentText+"; expires="+date.toGMTString()+"; path=/";
        $('.message-box').fadeOut(200);
    });
})

/* Выпадающая кнопка для админки */
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}