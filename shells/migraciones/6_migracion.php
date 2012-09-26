<?php
/**
 * Modificaciones para implementar la administración y moderación de posts.
 */

// Creamos tabla para eventos de moderación y administración de posts.
Database::get_instance()->update('
CREATE TABLE `post_moderado` (
  `post_id` INTEGER NULL DEFAULT NULL,
  `usuario_id` INTEGER NOT NULL,
  `tipo` INTEGER NOT NULL,
  `padre_id` INTEGER NULL DEFAULT NULL,
  `razon` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  FOREIGN KEY (usuario_id) REFERENCES `usuario` (`id`),
  FOREIGN KEY (post_id) REFERENCES `post` (`id`),
  FOREIGN KEY (padre_id) REFERENCES `post` (`id`)
);');

/**
 * usuario_id: ID del moderador que realiza la acción.
 * post_id:    POST que es moderado.
 * tipo:       TIPO de advertencia. Por ejemplo: spam.
 * padre_id:   Post que se crea para que el usuario revise.
 *             Si el post debe ser editado, se crea otro como borrador. El ID de
 *             ese borrador es el colocadó aquí.
 * razon:      En caso de ser un motivo no especificado, se colocá un texto para explicar.
 */