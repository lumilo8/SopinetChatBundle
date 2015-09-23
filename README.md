# Propósito:

- ChatBundle permite la gestión del envío de mensajes en Backend a un grupo de Dispositivos, pertenecientes a una serie de Usuarios.
- El sistema permite disponer de distintos tipos de mensajes. Estos mensajes permiten ser asociados a un Chat (grupo de Usuarios "estático"), o bien, ser asociados a un conjunto de Usuarios dinámico, a partir de unas reglas.
- El sistema permite abstraernos de todo el control de envío de notificaciones en Android (GCM), reenvío en iOS (APNs), administración de los mensajes, dispositivos, y paquetes de mensajes en Sonata.
- El sistema es áltamente escalable y permite la creación de otros tipos de Mensajes fácilmente desde fuera del sistema.

# Requisitos:

- El sistema hace uso de Application\Sonata\UserBundle\Entity\User.php, por tanto, éste debe ser el sistema para Usuarios por defecto.
- El sistema hace uso de RMS_PUSH_NOTIFICATION
- El sistema hace uso del protocolo de interfaz de Login estándar, definido en Sopinet: esto es, un servicio "sopinet_login_helper", que dispoga de una función públia getUser(Request $request), la cual devuelva un objeto de tipo User. Más información aquí: http://redmine.sopinet.es:3000/projects/symfony-development/wiki/Requisitos_para_la_implementaci%C3%B3n_del_checkeo_de_Usuario_desde_API
- Es probable que se estén utilizando otros bundles (como fos_rest_bundle) y la gestión de dependencias en composer.json no esté debidamente especificada, pero estos bundles son típicos que utilizamos en BasicSymfony.

# "Desacoples" Pendientes:

- El sistema hace uso de ApiHelper, el cual, está dentro de este mismo bundle, eso está pendiente de desacoplar.

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
new Sopinet\ChatBundle\SopinetChatBundle(),
```

## Configuración de RMS_PUSH_NOTIFICATION:

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

### Configuración de sopinet_chat (No es necesaria, si no se indica se activará por defecto):

```
sopinet_chat:
    anyType: false (default)
    enabledAndroid: true (default)
    enabledIOS: true (default)
```

## Integración con User

En nuestra entidad user habrá que incorporar un trait:
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

# Utilización básica

El Bundle dispone de un Controlador básico, con una serie de funciones básicas implementadas, la documentación de dichas funciones estará
automáticamente disponible en api/doc. Cabe señalar que no se indica en la documentación de dichas funciones los parámetros de login
pues estos quedan desacoplados a través del servicio sopinet_login_helper.

## Instación:
```
SopinetChatBundle:
    resource: "@SopinetChatBundle/Resources/config/routing.yml"
    prefix:   /api/chat
```

Nótese que, por tanto, todas las llamadas comenzarán por "api/chat", aunque esto se puede cambiar.

## Funciones disponibles (más información en api/doc):

- sendMessage (manda un mensaje)
- createChat (crea un chat estático)
- registerDevice (registra un dispositivo y lo asocia a un usuario)
- sendUnprocessNotification (para iOS)
- cleanUnprocessNotification (para iOS)

# SonataAdmin

El Bundle también incluye por defecto la carga de toda la gestión de entidades a través de Sonata, para incluirla basta con el grupo "SopinetChat"
en tu config.yml, en la parte de SonataAdmin, algo como:
```
# SonataAdmin
sonata_admin:
    dashboard:
        blocks:
            - { position: left, type: sonata.admin.block.admin_list, settings: { groups: [SopinetChat] } }
```

# Utilización avanzada

## Servicio InterfaceHelper

Todas las funciones disponibles definidas más arriba, en el controlador por defecto, lo están también como servicios, con la misma nomenclatura 
y con los mismos parámetros como servicios.

El servicio principal está cargado por defecto y su nombre es: "sopinet_chatbundle_interfacehelper"
 
Por tanto, accediendo a él, se podrán utilizar las funciones anteriores. Éstas reciben un parámetro de tipo Request, con los datos necesario.

De ese modo, se podría implementar un controlador de Chat específico en cada aplicación, que aplicase una lógica concreta y utilizase los servicios
necesarios en los momentos que su lógica requiriese.

## Ejemplo: Mandar mensaje si no has mandado más de 100

Imaginemos un ejemplo sencillo: queremos que nuestro sendMessage sólo mande un mensaje si el aún no nos hemos excedido de mandar 100 o más
mensajes. Implementaremos un Controlador propio, con una función propia donde se haga dicha comprobación:

```php
class MyCustomApiChatController extends FOSRestController
{
    /** Aquí vendría la documentación api/doc, importante hacerla **/
    public function sendMessageAction(Request $request)
    {
        /** @var InterfaceHelper $interfaceHelper */
        $interfaceHelper = $this->get('sopinet_chatbundle_interfacehelper');

        /** Interface de LoginHelper genérica **/
        $loginHelper = $this->get('sopinet_login_helper');
        
        /** ApiHelper, pendiente de modularizar! */
        $apiHelper = $this->get('apihelper');

        /** @var User $user */
        try {
            $user = $loginHelper->getUser($request);
        } catch(Exception $e) {
            return $apiHelper->responseDenied($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        
        if (count($user->getMessages() > 100) {
            return $apiHelper->responseDenied("Error limit messages", Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $message = $interfaceHelper->sendMessage($request);
        } catch (Exception $e) {
            return $apiHelper->responseDenied($e->getMessage());
        }

        return $apiHelper->responseOk($message, "create");        
    }
}
```

# Tipos de Mensajes

## Introducción

El sistema de ChatBundle utiliza una serie de tipos de mensaje. Actualmente están disponibles dos, que vienen de base:
text e image, estos se pueden configurar, habilitar o deshabilitar.

El sistema permite extender los mensajes y cubrir múltiples necesidades.

## Mensajes básicos

TODO: Aquí explicaré mejor qué tipos tenemos y qué parámetros de configuración permiten

## Mensajes extra

En el proyecto YouChat hay varios mensajes extra creados.
TODO: Aquí explicaré mejor cómo se crean y qué posibilidades ofrecen