<section id="{$HTMLID}" class="default-video [ js-default-video ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-video__wrap">
        <div class="default-video__media">
            <a id="{$HTMLID}_{$Video.VideoID}" class="default-video__link" data-video<% if $OpenInModal %>-modal<% end_if %>="{$Video.IframeURL}">
                <div class="default-video__thumbnail">
                    <% if $Thumbnail %>
                        <picture>
                            <source media="(max-width: 479px)" srcset="{$Thumbnail.ScaleMaxWidth(480).URL}">
                            <source media="(max-width: 767px)" srcset="{$Thumbnail.ScaleMaxWidth(768).URL}">
                            <source media="(max-width: 1439px)" srcset="{$Thumbnail.ScaleMaxWidth(1440).URL}">
                            <source media="(max-width: 1919px)" srcset="{$Thumbnail.ScaleMaxWidth(1920).URL}">
                            <img loading="lazy" src="{$Thumbnail.ScaleMaxWidth(1920).URL}" alt="{$Thumbnail.Title.ATT}" width="{$Thumbnail.Width}" height="{$Thumbnail.Height}" style="object-fit: cover; object-position: {$getImageFocusPosition($Thumbnail.ID)}">
                        </picture>
                    <% else_if $Video %>
                        <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                    <% end_if %>
                </div>
                <div class="default-video__icon colour--{$SecondaryColour.ColourClasses}"></div>
            </a>

            <% if $Caption %>
                <div class="default-video__caption">
                    <p>{$Caption}</p>
                </div>
            <% end_if %>
        </div>
    </div>
</section>
