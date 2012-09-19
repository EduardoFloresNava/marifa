<?php

$sql = array();

$sql[] = 'CREATE TABLE `foto_comentario` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `foto_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `comentario` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `foto` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `creacion` DATETIME NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` MEDIUMTEXT NOT NULL,
  `url` VARCHAR(300) NULL DEFAULT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  `ultima_visita` DATETIME NULL DEFAULT NULL,
  `visitas` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `foto_voto` (
  `foto_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`foto_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `post_categoria` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `seo` VARCHAR(50) NOT NULL,
  `imagen` VARCHAR(32) NOT NULL DEFAULT \'comments.png\',
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `post_comentario` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `fecha` DATETIME NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `post_denuncia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `motivo` INTEGER NOT NULL,
  `comentario` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `post_favorito` (
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`post_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `post` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `post_categoria_id` INTEGER NOT NULL,
  `comunidad_id` INTEGER NULL DEFAULT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `vistas` INTEGER NOT NULL DEFAULT 0,
  `privado` bit NOT NULL DEFAULT 0,
  `sponsored` bit NOT NULL DEFAULT 0,
  `sticky` bit NOT NULL DEFAULT 0,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `post_punto` (
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `cantidad` INTEGER NOT NULL DEFAULT 1,
  PRIMARY KEY (`post_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `usuario_aviso` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `moderador_id` INTEGER NOT NULL,
  `asunto` VARCHAR(50) NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario_bloqueo` (
  `usuario_id` INTEGER NOT NULL,
  `bloqueado_id` INTEGER NOT NULL,
  PRIMARY KEY (`usuario_id`, `bloqueado_id`)
);';

$sql[] = 'CREATE TABLE `usuario_seguidor` (
  `usuario_id` INTEGER NOT NULL,
  `seguidor_id` INTEGER NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`usuario_id`, `seguidor_id`)
);';

$sql[] = 'CREATE TABLE `mensaje` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `emisor_id` INTEGER NOT NULL,
  `receptor_id` INTEGER NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  `asunto` VARCHAR(200) NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `padre_id` INTEGER NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `nick` VARCHAR(16) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `rango` INTEGER NOT NULL DEFAULT 3,
  `puntos` INTEGER NOT NULL DEFAULT 10,
  `puntos_disponibles` INTEGER NOT NULL DEFAULT 10,
  `registro` DATETIME NOT NULL,
  `lastlogin` DATETIME NOT NULL,
  `lastactive` DATETIME NOT NULL,
  `lastip` INTEGER(11) NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario_nick` (
  `usuario_id` INTEGER NOT NULL,
  `nick` VARCHAR(16) NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`usuario_id`, `nick`, `fecha`),
KEY (`usuario_id`)
);';

$sql[] = 'CREATE TABLE `usuario_rango` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(32) NOT NULL,
  `color` INTEGER NOT NULL,
  `imagen` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `session` (
  `id` VARCHAR(32) NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `ip` INTEGER NOT NULL,
  `expira` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario_suspencion` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `moderador_id` INTEGER NOT NULL,
  `motivo` MEDIUMTEXT NOT NULL,
  `inicio` DATETIME NOT NULL,
  `fin` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `afiliados` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(35) NOT NULL,
  `url` VARCHAR(200) NOT NULL,
  `banner` VARCHAR(100) NOT NULL,
  `descripcion` VARCHAR(200) NOT NULL,
  `ingresos` INTEGER NOT NULL DEFAULT 0,
  `salidas` INTEGER NOT NULL DEFAULT 0,
  `fecha` DATETIME NOT NULL,
  `activo` bit NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario_baneo` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `moderador_id` INTEGER NOT NULL,
  `tipo` INTEGER NOT NULL,
  `razon` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `configuracion` (
  `clave` VARCHAR(100) NOT NULL,
  `valor` MEDIUMTEXT NULL DEFAULT NULL,
  `defecto` MEDIUMTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`clave`)
);';

$sql[] = 'CREATE TABLE `usuario_recuperacion` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `hash` VARCHAR(32) NOT NULL,
  `fecha` DATETIME NOT NULL,
  `tipo` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `denuncia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `objeto_id` INTEGER NOT NULL,
  `tipo` INTEGER NOT NULL,
  `motivo` INTEGER NOT NULL,
  `comentario` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `noticia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `usuario_perfil` (
  `usuario_id` INTEGER NOT NULL,
  `campo` VARCHAR(50) NOT NULL,
  `valor` MEDIUMTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
);';

$sql[] = 'CREATE TABLE `foto_favorito` (
  `foto_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`foto_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `post_tag` (
  `post_id` INTEGER NOT NULL,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`post_id`, `nombre`)
);';

$sql[] = 'CREATE TABLE `post_seguidor` (
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`post_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `post_compartido` (
  `post_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`post_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `comunidad` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` MEDIUMTEXT NOT NULL,
  `comunidad_categoria_id` INTEGER NOT NULL,
  `foto` VARCHAR(200) NOT NULL,
  `cabecera` VARCHAR(200) NOT NULL,
  `oficial` bit NOT NULL DEFAULT 0,
  `fundacion` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `comunidad_categoria` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `seo` VARCHAR(50) NOT NULL,
  `imagen` VARCHAR(32) NULL DEFAULT \'comments.png\',
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `comunidad_miembro` (
  `comunidad_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `rango` INTEGER NOT NULL,
  PRIMARY KEY (`comunidad_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `comunidad_comentario` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `comunidad_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `contenido` MEDIUMTEXT NOT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `comunidad_seguidor` (
  `comunidad_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`comunidad_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `usuario_rango_permiso` (
  `rango_id` INTEGER NOT NULL,
  `permiso` INTEGER NOT NULL,
  PRIMARY KEY (`rango_id`, `permiso`)
);';

$sql[] = 'CREATE TABLE `usuario_visita` (
  `usuario_id` INTEGER NOT NULL,
  `visitado_id` INTEGER NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`usuario_id`, `visitado_id`, `fecha`)
);';

$sql[] = 'CREATE TABLE `suceso` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `objeto_id` INTEGER NOT NULL,
  `objeto_id1` INTEGER NULL DEFAULT NULL,
  `objeto_id2` INTEGER NULL DEFAULT NULL,
  `tipo` VARCHAR(50) NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `suceso_administracion` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `usuario_id` INTEGER NOT NULL,
  `objeto_id` INTEGER NOT NULL,
  `objeto_id1` INTEGER NULL DEFAULT NULL,
  `objeto_id2` INTEGER NULL DEFAULT NULL,
  `tipo` VARCHAR(50) NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
);';

$sql[] = 'CREATE TABLE `suceso_visto` (
  `suceso_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`suceso_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `suceso_administracion_visto` (
  `suceso_administracion_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  PRIMARY KEY (`suceso_administracion_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `post_comentario_voto` (
  `post_comentario_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `cantidad` INTEGER NOT NULL DEFAULT 1,
  PRIMARY KEY (`post_comentario_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `comunidad_comentario_voto` (
  `comunidad_comentario_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `cantidad` INTEGER NOT NULL,
  PRIMARY KEY (`comunidad_comentario_id`, `usuario_id`)
);';

$sql[] = 'CREATE TABLE `usuario_configuracion` (
  `usuario_id` INTEGER NOT NULL,
  `clave` VARCHAR(80) NOT NULL,
  `valor` MEDIUMTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
);';

// CLAVES FORANEAS.
$sql_fk[] = 'ALTER TABLE `foto_comentario` ADD FOREIGN KEY (foto_id) REFERENCES `foto` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto_comentario` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto_voto` ADD FOREIGN KEY (foto_id) REFERENCES `foto` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto_voto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_comentario` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_comentario` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_denuncia` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_denuncia` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_favorito` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_favorito` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post` ADD FOREIGN KEY (post_categoria_id) REFERENCES `post_categoria` (`id`);';
$sql_fk[] = 'ALTER TABLE `post` ADD FOREIGN KEY (comunidad_id) REFERENCES `comunidad` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_punto` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_punto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_aviso` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_aviso` ADD FOREIGN KEY (moderador_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_bloqueo` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_bloqueo` ADD FOREIGN KEY (bloqueado_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_seguidor` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_seguidor` ADD FOREIGN KEY (seguidor_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `mensaje` ADD FOREIGN KEY (emisor_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `mensaje` ADD FOREIGN KEY (receptor_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `mensaje` ADD FOREIGN KEY (padre_id) REFERENCES `mensaje` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario` ADD FOREIGN KEY (rango) REFERENCES `usuario_rango` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_nick` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `session` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_suspencion` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_suspencion` ADD FOREIGN KEY (moderador_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_baneo` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_baneo` ADD FOREIGN KEY (moderador_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_recuperacion` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `denuncia` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `noticia` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_perfil` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto_favorito` ADD FOREIGN KEY (foto_id) REFERENCES `foto` (`id`);';
$sql_fk[] = 'ALTER TABLE `foto_favorito` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_tag` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_seguidor` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_seguidor` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_compartido` ADD FOREIGN KEY (post_id) REFERENCES `post` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_compartido` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad` ADD FOREIGN KEY (comunidad_categoria_id) REFERENCES `comunidad_categoria` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_miembro` ADD FOREIGN KEY (comunidad_id) REFERENCES `comunidad` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_miembro` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_comentario` ADD FOREIGN KEY (comunidad_id) REFERENCES `comunidad` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_comentario` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_seguidor` ADD FOREIGN KEY (comunidad_id) REFERENCES `comunidad` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_seguidor` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_rango_permiso` ADD FOREIGN KEY (rango_id) REFERENCES `usuario_rango` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_visita` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_visita` ADD FOREIGN KEY (visitado_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso_administracion` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso_visto` ADD FOREIGN KEY (suceso_id) REFERENCES `suceso` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso_visto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso_administracion_visto` ADD FOREIGN KEY (suceso_administracion_id) REFERENCES `suceso_administracion` (`id`);';
$sql_fk[] = 'ALTER TABLE `suceso_administracion_visto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_comentario_voto` ADD FOREIGN KEY (post_comentario_id) REFERENCES `post_comentario` (`id`);';
$sql_fk[] = 'ALTER TABLE `post_comentario_voto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_comentario_voto` ADD FOREIGN KEY (comunidad_comentario_id) REFERENCES `comunidad_comentario` (`id`);';
$sql_fk[] = 'ALTER TABLE `comunidad_comentario_voto` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';
$sql_fk[] = 'ALTER TABLE `usuario_configuracion` ADD FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`);';

// Ejecutamos insercion de tablas.
foreach ($sql as $t)
{
	if (Database::get_instance()->update($t))
	{
		preg_match('/CREATE TABLE `(.*)`/', $t, $m);
		throw new Database_Exception("No se pudo crear la tabla '{$m[1]}'", 22);
	}
}

// Ejecutamos modificaciones claves foraneas.
foreach ($sql_fk as $t)
{
	if (Database::get_instance()->update($t))
	{
		preg_match('/ALTER TABLE `(.*)`/', $t, $m);
		throw new Database_Exception("Error al ejecutar la alteración de la tabla '{$m[1]}' con $t", 22);
	}
}

return TRUE;