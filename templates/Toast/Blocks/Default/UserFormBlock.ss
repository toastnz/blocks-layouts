<% if $Form %>
    <section id="{$HTMLID}" class="default-user-form [ js-default-user-form ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-userform__wrap">
            <div class="default-userform__content">
                {$Form}
            </div>
        </div>
    </section>
<% end_if %>