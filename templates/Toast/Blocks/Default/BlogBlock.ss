<% if $Posts.Count %>
    <section id="{$HTMLID}" class="default-blog [ js-default-blog ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-blog__header">
                <div class="default-blog__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-blog__wrap lg-up-{$Columns}" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}_Text">
            <% loop $Posts %>
                <div class="default-blog-item [ js-in-view ]">
                    <a href="{$Link}" class="default-blog-item__link">
                        <div class="default-blog-item__media" data-src="{$FeaturedImage.ScaleMaxWidth(800).URL}" data-equalize-watch="{$Top.HTMLID}_Media">
                            <img src="{$FeaturedImage.ScaleMaxWidth(800).URL}" width="{$FeaturedImage.getWidth()}" height="{$FeaturedImage.getHeight()}" loading="lazy" alt="{$FeaturedImage.Title.ATT}">
                        </div>

                        <div class="default-blog-item__details">
                            <div data-equalize-watch="{$Top.HTMLID}_Text">
                                <% if $Title %>
                                    <span class="default-blog-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-blog-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>

                            <% if $Top.SecondaryColour.ID && $Top.SecondaryColour.ID != $Top.PrimaryColour.ID %>
                                <span class="default-blog-item__button button colour--{$Top.SecondaryColour.ColourClasses}">Read more</span>
                            <% else %>
                                <span class="default-blog-item__button button {$Top.getButtonClasses($Top.PrimaryColour.ID, 'primary')}">Read more</span>
                            <% end_if %>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
