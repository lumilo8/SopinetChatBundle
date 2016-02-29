# Tipos de Mensajes

## Introducción

El sistema de SopinetChatBundle utiliza un sistema de tipos de mensaje.

Todos los mensajes que se envíen (notificaciones) tendrán un atributo denominado: "type" que indicará el tipo de mensaje, así como un atributo llamado "text", que será de tipo string que tendrá el contenido del mensaje.

## Mensajes básicos

Son los mensajes que SopinetChatBundle trae por defecto.

### Mensaje de Texto (type = text)

Este es un mensaje de tipo de texto normal.
En el campo "text" será un string con una cadena de texto normal.

### Mensaje Imagen (type = image)

Este mensaje contiene una imagen.
Cuando el Cliente envía al Servidor un mensaje de este tipo, incluye un parámetro denominado "file", que tiene la imagen en cuestión.
Cuando el Cliente recibe un mensaje de este tipo en el atributo "text" vendrá la URL de dicha imagen para poder descargarla.

### Mensaje de Recepción (received)

Este mensaje confirma la recepción de otro mensaje.
En el atributo "text" se insertará el ID del mensaje que se está confirmando.

### Mensaje cambio de Estado de Usuario (userState)

Este mensaje indica el estado de conexión del usuario que envía dicho mensaje.
En el atributo "text" se enviará 0 ó 1 dependiendo del estado.

## Mensajes extra

Son mensajes adicionales que cualquier proyecto puede definir para satisfacer así su propia lógica de negocio.

### Métodos getMy

En la Entidad Message hay una serie de métodos que están pensados para poder ser sobreescritos, son los métodos que comienzan por "getMy":

#### getMyType

Devolverá el tipo del Mensaje, por defecto devolverá el mismo nombre de la clase en minúsculas y quitando la palabra "message".

#### getMyForm

Devolverá la clase del Formulario a utilizar para procesar el Mensaje. Por defecto el formulario que se usará será: Sopinet\ChatBundle\Form\MessageType.

#### getMyDestionationUsers

Usuarios a los que será enviado el Mensaje. Por defecto, serán todos los usuarios asociados al Chat al que pertenece el Mensaje.

#### getMyMessageObject

Devuelve un objeto Message desde la entidad, preparando los datos necesarios para enviar a través de la Notificación.

#### getMyIOSNotificationFields

Devuelve el campo que iOS recibirá en su notificación principal. Por defecto se devuelve el nombre de usuario.

#### getMyIOSContentAvailable

Devuelve true o false dependiendo de si la notificación (mensaje) iOS tiene asociado un procesamiento o no.

### Cómo crear un Mensaje extra propio

Vamos a crear un ejemplo de Mensaje para ver el potencial real. Tendrá las siguientes características:

1. Lo llamaremos MessageCustomGlobal.

2. El mensaje tendrá un mensaje de sistema y podrá llevar explícita una importancia (colorLevel): normal, critical.

3. Si el colorLevel es critical, entonces se añadirá un texto al mensaje: "¡ATENCIÓN!: ".

4. El mensaje se enviará a todos los usuarios existentes.

5. Por último, este mensaje no se podrá utilizar por parte de la interface API del sistema, sino que estará pensado para ser utilizado de forma interna, a través de código.

#### Creación de Entity

Crearemos una nueva clase Entity para el mensaje en nuestro proyecto:

```
<?php
namespace AppBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MessageCustomGlobal extends Message
{
    const COLORLEVEL_NORMAL = "normal";
    const COLORLEVEL_CRITICAL = "critical";

    /**
     * @var string
     * @ORM\Column(name="colorLevel", type="string", columnDefinition="enum('normal','critical')")
     */
    protected $colorLevel;

    public function setColorLevel($colorLevel) {
        $this->colorLevel = $colorLevel;
        
        return $this;
    }
    
    public function getColorLevel() {
        return $this->colorLevel;
    }

    public function getMyForm() {
        return "\AppBundle\Form\MessageCustomGlobalType";
    }

    public function getMyDestionationUsers($container) {
        $userManager = $container->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return $users;
    }
    
    public function getMyMessageObject($container, $request = null) {
        $messageObject = parent::getMyMessageObject($container, $request);
        if ($this->colorLevel == MessageCustomGlobal::COLORLEVEL_CRITICAL) {
            $messageObject->text .= "¡ATENCIÓN!: " . $messageObject->text;
        }
        return $messageObject;
    }
}
```

#### Creación de FormType (opcional)

Crearemos un FormType específico para guardar el atributo colorLevel.

```php
<?php

namespace AppBundle\Form;

use Sopinet\ChatBundle\Form\MessageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageCustomGlobalType extends MessageType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('colorLevel')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MessageCustomGlobal',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sopinet_chatbundle_messagecustomglobal';
    }
}
```

#### Configuración

Ahora indicamos en el fichero de Configuración (config.yml) nuestro nuevo tipo de mensaje. También indicamos que no podrá ser utilizado a través de las peticiones normales de la API (interfaceEnabled: false).

```
sopinet_chat:
    ...
    extra_type_message:
        customglobal:
            interfaceEnabled: false
            class: 'AppBundle\Entity\MessageCustomGlobal'
    ...
```

#### Actualización de base de datos y caché

Actualizaremos nuestro schema de base de datos:
```
    php app/console doctrine:schema:update --force
```

Limpiamos la caché:
```
    php app/console cache:clear
```

[Volver al índice](README.md)
