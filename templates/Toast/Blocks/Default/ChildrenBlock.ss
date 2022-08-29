<section class="default-children background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-children__header">
            <div class="default-children__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-children__wrap lg-up-{$Columns}" data-equalize="default-children">
        <% loop $Items.Sort('SortOrder') %>
            <div class="default-children-item">
                <a href="{$Link.LinkURL}" class="default-children-item__link">
                    <div class="default-children-item__media" data-src="{$Image.FocusFill(500,600).URL}">
                        <img src="{$Image.FocusFill(5,6).URL}" alt="{$Image.AltText}" />
                        <div class="default-children-item__icon" data-src="{$Icon.URL}"></div>
                    </div>

                    <div class="default-children-item__details background-colour--{$Top.AccentColour} {$Top.getLightOrDark($Top.AccentColour)}">
                        <div data-equalize-watch="default-childrens">
                            <% if $Title %>
                                <span class="default-children-item__title">{$Title.XML}</span>
                            <% end_if %>
    
                            <% if $Summary %>
                                <p class="default-children-item__summary">{$Summary.XML}</p>
                            <% end_if %>
                        </div>

                        <span class="default-children-item__button">{$Link.Title}</span>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
</section>