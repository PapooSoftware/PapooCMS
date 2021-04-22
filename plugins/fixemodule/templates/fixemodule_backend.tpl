{if $IS_ADMIN}
    {include file=head.inc.utf8.html}
    {include file=menu.inc.html}
    <link rel="stylesheet" type="text/css" href="{$css_path}/backend.css"/>
    <link rel="stylesheet" type="text/css" href="{$css_path}/../../plugincreator/css/backend.css"/>
    <link rel="stylesheet" type="text/css" href="{$css_path}/codemirror.css"/>

    <div class="artikel">
        {if $fixemodule.template == "edit_modul"}
            {include file=../../../plugins/fixemodule/templates/edit_modul.tpl}
        {else}
            {include file=../../../plugins/fixemodule/templates/main.tpl}
        {/if}
    </div>

    {include file=foot.inc.html}
{/if}
