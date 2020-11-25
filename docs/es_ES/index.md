# Shutter Management Plugin

# Description

Este complemento facilita la gestión de la posición de sus persianas según la posición del sol. Este complemento funciona completamente localmente y no requiere una conexión externa.

Puedes encontrar [aquí](https://www.jeedom.com/blog/?p=4310) un artículo que muestra un ejemplo de configuración del complemento.

# Configuración de complementos

Nada especial aquí solo para instalar y activar el complemento.

## Como funciona ?

El complemento ajustará la posición de sus persianas en relación con las posiciones del sol (acimut y altitud) según la condición.

# Configuración de las persianas

La configuración se divide en varias pestañas :

## Equipement

Encontrarás en la primera pestaña toda la configuración de tu equipo :

- **Nombre del equipo** : nombre de su equipo.
- **Objeto padre** : indica el objeto padre al que pertenece el equipo.
- **Categoría** : le permite elegir la categoría de su equipo.
- **Activar** : activa su equipo.
- **Visible** : hace que su equipo sea visible en el tablero.

## Configuration

### Configuration

- **Verificación** : frecuencia de verificación de las condiciones y posición de las aletas.
- **Recuperar el control** : prohíbe que el sistema de gestión del obturador cambie su posición si se ha movido manualmente. Ejemplo : el sistema cierra la persiana, tú la abres, ya no la tocará hasta que se active el comando "Reanudar gestión" o si ha pasado el tiempo para tomar el control.
- **Latitud** : la latitud de tu persiana / casa.
- **Longitud** : la longitud de tu persiana / casa.
- **Altitud** : la altura de tu persiana / casa.
- **Estado del obturador** : comando que indica la posición actual de la persiana.
- **Posición del obturador** : control para posicionar la aleta.
- **Actualizar la posición del obturador (opcional)** : comando para actualizar la posición del obturador.
- **Tiempo maximo para un viaje** : tiempo para hacer un movimiento completo (arriba y abajo o arriba y abajo) en segundos.

## Condition

- **Condición para la acción** : si esta condición no es cierta, el complemento no modificará la posición del panel.
- **El cambio de modo cancela las suspensiones pendientes** : si está marcada, un cambio de modo del obturador lo devuelve a la gestión automática.
- **Las acciones inmediatas son sistemáticas y prioritarias** : si está marcado, las acciones inmediatas se ejecutan incluso si está suspendido y sin tener en cuenta el orden de las condiciones.

La tabla de condiciones le permite especificar condiciones de posicionamiento específicas, que se apoderan de la tabla de posición de la aleta :
- **Posición** : si la condición es verdadera, la posición del obturador.
- **Modo** : la condición solo funciona si el obturador está en este modo (puede poner varios separados por comas ``,``). Si este campo no se completa, la condición se probará sea cual sea el modo.

>**Importante**
>
>Estamos hablando del modo de obturador aquí, NO TIENE NADA QUE VER con el complemento de modo

- **Acción inmediata** : actúa inmediatamente tan pronto como la condición es verdadera (por lo tanto, no espera la verificación cron).
- **Suspender** : si la condición es verdadera, suspende la gestión automática de la persiana.
- **Condición** : su condicion.
- **Comentario** : campos libres para comentarios.

## Positionnement

- **% apertura** : el% cuando el obturador está abierto.
- **% de cierre** : el% cuando el obturador está cerrado.
- **Acción por defecto** : la acción predeterminada si ninguna condición y posición es válida.

Aquí es donde podrá gestionar la posición del obturador de acuerdo con la posición del sol.

- **Acimut** : ángulo de posición del sol.
- **Elevacion** : ángulo de altura del sol.
- **Posición** : posición del obturador para tomar si el sol está en el Azimut y los límites de elevación.
- **Condición** : condición adicional para satisfacer para que el obturador tome esta posición (puede estar vacío).
- **Comentario** : campos libres para comentarios.

>**CONSEJO**
>
>Pequeño consejo del sitio [suncalc.org](https://www.suncalc.org) permite, una vez ingresada su dirección, ver la posición del sol (y por lo tanto los ángulos Azimut y elevación) según las horas del día (solo arrastre el pequeño sol en la parte superior).

## Planning

Aquí puede ver los planes de posicionamiento de las persianas realizados en la planificación de la Agenda.

## Commandes

- **Azimut del sol** : ángulo azimutal actual del sol.
- **Salida del sol** : ángulo de elevación actual del sol.
- **Acción de la fuerza** : obliga a calcular la posición del obturador de acuerdo con la posición del sol y las condiciones y le aplica el resultado independientemente del estado de gestión (en pausa o no)).
- **Última posición** : última posición solicitada al obturador por el complemento.
- **Estado de gestión** : estado de gestión (suspendido o no)).
- **Reanudar** : obliga a la gestión a volver al modo automático (tenga en cuenta que es este comando el que debe ejecutarse para volver a la gestión automática si ha modificado la posición de su persiana manualmente y ha marcado la casilla "No recuperar el control").
- **Suspender** : suspende el posicionamiento automático del obturador.
- **Refrescar** : actualizar los valores de los comandos "Acimut del sol" y "Elevación del sol"".
- **Modo** : modo de obturador actual.

Puede agregar comandos de "modo", el nombre del comando será el nombre del modo.

# Panel

El complemento tiene un panel de administración para escritorio y móvil. Para activarlo, simplemente vaya a Complementos → Administración de complementos, haga clic en el complemento de administración del panel y, en la parte inferior derecha, marque las casillas para mostrar los paneles de escritorio y móviles.
