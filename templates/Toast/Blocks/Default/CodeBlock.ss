<% if $Content %>
    <section id="{$BlockID}" tabIndex="0" class="default-code [ js-default-code ] ThemeColour_{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-code__wrap">
            <div class="default-code__content">
                {$Content}
            </div>
        </div>

        {$ExtraRequirements}
    </section>
<% end_if %>
