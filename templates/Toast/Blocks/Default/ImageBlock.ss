<colour-block id="{$BlockID}" tabIndex="0" class="default-image [ js-default-image ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-image__wrap">
            <div class="default-image__media">
                <% if $Heading %>
                    <h2 class="default-image__heading">{$Heading}</h2>
                <% end_if %>

                <% if $Image %>
                    <% with $Image %>
                        <picture class="default-image__image">
                            <% if $Extension="svg" %>
                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                            <% else %>
                                <% with $Convert('webp') %>
                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                    <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                    <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(1920).SizeAttr} loading="lazy" alt="{$Title.ATT}" style="object-position: {$FocusPosition}">
                                <% end_with %>
                            <% end_if %>
                        </picture>
                    <% end_with %>
                <% end_if %>
            </div>

            <% if $Content %>
                <div class="default-image__content">
                    {$Content}
                </div>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
