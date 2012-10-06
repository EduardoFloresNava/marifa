<?php
/**
 * Modificaciones para implementar denuncias en fotos y usuarios.
 * Además otras pequeñas para la moderación y limpieza.
 */

// Creamos la tabla para denunciar fotos.
Database::get_instance()->update('
CREATE TABLE `foto_denuncia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `foto_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `motivo` INTEGER NOT NULL,
  `comentario` MEDIUMTEXT NULL DEFAULT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);');

/**
 * id:         ID de la denuncia.
 * foto_id:    ID de la foto denunciada.
 * usuario_id: ID del usuario que denuncia la foto.
 * motivo:     Constante que representa el motivo. Se encuentra tabulado en el modelo.
 * comentario: Si es un motivo personalizado un texto para indicar la razón.
 * fecha:      Fecha en la cual se realiza la denuncia.
 * estado:     Estado en el que se encuentra la denuncia.
 */

// Creamos la tabla para denunciar usuarios.
Database::get_instance()->update('
CREATE TABLE `usuario_denuncia` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `denunciado_id` INTEGER NOT NULL,
  `usuario_id` INTEGER NOT NULL,
  `motivo` INTEGER NOT NULL,
  `comentario` MEDIUMTEXT NULL DEFAULT NULL,
  `fecha` DATETIME NOT NULL,
  `estado` INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);');

/**
 * id:            ID de la denuncia.
 * denunciado_id: ID del usuario denunciado.
 * usuario_id:    ID del usuario que denuncia al usuario.
 * motivo:        Constante que representa el motivo. Se encuentra tabulado en el modelo.
 * comentario:    Si es un motivo personalizado un texto para indicar la razón.
 * fecha:         Fecha en la cual se realiza la denuncia.
 * estado:        Estado en el que se encuentra la denuncia.
 */
