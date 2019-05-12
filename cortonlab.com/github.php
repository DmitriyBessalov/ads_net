<?php
exec('cd /var/www/www-root/data/www && git reset --hard origin/master');
echo '{"status":"ok"}';