<colour-block id="{$BlockID}" tabIndex="0" class="default-image [ js-default-image ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-image__wrap">
            <div class="default-image__media">
                <% if $Heading %>
                    <h2 class="default-image__heading">{$Heading.XML}</h2>
                <% end_if %>

                <% with $Image %>
                    <picture class="default-image__image">
                        <% if $Extension="svg" %>
                            <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}">
                        <% else %>
                            <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                            <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                            <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                            <source media="(max-width: 1919px)" srcset="{$ScaleMaxWidth(1920).URL}">
                            <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}" style="object-position: {$FocusPosition}">
                        <% end_if %>
                    </picture>
                <% end_with %>
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
