
### Excel-import-Laravel

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>
<p align="center">
</p>
<a href="https://git.io/typing-svg"><img src="https://readme-typing-svg.herokuapp.com?font=Fira+Code&size=30&pause=1000&center=true&vCenter=true&multiline=true&width=1080&height=160&lines=I+welcome+everyone!+My+name+is+Rinat.+;I+am+engaged+in+web+development+of+back-end+applications+and;websites+and+a+little+front-end." alt="Typing SVG" /></a>

### Project written on Laravel: Instructions and additional information for installing and testing the application:
* composer install or composer update
* Create a DB (in the .env file and the database, enter the correct data for configuration)
* php artisan migrate

### To run the project locally, you need to type commands in the terminal in turn ==>
* php artisan serve
* vite
* php artisan queue:work --queue=imports

### For Unit-Feature tests:
* cp .env .env.testing
* php artisan make:test TicketTest --unit
* php artisan migrate --seed --env=testing
* php artisan migrate:refresh --seed --env=testing
* composer dump-autoload
* php artisan test

### Additional actions in case of errors...
* php artisan route:cache
* php artisan route:clear
* php artisan config:clear
* php artisan cache:clear
* php artisan optimize
### package.json =>
* {
  "private": true,
  "scripts": {
  "dev": "vite",
  "build": "vite build"
  },
  "devDependencies": {
  "@inertiajs/vue3": "^1.0.0",
  "@tailwindcss/forms": "^0.5.3",
  "@vitejs/plugin-vue": "^4.0.0",
  "alpinejs": "^3.4.2",
  "autoprefixer": "^10.4.12",
  "axios": "^1.1.2",
  "laravel-vite-plugin": "^0.7.2",
  "lodash": "^4.17.19",
  "postcss": "^8.4.18",
  "tailwindcss": "^3.2.1",
  "vite": "^4.0.0",
  "vue": "^3.2.41"
  }
  }

