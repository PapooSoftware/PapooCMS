{if $plugin_querverlinkungen.verlinkte_artikel}
    <div class="modul" id="mod_querverlinkungenartikelartikel_frontend">
        <ul>
            {foreach item=artikel from=$plugin_querverlinkungen.verlinkte_artikel}
                <li>
                    <a title="{$artikel.name}" href="{$artikel.url}">{$artikel.name}</a>
                </li>
            {/foreach}
        </ul>
    </div>
{/if}