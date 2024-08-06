<section id="{$HTMLID}" tabIndex="0" class="default-slider [ js-default-slider ] background-colour--{$PrimaryColour.getColourClasses}} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-slider__header">
            <div class="default-slider__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-slider__wrap" data-equalize="{$HTMLID}__media">
        <div class="default-slider__container">
            <div class="[ js-default-slider__container ]">
                <% loop $Items.Sort('SortOrder') %>
                    <% if $Image || $Video %>
                        <div class="default-slider-item">

                            <div class="default-slider-item__media colour--{$Top.SecondaryColour.getColourClasses}}" data-src="<% if $Image %>{$Image.ScaleMaxWidth(1000).URL}<% else_if $Video %>{$Video.ThumbnailURL('large')}<% end_if %>" <% if $Video %>data-video="{$Video.IframeURL}"<% end_if %> data-equalize-watch="{$Top.HTMLID}__media">
                                <% if $Video %>
                                    {$SVG('play')}
                                <% end_if %>

                                <% if $Image %>
                                    <img src="{$Image.ScaleMaxWidth(10).URL}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                                <% else %>
                                    <div class="default-slider-item__aspect"></div>
                                <% end_if %>
                            </div>
                        </div>
                    <% end_if %>
                <% end_loop %>
            </div>
        </div>
    </div>
</section>
