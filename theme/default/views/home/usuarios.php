<h2 class="title">Usuarios en la comunidad</h2>
<div class="user-wall">
	{loop="$usuarios"}
	<div class="user {if="$value.online"}online{else}offline{/if}">
		<img src="{function="Utils::get_gravatar($value.email, 130, 130)"}" />
		<h3 >{$value.nick}</h3>
	</div>
	{/loop}
</div>