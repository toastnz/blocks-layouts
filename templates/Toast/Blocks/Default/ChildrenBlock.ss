<colour-block id="{$BlockID}" tabindex="-1" class="default-children [ js-default-children ] {$IncludeClasses} {$ExtraClasses}">
    <section<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-children__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-children__wrap columns-{$Columns}" role="list">
            <% if $Items.Count %>
                <% loop $Items %>
                    <% include Toast\BlockItems\Default\ChildrenBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
