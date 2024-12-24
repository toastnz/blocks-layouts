<colour-block id="{$BlockID}" tabIndex="0" class="default-accordion [ js-default-accordion ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-accordion__header">
                <div class="default-accordion__content">
                    <% if $Heading %>
                        <h2 class="default-accordion__heading">{$Heading.XML}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-accordion__wrap">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <div id="{$BlockItemID}" class="default-accordion-item [ js-default-accordion__item js-in-view ]">
                        <div class="default-accordion-item__header [ js-default-accordion__trigger ]">
                            <span class="default-accordion-item__title">{$Title}</span>
                        </div>

                        <div class="default-accordion-item__wrap">
                            <div class="default-accordion-item__content">
                                <div class="default-accordion-item__text">
                                    {$Content}
                                </div>
                            </div>
                        </div>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
