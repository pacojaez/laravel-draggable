<p align="center">
<img src="https://img.shields.io/badge/Versión-v1.0.1-green" alt="Latest Stable Version"></a>
</p>

## SortableJS

Esta aplicación es un ejemplo de uso en laravel del componente sortablejs

## Procesos seguidos para la construcción base y de requisitos

### Instalamos laravel
editamos el .env

### Creamos el sistema de Auth
Agregamos requisitos

    composer require laravel/jetstream

Instalamos jetstream con

    php artisan jetstream:install livewire

Al finalizar ejecutamos

    npm install && npm run dev

Publicamos las vistas

    php artisan vendor:publish --tag=jetstream-views

Los logos se encuentran en

    resources/views/vendor/jetstream/components/application-logo.blade.php
    resources/views/vendor/jetstream/components/authentication-card-logo.blade.php
    resources/views/vendor/jetstream/components/application-mark.blade.php

Lanzamos las migraciones para crear las tablas de auth

    php artisan migrate

Si queremos requerir términos de servicio y política de privacidad, editar

    config/jetstream.php

Y quitar el comentario a la frase

    Features::termsAndPrivacyPolicy(),

Escribir las condiciones del servicio en los fichero


    resources/markdown/terms.md
    resources/markdown/policy.md fil


Si hemos modificado logos o customizado algo de las vistas debemos actualizar usar el comando

    npm run dev

#Agregamos plantilla de turnos

Lo hacemos con livewire

    php artisan make:livewire templatesBoard

Requerimos para poder usar arrastras y soltar instalar la librería

    npm install sortablejs

Tendremos que agregarlo a nivel global, en este caso usamos el app.js de resources

    window.Sortable = require('sortablejs').default;

Quedando así el fichero

    require('./bootstrap');
    window.Sortable = require('sortablejs').default;
    import Alpine from 'alpinejs';
    
    window.Alpine = Alpine;
    
    Alpine.start();
