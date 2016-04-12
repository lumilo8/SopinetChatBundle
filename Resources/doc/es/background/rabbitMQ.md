# RabbitMQ

Para que SopinetChatBundle pueda funcionar en Background es necesario activar un sistema de colas.

Es necesario que la configuración del SopinetChatBundle, background esté a true:

```
sopinet_chat:
   background: true
```
Para activar el funcionamiento en Background instalaremos y configuraremos el bundle: [RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle)

## Instalar vía composer:

```
composer require oldsound/rabbitmq-bundle
```

## Añadir a AppKernel

```
new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle()
```

## Añadir a la Configuración

Los parámetros indicados como puerto, host, etc... son sólo de ejemplo, recuerda que debes instalar también [RabbitMQ](https://www.rabbitmq.com) en tu servidor.

```
# SopinetChatBundle - RabbitMQBundle
old_sound_rabbit_mq:
    connections:
        default:
            host:     'localhost'
            port:     5672
            user:     'guest'
            password: 'guest'
            vhost:    '/'
            lazy:     false
            connection_timeout: 3
            read_write_timeout: 3

            # requires php-amqplib v2.4.1+ and PHP5.4+
            keepalive: false

            # requires php-amqplib v2.4.1+
            heartbeat: 0
    producers:
        send_message_package:
            connection:       default
            exchange_options: {name: 'send_message_package', type: direct}
    consumers:
        send_message_package:
            connection:       default
            exchange_options: {name: 'send_message_package', type: direct}
            queue_options:    {name: 'send_message_package'}
            callback:         sopinet_chatbundle_sendMessagePackage
```

## Lanzar el consumidor

Normal:
```
php app/console rabbitmq:consumer send_message_package
```

En Background:
```
nohup php app/console rabbitmq:consumer send_message_package &
```

[Volver al índice](README.md)