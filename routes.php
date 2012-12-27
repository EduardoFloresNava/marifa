<?php defined('APP_BASE') || die('No direct access allowed.');

/**
 * Sobreescritura de rutas.
 *
 * Cada elemento debe ser un arreglo el cual puede contener 3 parámetros.
 *  - El primero es la URL que se desea hacer coincidir la cual DEBE EMPEZAR con /.
 *      - La URL puede tener campos variables, estos campos comienzan con : y son seguido de una cadena de texto que representa su nombre.
 *        Por ejemplo: /post/:pagina, que va a coincidir con /post/1 o /post/a
 *        Un nombre especial es :action que se mapea directamente como la acción del controlador.
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
 *     - params_map: Este arreglo permite especificar la posición donde será colocado un parámetro.
 *                Es un arreglo donde deben ponerse en orden los nombres de los parámetros o NULL para uno vacio.
 *                Por ejemplo: array('/post/categoria/:categoria', array('controller' => 'home', 'action' => 'index'), array('params_map' => array(NULL, 'categoria')))
 *                    Mapea :categoria al 2do parámetro en lugar del primero, siendo equivalente a /home/index//:categoria (en caso de no estar params_map sería /home/index/:categoria/
 *                    Como se puso NULL como primer elemento, se toma un argumento vacio.
 *                Otro ejemplo: array('/post/categoria/:categoria/:pagina', array('controller' => 'home', 'action' => 'index'), array('params_map' => array('pagina', 'categoria')))
 *                    Donde mapea :categoria al 2do y :pagina al 1ro, siendo equivalente a /home/index/:pagina/:categoria (en caso de no estar params_map sería /home/index/:categoria/:params
 *
 *
 *  Algo importante a destacar es que el orden de las rutas es importante ya que se van verificando en orden una a una. Luego se procesan las de los plugins.
 */

return array(
	array('/login/?', array('controller' => 'usuario', 'action' => 'login')), // Atajo al login.
	array('/logout/?', array('controller' => 'usuario', 'action' => 'logout')), // Atajo al logout.
	array('/register/?', array('controller' => 'usuario', 'action' => 'register')), // Atajo al registro.

	// Rutas para los posts.
	array('/post/:pagina/?', array('controller' => 'home', 'action' => 'index'), array('filters' => array('pagina' => '(\d+)'))),
	array('/post/categoria/:categoria/?:pagina?', array('controller' => 'home', 'action' => 'index'), array('params_map' => array('pagina', 'categoria'))), // Atajo a las categorias.
	array('/post/:categoria/:id/(:nombre).html', array('controller' => 'post', 'action' => 'index'), array('params_map' => array('id'))), // URL a una foto.

	// Rutas para las fotos.
	array('/foto/:pagina/?', array('controller' => 'foto', 'action' => 'index'), array('filters' => array('pagina' => '(\d+)'))),
	array('/foto/categoria/:categoria/?:pagina?', array('controller' => 'foto', 'action' => 'index'), array('params_map' => array('pagina', 'categoria'))), // Atajo a las categorias.
	array('/foto/:categoria/:id/(:nombre).html', array('controller' => 'foto', 'action' => 'ver'), array('params_map' => array('id'))), // URL a una foto.

	array('/\@:usuario/?:action?/?:pagina?/?:pagina_2?', array('controller' => 'perfil', 'action' => 'index')), // Atajo al perfil del usuario.
);