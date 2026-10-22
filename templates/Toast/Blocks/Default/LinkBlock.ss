<colour-block id="{$BlockID}" tabindex="-1" class="default-link [ js-default-link ] {$IncludeClasses} {$ExtraClasses}">
    <section <% if $Heading %>aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-link__header">
                <div class="default-link__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-link__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-link__wrap columns-{$Columns}" role="list">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% include Toast\BlockItems\Default\LinkBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
