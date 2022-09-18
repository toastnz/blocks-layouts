<% if $Content %>
    <section class="default-code background-colour--{$getColourClassName($BGColour)} {$getLightOrDark($BGColour)} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-code__wrap">
            <div class="default-code__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>