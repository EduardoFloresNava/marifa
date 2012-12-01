<?php defined('APP_BASE') || die('No direct access allowed.');

/**
 * Sobreescritura de rutas.
 *
 * Cada elemento debe ser un arreglo el cual puede contener 3 parámetros.
 *  - El primero es la URL que se desea hacer coincidir la cual DEBE EMPEZAR con /.
 *  - El segundo puede ser un arreglo o una cadena de caracteres.
 *      - En caso de especificar una cadena de caracteres, la URL se transforma en un alias de la otra.
 *        Por ejemplo:
 *             array('/login', '/usuario/login')
 *        Hace que llamar a /login sea equivalente a llamar a /usuario/login.
 *      - En el caso de espefificar un arreglo, esté debe tener los siguientes parámetros:
 *          - controller (requerido): Nombre del controlador que se quiere llamar.
 *          - action [requerido]: Nombre de la acción a ejecutar.
 *          - directory [opcional]: Si hay que usar un directorio. (Los plugins no soportan directorios.
 *          - plugin [opcional]: Realiza la llamada en un plugin. No poner si no es necesario.
 *            Si se especifica un plugin no se toma en cuenta directory.
 *        Por ejemplo:
 *             array('/login', array('controller' => 'usuario', 'action' => 'login'))
 *        El cual hace lo mismo que el anterior solo que esta forma es preferida por gastar menos recursos.
 *
 *  - El tercer parámetro es un arreglo opcional. Es un arreglo que puede contener alguna de las siguientes propiedades.
 *     - methods: Cadena que especifica lo métodos HTTP a los que responde, las opciones son PUT, GET, POST y DELETE.
 *                Para especificar varios se han de separar con comas, por ejemplo: 'PUT,POST'. Si no se especifica son todos.
 *     - name: Nombre de la ruta, actualmente no se utiliza.
 *     - filters: Arreglo que especifica el listado de expresiones regulares que debe satisfacer cada variable.
 *                Es un arreglo asociativo donde la clave es el nombre de la variable y el valor la expresión regular.
 *                Por ejemplo: array('/post/:id/', array('controller' => 'post', 'action' => 'index'), array('filters' => array('id' => '(\d+)')))
 *                Donde se especifica que id debe ser un número.
 *
 *
 *  Algo importante a destacar es que el orden de las rutas es importante ya que se van verificando en orden una a una. Luego se procesan las de los plugins.
 */

return array(
	array('/login', array('controller' => 'usuario', 'action' => 'login')), // Atajo al login.
	array('/logout', array('controller' => 'usuario', 'action' => 'logout')), // Atajo al logout.
	array('/register/', array('controller' => 'usuario', 'action' => 'register')), // Atajo al registro.
	//array('/perfil/:usuario/', array('controller' => 'perfil', 'action' => 'index'), array('filters' => array('usuario' => '()')), // Atajo al perfil del usuario.
);