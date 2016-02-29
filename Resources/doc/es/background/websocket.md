# WebSocket

Para que SopinetChatBundle pueda funcionar con un Cliente-Web es necesario activar WebSocket.

Es necesario que la configuración del SopinetChatBundle, enabledWeb esté a true:

```
sopinet_chat:
   enabledWeb: true
```

Para activar el funcionamiento con WebSocket utilizaremos el Bundle GOSWebSocketBundle.

## Instalar vía composer

```
   composer require gos/web-socket-bundle
```

## Añadir a AppKernel

```
   new Gos\Bundle\WebSocketBundle\GosWebSocketBundle(),
   new Gos\Bundle\PubSubRouterBundle\GosPubSubRouterBundle(),
```

## Añadir la Configuración

```
# SopinetChatBundle -  Web Socket Configuration
gos_web_socket:
    server:
        port: %chat_web_port%        #The port the socket server will listen on
        host: %chat_web_ip%   #The host ip to bind to
        router:
            resources:
                - @SopinetChatBundle/Resources/config/pubsub/routing.yml
    pushers:
        wamp:
            host: %chat_web_ip%
            port: %chat_web_port%
```

Establecer parámetros chat_web_ip y chat_web_port (por ejemplo, el local: 127.0.0.1 y 8080)

## Lanzar WebSocket

```
php app/console gos:websocket:server
```

(Lo ideal sería lanzarlo con algún tipo de script en segundo plano, nohup &)

## Más información sobre el Bundle

Si necesita más información sobre el Bundle, puede consultar la web oficial:
https://github.com/GeniusesOfSymfony/WebSocketBundle

[Volver al índice](README.md)
