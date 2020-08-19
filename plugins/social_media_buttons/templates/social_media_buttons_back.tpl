{if $IS_ADMIN}{*<!-- Wir nur in der Admin angezeigt -->*}
    {*<!-- Hier kommt der Kopf rein-->*}
    {include file=head.inc.utf8.html}

    <!-- Men� kommt hier rein-->
    {include file=menu.inc.html}
    <div class="artikel">

        {$message.plugin.social_media_buttons.kopf}<br/>

        {foreach from=$plugin.social_media_buttons.errors item=error}
            <div class="error">{$error}</div>
        {/foreach}

        <form method="post" name="social_media_buttons">
            <fieldset>
                <legend>{$message.plugin.social_media_buttons.configuration}</legend>

                <p>{$message.plugin.social_media_buttons.fa_hinweis}
                    <label><input name="config[fontawesome]" value="1"
                                  type="checkbox" {if $plugin.social_media_buttons.config.fontawesome == "1"} checked="checked"{/if}>{$message.plugin.social_media_buttons.fa_label}
                    </label>
                </p>

                <p>
                    {$message.plugin.social_media_buttons.theme_hinweis}
                    <label>
                        <select name="config[theme]">
                            <option value="standard"{if $plugin.social_media_buttons.config.theme == "standard"} selected{/if}>
                                Standard
                            </option>
                            <option value="grey"{if $plugin.social_media_buttons.config.theme == "grey"} selected{/if}>
                                Grey
                            </option>
                            <option value="white"{if $plugin.social_media_buttons.config.theme == "white"} selected{/if}>
                                White
                            </option>
                        </select>
                    </label>
                </p>

                <p>
                    {$message.plugin.social_media_buttons.vertical_hinweis}
                    <label><input name="config[vertical]" value="1"
                                  type="checkbox" {if $plugin.social_media_buttons.config.vertical == "1"} checked="checked"{/if}>
                        {$message.plugin.social_media_buttons.vertical_label}
                    </label>
                </p>

            </fieldset>

            <fieldset>
                <legend>{$message.plugin.social_media_buttons.form_legend}</legend>

                {foreach item=button from=$plugin.social_media_buttons.buttons}
                    <div class="zeile">
                        <label><input id="{$button.name}" name="buttons[]" value="{$button.name}"
                                      type="checkbox" {if $button.aktiv == "1"} checked="checked"{/if}>{$button.display}
                        </label>
                    </div>
                {/foreach}

                <div class="zeile">
                    <input class="submit_back" type="submit" name="submit" value="{$message.plugin.social_media_buttons.save}">
                </div>

            </fieldset>
        </form>


    </div>
    {*<!-- Hier kommt der Fuss rein-->*}
    {include file=foot.inc.html}
{/if}
