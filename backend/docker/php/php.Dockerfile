FROM bref/php-84-fpm-dev:latest

RUN yum install -y make git

COPY --from=composer:2 /usr/bin/composer /usr/bin/
