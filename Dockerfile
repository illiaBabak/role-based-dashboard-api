FROM composer:lts 

WORKDIR /src

RUN docker-php-ext-install mysqli

COPY composer.json composer.lock ./

RUN composer install

COPY . .

EXPOSE 8000

CMD ["composer", "serve"]

