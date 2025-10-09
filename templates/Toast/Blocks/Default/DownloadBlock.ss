<colour-block id="{$BlockID}" tabIndex="0" class="default-download [ js-default-download ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-download__header">
                <div class="default-download__content">
                    <% if $Heading %>
                        <h2 class="default-download__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-download__wrap">
            <div class="default-download__list">
                <% if $Items.Count %>
                    <% loop $Items.Sort('SortOrder') %>
                        <% include Toast\BlockItems\Default\DownloadBlockItem %>
                    <% end_loop %>
                <% end_if %>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
