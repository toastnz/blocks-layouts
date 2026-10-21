<div id="{$BlockItemID}" class="default-accordion-item [ js-default-accordion__item js-in-view ]">
    <button type="button" id="{$BlockItemID}_trigger" class="default-accordion-item__header [ js-default-accordion__trigger ]" aria-controls="{$BlockItemID}_panel" aria-expanded="<% if $Parent.AccordionDisplay == 'all-open' %>true<% else_if $Parent.AccordionDisplay == 'first-open' && $IsFirst %>true<% else %>false<% end_if %>">
        <span class="default-accordion-item__title">{$Title}</span>
    </button>

    <div id="{$BlockItemID}_panel" class="default-accordion-item__wrap" role="region" aria-labelledby="{$BlockItemID}_trigger">
        <div class="default-accordion-item__content">
            <div class="default-accordion-item__text">
                {$Content}
            </div>
        </div>
    </div>
</div>
