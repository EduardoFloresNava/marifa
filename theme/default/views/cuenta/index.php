<div class="row">
	<div class="span10">
		<form method="POST" class="form-horizontal" action="">

			{loop="$error"}
			<div class="alert">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Error: </strong>{$value}
			</div>
			{/loop}
			{if="isset($success)"}
			<div class="alert alert-success">
				<a class="close" data-dismiss="alert">×</a>
				<strong>Felicitaciones: </strong>{$success}
			</div>
			{/if}

			<div class="control-group{if="$estado_email == -1"} error{elseif="$estado_email == 1"} success{/if}">
				<label class="control-label" for="email">E-Mail</label>
				<div class="controls">
					<input type="text" id="nick" name="email" value="{$email}" />
					<p class="help-inline">E-Mail al cual asociar su cuenta. En caso de editarlo deber&aacute; validar la cuenta.</p>
				</div>
			</div>

			<div class="control-group{if="$estado_origen == -1"} error{elseif="$estado_origen== 1"} success{/if}">
				<label class="control-label" for="origen">Lugar de origen</label>
				<div class="controls">
					<select id="origen" name="origen">
						<option value="">Seleccione una opci&oacute;n</option>
						{loop="$paices"}
							<optgroup label="{$value.0|htmlentities:ENT_NOQUOTES,"UTF-8"}">{$aux_rr=$key}
							{loop="$value.1"}
								<option value="{$aux_rr}.{$key}"{if="$origen == $aux_rr.'.'.$key"} selected="selected"{/if}>{$value|htmlentities:ENT_NOQUOTES,"UTF-8"}</option>
							{/loop}
							</optgroup>
						{/loop}
					</select>
					<p class="help-inline">Su lugar de origen.</p>
				</div>
			</div>

			<div class="control-group{if="$estado_sexo == -1"} error{elseif="$estado_sexo == 1"} success{/if}">
				<label class="control-label" for="sexo">Sexo</label>
				<div class="controls">
					<select id="sexo" name="sexo">
						<option value=""{if="$sexo != 'f' && $sexo != 'm'"} selected="selected"{/if}>Seleccione una opci&oacute;n</option>
						<option value="f"{if="$sexo == 'f'"} selected="selected"{/if}>Femenino</option>
						<option value="m"{if="$sexo == 'm'"} selected="selected"{/if}>Masculino</option>
					</select>
				</div>
			</div>

			<div class="control-group{if="$estado_nacimiento == -1"} error{elseif="$estado_nacimiento == 1"} success{/if}">
				<label class="control-label" for="dia">Nacimiento</label>
				<div class="controls controls-row">
					<select class="span1" id="dia" name="dia">
						<option value="">-- D&iacute;a --</option>
						<option value="1"{if="isset($nacimiento.2) && $nacimiento.2 == 1"} selected="selected"{/if}>1</option>
						<option value="2"{if="isset($nacimiento.2) && $nacimiento.2 == 2"} selected="selected"{/if}>2</option>
						<option value="3"{if="isset($nacimiento.2) && $nacimiento.2 == 3"} selected="selected"{/if}>3</option>
						<option value="4"{if="isset($nacimiento.2) && $nacimiento.2 == 4"} selected="selected"{/if}>4</option>
						<option value="5"{if="isset($nacimiento.2) && $nacimiento.2 == 5"} selected="selected"{/if}>5</option>
						<option value="6"{if="isset($nacimiento.2) && $nacimiento.2 == 6"} selected="selected"{/if}>6</option>
						<option value="7"{if="isset($nacimiento.2) && $nacimiento.2 == 7"} selected="selected"{/if}>7</option>
						<option value="8"{if="isset($nacimiento.2) && $nacimiento.2 == 8"} selected="selected"{/if}>8</option>
						<option value="9"{if="isset($nacimiento.2) && $nacimiento.2 == 9"} selected="selected"{/if}>9</option>
						<option value="10"{if="isset($nacimiento.2) && $nacimiento.2 == 10"} selected="selected"{/if}>10</option>
						<option value="11"{if="isset($nacimiento.2) && $nacimiento.2 == 11"} selected="selected"{/if}>11</option>
						<option value="12"{if="isset($nacimiento.2) && $nacimiento.2 == 12"} selected="selected"{/if}>12</option>
						<option value="13"{if="isset($nacimiento.2) && $nacimiento.2 == 13"} selected="selected"{/if}>13</option>
						<option value="14"{if="isset($nacimiento.2) && $nacimiento.2 == 14"} selected="selected"{/if}>14</option>
						<option value="15"{if="isset($nacimiento.2) && $nacimiento.2 == 15"} selected="selected"{/if}>15</option>
						<option value="16"{if="isset($nacimiento.2) && $nacimiento.2 == 16"} selected="selected"{/if}>16</option>
						<option value="17"{if="isset($nacimiento.2) && $nacimiento.2 == 17"} selected="selected"{/if}>17</option>
						<option value="18"{if="isset($nacimiento.2) && $nacimiento.2 == 18"} selected="selected"{/if}>18</option>
						<option value="19"{if="isset($nacimiento.2) && $nacimiento.2 == 19"} selected="selected"{/if}>19</option>
						<option value="20"{if="isset($nacimiento.2) && $nacimiento.2 == 20"} selected="selected"{/if}>20</option>
						<option value="21"{if="isset($nacimiento.2) && $nacimiento.2 == 21"} selected="selected"{/if}>21</option>
						<option value="22"{if="isset($nacimiento.2) && $nacimiento.2 == 22"} selected="selected"{/if}>22</option>
						<option value="23"{if="isset($nacimiento.2) && $nacimiento.2 == 23"} selected="selected"{/if}>23</option>
						<option value="24"{if="isset($nacimiento.2) && $nacimiento.2 == 24"} selected="selected"{/if}>24</option>
						<option value="25"{if="isset($nacimiento.2) && $nacimiento.2 == 25"} selected="selected"{/if}>25</option>
						<option value="26"{if="isset($nacimiento.2) && $nacimiento.2 == 26"} selected="selected"{/if}>26</option>
						<option value="27"{if="isset($nacimiento.2) && $nacimiento.2 == 27"} selected="selected"{/if}>27</option>
						<option value="28"{if="isset($nacimiento.2) && $nacimiento.2 == 28"} selected="selected"{/if}>28</option>
						<option value="29"{if="isset($nacimiento.2) && $nacimiento.2 == 29"} selected="selected"{/if}>29</option>
						<option value="30"{if="isset($nacimiento.2) && $nacimiento.2 == 30"} selected="selected"{/if}>30</option>
						<option value="31"{if="isset($nacimiento.2) && $nacimiento.2 == 31"} selected="selected"{/if}>31</option>
					</select>
					<select class="span2" id="mes" name="mes">
						<option value="">-- Mes --</option>
						<option value="1"{if="isset($nacimiento.1) && $nacimiento.1 == 1"} selected="selected"{/if}>Enero</option>
						<option value="2"{if="isset($nacimiento.1) && $nacimiento.1 == 2"} selected="selected"{/if}>Febrero</option>
						<option value="3"{if="isset($nacimiento.1) && $nacimiento.1 == 3"} selected="selected"{/if}>Marzo</option>
						<option value="4"{if="isset($nacimiento.1) && $nacimiento.1 == 4"} selected="selected"{/if}>Abril</option>
						<option value="5"{if="isset($nacimiento.1) && $nacimiento.1 == 5"} selected="selected"{/if}>Mayo</option>
						<option value="6"{if="isset($nacimiento.1) && $nacimiento.1 == 6"} selected="selected"{/if}>Junio</option>
						<option value="7"{if="isset($nacimiento.1) && $nacimiento.1 == 7"} selected="selected"{/if}>Julio</option>
						<option value="8"{if="isset($nacimiento.1) && $nacimiento.1 == 8"} selected="selected"{/if}>Agosto</option>
						<option value="9"{if="isset($nacimiento.1) && $nacimiento.1 == 9"} selected="selected"{/if}>Septiembre</option>
						<option value="10"{if="isset($nacimiento.1) && $nacimiento.1 == 10"} selected="selected"{/if}>Octubre</option>
						<option value="11"{if="isset($nacimiento.1) && $nacimiento.1 == 11"} selected="selected"{/if}>Noviembre</option>
						<option value="12"{if="isset($nacimiento.1) && $nacimiento.1 == 12"} selected="selected"{/if}>Diciembre</option>
					</select>
					<input type="text" class="span1" id="ano" name="ano" placeholder="A&ntilde;o..." value="{if="isset($nacimiento.0)"}{$nacimiento.0}{/if}" />
					<p class="help-inline">Su apellido, se permiten caracteres alphanuméricos, espacios y '.</p>
				</div>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Actualizar</button>
			</div>
		</form>
	</div>
	<div class="span2">
		<img class="thumbnail" src="{function="Utils::get_gravatar($email, 150, 150)"}" />
	</div>
</div>