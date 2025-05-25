<colour-block id="{$BlockID}" tabIndex="0" class="default-video [ js-default-video ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-video__wrap">
            <div class="default-video__media">
                <% if $Heading %>
                    <h2 class="default-video__heading">{$Heading}</h2>
                <% end_if %>

                <a id="{$BlockID}_{$Video.VideoID}" class="default-video__link" data-video<% if $OpenInModal %>-modal<% end_if %>="{$Video.IframeURL}">
                    <div class="default-video__thumbnail">
                        <% if $Thumbnail %>
                            <% with $Thumbnail %>
                                <picture>
                                    <% if $Extension="svg" %>
                                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                                    <% else %>
                                        <% with $Convert('webp') %>
                                            <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                            <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                            <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                            <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(1920).SizeAttr} style="object-position: {$FocusPosition}">
                                        <% end_with %>
                                    <% end_if %>
                                </picture>
                            <% end_with %>
                        <% else_if $Video %>
                            <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
                        <% end_if %>
                    </div>
                    <div class="default-video__icon"></div>
                </a>

                <% if $Content %>
                    <div class="default-video__caption">
                        {$Content}
                    </div>
                <% end_if %>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
