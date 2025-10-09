<colour-block id="{$BlockID}" tabIndex="0" class="default-children [ js-default-children ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    <% if $Heading %>
                        <h2 class="default-children__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-children__wrap columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items %>
                    <% include Toast\BlockItems\Default\ChildrenBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
