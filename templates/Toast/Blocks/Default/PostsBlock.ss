<colour-block id="{$BlockID}" tabIndex="0" class="default-posts [ js-default-posts ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-posts__header">
                <div class="default-posts__content">
                    <% if $Heading %>
                        <h2 class="default-posts__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-posts__wrap columns-{$Columns}">
            <% if $Posts.Count %>
                <% loop $Posts %>
                    <% include Toast\BlockItems\Default\PostsBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

    </section>

    {$ExtraRequirements}
</colour-block>
