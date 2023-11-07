<% if $Posts.Count %>
    <section id="{$HTMLID}" class="default-children [ js-default-children ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-children__wrap lg-up-{$Columns}" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}_Text">
            <% loop $Posts %>
                <div class="default-children-item [ js-in-view ]">
                    <a href="{$Link}" class="default-children-item__link">
                        <div class="default-children-item__media" data-src="{$FeaturedImage.ScaleMaxWidth(800).URL}" data-equalize-watch="{$Top.HTMLID}_Media">
                            <img src="{$FeaturedImage.ScaleWidth(8).URL}" width="{$FeaturedImage.getWidth()}" height="{$FeaturedImage.getHeight()}" loading="lazy" alt="{$FeaturedImage.Title.ATT}">
                        </div>

                        <div class="default-children-item__details">
                            <div data-equalize-watch="{$Top.HTMLID}_Text">
                                <% if $Title %>
                                    <span class="default-children-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-children-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>

                            <span class="default-children-item__button colour--{$Top.SecondaryColour.ColourClasses}">Read more</span>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
