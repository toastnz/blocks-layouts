<% if $Content %>
    <section id="{$HTMLID}" class="default-code [ js-default-code ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-code__wrap">
            <div class="default-code__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>