# ¿Qué es?:

- ChatBundle permite la gestión del envío de mensajes en Backend a un grupo de Dispositivos, pertenecientes a una serie de Usuarios.
- El sistema permite disponer de distintos tipos de mensajes. Estos mensajes permiten ser asociados a un Chat (grupo de Usuarios "estático"), o bien, ser asociados a un conjunto de Usuarios dinámico, a partir de unas reglas.
- El sistema permite abstraernos de todo el control de envío de notificaciones en Android (GCM), reenvío en iOS (APNs), administración de los mensajes, dispositivos, y paquetes de mensajes en Sonata.
- El sistema es áltamente escalable y permite la creación de otros tipos de Mensajes fácilmente desde fuera del sistema.

# Instalación

- [Requisitos previos (lectura opcional)](Resources/doc/es/0-previous_requirements.md)
- [Cómo instalar y configurar el Bundle](Resources/doc/es/1-setting_up_the_bundle.md)

# Utilización

- [Utilización básica de API](Resources/doc/es/2-basic_use.md)

# Extras

- [Integración con SonataAdmin](Resources/doc/es/extras/integrate_sonata.md)

# Utilización avanzada

- [Utilizando el bundle desde el Servicio InterfaceHelper](Resources/doc/es/3-advance_use_service.md)
- [Tipos de Mensajes](4-advance_use_messages.md)

# Servicios en Background

Hay una serie de servicios añadidos en Background que se pueden habilitar en el sistema,

- [Comprobación de Usuario Online](Resources/doc/es/background/check_user_state.md)
- [WebSocket](Resources/doc/es/background/websocket.md)
- [RabbitMQ](Resources/doc/es/background/rabbitMQ.md)
