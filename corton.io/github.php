<?php
exec('cd /var/www/www-root/data/www && git pull git@github.com:DmitriyBessalov/corton.git');
echo '{"status":"successfully"}';
