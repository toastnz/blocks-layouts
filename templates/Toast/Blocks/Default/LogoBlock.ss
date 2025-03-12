<colour-block id="{$BlockID}" class="default-logo [ js-default-logo ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-logo__header">
                <div class="default-logo__content">
                    <% if $Heading %>
                        <h2 class="default-logo__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-logo__wrap columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% if $Image %>
                        <div id="{$BlockItemID}" class="default-logo-item [ js-in-view ]">
                            <% if $BrandLink %>
                                <a href="{$BrandLink}" class="default-logo-item__link" target="_blank" aria-label="Visit the website for {$Title.ATT}. (Opens in a new tab)">
                            <% end_if %>

                            <% with $Image %>
                                <picture class="default-logo-item__picture">
                                    <% if $Extension="svg" %>
                                        <img class="[ js-default-logo__image ]" loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                                    <% else %>
                                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).Convert('webp').URL}">
                                        <img class="[ js-default-logo__image ]" loading="lazy" src="{$ScaleMaxWidth(960).Convert('webp').URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(960).SizeAttr}>
                                    <% end_if %>
                                </picture>
                            <% end_with %>

                            <% if $BrandLink %>
                                </a>
                            <% end_if %>
                        </div>
                    <% end_if %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
