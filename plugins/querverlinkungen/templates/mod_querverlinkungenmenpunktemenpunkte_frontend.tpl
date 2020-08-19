{if $plugin_querverlinkungen.verlinkte_menuepunkte}
    <div class="modul" id="mod_querverlinkungenmenuepunktemenuepunkte_frontend">
        <ul>
            {foreach item=menuepunkt from=$plugin_querverlinkungen.verlinkte_menuepunkte}
                <li>
                    <a title="{$menuepunkt.name}" href="{$menuepunkt.url}">{$menuepunkt.name}</a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}