{
	response: "ERROR",
	body: {
		{if="isset($error) && ! empty($error)"}error: "{$error}",{/if},
		error_nick: {if="$error_nick"}true{else}false{/if},
		error_email: {if="$error_email"}true{else}false{/if},
		error_password: {if="$error_password"}true{else}false{/if},
		error_c_password: {if="$error_c_password"}true{else}false{/if},
		error_captcha: {if="$error_captcha"}true{else}false{/if} 
	}
}