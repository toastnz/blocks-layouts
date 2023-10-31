<% if $Form %>
    <section id="{$HTMLID}" class="default-userform [ js-default-user-form ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-userform__wrap">
            <% if $Content %>
                <div class="default-userform__content">
                    {$Content}
                </div>
            <% end_if %>

            <div class="default-userform__content">
                {$Form}
            </div>
        </div>
    </section>
<% end_if %>
