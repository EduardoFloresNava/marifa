<?php
$filter = $this->getFilter();
$show = $filter->message('spoiler');

$counter = rand();
$click  = "document.getElementById('spoilerContent-". $counter ."').style.display = (document.getElementById('spoilerContent-". $counter ."').style.display == 'block' ? 'none' : 'block');";
$click .= "this.className = (document.getElementById('spoilerContent-". $counter ."').style.display == 'block' ? 'btn decoda-spoilerButton active' : 'btn decoda-spoilerButton');"; ?>

<div class="decoda-spoiler">
	<button class="btn decoda-spoilerButton" type="button" onclick="<?php echo $click; ?>"><?php echo $show; ?></button>

	<div class="decoda-spoilerContent" id="spoilerContent-<?php echo $counter; ?>" style="display: none">
		<?php echo $content; ?>
	</div>
</div>