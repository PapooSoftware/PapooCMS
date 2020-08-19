{if $IS_ADMIN}{*<!-- Wir nur in der Admin angezeigt -->*}
    <div id="inhalt_sitemap">
        <div class="sitemapheader">
            <div>{$message_168}</div>
        </div>

        <div style="clear:both;"></div>
        {foreach item=kat from=$catlist_data.0}

            {if $kat.cat_id>=1}<h1 class="sitemap_menucat">{$kat.cat_text}</h1>{/if}
            <ul class="artikel_sitemap">
                {foreach item=table from=$table_data}
                    {if $kat.cat_id==$table.cat_cid  or $no_categories==1}
                        {$table.shift_anfang}
                        <div>
                            <a class="no_active_link" menuid="{$table.menuid}">

                                {if $table.menulink=="shop.php" or $table.menulink=="shop"}
                                    <i class="fa fa-shopping-cart"></i>
                                {else}
                                    {if $table.extern}
                                        <i class="fa fa-share"></i>
                                    {elseif $table.template}
                                        <i class="fa fa-cog"></i>
                                    {else}
                                        <i class="fa fa-folder-open"></i>
                                    {/if}
                                {/if}
                                {$table.text}{$plugin_querverlinkungen.menuepunkte_menuepunkte_zuordnungen.select[$table.menuid]}
                            </a>
                        </div>
                        {*{if $table.artikel or $table.produkte}*}
                            {*<ul class="ul_artikel">*}
                                {*{foreach item=artikel from=$table.artikel}*}
                                    {*<li class="article">*}
                                        {*<div>*}
                                            {*<a href="./artikel.php?menuid=11&amp;reporeid={$artikel.reporeid}"*}
                                                    {*class="sitemap_artikel no_active_link">*}
                                                {*<i class="fa fa-font"></i>{$artikel.text}*}
                                            {*</a>{$plugin_querverlinkungen.artikel_artikel_zuordnungen.select[$artikel.reporeid]}*}
                                        {*</div>*}
                                    {*</li>*}
                                {*{/foreach}*}
                            {*</ul>*}
                        {*{/if}*}

                        {$table.shift_ende}
                    {/if}
                {/foreach}
            </ul>
        {/foreach}
    </div>
{/if}

<script type="text/javascript" src="./js/jq_content_tree.js"></script>
{if $user_content_tree_show_all}
    <script type="text/javascript"> jq_content_tree_show_all(); </script>
{/if}