<div class="clearfix">
	<h1 class="left">{$fixemodule.modul.name}</h1>
</div>
<div class="clearfix">
	<h2 class="left">{$fixemodule.modul.beschreibung}</h2>
	<a class="right" href="{$fixemodule.base_url}">Zurück zur Übersicht</a>
</div>

{* Message Entwicklermodus *}
{if $fixemodule.dev_mode}
	<div class="message">
		<p>Der Entwickler-Modus ist aktiv. Sie können Felder hinzufügen und Felder oder das Modul löschen.
			Um den Entwickler-Modus auszuschalten, setzen Sie die Konstante
			<code>FIXEMODULE_DEV_MODE</code> in der Klasse
			<code>plugins/fixemodule/fixemodule_class.php</code> auf <code>false</code>.
			Dann können nur noch Feldinhalte bearbeitet werden.</p>

		<p>Jedes Feld kann im Frontend durch eine eindeutige Template-Variable verwendet werden.</p>
	</div>
{/if}

{include file=../../../plugins/fixemodule/templates/messages.tpl}

<form id="moduldaten" method="POST" action="">
<input class="hidden" type="submit" name="modul_speichern" value="Modul speichern" />

{* Feld hinzufügen *}
	<fieldset>
		<legend>Moduldaten</legend>
		<label>Name</label>
		<input name="modul_name" type="text" value="{$fixemodule.modul.name}" required="required">
		<label>Beschreibung</label>
		<input name="modul_beschreibung" value="{$fixemodule.modul.beschreibung}" type="text" required="required">
	</fieldset>
{if $fixemodule.dev_mode}
	<fieldset>
		<legend>Neues Feld hinzufügen</legend>
		<label>Name</label>
		<input name="neues_feld_name" type="text">
		<label>Typ</label>
		<select name="neues_feld_feldtyp">
			{foreach from=$fixemodule.feldtypen item=feldtyp}
				<option value="{$feldtyp.id}">{$feldtyp.name}</option>
			{/foreach}
		</select>
		<div>
			<input name="neues_feld" class="submit_back" type="submit" value="Feld hinzufügen">
		</div>
	</fieldset>
{/if}

{* Alle Felder *}
{if $fixemodule.felder}
	<fieldset>
		<legend>Felder</legend>

		{foreach from=$fixemodule.felder item=feld}
			<div>
				<div class="clearfix">
					<h3 class="left">{$feld.name}</h3>
					{if $fixemodule.dev_mode}
						<a class="right submit_back_red" href="{$fixemodule.base_url}&action=feld_loeschen&feld_id={$feld.id}">Feld Löschen</a>
					{/if}
				</div>
				<div class="clearfix">

					{if $feld.feldtyp.name == "Text"}
						<textarea rows="1" cols="80" name="felder[{$feld.id}]">{$feld.inhalt}</textarea>
					{elseif $feld.feldtyp.name == "Bild"}
						<div class="feld-bild-wrapper">
							<textarea class="feld-bild" id="feld-{$feld.id}" name="felder[{$feld.id}]" style="width: 800px; height: 450px;">{$feld.inhalt}</textarea>
						</div>
					{elseif $feld.feldtyp.name == "HTML"}
						<div class="feld-html-wrapper">
							<textarea class="feld-html" id="feld-{$feld.id}" name="felder[{$feld.id}]" style="width: 800px; height: 450px;">{$feld.inhalt}</textarea>
						</div>
					{elseif $feld.feldtyp.name == 'Checkbox'}
						<div class="feld-checkbox-wrapper">
							<!-- Verstecktes Feld, um den Status "nicht angehakt" zu übermitteln -->
							<input type="hidden" name="felder[{$feld.id}]" value="0" />
							<input type="checkbox" id="feld-{$feld.id}" name="felder[{$feld.id}]" value="1"{if $feld.inhalt} checked="checked"{/if} />
							<label for="feld-{$feld.id}">{$feld.name}</label>
						</div>
					{/if}

				</div>
				<div>
					{if $feld.feldtyp.name == "Bild"}
						<div>
							Template-Variablen:
							<a class="click">anzeigen</a>
							<ul class="show">
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.tag{rdelim}</code>: das komplette
									<code>&lt;img&gt;</code>-tag
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.src{rdelim}</code>: das
									<code>src</code>-Attribut
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.filename{rdelim}</code>: nur der Dateiname des Bildes ohne Pfad
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.width{rdelim}</code>: das
									<code>width</code>-Attribut
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.height{rdelim}</code>: das
									<code>height</code>-Attribut
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.alt{rdelim}</code>: das
									<code>alt</code>-Attribut
								</li>
								<li>
									<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.title{rdelim}</code>: das
									<code>title</code>-Attribut
								</li>
							</ul>
						</div>
						<div>
							Platzhalter:
							<a class="click">anzeigen</a>
							<ul class="show">
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.tag#</code>: das komplette
									<code>&lt;img&gt;</code>-tag
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.src#</code>: das
									<code>src</code>-Attribut
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.filename#</code>: nur der Dateiname des Bildes ohne Pfad
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.width#</code>: das
									<code>width</code>-Attribut
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.height#</code>: das
									<code>height</code>-Attribut
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.alt#</code>: das
									<code>alt</code>-Attribut
								</li>
								<li>
									<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}.title#</code>: das
									<code>title</code>-Attribut
								</li>
							</ul>
						</div>
					{else}
						<div>
							Template-Variable:
							<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.{$feld.slug}{rdelim}</code>
						</div>
						<div>
							Platzhalter:
							<code>#fixemodule.{$fixemodule.modul.slug}.{$feld.slug}#</code>
						</div>
					{/if}
				</div>
			</div>
			<hr/>
		{/foreach}

	</fieldset>
{/if}

	{* HTML-Template *}
	<fieldset>
		<legend>HTML-Template</legend>

		{if $fixemodule.dev_mode}
		Alle Platzhalter von Feldern können in dem HTML-Template verwendet werden.

		<textarea id="htmltextarea" name="html" style="width: 800px; height: 450px;">{$fixemodule.modul.html}</textarea>
		{/if}
		<div>
			Über die folgenden Variablen kann dann das HTML-Template eingebunden werden:
		</div>
		<div>
			Template-Variable:
			<code>{ldelim}$fixemodule.{$fixemodule.modul.slug}.html{rdelim}</code>
		</div>
		<div>
			Platzhalter:
			<code>#fixemodule.{$fixemodule.modul.slug}.html#</code>
		</div>
	</fieldset>

	{* Duplizieren *}
	<fieldset>
		<legend>Duplizieren</legend>
		<input class="right submit_back bg_blue" type="submit" name="modul_duplizieren" value="Dieses Modul duplizieren" formnovalidate="formnovalidate">
	</fieldset>

{* Speichern *}
{if $fixemodule.felder|@count > 0 || $fixemodule.dev_mode}
	<fieldset>
		<legend>Speichern</legend>
		{*{if $fixemodule.felder|@count > 0}*}
		<input class="left submit_back" type="submit" name="modul_speichern" value="Modul speichern">
		{*{/if}*}
		<div class="right" method="post">
			<input class="right submit_back_red" type="submit" name="modul_loeschen" value="Modul löschen" formnovalidate="formnovalidate">
		</div>
	</fieldset>
{/if}
</form>

<script src="./tiny_mce/tiny_mce.js" charset="utf-8"></script>
<script src="./tiny_mce/FileBrowser.js"></script>
<script src="../plugins/fixemodule/js/codemirror.js"></script>
<script src="../plugins/fixemodule/js/app.js"></script>
