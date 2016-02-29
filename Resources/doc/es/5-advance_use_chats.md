# Tipos de Chats

## Introducción

El sistema de SopinetChatBundle utiliza un sistema de tipos de chats.

Todos los chats que se creen tendrán asignado un tipo.

El tipo (type) será especificado a la función createChat cada vez que se cree un Chat.

## Chats básicos

Por defecto existe un tipo de Chat básico en SopinetChatBundle, con el type: "chat".

## Chats extra

Son chats adicionales que cualquier proyecto puede definir para satisfacer así su propia lógica de negocio.

### Métodos getMy

En la Entidad Chat hay una serie de métodos que están pensados para poder ser sobreescritos, son los métodos que comienzan por "getMy":

#### getMyType

Devolverá el tipo del Chat, por defecto devolverá el mismo nombre de la clase en minúsculas y quitando la palabra "chat".

#### getMyForm

Devolverá la clase del Formulario a utilizar para procesar el Chat. Por defecto el formulario que se usará será: Sopinet\ChatBundle\Form\ChatType.

#### getMyChatExist

Comprueba si existe un Chat, devolverá null en caso de que no exista o el Objeto Chat en el caso de que sí. Con esta función se puede sobreescribir la lógica respecto a qué significa que un Chat exista. Por defecto, el Chat existirá si ya hay uno con esos miembros creado.

#### getMyAddMessageObject

A través de esta función se podrán añadir más atributos al Objeto de datos que se envía en los Mensajes. Por defecto no se añade ningún dato.

#### getMyChatMembers (SIN IMPLEMENTAR AÚN)

(FUNCIÓN SIN IMPLEMENTAR). Esta función aún no está implementada, pero está prevista que exista en próximas versiones. La función permitirá saber qué usuarios pertenecen a un Chat y podrá ser sobreescrita, de esa forma, los mensajes de dicho Chat podrán consultar directamente a ella la información de usuarios para el envío de mensajes.

### Cómo crear un Chat extra propio

Vamos a crear un ejemplo de Chat para ver el potencial real. Tendrá las siguientes características:

Básicamente será un Chat dedicado a cada Provincia.

TODO: Falta describir las características del ejemplo

#### Creación de Entity

Crearemos una nueva clase Entity para el chat en nuestro proyecto:

TODO: Falta escribir un ejemplo

#### Creación de FormType (opcional)

TODO: Falta escribir un ejemplo (aunque el FormType es opcional)

#### Configuración

Ahora indicamos en el fichero de Configuración (config.yml) nuestro nuevo tipo de Chat.

```
sopinet_chat:
    ...
    extra_type_chat:
        customprovince:
            class: 'AppBundle\Entity\ChatCustomProvince'
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
