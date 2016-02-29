# Instalación básica:

## Instalación del bundle desde el repositorio privado:

```
"require": {
  "sopinet/chatbundle": "dev-master",
},
"repositories": [
  {
    "type": "vcs",
    "url":  "git@gitlab.sopinet.es:hidabe/chatbundle.git"
  }
],
```

## Añadimos a AppKernel:

```php
new FOS\RestBundle\FOSRestBundle(),
new JMS\SerializerBundle\JMSSerializerBundle(),
new Sopinet\ApiHelperBundle\SopinetApiHelperBundle(),
new FOS\UserBundle\FOSUserBundle(),
new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
new RMS\PushNotificationsBundle\RMSPushNotificationsBundle(),
new Sopinet\ChatBundle\SopinetChatBundle(),
```

## Configuración de Bundles:

### FOSRestAPI

Añadir a config.yml:

```yaml
fos_rest:
    routing_loader:
        default_format: json
```

### FOSUserBundle

Añadir a config.yml

```
fos_user:
    db_driver: orm # other valid values are 'mongodb', 'couchdb' and 'propel'
    firewall_name: main
    user_class: AppBundle\Entity\YourUserEntity
```

Si se está usando SonataUser: 

```
    user_class: Application\Sonata\UserBundle\Entity\User)
```

Añadir a security.yml

```
security:
    encoders:
        FOS\UserBundle\Model\UserInterface: bcrypt
```

Añadir a routing.yml:

```yaml
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"
```

Ejecutar desde consola:

```
php bin/console doctrine:schema:update --force
```

### RMSPushNotificationsBundle

```
rms_push_notifications:
    android:
        gcm:
            api_key: %chat_gcm_key%
    ios:
        sandbox: %chat_apn_sandbox%
        pem: %chat_apn_pem%
        passphrase: %chat_apn_passpharase%
```

## Configuración de SopinetChatBundle (opcional):

Todos los parámetros de configuración son opcionales, si no se especifica nada el Bundle funcionará por defecto.

```
sopinet_chat:
    anyType: false (Permite el envío de cualquier tipo de Mensajes incluso no definidos)
    enabledAndroid: true (Activación de funcionamiento en Android)
    enabledIOS: true (Activación de funcionamiento en iOS)
    enabledWeb: false (Activación de funcionamiento en Cliente Web)
    background: false (Activación de funcionamiento a través de una cola de mensajes en Background)
    soundIOS: default (Se puede indicar el nombre del fichero local en iOS que sonará en las notificaciones)
```

enabledWeb, Necesitarás configurar WebSocket si lo quieres activar:
[WebSocket](background/websocket.md)

background, Necesitarás configurar RabbitMQ si lo quieres activar:
[RabbitMQ](background/rabbitMQ.md)


## Integración con tu entidad User

### YourUserEntity

En nuestra entidad User habrá que incorporar un trait:
```php
    use \Sopinet\ChatBundle\Model\UserChat {
        __construct as _traitconstructor;
    }
```
Y en el constructor:
```php
    public function __construct()
    {
        $this->_traitconstructor();
        parent::__construct();
    }
```

### Conexión con UserInterface

Habrá que indicar a SopinetChatBundle qué clase Usuario estamos utilizando, para ello, se realizará la especificación mediante una interface. En el fichero config.yml:

```
doctrine:
    orm:
        resolve_target_entities:
            Sopinet\ChatBundle\Model\UserInterface: AppBundle\Entity\YourUserEntity
```

Si se está usando SonataUser, sería:
```
doctrine:
    orm:
        resolve_target_entities:
            Sopinet\ChatBundle\Model\UserInterface: Application\Sonata\UserBundle\Entity\User
```

## Otras configuraciones obligatorias

### Habilitar el tipo enum

```
doctrine:
    dbal:
        mapping_types:
            enum: string
```

### sopinet_login_helper

Se debe cumplir con la interface de login de Sopinet:
http://redmine.sopinet.es:3000/projects/symfony-development/wiki/Requisitos_para_la_implementaci%C3%B3n_del_checkeo_de_Usuario_desde_API

TODO: Describir mejor esta parte.

[Volver al índice](README.md)
