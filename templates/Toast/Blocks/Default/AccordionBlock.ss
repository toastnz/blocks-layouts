<% if $Items.Count %>
    <section id="{$HTMLID}" class="default-accordion [ js-default-accordion ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-accordion__header">
                <div class="default-accordion__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>
    
        <div class="default-accordion__wrap">
            <% loop $Items.Sort('SortOrder') %>
                <div class="default-accordion-item [ js-default-accordion__item ]">
                    <div class="default-accordion-item__header background-colour--{$Top.SecondaryColour.getColourClasses} [ js-default-accordion__trigger ]">
                        <span class="default-accordion-item__title">{$Title} {$SVG('accordion')}</span>
                    </div>
    
                    <div class="default-accordion-item__content [ js-default-accordion__target ]">
                        <div class="default-accordion-item__wrap">
                            {$Content}
                        </div>
                    </div>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
