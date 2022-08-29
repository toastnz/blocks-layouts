<section class="default-blog background-colour--{$BGColour} {$getLightOrDark($BGColour)} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-blog__header">
            <div class="default-blog__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-blog__wrap lg-up-{$Columns}" data-equalize="default-blogs">
        <% loop $Items.Sort('SortOrder') %>
            <div class="default-blog-item">
                <a href="{$Link.LinkURL}" class="default-blog-item__link">
                    <div class="default-blog-item__media" data-src="{$Image.FocusFill(500,600).URL}">
                        <img src="{$Image.FocusFill(5,6).URL}" alt="{$Image.AltText}" />
                        <div class="default-blog-item__icon" data-src="{$Icon.URL}"></div>
                    </div>

                    <div class="default-blog-item__details background-colour--{$AccentColour} {$getLightOrDark($AccentColour)}">
                        <div data-equalize-watch="default-blogs">
                            <% if $Title %>
                                <span class="default-blog-item__title">{$Title.XML}</span>
                            <% end_if %>
    
                            <% if $Summary %>
                                <p class="default-blog-item__summary">{$Summary.XML}</p>
                            <% end_if %>
                        </div>

                        <span class="default-blog-item__button">{$Link.Title}</span>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
</section>