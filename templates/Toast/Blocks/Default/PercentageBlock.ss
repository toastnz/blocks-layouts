<colour-block id="{$BlockID}" tabIndex="0" class="default-percentage [ js-default-percentage ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-percentage__header">
                <div class="default-percentage__content">
                    <% if $Heading %>
                        <h2 class="default-percentage__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-percentage__wrap">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% include Toast\BlockItems\Default\PercentageBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
