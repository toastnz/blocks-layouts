<% if $Content %>
    <section class="default-text background-colour--{$BGColourClassName} {$getLightOrDark($BGColourClassName)} {$ExtraClasses}">
        <div class="default-text__wrap">
            <div class="default-text__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>