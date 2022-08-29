<% if $Image %>
    <section class="default-image background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
        <div class="default-image__wrap">
            <div class="default-image__media">
                <div class="default-image__image" data-src="{$Image.ScaleMaxWidth(1920).URL}">
                    <img src="{$Image.ScaleMaxWidth(192).URL}" alt="{$Image.Title.ATT}">
                </div>
            </div>

            <% if $Caption %>
                <div class="default-image__content background-colour--{$AccentColour} {$getLightOrDark($AccentColour)}">
                    <p class="{$TextColour}">{$Caption}</p>
                </div>
            <% end_if %>
        </div>
    </section>
<% end_if %>