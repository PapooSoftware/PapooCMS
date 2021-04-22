<h1>Fixe Module</h1>

<p>Dies ist die Übersicht über alle fixen Module. In jedem Modul können verschiedene Felder befüllt werden.</p>

{if $fixemodule.dev_mode}
    <div class="message">
        <p>Der Entwickler-Modus ist aktiv. Sie können Module hinzufügen oder löschen.
        Um den Entwickler-Modus auszuschalten, setzen Sie die Konstante <code>FIXEMODULE_DEV_MODE</code> in der Klasse <code>plugins/fixemodule/fixemodule_class.php</code> auf <code>false</code>.
        Dann können nur noch Feldinhalte von vorhandenen Modulen bearbeitet werden.</p>

        <p>Jedes Feld kann im Frontend durch eine eindeutige Template-Variable verwendet werden.</p>
    </div>
{/if}

{include file=../../../plugins/fixemodule/templates/messages.tpl}

{if $fixemodule.dev_mode}
    <fieldset>
        <legend>Neues Modul hinzufügen</legend>
        <form action="" method="post">
            <div>
                <input name="neues_modul_name" type="text" placeholder="Name" required>
                <input name="neues_modul_beschreibung" type="text" placeholder="Beschreibung" required>
            </div>
            <div>
                <input name="neues_modul" class="submit_back" type="submit" value="Speichern">
            </div>
        </form>
    </fieldset>
{/if}

{if $fixemodule.module}
    <h2>Vorhandene Module</h2>
    <ul>
        {foreach from=$fixemodule.module item=modul}
            <li>
                <a href="{$fixemodule.base_url}&action=edit_modul&modul_id={$modul.id}">{$modul.name}</a><br/>{$modul.beschreibung}
            </li>
        {/foreach}
    </ul>
{/if}
