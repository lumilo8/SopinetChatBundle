# Utilización básica

El Bundle dispone de un Controlador básico, con una serie de funciones básicas implementadas, la documentación de dichas funciones estará
automáticamente disponible en api/doc. Cabe señalar que no se indica en la documentación de dichas funciones los parámetros de login
pues estos quedan desacoplados a través del servicio sopinet_login_helper.

## Instalación:
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

