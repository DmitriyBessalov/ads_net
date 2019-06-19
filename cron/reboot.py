#!/usr/bin/env python3
import pyodbc
driver = 'DRIVER={MySQL}'
server = 'SERVER=185.75.90.54'
port = 'PORT=3306'
db = 'DATABASE=corton'
user = 'UID=corton'
pw = 'PWD=W1w5J7e6'
conn_str = ';'.join([driver, server, port, db, user, pw])

conn = pyodbc.connect(conn_str)
cursor = conn.cursor()

cursor.execute('INSERT INTO `corton`.`platforms_domen_memory`(`domen`, `id`) SELECT `ploshadki`.`domen`, `ploshadki`.`id` FROM `corton`.`ploshadki`')