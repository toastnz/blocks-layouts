<colour-block id="{$BlockID}" tabindex="-1" class="default-userform [ js-default-user-form ] {$IncludeClasses} {$ExtraClasses}">
    <section <% if $Heading %>aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <div class="default-userform__wrap">
            <% if $Heading || $Content %>
                <div class="default-userform__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-userform__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            <% end_if %>

            <div class="default-userform__content">
                {$Form}
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
