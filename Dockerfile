FROM php:8.1-fpm-alpine3.14 as php

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev acl tzdata

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
	; \
	\
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo \
		pdo_pgsql \
	; \
	pecl install \
		apcu \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

RUN apk add --no-cache \
        wkhtmltopdf \
        xvfb \
        ttf-dejavu ttf-droid ttf-freefont ttf-liberation \
    ;

RUN ln -s /usr/bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf;
RUN chmod +x /usr/local/bin/wkhtmltopdf;

ARG XDEBUG_ENABLE=false
RUN if [ $XDEBUG_ENABLE = "true" ] ; then \
        apk --no-cache add pcre-dev ${PHPIZE_DEPS};\
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        touch /var/log/xdebug_remote.log; \
        chmod 777 /var/log/xdebug_remote.log; \
        echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.log=/var/log/xdebug_remote.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.idekey=phpstorm" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.remote_connect_back=Off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.client_host=172.17.0.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi \
;

COPY --from=composer:2.2.12 /usr/bin/composer /usr/bin/composer

WORKDIR /srv/api

COPY composer.json composer.lock symfony.lock ./

RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	composer clear-cache

COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY migrations migrations/
COPY templates templates/
COPY VERSION ./
COPY .env ./
RUN composer dump-env prod; \
	rm .env
RUN mkdir -p storage

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/afi.ini $PHP_INI_DIR/conf.d/
COPY docker/php/conf.d/afi_prod.ini $PHP_INI_DIR/conf.d/

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ARG UID=false
ARG GID=false

RUN if [ $UID != "false" && $GID != "false" ] ; then \
  chown -Rf www-data:www-data /srv/api /srv/api.* \
	&& usermod -u ${UID} www-data \
    && groupmod -g ${GID} www-data; \
  fi \
;

ENV WKHTMLTOPDF_PATH=/usr/local/bin/wkhtmltopdf
ENV WKHTMLTOIMAGE_PATH=/usr/local/bin/wkhtmltopdf

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM php AS php_dev

ENV APP_ENV=dev
VOLUME /srv/api/var/

RUN rm $PHP_INI_DIR/conf.d/afi_prod.ini; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY docker/php/conf.d/afi_dev.ini $PHP_INI_DIR/conf.d/

RUN rm -f .env.local.php

FROM nginx:1.21.6-alpine AS nginx
COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
WORKDIR /srv/api/public
COPY --from=php /srv/api/public ./