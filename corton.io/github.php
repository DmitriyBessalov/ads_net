<?php
exec('cd /var/www/www-root/data/www && git clean -f && git pull git@github.com:DmitriyBessalov/corton.git');
echo '{"status":"ok"}';
