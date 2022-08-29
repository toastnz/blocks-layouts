<% if $Content %>
    <section class="default-code background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
        <div class="default-code__wrap">
            <div class="default-code__content background-colour--{$AccentColour} {$getLightOrDark($AccentColour)}">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>