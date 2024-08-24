<colour-block id="{$BlockID}" tabIndex="0" class="default-userform [ js-default-user-form ] theme-colour--{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-userform__wrap">
            <% if $Content %>
                <div class="default-userform__content">
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
