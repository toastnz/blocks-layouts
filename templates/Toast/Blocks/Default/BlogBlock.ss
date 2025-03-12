<colour-block id="{$BlockID}" tabIndex="0" class="default-blog [ js-default-blog ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-blog__header">
                <div class="default-blog__content">
                    <% if $Heading %>
                        <h2 class="default-blog__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-blog__wrap columns-{$Columns}">
            <% if $Posts.Count %>
                <% loop $Posts %>
                    <div class="default-blog-item [ js-in-view ]">
                        <a href="{$Link}" class="default-blog-item__link">
                            <% if $FeaturedImage %>
                                <div class="default-blog-item__media">
                                    <% with $FeaturedImage %>
                                        <picture>
                                            <% if $Extension="svg" %>
                                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="960" height="960" loading="lazy" alt="{$Title.ATT}">
                                            <% else %>
                                                <source media="(max-width: 479px)" srcset="{$FocusFillMax(480,480).Convert('webp').URL}">
                                                <img loading="lazy" src="{$FocusFillMax(960,960).Convert('webp').URL}" alt="{$Title.ATT}" width="960" height="960" style="object-position: {$FocusPosition}">
                                            <% end_if %>
                                        </picture>
                                    <% end_with %>
                                </div>
                            <% end_if %>

                            <div class="default-blog-item__details">
                                <% if $Title %>
                                    <span class="default-blog-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-blog-item__summary">{$Summary.XML}</p>
                                <% end_if %>

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
