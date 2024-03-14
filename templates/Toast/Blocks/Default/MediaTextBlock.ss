<section id="{$HTMLID}" class="default-media-text [ js-default-media-text ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-media-text__wrap">
        <div class="default-media-text__media align-{$MediaAlignment}">
            <% if $Video %>
                <div class="default-media-text__video" data-video-modal="{$Video.IframeURL}">
                    <% if $Image %>
                        <picture data-as="background">
                            <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                            <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                            <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                            <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                            <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" style="object-fit: cover; object-position: {$getImageFocusPosition($Image.ID)}">
                        </picture>
                    <% else_if $Video %>
                        <img data-as="background" loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                    <% end_if %>
                </div>
            <% else %>
                <div class="default-media-text__image">
                    <picture data-as="background">
                        <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                        <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                        <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                        <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                        <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}" style="object-fit: cover; object-position: {$getImageFocusPosition($Image.ID)}">
                    </picture>
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
