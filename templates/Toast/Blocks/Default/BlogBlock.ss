<colour-block id="{$BlockID}" tabIndex="0" class="default-blog [ js-default-blog ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Content %>
            <div class="default-blog__header">
                <div class="default-blog__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-blog__wrap lg-up-{$Columns}" data-match-height="{$BlockID}_Media" data-equalize="{$BlockID}_Text">
            <% if $Posts.Count %>
                <% loop $Posts %>
                    <div class="default-blog-item [ js-in-view ]">
                        <a href="{$Link}" class="default-blog-item__link">
                            <div class="default-blog-item__media" data-equalize-watch="{$Top.BlockID}_Media">
                                <% with $FeaturedImage %>
                                    <picture>
                                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                        <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                        <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                    </picture>
                                <% end_with %>
                            </div>

                            <div class="default-blog-item__details">
                                <div data-equalize-watch="{$Top.BlockID}_Text">
                                    <% if $Title %>
                                        <span class="default-blog-item__title">{$Title.XML}</span>
                                    <% end_if %>

                                    <% if $Summary %>
                                        <p class="default-blog-item__summary">{$Summary.XML}</p>
                                    <% end_if %>
                                </div>

                                <span class="default-blog-item__button read-more">Read more</span>
                            </div>
                        </a>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>

    </section>

    {$ExtraRequirements}
</colour-block>
