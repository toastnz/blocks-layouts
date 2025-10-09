<colour-block id="{$BlockID}" class="default-logo [ js-default-logo ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-logo__header">
                <div class="default-logo__content">
                    <% if $Heading %>
                        <h2 class="default-logo__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-logo__wrap [ js-default-logo__wrap ] columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% if $Image %>
                        <% include Toast\BlockItems\Default\LogoBlockItem %>
                    <% end_if %>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
