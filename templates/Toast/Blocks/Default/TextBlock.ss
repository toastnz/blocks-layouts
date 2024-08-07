<% if $Content || $Heading %>
    <section id="{$BlockID}" tabIndex="0" class="default-text [ js-default-text ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-text__wrap">
            <% if $Heading %>
                <div class="default-text__content">
                    <h2 class="default-text__heading colour--{$SecondaryColour.getColourClasses}">{$Heading.XML}</h2>
                </div>
            <% end_if %>

            <div class="default-text__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>
