<colour-block id="{$BlockID}" tabIndex="0" class="default-collection {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-collection__header">
                <div class="default-collection__content">
                    <% if $Heading %>
                        <h2 class="default-collection__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-collection__wrap columns-{$Columns}">
            <% if $RelevantPages.Count %>
                <% loop $RelevantPages %>
                    <% include Toast\BlockItems\Default\CollectionBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

    </section>

    {$ExtraRequirements}
</colour-block>
