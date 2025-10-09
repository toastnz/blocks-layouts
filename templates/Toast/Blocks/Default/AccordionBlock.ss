<colour-block id="{$BlockID}" tabIndex="0" class="default-accordion [ js-default-accordion ] {$IncludeClasses} {$ExtraClasses}" data-state="{$AccordionDisplay}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-accordion__header">
                <div class="default-accordion__content">
                    <% if $Heading %>
                        <h2 class="default-accordion__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-accordion__wrap">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% include Toast\BlockItems\Default\AccordionBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
