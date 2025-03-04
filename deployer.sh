#!/bin/bash
git reset HEAD --hard
git pull origin master
php /opt/cpanel/composer/bin/composer dump-env prod
php /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
npm install
npm run build