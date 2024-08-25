<colour-block id="{$BlockID}" tabIndex="0" class="default-accordion [ js-default-accordion ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Content %>
            <div class="default-accordion__header">
                <div class="default-accordion__content">
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

                        <div class="default-accordion-item__content [ js-default-accordion__target ]">
                            <div class="default-accordion-item__wrap">
                                {$Content}
                            </div>
                        </div>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
