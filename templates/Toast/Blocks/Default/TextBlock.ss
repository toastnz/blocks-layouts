<% if $Content %>
    <section id="{$HTMLID}" tabIndex="0" class="default-text [ js-default-text ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-text__wrap">
            <div class="default-text__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>
