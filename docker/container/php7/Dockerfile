FROM ubuntu:18.04

# Install any custom system requirements here

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get -qq update \
  && apt-get -qq -y upgrade \
  && apt-get install -qq -y --no-install-recommends \
                wget \
                curl \
                mc \
                htop \
                git \
                software-properties-common

ENV LC_ALL=C.UTF-8
RUN add-apt-repository ppa:ondrej/php \
 && apt-get -qq update


RUN apt-get -qq -y install  php7.2-cli \
                php7.2-fpm \
                php7.2-curl \
                php7.2-gd \
                php7.2-memcache \
                php7.2-memcached \
                php7.2-mysql \
                php7.2-sqlite3 \
                php7.2-json \
                php7.2-intl \
                php7.2-xdebug \
                php7.2-xsl \
        && apt-get -qq autoclean

RUN apt-get -qq -y install php7.2-dev libmcrypt-dev php-pear \
 && pecl channel-update pecl.php.net \
 && pecl install mcrypt-1.0.1


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN sed -i -e "s/DAEMON_ARGS=\"/DAEMON_ARGS=\"--allow-to-run-as-root /g" /etc/init.d/php7.2-fpm

RUN mkdir /run/php

WORKDIR /var/www
EXPOSE 9000-9005

CMD ["php-fpm7.2", "--nodaemonize"]
