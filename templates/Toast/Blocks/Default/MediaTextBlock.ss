<section id="{$HTMLID}" class="default-media-text [ js-default-media-text ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-media-text__wrap">
        <div class="default-media-text__media align-{$MediaAlignment}">
            <% if $Video %>
                <div class="default-media-text__video" data-video-modal="{$Video.IframeURL}" data-src="{$Image.ScaleWidth(1200).URL}" style="background-position: {$getImageFocusPosition($Image.ID)}">
                    <img src="{$Image.ScaleWidth(12).URL}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                </div>
            <% else %>
                <div class="default-media-text__image" data-src="{$Image.ScaleWidth(1200).URL}" style="background-position: {$getImageFocusPosition($Image.ID)}">
                    <img src="{$Image.ScaleWidth(12).URL}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                </div>
            <% end_if %>
        </div>

        <div class="default-media-text__content">
            <div class="default-media-text__text">

                {$Content}

                <% if $CTALink %>
                    <% if $SecondaryColour.ID && $SecondaryColour.ID != $PrimaryColour.ID %>
                        <a href="{$CTALink.LinkURL}" {$CTALink.TargetAttr} class="default-media-text__link button colour--{$SecondaryColour.ColourClasses}">{$CTALink.Title}</a>
                    <% else %>
                        <a href="{$CTALink.LinkURL}" {$CTALink.TargetAttr} class="default-media-text__link button {$getButtonClasses($PrimaryColour.ID, 'primary')}">{$CTALink.Title}</a>
                    <% end_if %>
                <% end_if %>
            </div>
        </div>
    </div>
</section>
