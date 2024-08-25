<colour-block id="{$BlockID}" tabIndex="0" class="default-text [ js-default-text ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-text__wrap">
            <% if $Heading %>
                <div class="default-text__content">
                    <h2 class="default-text__heading">{$Heading.XML}</h2>
                </div>
            <% end_if %>

            <div class="default-text__content">
                {$Content}
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
