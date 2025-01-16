<colour-block id="{$BlockID}" tabIndex="0" class="default-media-text [ js-default-media-text ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-media-text__wrap">
            <div class="default-media-text__media align-{$MediaAlignment}">
                <% if $Video %>
                    <div class="default-media-text__video" data-video-modal="{$Video.IframeURL}">
                        <% if $Image %>
                            <% with $Image %>
                                <picture>
                                    <% if $Extension="svg" %>
                                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                                    <% else %>
                                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                        <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                        <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                        <source media="(max-width: 1919px)" srcset="{$ScaleMaxWidth(1920).URL}">
                                        <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-fit: cover; object-position: {$FocusPosition}">
                                    <% end_if %>
                                </picture>
                            <% end_with %>
                        <% else_if $Video %>
                            <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                        <% end_if %>
                    </div>
                <% else %>
                    <div class="default-media-text__image">
                        <% if $Image %>
                            <% with $Image %>
                                <picture>
                                    <% if $Extension="svg" %>
                                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                                    <% else %>
                                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                        <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                        <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                        <source media="(max-width: 1919px)" srcset="{$ScaleMaxWidth(1920).URL}">
                                        <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                    <% end_if %>
                                </picture>
                            <% end_with %>
                        <% end_if %>
                    </div>
                <% end_if %>
            </div>

            <div class="default-media-text__content">
                <div class="default-media-text__text">
                    <% if $Heading %>
                        <h2 class="default-media-text__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}

                    <% if $CTALink %>
                        <a href="{$CTALink.LinkURL}" {$CTALink.LinkAttributes} class="default-media-text__link read-more">{$CTALink.Title}</a>
                    <% end_if %>
                </div>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
