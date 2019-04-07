<?php
exec('cd /var/www/www-root/data/github/corton && git pull git@github.com:DmitriyBessalov/corton.git');
echo '{"status":"Ok"}';
