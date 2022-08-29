<% if $Content %>
    <section class="default-text background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
        <div class="default-text__wrap">
            <div class="default-text__content background-colour--{$AccentColour} {$getLightOrDark($AccentColour)}">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>