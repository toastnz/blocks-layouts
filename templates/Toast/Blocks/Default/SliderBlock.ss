<colour-block id="{$BlockID}" tabindex="-1" class="default-slider [ js-default-slider ] {$IncludeClasses} {$ExtraClasses}">
    <section aria-roledescription="carousel"<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% else %> aria-label="Image carousel"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-slider__header">
                <div class="default-slider__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-slider__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-slider__wrap [ js-default-slider__wrap ]">
            <div class="default-slider__container [ js-default-slider__container ]">
                <div class="default-slider__slider [ js-default-slider__slider ]">
                    <% if $Images.Count %>
                        <% loop $Images.Sort('Sort') %>
                            <% include Toast\BlockItems\Default\SliderBlockItem %>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
