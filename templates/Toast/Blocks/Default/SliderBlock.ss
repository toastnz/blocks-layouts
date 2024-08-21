<section id="{$BlockID}" tabIndex="0" class="default-slider [ js-default-slider ] ThemeColour_{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-slider__header">
            <div class="default-slider__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-slider__wrap" data-match-height="{$BlockID}__media">
        <div class="default-slider__container colour--{$SecondaryColour.ColourClasses}">
            <div class="default-slider__slider [ js-default-slider__container ]">
                <% if $Items.Count %>
                    <% loop $Items.Sort('SortOrder') %>
                        <% if $Image || $Video %>
                            <div class="default-slider-item">
                                <div class="default-slider-item__layout" data-equalize-watch="{$$Top.BlockID}__media">
                                    <div class="default-slider-item__media">
                                        <% if $Image %>
                                            <% with $Image %>
                                                <picture>
                                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                                    <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                                    <source media="(max-width: 1919px)" srcset="{$ScaleMaxWidth(1920).URL}">
                                                    <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                                </picture>
                                            <% end_with %>
                                        <% else_if $Video %>
                                            <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                                        <% end_if %>

                                        <% if $Video %>
                                            <div class="default-slider-item__icon"></div>
                                        <% end_if %>
                                    </div>
                                </div>
                            </div>
                        <% end_if %>
                    <% end_loop %>
                <% end_if %>
            </div>
        </div>
    </div>

    {$ExtraRequirements}
</section>
