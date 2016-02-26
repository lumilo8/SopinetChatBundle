# SonataAdmin

El Bundle también incluye por defecto la carga de toda la gestión de entidades a través de Sonata, para incluirla basta con el grupo "SopinetChat"
en tu config.yml, en la parte de SonataAdmin, algo como:

```
sonata_admin:
    dashboard:
        blocks:
            - { position: left, type: sonata.admin.block.admin_list, settings: { groups: [SopinetChat] } }
```

[Volver al índice](README.md)
