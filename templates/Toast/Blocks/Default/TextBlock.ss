<colour-block id="{$BlockID}" tabindex="-1" class="default-text [ js-default-text ] {$IncludeClasses} {$ExtraClasses}">
    <section<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <div class="default-text__wrap">
            <% if $Heading %>
                <div class="default-text__content">
                    <h2 id="{$BlockID}_heading" class="default-text__heading">{$Heading}</h2>
                </div>
            <% end_if %>

            <% if $Content %>
                <div class="default-text__content">
                    {$Content}
                </div>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
