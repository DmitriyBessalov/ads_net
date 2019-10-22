document.body.scrollTo(0, 0);

const cortonjson='['+
    '['+
        '"https://www.7ya.ru/article/Sekrety-uspeshnoj-sjemki-na-smartfon/",'+
        '"https://www.netprint.ru/lp/7ya_50foto?utm_source=partners&utm_medium=article&utm_campaign=2019",'+
        '"https://api.cortonlab.com/7ya/1-pc.jpg",'+
        '"https://api.cortonlab.com/7ya/1-mob.jpg"'+
    '],['+
        '"https://www.7ya.ru/article/Vybiraem-repetitora-pravilno-sovety-kotorye-pomogut-izbezhat-oshibok/",'+
        '"https://tutor.ru/general?utm_source=semya&utm_medium=pr&utm_campaign=art1",'+
        '"https://api.cortonlab.com/7ya/2-pc.png",'+
        '"https://api.cortonlab.com/7ya/2-mob.png"'+
    '],['+
        '"https://www.7ya.ru/article/Shkola-Sadik-Uspokoit-rebenka-i-povysit-immunitet/",'+
        '"https://shop.evalar.ru/catalog/item/baby-formula-bears-immunity/?utm_source=7ya_septemberRW&utm_medium=article&utm_campaign=mishki%7C%7Carticle%7C%7C7ya_september%7C%7Cmain%7C%7Crw",'+
        '"https://api.cortonlab.com/7ya/3-pc.png",'+
        '"https://api.cortonlab.com/7ya/3-mob.png"'+
    ']'+
']',
s2s=JSON.parse(cortonjson);

for(var i=0; i<s2s.length; i++) {
    if (s2s[i][0]===location.href)
    {
        console.log(s2s[i][0]);
        const regex = /^(https?:\/\/)?(.*?)($|[/?])/;
        let host = regex.exec(s2s[i][1]);

        var tags = document.getElementsByClassName('article_info');

        tags[0].innerHTML= ''+
            '<div id="corton-promo"></div>'+
            '<div id="corton_scroll_to_site">'+
            '<div id="corton_sticky_container">'+
            '<div id="corton_overlay"></div>'+
            '<div id="corton_border_title"><div>Листая дальше вы перейдёте на: &nbsp;<a href="'+s2s[i][1]+'">'+host[2]+'</a></div></div>'+
            '<div id="corton_browser_container">'+
            '<a href="'+s2s[i][1]+'" rel="noopener nofollow">'+
            '<div id="corton_header">'+
            '<div id="corton_favicon" style="background-image: url(\'https://favicon.yandex.net/favicon/'+host[2]+'\');"></div>'+
            '<div id="corton_link">'+host[2]+'</div>'+
            '</div>'+
            '<img id="corton_image_fon">'+
            '</div>'+
            '</a>'+
            '</div>'+
            '</div>'+
            '</div>'+

            '<style type="text/css">'+
                '#corton_osvetlenie{position:fixed;top:0;left:0;right:0;bottom:0;z-index:2147483647;background-color:#FFF;opacity:0;pointer-events:none;transition:opacity 1s ease-in;} '+
                '#corton_border_title{background-color:#fff8d9;max-height:82px;height:82px;display: flex; justify-content: center; align-items: center;} '+
                'footer, .ocenka_teaser {display: none !important;} '+
                '#corton-promo img {max-width:100% !important;box-shadow:unset !important;border:unset !important;} '+
                '#corton_border_title p {text-align: center;} '+
                '.article_info {float: unset !important;}' +
                '@media (max-width: 1024px) { '+
                '#corton_image_layer{position:sticky;bottom:0;z-index:2147483646;background-color:#000;} '+
                '#corton_image_fon_mobile{width: 100%; max-width: 100% !important;opacity:.95;position: relative;top:0;border:none; margin: 0px;} '+
                '#corton_gradient_conteyner{height:2000px;position: relative;z-index: 2147483647;} '+
                '#corton_gradient{width:100%;height:100px;background-image:linear-gradient(rgba(0,0,0,0.1),rgba(255,0,0,0));} '+
                '#corton_scroll_to_site {width: 100%;z-index:2147483647;position:relative;} '+
                '#corton_scroll_to_site a{text-decoration:none;} '+
                '#corton_browser_container{display:none;} '+
                '#corton-promo {background-color:#FFFFFF;max-width: unset;position: relative;} '+
                '#corton_fon {position: relative;z-index:2147483647;background-color: #FFF;padding: 0 20px;} '+
                '}'+
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
                '#corton-promo{min-height:unset !important;} '+
                '}'+
            '</style>';
            var corton_promo=document.getElementById("corton-promo"),
                scroll_to_site = document.getElementById("corton_scroll_to_site"),
                overlay=document.getElementById("corton_overlay"),
                browser_container=document.getElementById("corton_browser_container"),
                image_fon=document.getElementById("corton_image_fon"),
                image_fon_mobile=document.getElementById("corton_image_fon_mobile"),
                image_layer = document.getElementById("corton_image_layer"),
                gradient_conteyner = document.getElementById("corton_gradient_conteyner"),
                osvetlenie = document.getElementById("corton_osvetlenie"),
                page_ready=0,

                article = document.getElementById("article");

        function osvetlenie_redirekt() {
            console.log('переход');
            //osvetlenie.style.opacity='1';
            //if (page_ready===1){
            //    setTimeout(function() {
            //        document.location.href = s2s[i][1];
            //    }, 300);
            //}
            //page_ready=2;
        }

        setTimeout(function() {
            document.body.scrollTo(0, 0);
        }, 400);

        function s2s_position() {
            if (page_ready === 0) {
                document.body.scrollTo(0, 0);
                return true;
            }

            scroll_to_site.style.left = -article.getBoundingClientRect().left+25 + 'px';
            scroll_to_site.style.width = innerWidth -17+ 'px';

            var stisky_top = corton_promo.getBoundingClientRect().top + corton_promo.scrollHeight;

            if (outerWidth <= 1024) {
                image_layer.style.bottom = outerHeight - image_fon_mobile.scrollHeight + 'px';

                if (outerHeight / 8 > gradient_conteyner.getBoundingClientRect().top) {
                    osvetlenie_redirekt();
                }
            } else {
                var i = (window.innerHeight - corton_promo.getBoundingClientRect().top) * 100 / innerHeight;
                console.log(i);

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
            scroll_to_site.style.left = -gradient_conteyner.getBoundingClientRect().left + 'px';
            scroll_to_site.style.width = outerWidth + 'px';
        }

        if (outerWidth<=1024){
            image_fon_mobile.src=s2s[i][3];
        }else{
            image_fon.src=s2s[i][2];
            scroll_to_site.style.left = -scroll_to_site.getBoundingClientRect().left + 'px';
            scroll_to_site.style.width = document.documentElement.clientWidth + 'px';
        }
        setTimeout(function() {
            page_ready=1;
            s2s_position();
        }, 1000);
    }
}