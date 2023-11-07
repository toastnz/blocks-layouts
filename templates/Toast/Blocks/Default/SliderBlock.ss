<section id="{$HTMLID}" class="default-slider [ js-default-slider ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-slider__header">
            <div class="default-slider__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-slider__wrap" data-equalize="{$HTMLID}__media">
        <div class="default-slider__container colour--{$SecondaryColour.ColourClasses}">
            <div class="[ js-default-slider__container ]">
                <% loop $Items.Sort('SortOrder') %>
                    <% if $Image || $Video %>
                        <div class="default-slider-item">

                            <div class="default-slider-item__media" <% if $Video %>data-video="{$Video.IframeURL}"<% end_if %> data-equalize-watch="{$Top.HTMLID}__media">
                                <% if $Video %>
                                    <div class="default-slider-item__aspect" data-src="<% if $Image %>{$Image.ScaleMaxWidth(1000).URL}<% else_if $Video %>{$Video.ThumbnailURL('large')}<% end_if %>"></div>
                                    <div class="default-slider-item__icon"></div>
                                <% else_if $Image %>
                                    <picture>
                                        <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                        <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                                        <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                                        <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" alt="{$Image.Title.ATT}">
                                    </picture>
                                <% end_if %>
                            </div>
                        </div>
                    <% end_if %>
                <% end_loop %>
            </div>
        </div>
    </div>
</section>
