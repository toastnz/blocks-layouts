<section id="{$HTMLID}" tabIndex="0" class="default-video [ js-default-video ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-video__header">
            <div class="default-video__content">{$Content}</div>
        </div>
    <% end_if %>

    <div class="default-video__wrap">
        <div class="default-video__media">
            <a href="#" class="default-video__link" data-video<% if $OpenInModal %>-modal<% end_if %>="{$Video.IframeURL}">
                <div href="#" class="default-video__thumbnail" data-src="<% if $ThumbnailID %>{$Thumbnail.FocusFill(1920,1080).URL}<% else %>{$Video.ThumbnailURL('large')}<% end_if %>" style="background-position: {$getImageFocusPosition($Thumbnail.ID)}"></div>
                <div class="default-video__icon">{$SVG('play')}</div>
            </a>

            <% if $Caption %>
                <div class="default-video__caption">
                    <p>{$Caption}</p>
                </div>
            <% end_if %>
        </div>
    </div>
</section>
