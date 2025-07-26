cd /var/www

# if mjml is not installed, install it
if [ ! -d "node_modules" ] || [ ! -f "node_modules/.bin/mjml" ]; then
  echo "Installing MJML locally..."
  npm init -y
  npm install mjml
else
  echo "MJML already installed."
fi

exec php-fpm
