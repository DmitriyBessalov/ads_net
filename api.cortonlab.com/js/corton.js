//Защита от повторного запуска скрипта
if (corton_complete!=1) {
    var corton_complete = 1;
    //Получение get параметров
    var get = (function() {
        var a = window.location.search;
        var b = new Object();
        a = a.substring(1).split("&");
        for (var i = 0; i < a.length; i++) {
            c = a[i].split("=");
            b[c[0]] = c[1];
        }
        return b;
    })();

    //Обработка промо статьи
    function corton_promo() {
        if (window.location.hostname === 'demo.cortonlab.com'){
            console.log('corton: '+corton_url);
            const promo_forcibly = style_b.getPropertyValue('--forcibly');
            const promo_selector = style_b.getPropertyValue('--selector');
            if (promo_forcibly==='1'){
                let sel = document.querySelectorAll(promo_selector);
                sel[0].innerHTML=
                '<div id="corton-promo"></div>';
            }
            console.log('corton: '+promo_forcibly,promo_selector);
        }

        var widget_promo=document.getElementById("corton-promo");

        adblock();
        var h3=0;
        var request ='https://api2.cortonlab.com/promo.php'+location.search;
        let result=[];
        console.log('corton:'+request);
        var xhr = new XMLHttpRequest();
        xhr.open('GET', request,true);
        xhr.withCredentials = true;
        xhr.send();
        xhr.onreadystatechange = function() {
            if (xhr.readyState != 4) {return}
            if (xhr.status === 200) {
                result = JSON.parse(xhr.responseText);
                console.log('corton: ajax:', result);
                if (!result) return false;
            }
            document.title = result['title'];

            var form='';
            if ((result['form_title']!='') && (result['form_text']!='')){
                form=
                    '<div class="promo-form">'+
                    '<div class="title">'+result['form_title']+'</div>'+
                    '<div class="text">'+result['form_text']+'</div>'+
                    '<div class="form">'+
                    '<div id="corton-form" class="form">'+
                    '<input class="inputtext" maxlength="256" name="name" placeholder="Ваше Имя" required="">'+
                    '<input class="inputtext" maxlength="256" name="phone" placeholder="Телефон" required="">'+
                    '<input type="submit" value="'+result['form_button']+'" onclick="cortonform();" class="button">'+
                    '<div id="corton_form_status"></div>'+
                    '</div>'+
                    '</div>'+
                    '</div>';
            }

            var style_p = window.getComputedStyle(widget_promo, null);
            let marker_reklamiy = style_p.getPropertyValue('--marker_reklamiy');

            if (marker_reklamiy==='1'){
                marker_reklamiy='<p style="line-height: 1.3; color: #999; font-size: 12px;">На правах рекламы </p>';
            }else{
                marker_reklamiy='';
            }

            var logo='<div style="height: 40px;display:flex;flex-direction:row-reverse;flex-wrap: wrap-reverse;justify-content: space-between;align-items: stretch;align-content: stretch;">'+
                     '<a href="https://cortonlab.com/" title="Powered by Corton" style="height:40px;max-width: 110px;">' +
                        '<img style="all:unset;width:80px; height:20px; border: none;" src="https://cortonlab.com/images/cortonlogo.png" />' +
                     '</a>' +
                     marker_reklamiy +
                '</div>';

            const promo_selector_title = style_b.getPropertyValue('--selectortitle');

            var promo= '<div id="corton_promo_content">'+result['text']+form+logo+'</div></div>' +
                '<div id="corton_scroll_to_site"></div>' +
                '<div id="corton_gradient_conteyner">'+
                    '<div id="corton_gradient"></div>'+
                '</div>'+
                '<div id="corton_image_layer">' +
                    '<img id="corton_image_fon_mobile">' +
                '</div>' +
                '<div id="corton_osvetlenie">';

            if (promo_selector_title.length!=0){
                let ele = document.querySelectorAll(promo_selector_title);
                ele[0].innerHTML=result['title'];
                widget_promo.innerHTML = '<div id="corton_fon">'+promo+'</div>';
            }else{
                widget_promo.innerHTML ='<div id="corton_fon"><h1>'+result['title']+'</h1>'+promo+'</div>';
            }

            const scroll2site_activ = style_p.getPropertyValue('--scroll2site_activ');
            if ((result['scroll2site']==1) && (scroll2site_activ==1))
            {
                const regex = /^(https?:\/\/)?(.*?)($|[/?])/;
                let host = regex.exec(result['scroll2site_url']);

                var scroll_to_site=document.getElementById("corton_scroll_to_site");
                scroll_to_site.innerHTML= ''+
                '<div id="corton_sticky_container">'+
                    '<div id="corton_overlay"></div>'+
                    '<div id="corton_border_title"><div>' + result['scroll2site_text'] + '&nbsp;<a href="'+result['scroll2site_url']+'">'+host[2]+'</a></div></div>'+
                    '<div id="corton_browser_container">'+
                         '<a href="'+result['scroll2site_url']+'" rel="noopener nofollow">'+
                            '<div id="corton_header">'+
                                '<div id="corton_favicon" style="background-image: url(\'http://favicon.yandex.net/favicon/'+host[2]+'\');"></div>'+
                                '<div id="corton_link">'+host[2]+'</div>'+
                            '</div>'+
                            '<img id="corton_image_fon">'+
                            '</div>'+
                         '</a>'+
                    '</div>'+
                '</div>'+

                '<style type="text/css">'+
                    '#corton_osvetlenie{position:fixed;top:0;left:0;right:0;bottom:0;z-index:2147483647;background-color:#FFF;opacity:0;pointer-events:none;transition:opacity 1s ease-in;} '+
                    '#corton_border_title{background-color:#fff8d9;max-height:82px;height:82px;display: flex; justify-content: center; align-items: center;} '+
                    'jdiv,footer{display: none !important;} '+
                    '#corton-promo img {max-width:100% !important;box-shadow:unset !important;border:unset !important;} '+
                    '#corton_border_title p {text-align: center;} '+
                    '@media (max-width: 1024px) { '+
                        '#corton_image_layer{position:sticky;bottom:0;z-index:2147483646;background-color:#000;} '+
                        '#corton_image_fon_mobile{width: 100%; max-width: 100% !important;opacity:.95;position: relative;top:0;border:none; margin: 0px;} '+
                        '#corton_gradient_conteyner{height:2000px;position: relative;z-index: 2147483647;} '+
                        '#corton_gradient{width:100%;height:100px;background-image:linear-gradient(rgba(0,0,0,0.1),rgba(255,0,0,0));} '+
                        '#corton_scroll_to_site {width: 100%;z-index:2147483647;position:relative;} '+
                        '#corton_scroll_to_site a{text-decoration:none;} '+
                        '#corton_browser_container{display:none;} '+
                        '#corton-promo{background-color:#FFFFFF;min-height:100vh;max-width: unset;position: relative;} '+
                        '#corton_fon{position: relative;z-index:2147483647;;background-color: #FFF;padding: 0 20px;} '+
                    '} '+
                    '@media (min-width: 1025px) { '+
                        '#corton_scroll_to_site{min-height: 160vh;position:absolute;left:0;background-color:#fff8d9;width:100%;transition:1s;z-index:2147483647;} '+
                        '#corton_scroll_to_site a{text-decoration:none;} '+
                        '#corton_sticky_container{position:sticky;top:0;} '+
                        '#corton_border_title{font-size:18px} '+
                        '#corton_overlay{position:absolute;top:-100vh;left:0;width:100%;height:100vh;background:#000;opacity:0;will-change: opacity;pointer-events:none;} '+
                        '#corton_browser_container{position: absolute;width:100%;height:100vh;transform-origin:center top;border-top-left-radius:24px;border-top-right-radius:24px;will-change:transform;box-shadow: 0 0 60px rgba(0,0,0,.3);background-color:#fff;overflow:hidden;} '+
                        '#corton_header{height:45px;background-color: #fcfcfc;position:relative;display:-webkit-box;display:flex;-webkit-box-pack:center;justify-content:center;-webkit-box-align:center;align-items:center;font-weight:500;border-bottom: 1px solid #ddd;flex-shrink:0;} '+
                        '#corton_link{margin-left:9px;font-size: 18px;} '+
                        '#corton_image_fon{width:100%;border:none; margin: 0px;} '+
                        '#corton_favicon{width:16px;height: 16px;margin-top:3px;} '+
                        '#corton-promo{min-height:100vh;} '+
                    '}'+
                '</style>';

                var corton_promo=document.getElementById("corton-promo"),
                    overlay=document.getElementById("corton_overlay"),
                    browser_container=document.getElementById("corton_browser_container"),
                    image_fon=document.getElementById("corton_image_fon"),
                    image_fon_mobile=document.getElementById("corton_image_fon_mobile"),
                    image_layer = document.getElementById("corton_image_layer"),
                    gradient_conteyner = document.getElementById("corton_gradient_conteyner"),
                    osvetlenie = document.getElementById("corton_osvetlenie"),
                    page_ready=0;

                function osvetlenie_redirekt() {
                    osvetlenie.style.opacity='1';
                    if (page_ready==1){
                        setTimeout(function() {
                            let href=encodeURIComponent(result['scroll2site_url']);
                            console.log('corton: https://stat.cortonlab.com/promo.php?prosmort_id=' + get['prosmort_id'] + '&host=' + location.hostname + '&a=c&anons_id=' + get['anons_id'] + '&t=' + get['t'] + '&p_id=' + result['id'] + '&href=' + href);
                            cxhr.open('GET', 'https://stat.cortonlab.com/promo.php?prosmort_id=' + get['prosmort_id'] + '&host=' + location.hostname + '&a=c&anons_id=' + get['anons_id'] + '&t=' + get['t'] + '&p_id=' + result['id']  + '&href=' + href);
                            cxhr.withCredentials = true;
                            cxhr.send();

                            if (result['scroll2site_url'].indexOf("?") > -1) {
                                var char = '&'
                            } else {
                                var char = '?'
                            }
                            document.location.href = result['scroll2site_url'] + char + 'sub_id1=-1&utm_source=corton&utm_medium=CPG&utm_campaign=' + result['id'] + '&utm_content=' + get['anons_id'] + '&utm_term=' + get['p_id'];
                        }, 300);
                    }
                    page_ready=2;
                }

                setTimeout(function() {
                    document.body.scrollTo(0, 0);
                }, 400);

                function s2s_position() {
                    if (page_ready == 0) {
                        document.body.scrollTo(0, 0);
                        return true;
                    }

                    if (outerWidth <= 1024) {
                        image_layer.style.bottom = outerHeight - image_fon_mobile.scrollHeight + 'px';

                        if (outerHeight / 8 > gradient_conteyner.getBoundingClientRect().top) {
                            osvetlenie_redirekt();
                        }
                    } else {
                        var stisky_top = corton_promo.getBoundingClientRect().top + corton_promo.scrollHeight;
                        var i = (window.innerHeight - stisky_top) * 100 / innerHeight;
                        if (i < 30) {
                            overlay.style.opacity = 0;
                            n = 600 / innerWidth;
                            browser_container.style.transform = 'scale(' + n + ', ' + n + ') translateY(0)';
                        } else {
                            e = (i - 30) * (1 / 70);
                            if (e > 1) e = 1;
                            overlay.style.opacity = 0.6 * e;
                            if (i <= 100) {
                                var n = 600 / innerWidth,
                                    mashtab = (n + e * (1 - n)),
                                    smeshenieY = innerHeight * 0.3 * e;
                                if (stisky_top < 41) {
                                    smeshenieY = smeshenieY + stisky_top - 41;
                                }
                                browser_container.style.transform = 'scale(' + mashtab + ', ' + mashtab + ') translateY(' + smeshenieY + 'px)';
                            } else {
                                let smeshenieY = innerHeight * 0.3 - (i - 100) * 0.01 * innerHeight - 41;
                                if (smeshenieY < -82) {
                                    smeshenieY = -82
                                }
                                ;
                                browser_container.style.transform = 'scale(1, 1) translateY(' + smeshenieY + 'px)';
                                if (innerHeight / 10 > browser_container.getBoundingClientRect().top) {
                                    osvetlenie_redirekt();
                                }
                            }
                        }
                    }
                }

                window.addEventListener("scroll", s2s_position);

                if (outerWidth<=1024) {
                    corton_promo.style.left = -gradient_conteyner.getBoundingClientRect().left + 'px';
                    corton_promo.style.width = outerWidth + 'px';
                }

                setTimeout(function() {
                    if (outerWidth<=1024){
                        image_fon_mobile.src="https://api.cortonlab.com/img/advertiser_screenshot_site/"+result['scroll2site_img_mobile'];
                    }else{
                        image_fon.src="https://api.cortonlab.com/img/advertiser_screenshot_site/"+result['scroll2site_img_desktop'];
                        scroll_to_site.style.left = -scroll_to_site.getBoundingClientRect().left + 'px';
                        scroll_to_site.style.width = document.documentElement.clientWidth + 'px';
                    }
                    page_ready=1;
                    s2s_position();
                }, 1000);
            }

            var promo_form=document.getElementById("corton-form");
            if (promo_form) {
                h3=promo_form.scrollHeight;
            };

            //Скрытие referrer при переходе со статьи
            var meta = document.querySelectorAll('meta[name=referrer]');
            if (meta.length!=0){
                meta[0].remove();
            }else{
                var meta = document.createElement('meta');
                meta.name = "referrer";
                meta.content = "no-referrer";
                document.getElementsByTagName('head')[0].appendChild(meta);
            }

            //Клик по ссылке в промо статье
            var a = document.querySelectorAll('div#corton-promo a');
            for(var i=0; i<a.length; i++) {
                if (a[i].getAttribute('href')!=="https://cortonlab.com/") {
                    if (a[i].getAttribute('href').indexOf("?") > -1) {
                        var char = '&'
                    } else {
                        var char = '?'
                    }

                    a[i].rel="noreferrer";
                    let link='';
                    if(a[i].classList.contains('scroll2site')){
                        link=result['scroll2site_url'] + char + 'sub_id1=-1&utm_source=corton&utm_medium=CPG&utm_campaign=' + result['id'] + '&utm_content=' + get['anons_id'] + '&utm_term=' + get['p_id'];
                    }else{
                        link=a[i].getAttribute('href') + char + 'sub_id1=' + i + '&utm_source=corton&utm_medium=CPG&utm_campaign=' + result['id'] + '&utm_content=' + get['anons_id'] + '&utm_term=' + get['p_id'];
                    }
                    console.log('corton: '+link);
                    a[i].setAttribute('href', link);

                    //console.log('promolink', a[i].getAttribute('href'));

                    a[i].onclick = function (e) {
                        let href=encodeURIComponent(this.href);
                        console.log('corton: https://stat.cortonlab.com/promo.php?prosmort_id=' + get['prosmort_id'] + '&host=' + location.hostname + '&ancor=' + this.outerText + '&a=c&anons_id=' + get['anons_id'] + '&t=' + get['t'] + '&p_id=' + result['id'] +'&href=' + href );
                        cxhr.open('GET', 'https://stat.cortonlab.com/promo.php?prosmort_id=' + get['prosmort_id'] + '&host=' + location.hostname + '&ancor=' + this.outerText + '&a=c&anons_id=' + get['anons_id'] + '&t=' + get['t'] + '&p_id=' + result['id'] +'&href=' + href );
                        cxhr.withCredentials = true;
                        cxhr.send();
                    }
                }
            }

            //Отправка статистики что промо страница загружена
            if (widget_promo.scrollHeight>50)
            {
                var cxhr = new XMLHttpRequest();
                console.log     ('corton: https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&a=l&anons_id='+get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']+'&ref='+document.referrer);
                cxhr.open('GET', 'https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&a=l&anons_id='+get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']+'&ref='+document.referrer);
                cxhr.withCredentials = true;
                cxhr.send();
            }
        };

        //Таймер когда активна вкладка
        var i=0;
        var scroll=false;
        var scrollfull=false;
        var st=true;
        var timer35=false;
        var timer85=false;
        var scrollh=document.body.scrollHeight/20;
        window.onblur = function () {i=-1;};
        window.onfocus = function () {i=0;};

        function lett() {
            window.scrollTo(0, 0);
            scroll=false;
        }
        setTimeout(lett, 300);
        var p=0;
        function letsGo(){
            if (timer85 && scrollfull){
                var cxhr = new XMLHttpRequest();
                console.log     ('corton: https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&host='+location.hostname+'&a=r&anons_id=' + get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']);
                cxhr.open('GET', 'https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&host='+location.hostname+'&a=r&anons_id=' + get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']);
                cxhr.withCredentials = true;
                cxhr.send();
            }else{
                if (timer35 && scroll && st){
                    var cxhr = new XMLHttpRequest();
                    console.log     ('corton: https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&host='+location.hostname+'&a=s&anons_id=' + get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']);
                    cxhr.open('GET', 'https://stat.cortonlab.com/promo.php?prosmort_id='+get['prosmort_id']+'&host='+location.hostname+'&a=s&anons_id=' + get['anons_id']+'&t='+get['t'] + '&p_id=' + result['id']);
                    cxhr.withCredentials = true;
                    cxhr.send();
                    st=false;
                }
                setTimeout(letsGo,1000);
            };
            if (i>=15)timer35=true;
            if (i>=20)timer85=true;
            if (scrollh<=pageYOffset) scroll=true;
            var promo_form=document.getElementsByClassName("promo-form");
            if (promo_form[0]) {
                if  (promo_form[0].getBoundingClientRect().top - window.innerHeight<=0) {
                    if (i>2) scrollfull=true;
                }
            }else{
                if (widget_promo.getBoundingClientRect().top+widget_promo.scrollHeight<=window.innerHeight){
                    if (i>2) scrollfull=true;
                }
            }
            if (i!=-1 && widget_promo.scrollHeight>50) {
                i++;
            };
        }
        letsGo();
    }

    //Обработка виджетов
    function corton_widget() {
        var show_recomend=0;
        var show_natpre=0;
//        var show_slider=0;
        var gadget='';
        var count=0;
        var result;
        var widget_load_status=0;
        var widget = '';
        var first_widget_check=true;
        var buttontext;
        var titletext;
        var w = 0;
        var wait=0;
        var anons_ids= [];
        var recomend_image_shape;
        var natpre_image_shape;

        const corton_body2=document.getElementsByTagName("body");

        const recomend_algorithm_output = style_b.getPropertyValue('--recomend-algorithm-output');
        const natpre_algorithm_output = style_b.getPropertyValue('--natpre-algorithm-output');
//        const slider_algorithm_output = style_b.getPropertyValue('--slider-algorithm-output');

        //Определение устройства пользователя
        function Device() {
            const device = {};
            const userAgent = window.navigator.userAgent.toLowerCase();
            device.iphone = function() {return !device.windows() && find('iphone')};
            device.ipod = function() {return find('ipod')};
            device.ipad = function() {return find('ipad')};
            device.android = function() {return !device.windows() && find('android')};
            device.androidPhone = function() {return device.android() && find('mobile')};
            device.androidTablet = function() {return device.android() && !find('mobile')};
            device.blackberry = function() {return find('blackberry') || find('bb10') || find('rim')};
            device.blackberryPhone = function() {return device.blackberry() && !find('tablet')};
            device.blackberryTablet = function() {return device.blackberry() && find('tablet')};
            device.windows = function() {return find('windows')};
            device.windowsPhone = function() {return device.windows() && find('phone')};
            device.windowsTablet = function() {return device.windows() && (find('touch') && !device.windowsPhone())};
            device.fxos = function() {return (find('(mobile') || find('(tablet')) && find(' rv:')};
            device.fxosPhone = function() {return device.fxos() && find('mobile')};
            device.fxosTablet = function() {return device.fxos() && find('tablet')};
            device.meego = function() {return find('meego')};
            device.mobile = function() {return (device.androidPhone()||device.iphone()||device.ipod()||device.windowsPhone()||device.blackberryPhone()||device.fxosPhone()||device.meego())};
            device.tablet = function() {return (device.ipad()||device.androidTablet()||device.blackberryTablet()||device.windowsTablet()||device.fxosTablet())};
            device.desktop = function() {return !device.tablet() && !device.mobile()};
            function find(needle) {return userAgent.indexOf(needle) !== -1}
            function findMatch(arr) {for (let d = 0; d < arr.length; d++) {if (device[arr[d]]()) {return arr[d]}}return 'unknown'}
            device.type = findMatch(['mobile', 'tablet', 'desktop']);
            if (device.type==='desktop'){
                if(document.body.clientWidth<992){device.type='tablet'}
                if(document.body.clientWidth<480){device.type='mobile'}
            }
            return device.type;
        }
        var device=Device();

        function zaglushka(tizer) {
            console.log('zag_'+tizer);
            var elem = document.getElementById('corton-'+tizer+'-widget',);
            elem.outerHTML='<iframe width="100%" height="300px" frameborder="0" scrolling="no" id="'+tizer+'-iframe" src="//'+location.hostname+'/corton_stub_'+tizer+'.html" /></iframe>';
        }

        function words() {
            // Слова исключения в этой строке
            var exclude = {'автор':'','боле':'','будет':'','был':'','важны':'','вед':'','вес':'','влияет':'','вообщ':'','врем':'','всемирн':'','всех':'','встретит':'','дал':'',
                'действительн':'','делат':'','дел':'','ден':'','друг':'', 'есл':'','ест':'','здес':'','имеет':'','именн':'','ин':'','когд':'','комментар':'','конгресс':'','контакт':'',
                'котор':'','куд':'','лиш':'','любят':'','мен':'','мн':'','может':'','можн':'','над':'','наш':'','недел':'','некотор':'', 'никак':'','нужн':'','очен':'','перед':'',
                'поиск':'','портфоли':'','посл':'','поэт':'','привет':'','работ':'','ранн':'','реклам':'','рубрик':'','сво':'','сентябр':'','следует':'','такж':'','так':'','течен':'',
                'тип':'', 'т':'','тож':'','тольк':'','туд':'','час':'','част':'','чащ':'','чтоб':'','эт':'','этот':''};

            let top10=[];
            let val;
            function splitword(wordsss){
                let arr = wordsss.toLowerCase().match(/[а-яё]{4,}/g);
                if (arr !== null)
                for (val of arr){
                    const lastchar= val.slice(-1);
                    switch (lastchar) {
                        case 'я':val = val.replace(/ья$|яя$|ая$|ия$|я$/, "");break;
                        case 'е':val = val.replace(/ое$|ее$|ие$|ые$|е$/, "");break;
                        case 'а':val = val.replace(/а$/, "");break;
                        case 'и':val = val.replace(/иями$|ями$|ьми$|еми$|ами$|ии$|и$/, "");break;
                        case 'ь':val = val.replace(/ь$/, "");break;
                        case 'о':val = val.replace(/его$|ого$|о$/, "");break;
                        case 'й':val = val.replace(/ий$|ей$|ый$|ой$|й$/, "");break;
                        case 'м':val = val.replace(/иям$|им$|ем$|ом$|ям$|ам$/, "");break;
                        case 'ы':val = val.replace(/ы$/, "");break;
                        case 'ю':val = val.replace(/ию$|ью$|ею$|ою$|ю$/, "");break;
                        case 'х':val = val.replace(/иях$|ях$|их$|ах$/, "");break;
                        case 'в':val = val.replace(/ев$|ов$/, "");break;
                        case 'у':val = val.replace(/у$/, "");
                    }
                    if (top10[val]){
                        top10[val]++;
                    }else{
                        top10[val]=1;
                    }
                }
            }

            splitword(document.title);
            const head  = document.head.querySelector("meta[name='description']"); if (head) {splitword(head.content);}
            const p  = document.getElementsByTagName("p" );for (const val of p ){splitword(val.innerText);}
            const h1 = document.getElementsByTagName("h1");for (const val of h1){splitword(val.innerText);}
            const h2 = document.getElementsByTagName("h2");for (const val of h2){splitword(val.innerText);}
            const h3 = document.getElementsByTagName("h3");for (const val of h3){splitword(val.innerText);}
            const h4 = document.getElementsByTagName("h4");for (const val of h4){splitword(val.innerText);}

            //console.log(top10);

            top10 = Object.keys(top10).sort(function (a, b){return top10[b] - top10[a]});
                top10 = top10.slice(0, 15);
            let top15 = top10.slice(0, 15);

            for (val of top15){
                if (val in exclude){
                    top10.splice(top10.indexOf(val), 1);
                }
            }

            top10 = top10.slice(0, 10);
            //console.log(top10);

            return top10;
        }

        if (location.hostname==='demo.cortonlab.com') {
            if (recomend_algorithm_output==='0'||recomend_algorithm_output==='3'||recomend_algorithm_output==='4'||recomend_algorithm_output==='5'){
                let div = document.createElement('div');
                div.id = 'corton-recomendation-widget';
                document.body.appendChild(div);
            }
            if (natpre_algorithm_output==='1'||natpre_algorithm_output==='3'||natpre_algorithm_output==='4'||natpre_algorithm_output==='5'){
                let div = document.createElement('div');
                div.id = 'corton-nativepreview-widget';
                document.body.appendChild(div);
            }
        }

        function widget_check() {
//            console.log('corton: widget_check');

            //Подготовка к получению данных виджетов
            if (widget_recomend && show_recomend == 0) {
                var style_r = window.getComputedStyle(widget_recomend, null);
                gadget = style_r.getPropertyValue('--' + device);
                if (gadget == 1 || location.host=='eva.ru') {
                    var height = style_r.getPropertyValue('--hsize');
                    if (height == "") height = 0;
                    var width = style_r.getPropertyValue('--wsize');
                    if (width == "") width = 0;
                    recomend_image_shape = style_r.getPropertyValue('--image_shape');
                    if (location.host=='eva.ru'){
                        recomend_image_shape=3;
                        width=4;height=1;
                    }
                    count = parseInt(width) * parseInt(height);
                    if (count !== 0) {
                        if (recomend_algorithm_output === '1') {
                            widget = widget +'&r=' + count;
                            titletext = style_r.getPropertyValue('--titletext');
                            show_recomend = 1;
                        } else {
                            var selector = style_r.getPropertyValue('--widgetparentid');
                            var abzats = style_r.getPropertyValue('--widgetpositionp');
                            if (selector != "") {
                                var ele = document.querySelectorAll(selector);
                                if (ele.length !== 0) {
                                    if (ele[0]) {
                                        widget = widget +'&r=' + count;
                                        switch (recomend_algorithm_output) {
                                            case '3':
                                                ele[0].parentNode.insertBefore(widget_recomend,ele[0]);
                                                break;
                                            case '4':
                                                ele[0].parentNode.insertBefore(widget_recomend,ele[0]);
                                                ele[0].remove();
                                                break;
                                            case '5':
                                                ele[0].parentNode.insertBefore(widget_recomend,ele[0].nextSibling);
                                                break;
                                            case '0':
                                                let children = ele[0].children;
                                                abzats--;
                                                if (abzats==-1){
                                                    ele[0].insertBefore(widget_recomend,children[0]);
                                                }else {
                                                    for (var r = 0; r < children.length - 1; r++) {
                                                        if (children[r].localName == 'p') {
                                                            if (abzats == 0) {
                                                                break;
                                                            }
                                                            abzats--;
                                                        }
                                                    }
                                                    children[r].appendChild(widget_recomend);
                                                }
                                        }
                                        titletext = style_r.getPropertyValue('--titletext');
                                        show_recomend = 1;
                                    }
                                } else {
                                    widget_recomend.remove();
                                }
                            } else {
                                widget_recomend.remove();
                            }
                        }
                    }
                } else {
                    widget_recomend.remove();
                }
            }

            if (widget_natpre && show_natpre == 0) {
                var style_e = window.getComputedStyle(widget_natpre, null);
                gadget = style_e.getPropertyValue('--' + device);
                if (gadget == 1 || location.host=='eva.ru') {
                    buttontext = style_e.getPropertyValue('--buttontext');
                    natpre_image_shape = style_e.getPropertyValue('--image_shape');
                    if (location.host=='eva.ru'){
                        natpre_image_shape=3;
                    }
                    if (natpre_algorithm_output === '1') {
                        widget = widget + '&e=1';
                        show_natpre = 1;
                    } else {
                        var selector = style_e.getPropertyValue('--widgetparentid');
                        var abzats = style_e.getPropertyValue('--widgetpositionp');
                        if (selector != "") {
                            var ele = document.querySelectorAll(selector);
                            if (ele.length !== 0) {
                                if (ele[0]) {
                                    widget = widget + '&e=1';
                                    switch (natpre_algorithm_output) {
                                        case '3':
                                            ele[0].parentNode.insertBefore(widget_natpre,ele[0]);
                                            break;
                                        case '4':
                                            ele[0].parentNode.insertBefore(widget_natpre,ele[0]);
                                            ele[0].remove();
                                            break;
                                        case '5':
                                            ele[0].parentNode.insertBefore(widget_natpre,ele[0].nextSibling);
                                            break;
                                        case '0':
                                            let children = ele[0].children;
                                            abzats--;
                                            if (abzats==-1){
                                                ele[0].insertBefore(widget_natpre,children[0]);
                                            }else {
                                                for (var r = 0; r < children.length - 1; r++) {
                                                    if (children[r].localName == 'p') {
                                                        if (abzats == 0) {
                                                            break;
                                                        }
                                                        abzats--;
                                                    }
                                                }
                                                children[r].appendChild(widget_natpre);
                                            }
                                    }
                                    show_natpre = 1;
                                }
                            } else {
                                widget_natpre.remove();
                            }
                        } else {
                            widget_natpre.remove();
                        }
                    }
                } else {
                    widget_natpre.remove();
                }
            }
        }

        //Запрос кода виджетов
        function widget_load() {
            if (widget_load_status==0) {
                widget_load_status=1;
//                console.log('corton: widget_load');
                var top10 = words();

                let category = style_b.getPropertyValue('--category');
                if (category) {
                    const categor = JSON.parse(category);
                    category = '';

                    let i = 0;
                    while (i < categor.length) {
                        //console.log(categor[i]['id_categoriya'], categor[i]['type_search'], categor[i]['regex']);
                        let obj = eval('/(' + categor[i]['regex'] + ')/');
                        delete matches;
                        if (categor[i]['type_search'] == 0) {
                            var matches = obj.exec(location.href);
                        } else {
                            var matches = obj.exec(document.body.innerHTML);
                        }
                        if (matches) {
                            category += '&c[]=' + categor[i]['id_categoriya'];
                        }
                        i++;
                    }
                }

                var request = 'https://api2.cortonlab.com/widgets.php?words=' + encodeURI(top10.join()) + widget+category;
                if (location.hostname=='demo.cortonlab.com'){request='https://api2.cortonlab.com/widgets-demo.php?words=' + encodeURI(top10.join())+'&sheme='+document.cookie.match(/scheme=(.+?);/)+'&host='+document.cookie.match(/host=(.+?);/) + widget;}
                console.log('corton: '+decodeURI(request));
                var xhr = new XMLHttpRequest();
                xhr.open('GET', request, true);
                xhr.withCredentials = true;
                xhr.send();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState != 4) {
                        return false;
                    }
                    if (xhr.status === 200) {
                        result = JSON.parse(xhr.responseText);
                        console.log('corton: ajax:', result);
                        widget_load_status=2;
                    }
                }
            }
        }

        //Подгружает код виджетов при скроле
        function show_widget() {
            if (widget_load_status!=2){
                if (wait!=200) {
                    wait++;
//                    console.log('corton: show_widget wait');
                    setTimeout(show_widget, 30);
                    return false;
                }else {
                    wait!=0;
//                    console.log('corton: show_widget end');
                    return false;
                }
            }
//            console.log('corton: show_widget',result);
            const sCurrentProtocol = document.location.protocol == "https:" ? "https://" : "http://";
            if (window.location.hostname === 'demo.cortonlab.com') {
                var promo_page='https://demo.cortonlab.com'+result['promo_page'];
            }else{
                var promo_page=sCurrentProtocol+result['promo_page'];
            }

            if (result['promo_page'].indexOf("?") > -1) {
                var promo_page = promo_page + '&';
            } else {
                var promo_page = promo_page + '?';
            }

            if (show_recomend==2) {
                if (result['anons_count'] > 0) {
                    console.log('corton: recomend_anons');
                    if (titletext != "") titletext = '<div class="corton-title">' + titletext + '</div>';
                    var htmll = '<div>' +
                        '<noindex><div class="corton-recomendation-wrapper 4x1">' +
                        titletext +
                        '<div class="corton-recomendation-row">';
                    count+=w;
                    for (; w < count; w++) {
                        if (result['anons_count'] > 0) {
                            htmll = htmll +
                                '<div class="corton-recomendation-section corton-anons" id="anons' + result['anons'][0][w] + 'r">' +
                                '<a href="' + promo_page + 'prosmort_id=' + result['prosmotr_id'] + '&anons_id=' + result['anons'][0][w] + '&p_id='+result['p_id']+'&t=r">' +
                                '<img src="https://api.cortonlab.com/img/' + result['anons'][5][w] + '/a/' + result['anons'][recomend_image_shape][w] + '">' +
                                '<p>' + result['anons'][1][w] + '</p>' +
                                '</a>' +
                                '</div>';
                            result['anons_count']--;
                        }
                    }
                    htmll = htmll +
                        '</div>' +
                        '</div>' +
                        '</div></noindex>';
                    widget_recomend.innerHTML = htmll;
                } else {
                    if (typeof(result['recomend_zag']) != "undefined" && result['recomend_zag'] !== null && result['recomend_zag'] != false) {
                        zaglushka( 'recomendation');
                    }else{
                        widget_recomend.remove();
                    }
                }
                if (location.host=='eva.ru') {
                    widget_load_status=0;
                    //console.log('corton: widget_load_status=0');
                    show_recomend = 0;
                }else{
                    show_recomend = 3;
                }
            }
            if (show_natpre==2){
                if (result['anons_count'] > 0) {
                    console.log('corton: natpre_anons');
                    if (buttontext == "") buttontext = 'Подробнее';
                    var htmll =
                        '<noindex><div class="corton-anons" id="anons' + result['anons'][0][w] + 'e">' +
                        '<div class="corton-left"> <a href="' + promo_page + 'prosmort_id=' + result['prosmotr_id'] + '&anons_id=' + result['anons'][0][w] +'&p_id='+result['p_id']+'&t=e"><img src="https://api.cortonlab.com/img/' + result['anons'][5][w] + '/a/' + result['anons'][natpre_image_shape][w] + '" width="290" height="180"></a> </div>' +
                        '<div class="corton-right">' +
                        '<a style="text-decoration: none" href="' + promo_page + 'prosmort_id=' + result['prosmotr_id'] + '&anons_id=' + result['anons'][0][w] + '&p_id='+result['p_id']+'&t=e"><div class="corton-title">' + result['anons'][1][w] + '</div></a>' +
                        '<a style="text-decoration: none" href="' + promo_page + 'prosmort_id=' + result['prosmotr_id'] + '&anons_id=' + result['anons'][0][w] + '&p_id='+result['p_id']+'&t=e"><p class="corton-content">' + result['anons'][2][w] + '</p></a>' +
                        '<a class="corton-link" href="' + promo_page + 'prosmort_id=' + result['prosmotr_id'] + '&anons_id=' + result['anons'][0][w] + '&p_id='+result['p_id']+'&t=e">' + buttontext + '</a>' +
                        '</div>' +
                        '</div></noindex>';
                    //Расчет позиции NatPrev
                    widget_natpre.innerHTML=htmll;
                    result['anons_count']--;
                    w++;
                }else {
                    if (typeof(result['natpre_zag']) != "undefined" && result['natpre_zag'] !== null && result['natpre_zag'] != false) {
                        zaglushka('nativepreview');
                    }else{
                        widget_natpre.remove();
                    }
                }
                if (location.host=='eva.ru') {
                    show_natpre = 0;
                }else{
                    show_natpre=3;
                }
            }
            if (location.host=='eva.ru'){
                w=0;
            }
        }

        //Проверка тизеров на длительность прочтения в 2 секунды
        var anons_ids_read=[];
        function checkread(show_widg) {
            for (i = 0; i < show_widg.length; i++) {
                var h=show_widg[i].getBoundingClientRect().top;
                var h2=h+show_widg[i].scrollHeight-window.innerHeight;
                if ((h > 0) && (h2 < 0)) {
                    {
                        if(!show_widg[i].classList.contains('read')){
                            var id = show_widg[i].id;
                            id=id.substring(5);
                            anons_ids_read.push(id);
                            show_widg[i].classList.add('read');
                        }
                    }
                }
            }

            if(0<anons_ids_read.length){
                var cxhr = new XMLHttpRequest();
                console.log('corton: https://stat.cortonlab.com/widget_show.php?prosmort_id='+result['prosmotr_id']+'&anons_ids='+anons_ids_read.join());
                cxhr.open('GET', 'https://stat.cortonlab.com/widget_show.php?prosmort_id='+result['prosmotr_id']+'&anons_ids='+anons_ids_read.join());
                cxhr.withCredentials = true;
                cxhr.send();
            }
            anons_ids_read.splice(0,anons_ids_read.length);
        }

        var show_widget_aktiv=false,
        max_recomend = 0,
        max_natpre = 0,
        max_recomend_old = 0,
        max_natpre_old = 0;

        //Поиск условия для загрузки виджетов
        function onscr() {
            widget_recomend = document.getElementById("corton-recomendation-widget");
            widget_natpre = document.getElementById("corton-nativepreview-widget");

            if (location.host=='eva.ru') {
                while(widget_recomend) {
                    widget_recomend.classList.add('corton-recomendation-widget');
                    widget_recomend.removeAttribute('id');
                    widget_recomend = document.getElementById("corton-recomendation-widget");
                }
                while(widget_natpre){
                    widget_natpre.classList.add('corton-nativepreview-widget');
                    widget_natpre.removeAttribute('id');
                    widget_natpre = document.getElementById("corton-nativepreview-widget");
                }
                const widget_recomend_all = document.getElementsByClassName("corton-recomendation-widget");
                if(widget_recomend_all.length!=0){
                    max_recomend=widget_recomend_all.length;
                    widget_recomend=widget_recomend_all[widget_recomend_all.length-1];
                }
                const widget_natpre_all = document.getElementsByClassName("corton-nativepreview-widget");
                if(widget_natpre_all.length!==0) {
                    max_natpre=widget_natpre_all.length;
                    widget_natpre = widget_natpre_all[widget_natpre_all.length - 1];
                }
                if (max_recomend!==max_recomend_old || max_natpre!==max_natpre_old ){
                    max_recomend_old=max_recomend;
                    max_natpre_old=max_natpre;
                    console.log('corton: '+max_recomend,max_natpre);
                    widget_check();
                }
            }else{
                widget_check();
            }
            show_widget_recomend = show_widget_natpre = false;

            if (widget_recomend) {
                if (widget_recomend.getBoundingClientRect().top !== 0) {
                    if (widget_recomend.getBoundingClientRect().top - window.innerHeight - window.innerHeight < 0) {
                        if(!widget_recomend.classList.contains('load')) {
                            show_recomend = 2;
                            show_widget_aktiv = true;
                            widget_recomend.classList.add('load');
                            show_widget_recomend=true;
                        }
                    }
                }
            }

            if (widget_natpre) {
                if (widget_natpre.getBoundingClientRect().top !== 0) {
                    if (widget_natpre.getBoundingClientRect().top - window.innerHeight - window.innerHeight / 2 < 0) {
                        if(!widget_natpre.classList.contains('load')) {
                            show_natpre = 2;
                            show_widget_aktiv = true;
                            widget_natpre.classList.add('load');
                            show_widget_natpre=true;
                        }
                    }
                }
            }


            if (show_widget_aktiv===false){
                return false;
            } else

                if (show_widget_recomend || show_widget_natpre){
                    widget_load();
                    show_widget();
                }


            let show_widg = [];

            last_wiggets=document.getElementsByClassName('corton-anons');
            //console.log(show_widget_aktiv, last_wiggets.length);

            for (i = 0; i < last_wiggets.length; i++) {
                var h=last_wiggets[i].getBoundingClientRect().top;
                var h2=h+last_wiggets[i].scrollHeight-window.innerHeight;

                if ((h > 0) && (h2 < 0)) {
                    {
                        show_widg.push(last_wiggets[i]);
                    }
                }
            }
            if (show_widg.length!=0)
                setTimeout(function() {
                    checkread(show_widg);
                }, 2000);
        }

        window.addEventListener("scroll", onscr);
        onscr();

        if (first_widget_check){
            first_widget_check=false;
            setTimeout(onscr, 500);
            setTimeout(onscr, 5000);
        }
    }

    //ajax отправка формы промо статьи
    function cortonform()
    {
        var cform=document.querySelector('#corton-form');
        var children = cform.children;
        var params = [];
        for (var i=0; i < children.length - 1; i++) {
            if (children[i].value==""){
                var status=document.getElementById("corton_form_status");
                status.innerHTML = 'Заполните обязательные поля формы';
                return false;
            }
            params.push(children[i].name + '=' + children[i].value);
        }
        params = params.join('&');
        cortonrequest.open('GET', 'https://stat.cortonlab.com/mail.php?'+params+'&host='+document.referrer);
        cortonrequest.send();
    }

    // Получение ответа отправленой формы статьи
    function getcortonrequest()
    {
        if (window.XMLHttpRequest) {return new XMLHttpRequest();}
        return new ActiveXObject('Microsoft.XMLHTTP');
    }
    cortonrequest = getcortonrequest();
    cortonrequest.onreadystatechange = function() {
        if (cortonrequest.readyState == 4) {
            var status=document.getElementById("corton_form_status");
            status.innerHTML = cortonrequest.responseText;
        }
    };

    //Блокировка чужой рекламы на промо странице
    var corton_adsblock=0;
    function adblock() {
        var widget_promo2 = document.getElementById("corton-promo");
        var style_p = window.getComputedStyle(widget_promo2, null);
        var block = style_p.getPropertyValue('--adblock');
        var arr3 = block.split(',');

        arr3.forEach(function (item, f, arr3) {
            if (item !== "") {
                var ele2 = document.querySelectorAll(item);
                for (var p = 0; p < ele2.length; p++) {
                    ele2[p].style.display = 'none';
                }
            }
        });
        if (corton_adsblock<20) {corton_adsblock++;setTimeout(adblock, 250);}else{setTimeout(adblock, 5000);}
    }

    //Расчёт готовности страницы для подгрузки виджетов и промо статьи
    function corton_delay() {
        if (window.location.hostname === 'demo.cortonlab.com') {
            let url = corton_url;
            url = url.split('?')[0];
            const url2 = url.split('/')[0];
            if (url===url2+'/promo') {
                const promo_selector = style_b.getPropertyValue('--selector');
                let sel = document.querySelectorAll(promo_selector);
                if (sel[0]) {
                    console.log('corton: '+url);
                    corton_promo();
                    return true;
                } else {
                    setTimeout(corton_delay, 40);
                }
            } else {
                if (document.readyState === "complete") {
                    const recomend_algorithm_output = style_b.getPropertyValue('--recomend-algorithm-output');
                    const natpre_algorithm_output = style_b.getPropertyValue('--natpre-algorithm-output');

                    if (recomend_algorithm_output !== '1' || natpre_algorithm_output !== '0' || slider_algorithm_output !== '1') {
                        corton_widget();
                        return true;
                    }
                } else {
                    setTimeout(corton_delay, 40);
                }
            }
        } else {
            let widget_promo = document.getElementById("corton-promo");
            if (widget_promo) {
                corton_promo();
                return true;
            } else {
                widget_recomend = document.getElementById("corton-recomendation-widget");
                widget_natpre = document.getElementById("corton-nativepreview-widget");
                if (widget_recomend || widget_natpre || widget_slider) {
                    corton_widget();
                    return true;
                }else{
                    if (document.readyState === "complete") {
                        if (corton_complete!==15){
                            setTimeout(corton_delay, 200);
                        }else{
                            corton_complete++;
                            return true;
                        }
                    }else{
                        setTimeout(corton_delay, 45);
                    }
                }
            }
        }
    }


    var corton_body = '';
    let style_b = '';
    let widget_recomend;
    let widget_natpre;
    let widget_slider;
    function corton_get_body() {

        corton_body = document.getElementsByTagName("body");
        if (corton_body.length !== 0) {
            style_b = window.getComputedStyle(corton_body[0], null);
            corton_delay();
        } else {
            setTimeout(corton_get_body, 40);
        }
    }

    corton_get_body();

    (function () {
        var eventMethod = "addEventListener";
        var eventer = window[eventMethod];
        var messageEvent = "message";
        eventer(messageEvent, function (e) {
            try {
                var array = JSON.parse(e.data);
                document.getElementById('corton-' + array['corton_tizer'] + '-iframe').height = array['height'] + 'px';
            } catch (e) {
            }
        }, false);
    })();
};
