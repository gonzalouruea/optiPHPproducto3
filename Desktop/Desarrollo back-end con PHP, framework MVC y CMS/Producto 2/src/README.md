# optiPHP

En este proyecto tendremos las funcionalidades basicas de :
Registro-Login: sistemas de alta del usuario en el sistema y de acceso (una vez dado de alta) a la aplicación web. En los casos que no se pueda dar de alta o acceder, el sistema debe mostrar los mensajes de error correspondientes. 

El sistema muestra menús distintos en función de si eres cliente particular, un cliente corporativo o administrador.

Panel administración: una vez se accede como administrador, tendrá acceso al Panel Administración donde podrá realizar reservas. En este apartado primero se elige sobre el tipo de reserva: solo  trayecto de aeropuerto a hotel, de hotel a aeropuerto o trayectos de ida y vuelta. Si se elige un trayecto de aeropuerto a hotel se solicita día de llegada, hora de llegada, número del vuelo, aeropuerto de origen. Si se solicita el de hotel a aeropuerto, se solicitará: día del vuelo, hora del vuelo, número de vuelo y hora de recogida. En casos de ambos trayectos, pues se solicita los dos casos anteriores. Después tendremos que solicitar el hotel de destino/recogida. Se suponen que los trayectos sólo pueden ser hotel->aeropuerto o aeropuerto->hotel. También debemos solicitar el número de viajeros y los datos personales de quien realiza la reserva si no han sido entrados anteriormente (se identifican por el email). Al realizar la reserva, se asignará un localizador que debe ser único y se le enviará un mail al cliente con el localizador y la información de la reserva. El administrador puede modificar o cancelar la reserva en cualquier momento.

El administrador podrá consultar una vista por semana, por día y por mes de los trayectos que han de realizar. Los trayectos se debe mostrar en formato calendario.  Cuando se accede a un trayecto se muestra la información detallada con todos los campos entrados.

Panel usuario particular: el usuario puede crear un cuenta donde aparecerán todas las reservas realizadas a su email. Además, ellos también pueden realizar reservas de la misma forma que el administrador. Ojo, los usuarios han de reservar con un mínimo de 48 horas y no pueden ni modificar ni cancelar la reserva en ese periodo. También tienen un apartado con sus datos personales que pueden modificar si se considera oportuno.

Ademas hay que ajustarse a la rubrica 
Registro-Login:  El registro y el login funcionan correctamente y dan los mensajes de error pertinentes.

Panel administración: Se accede al panel y crear nuevas reservas Se pueden configurar los datos de las reservas. Además se añade, modifica y elimina reservas, vehiculos y destinos. Se visualizan las reservas y se puede elegir la vista semanal, mensual o diaria del calendario.

Panel principal: El cliente particular puede ver sus reservas y distinguir entre las realizadas personalmente o por el admin.

Perfil: Se puede modificar el nombre del usuario, correo electrónico, contraseña y se guardan los cambios correctamente.