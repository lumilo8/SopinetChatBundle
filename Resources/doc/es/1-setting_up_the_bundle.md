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

Añadir a routing.yml:

```yaml
fos_user:
    resource: "@FOSUserBundle/Resources/config/routing/all.xml"
```

Ejecutar desde consola:

```
php bin/console doctrine:schema:update --force
```

### FOSUserBundle

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

## Configuración de SopinetChatBundle:

TODO: Completar y revisar
Debido a un fallo que está pendiente de revisar, anyType siempre tiene que estar activo por ahora:

```
sopinet_chat:
    anyType: true
    enabledAndroid: true (default)
    enabledIOS: true (default)
```

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
