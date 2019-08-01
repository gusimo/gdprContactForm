FROM php:7.3-fpm-buster


RUN apt-get update && apt-get upgrade -y && apt-get install -y git curl unzip msmtp ca-certificates \
	libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev

# install mysqli and gd needed for operation
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
 	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/html
#copy project
COPY ./captcha.php ./captcha.php
COPY ./dbinit.php ./dbinit.php
COPY ./captchaid.php ./captchaid.php
COPY ./formtarget.php ./formtarget.php
#copy project configuration
COPY config.inc.php ./config.inc.php
#copy msmtp configuration
COPY .msmtprc /var/www/.msmtprc
#set access rights
RUN chown www-data:www-data /var/www/.msmtprc && chmod 600 /var/www/.msmtprc
#copy php settings for msmtp
COPY sendmail_php.ini /usr/local/etc/php/conf.d/sendmail.ini


#install securimage
RUN curl -o securimage.zip -L https://www.phpcaptcha.org/latest.zip

RUN unzip securimage.zip -d ./
#remove unnecessary zip
RUN rm securimage.zip

#set correct access rights
RUN chown -R www-data:www-data /var/www/html

