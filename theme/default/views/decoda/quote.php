<blockquote>
	<?php echo $content; ?>
	<?php if (!empty($author)) { ?>
	<small><?php echo $this->getFilter()->message('quoteBy', array(
						'author' => htmlentities($author, ENT_NOQUOTES, 'UTF-8')
					)); ?></small>
	<?php } ?>
</blockquote>