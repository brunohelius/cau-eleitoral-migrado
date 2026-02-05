# Implantação da APP

## Detalhes da aplicação

* PHP 7.2
* Framework: Lumen 5.8

## Dependências do PHP

cli | common | curl | gd | intl | json | mbstring | opcache | pgsql | readline | xml | zip

## Configurações do servidor

**Instalar Composer:**

***Linux:***

    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

***Windows:***

Baixe e execute o <a href="https://getcomposer.org/Composer-Setup.exe">Composer-Setup.exe</a>.

## Iniciando a APP

**Efetuando o clone da app:**

    git clone git@ssh.dev.azure.com:v3/caubr/Eleitoral/Eleitoral-Backend

**Executar o composer:**

    composer install -vvv

**Gerando proxies doctrine:**

    php artisan doctrine:generate:proxies

**Gerando documentação swagger:**

    php artisan swagger-lume:generate

**Subindo aplicação:**

    php -S localhost:8000 -t public

## Informações Gerais

**Executar Test:**

    ./vendor/bin/phpunit tests/Unit/Business/<nome do arquivo>

**Executar Test:**

    ./vendor/bin/phpunit tests/Unit/Business/<nome do arquivo>

**Rotas:**

* Padrões: http://url/
* Api Rest com retorno json: http://url/api

**Limpar cache:**

    php artisan cache:clear

# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We
believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain
out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction,
queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All
security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
