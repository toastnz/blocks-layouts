<colour-block id="{$BlockID}" tabindex="-1" class="default-collection {$IncludeClasses} {$ExtraClasses}">
    <section <% if $Heading %>aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-collection__header">
                <div class="default-collection__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-collection__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-collection__wrap columns-{$Columns}" role="list">
            <% if $RelevantPages.Count %>
                <% loop $RelevantPages %>
                    <% include Toast\BlockItems\Default\CollectionBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

    </section>

    {$ExtraRequirements}
</colour-block>
