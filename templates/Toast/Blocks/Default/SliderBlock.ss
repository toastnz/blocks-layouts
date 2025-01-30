<colour-block id="{$BlockID}" tabIndex="0" class="default-slider [ js-default-slider ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-slider__header">
                <div class="default-slider__content">
                    <% if $Heading %>
                        <h2 class="default-slider__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-slider__wrap [ js-default-slider__wrap ]">
            <div class="default-slider__container [ js-default-slider__container ]">
                <div class="default-slider__slider [ js-default-slider__slider ]">
                    <% if $Images.Count %>
                        <% loop $Images %>
                            <div class="default-slider-item">
                                <div class="default-slider-item__layout">
                                    <div class="default-slider-item__media">
                                        <picture>
                                            <% if $Extension="svg" %>
                                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                                            <% else %>
                                                <% with $Convert('webp') %>
                                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                                    <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                                    <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                                <% end_with %>
                                            <% end_if %>
                                        </picture>
                                    </div>
                                </div>
                            </div>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
