<colour-block id="{$BlockID}" tabindex="-1" class="default-resources [ js-default-resources ] {$IncludeClasses} {$ExtraClasses}">
    <section<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-resources__header">
                <div class="default-resources__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-resources__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-resources__wrap">
            <div class="default-resources__list">
                <% if $Items.Count %>
                    <% loop $Items.Sort('SortOrder') %>
                        <% include Toast\BlockItems\Default\ResourcesBlockItem %>
                    <% end_loop %>
                <% end_if %>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
