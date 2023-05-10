<% if $Items.Count %>
    <section id="{$HTMLID}" class="default-children [ js-default-children ] background-colour--{$getColour($PrimaryColour, 'class, brightness')} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>
        
        <div class="default-children__wrap lg-up-{$Columns}" data-equalize="{$HTMLID}">
            <% loop $Items.Sort('SortOrder') %>
                <div class="default-children-item">
                    <a href="{$Link.LinkURL}" class="default-children-item__link">
                        <div class="default-children-item__media" data-src="{$Image.FocusFill(500,600).URL}">
                            <img src="{$Image.Fill(5,6).URL}" width="5" height="6" loading="lazy" alt="{$Image.Title.ATT}">
                            <div class="default-children-item__icon" data-src="{$Icon.URL}"></div>
                        </div>
        
                        <div class="default-children-item__details background-colour--{$getColour($SecondaryColour, 'class, brightness')}">
                            <div data-equalize-watch="{$Top.HTMLID}">
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
<% end_if %>