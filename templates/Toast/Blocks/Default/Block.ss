<% if $Content %>
    <section id="{$HTMLID}" class="default-text [ js-default-text ] background-colour--{$getColour($PrimaryColour, 'class, brightness')} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-text__wrap">
            <div class="default-text__content">
                {$Content}
            </div>
        </div>
    </section>
<% end_if %>