# ¿Qué es?:

- ChatBundle permite la gestión del envío de mensajes en Backend a un grupo de Dispositivos, pertenecientes a una serie de Usuarios.
- El sistema permite disponer de distintos tipos de mensajes. Estos mensajes permiten ser asociados a un Chat (grupo de Usuarios "estático"), o bien, ser asociados a un conjunto de Usuarios dinámico, a partir de unas reglas.
- El sistema permite abstraernos de todo el control de envío de notificaciones en Android (GCM), reenvío en iOS (APNs), administración de los mensajes, dispositivos, y paquetes de mensajes en Sonata.
- El sistema es áltamente escalable y permite la creación de otros tipos de Mensajes fácilmente desde fuera del sistema.

# Instalación

- [Cómo instalar y configurar el Bundle](Resources/doc/1-setting_up_the_bundle.md)

# Utilización

- Utilización básica de API: - Link
- Compatibilidad con SonataAdmin: - Link

# Extras

- Integración con Sonata

# Utilización avanzada

- Utilizando el bundle desde el Servicio InterfaceHelper: Link
- Tipos de Mensajes: Link

# Servicios en Background

Hay una serie de servicios añadidos en Background que se pueden habilitar en el sistema,

- Comprobación de Usuario Online: Link
- WebSocket: Link
- RabbitMQ: Link 
