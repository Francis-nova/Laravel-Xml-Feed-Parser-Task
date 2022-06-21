FROM php:8.1-fpm

RUN apt-get update && apt-get -y install cron

RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libxml2-dev \
    libicu-dev \
    libbz2-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++

RUN docker-php-ext-install \
    gd \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    simplexml \
    zip

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Bucharest /etc/localtime
RUN "date"

ARG uid
ARG gid

RUN groupadd -g $gid appjobsgroup
RUN useradd -G www-data,root -u $uid -g $gid -d /home/appjobsuser appjobsuser
RUN mkdir -p /home/appjobsuser/.composer && \
    chown -R appjobsuser:appjobsgroup /home/appjobsuser && \
    su appjobsuser

RUN chown -R appjobsuser:appjobsgroup /home/appjobsuser
WORKDIR /var/www/appjobs
