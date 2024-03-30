FROM php:8.3-apache-bookworm

LABEL maintainer="Adrian Coral <adriancoral@gmail.com>"

ARG user=acoral
ARG uid=1000
ARG gid=1000

LABEL so.debian="bullseye"
LABEL php="8.3"

# persistent dependencies
RUN set -eux; \
	apt-get update && apt-get install -y apt-transport-https && \
	apt-get install -y --no-install-recommends

# install the PHP extensions & others
RUN set -ex; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libjpeg-dev \
		libxml2-dev \
		libmcrypt-dev \
		zlib1g-dev \
		libfreetype6-dev \
		libmemcached-dev \
		libmagickwand-dev \
		libpng-dev \
		libzip-dev \
		less \
		wget \
		unzip \
        git \
        supervisor

RUN docker-php-ext-install zip;

RUN docker-php-ext-install pcntl;

RUN	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		iconv \
		opcache \
        sqlite3; \
        pecl install redis-6.0.2; \
    	docker-php-ext-enable redis; \
        pecl install xdebug-3.3.1; \
        docker-php-ext-enable xdebug;

RUN	docker-php-ext-install mysqli pdo_mysql;

RUN mkdir -p /var/log/supervisor && \
    touch /var/log/supervisor/supervisor.log && \
    chmod 666 /var/log/supervisor/supervisor.log && \
    mkdir -p /etc/supervisor/conf.d

RUN a2enmod expires \
	&& a2enmod headers \
	&& a2enmod rewrite \
    && a2enmod ssl

# Add the user UID:1000, GID:1000 and add to sudo group
RUN groupadd -r $user -g $gid && useradd -u $uid -r -g $user -m  $user -s /bin/bash && \
    echo "%${user}   ALL=(ALL:ALL) NOPASSWD:ALL" >> /etc/sudoers && \
    echo "alias t=vendor/bin/phpunit" >> /home/$user/.bash_aliases && \
    echo "alias f=vendor/bin/php-cs-fixer" >> /home/$user/.bash_aliases \

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD ./supervisord.conf /etc/supervisord.conf

# default command
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
