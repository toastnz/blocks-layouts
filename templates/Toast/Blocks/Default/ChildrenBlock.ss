<colour-block id="{$BlockID}" tabIndex="0" class="default-children [ js-default-children ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    <% if $Heading %>
                        <h2 class="default-children__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-children__wrap columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items %>
                    <div id="{$BlockItemID}" class="default-children-item [ js-in-view ]">
                        <a href="{$Link}" class="default-children-item__link">
                            <% if $FeaturedImage %>
                                <div class="default-children-item__media">
                                    <% with $FeaturedImage %>
                                        <picture>
                                            <% if $Extension="svg" %>
                                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                                            <% else %>
                                                <% with $Convert('webp') %>
                                                    <source media="(max-width: 479px)" srcset="{$FocusFillMax(480,480).URL}">
                                                    <source media="(max-width: 767px)" srcset="{$FocusFillMax(768,768).URL}">
                                                    <img loading="lazy" src="{$FocusFillMax(960,960).URL}" alt="{$Title.ATT}" width="960" height="960" style="object-position: {$FocusPosition}">
                                                <% end_with %>
                                            <% end_if %>
                                        </picture>
                                    <% end_with %>
                                </div>
                            <% end_if %>

                            <div class="default-children-item__details">
                                <% if $Title %>
                                    <span class="default-children-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-children-item__summary">{$Summary.XML}</p>
                                <% end_if %>

                                <span class="default-children-item__button read-more">Read more</span>
                            </div>
                        </a>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
