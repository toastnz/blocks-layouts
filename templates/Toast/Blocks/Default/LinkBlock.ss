<section class="default-link background-colour--{$getColourClassName($BGColour)} {$getLightOrDark($BGColour)}  {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-link__header">
            <div class="default-link__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-link__wrap lg-up-{$Columns}" data-equalize="default-links">
        <% loop $Items.Sort('SortOrder') %>
            <div class="default-link-item">
                <a href="{$Link.LinkURL}" class="default-link-item__link">
                    <div class="default-link-item__media" data-src="{$Image.FocusFill(500,600).URL}">
                        <img src="{$Image.FocusFill(5,6).URL}" alt="{$Image.AltText}" />
                        <div class="default-link-item__icon" data-src="{$Icon.URL}"></div>
                    </div>

                    <div class="default-link-item__details background-colour--{$getColourClassName($Top.AccentColour)} {$Top.getLightOrDark($Top.AccentColour)}">
                        <div data-equalize-watch="default-links">
                            <% if $Title %>
                                <span class="default-link-item__title">{$Title.XML}</span>
                            <% end_if %>
    
                            <% if $Summary %>
                                <p class="default-link-item__summary">{$Summary.XML}</p>
                            <% end_if %>
                        </div>

                        <span class="default-link-item__button">{$Link.Title}</span>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
</section>