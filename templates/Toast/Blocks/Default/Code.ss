<% if $Content %>
    <section class="default-code background-colour--{$BGColourClassName} {$getLightOrDark($BGColourClassName)} {$ExtraClasses}">
        <div class="default-code__wrap">
            <div class="default-code__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>