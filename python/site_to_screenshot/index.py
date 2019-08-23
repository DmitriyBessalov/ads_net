#!/usr/bin/python
# -*- coding: utf-8 -*-
import sys
from selenium import webdriver
from PIL import Image

"""Требуется установить драйвер 'sudo apt-get install phantomjs' """

url = sys.argv[1]
promo_id = sys.argv[2]

browser = webdriver.PhantomJS()
browser.set_window_size(1280, 768)
browser.get(url)
browser.save_screenshot('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_desktop.png')
im = Image.open('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_desktop.png')
width, height = im.size
if height > 1000:
    height = 1000
im = im.crop((int(0), int(0), int(1280), height))
im.save('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_desktop.png')

browser.set_window_size(479, 900)
browser.get(url)
browser.save_screenshot('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_mobile.png')
browser.quit()
im = Image.open('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_mobile.png')
if height > 800:
    height = 800
im = im.crop((int(0), int(0), int(479), height))
im.save('/var/www/www-root/data/www/api.cortonlab.com/img/rekl_screenshot_site/' + promo_id + '_mobile.png')
