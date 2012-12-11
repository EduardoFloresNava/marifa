[
	{loop="$sucesos"}
	{
		"id": {$value.id},
		"html": {$value.html}
	}{if="$key < count($sucesos) - 1"},{/if}
	{/loop}
]