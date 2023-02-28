<section class="default-accordion {$IncludeClasses} {$ExtraClasses} background-colour--{$getColourForTemplate($PrimaryColour)} [ js-accordion ]" data-state="{$AccordionDisplay}">
    <% if $Content %>
        <div class="default-accordion__header">
            <div class="default-accordion__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-accordion__wrap">
        <% loop $Items %>
            <div class="default-accordion-item [ js-accordion--item ]">
                <div class="default-accordion-item__header background-colour--{$Top.getColourForTemplate($Top.SecondaryColour) [ js-accordion--trigger ]">
                    <span class="default-accordion-item__title">{$Title} {$SVG('accordion')}</span>
                </div>

                <div class="default-accordion-item__content [ js-accordion--target ]">
                    <div class="default-accordion-item__wrap">
                        {$Content}
                    </div>
                </div>
            </div>
        <% end_loop %>
    </div>
</section>