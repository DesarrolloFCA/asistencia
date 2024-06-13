# syntax=docker/dockerfile:1.0-experimental

# stage base
FROM siudocker/php:7.4-web-v1.0.3 

RUN apk --no-cache add \
    git yarn php-xmlreader

WORKDIR /usr/local/proyectos

RUN curl -sS "https://getcomposer.org/download/latest-2.x/composer.phar" -o /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# instala las fuentes de microsoft para los graficos
RUN apk --no-cache add msttcorefonts-installer fontconfig && \
    update-ms-fonts && \
    fc-cache -f
