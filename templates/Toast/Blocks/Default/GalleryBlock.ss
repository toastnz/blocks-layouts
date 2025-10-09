<colour-block id="{$BlockID}" tabIndex="0" class="default-gallery [ js-default-gallery ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-gallery__header">
                <div class="default-gallery__content">
                    <% if $Heading %>
                        <h2 class="default-gallery__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-gallery__wrap [ js-default-gallery__list ] columns-{$Columns}">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <% include Toast\BlockItems\Default\GalleryBlockItem %>
                <% end_loop %>
            <% end_if %>
        </div>

        <div class="default-gallery-modal [ js-default-gallery__modal ]">
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
