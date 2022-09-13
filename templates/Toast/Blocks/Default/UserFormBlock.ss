<% if $Form %>
    <section class="default-userform background-colour--{$BGColourClassName} {$getLightOrDark($BGColourClassName)} {$ExtraClasses}">
        <div class="default-userform__wrap">
            <div class="default-userform__content">
                {$Form}
            </div>
        </div>
    </section>
<% end_if %>