<colour-block id="{$BlockID}" tabindex="-1" class="default-gallery [ js-default-gallery ] {$IncludeClasses} {$ExtraClasses}">
    <section <% if $Heading %>aria-labelledby="{$BlockID}_heading"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-gallery__header">
                <div class="default-gallery__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-gallery__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-gallery__wrap [ js-default-gallery__list ] columns-{$Columns}" role="list">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% include Toast\BlockItems\Default\GalleryBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="default-gallery-modal [ js-default-gallery__modal ]" role="dialog" aria-modal="true" aria-label="<% if $Heading %>{$Heading.ATT} – gallery<% else %>Gallery<% end_if %>">
            <div class="default-gallery-modal__container [ js-default-gallery__container ]">
                <div class="default-gallery-modal__slider [ js-default-gallery__slider ]">
                    <% if $Items.Count %>
                        <% loop $Items.Sort('SortOrder') %>
                            <% include Toast\BlockItems\Default\GalleryBlockModalItem %>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
