<colour-block id="{$BlockID}" tabindex="-1" class="default-image [ js-default-image ] {$IncludeClasses} {$ExtraClasses}">
    <section <% if $Heading %>aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <div class="default-image__wrap">
            <div class="default-image__media">
                <% if $Heading %>
                    <h2 id="{$BlockID}_heading" class="default-image__heading">{$Heading}</h2>
                <% end_if %>

                <% if $Image %>
                    <% with $Image %>
                        <picture class="default-image__image">
                            <% if $Extension="svg" %>
                                <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr}>
                            <% else %>
                                <% with $Convert('webp') %>
                                    <source media="(max-width: 639px)" srcset="{$ScaleMaxWidth(640).URL}">
                                    <source media="(max-width: 959px)" srcset="{$ScaleMaxWidth(960).URL}">
                                    <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                    <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(1920).SizeAttr} style="object-position: {$FocusPosition}">
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
