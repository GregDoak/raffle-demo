FROM bref/php-84-fpm-dev:latest

RUN yum install -y make git

COPY --from=bref/extra-gd-php-84:latest /opt /opt
COPY --from=composer:2 /usr/bin/composer /usr/bin/
