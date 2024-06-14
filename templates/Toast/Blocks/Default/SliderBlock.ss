<section id="{$HTMLID}" class="default-slider [ js-default-slider ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-slider__header">
            <div class="default-slider__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-slider__wrap" data-match-height="{$HTMLID}__media">
        <div class="default-slider__container colour--{$SecondaryColour.ColourClasses}">
            <div class="default-slider__slider [ js-default-slider__container ]">
                <% loop $Items.Sort('SortOrder') %>
                    <% if $Image || $Video %>
                        <div class="default-slider-item">
                            <div class="default-slider-item__layout" data-equalize-watch="{$Top.HTMLID}__media">
                                <div class="default-slider-item__media">
                                    <% if $Image %>
                                        <picture>
                                            <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                                            <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                            <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                                            <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                                            <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" style="object-fit: cover; object-position: {$getImageFocusPosition($Image.ID)}">
                                        </picture>
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
            </div>
        </div>
    </div>
</section>
