<% if $Form %>
    <section class="default-userform background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
        <div class="default-userform__wrap">
            <div class="default-userform__content background-colour--{$AccentColour} {$getLightOrDark($AccentColour)}">
                {$Form}
            </div>
        </div>
    </section>
<% end_if %>