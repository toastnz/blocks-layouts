<colour-block id="{$BlockID}" tabindex="-1" class="default-blog [ js-default-blog ] {$IncludeClasses} {$ExtraClasses}">
    <section<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-blog__header">
                <div class="default-blog__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-blog__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-blog__wrap columns-{$Columns}" role="list">
            <% if $Posts.Count %>
                <% loop $Posts %>
                    <% include Toast\BlockItems\Default\BlogBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

    </section>

    {$ExtraRequirements}
</colour-block>
