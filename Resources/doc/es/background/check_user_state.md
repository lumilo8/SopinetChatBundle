## Comprobación de Usuario Online

Hay una función disponible en UserHelper llamada: doPing.
Dicha función permite indicar a chatBundle que un usuario está Online en este momento.
La idea es que la función se llame cada vez que el usuario hace algún tipo de petición a la aplicación.

Se puede llamar a dicha función así:
```
    /** @var UserHelper $userHelper */
    $userHelper = $this->container->get('sopinet_chatbundle_userhelper');
    $userHelper->doPing($user);
```

Por otro lado, dispondremos de un comando: "sopinet:chatBundle:checkUserState", que nos permitirá comprobar que no hace más de X segundos que el usuario hizo el último Ping, si hace más de X segundos, el sistema marcará al usuario como Offline y notificará a todos los demás usuarios que tenían iniciados con él un Chat.

Se puede activar el comando "sopinet:chatBundle:checkUserState" de forma recursiva con una nueva tarea cron (crontab -e), del tipo:
```
	* * * * * php /var/www/symfony-project/app/console sopinet:chatBundle:checkUserState 60
```

[Volver al índice](README.md)
