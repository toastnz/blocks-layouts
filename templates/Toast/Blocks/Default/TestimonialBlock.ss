<colour-block id="{$BlockID}" tabindex="-1" class="default-testimonial [ js-default-testimonial ] {$IncludeClasses} {$ExtraClasses}">
    <section aria-roledescription="carousel"<% if $Heading %> aria-labelledby="{$BlockID}_heading"<% else %> aria-label="Testimonials"<% end_if %>>
        <% if $Heading || $Content %>
            <div class="default-testimonial__header">
                <div class="default-testimonial__content">
                    <% if $Heading %>
                        <h2 id="{$BlockID}_heading" class="default-testimonial__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-testimonial__wrap [ js-default-testimonial__wrap ]">
            <div class="default-testimonial__list [ js-default-testimonial__list ]">
                <div class="default-testimonial__slides  [ js-default-testimonial__slides ]">
                    <% if $Items.Count %>
                        <% loop $Items.Sort('SortOrder') %>
                            <% include Toast\BlockItems\Default\TestimonialBlockItem %>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>

        {$ExtraRequirements}
    </section>
</colour-block>
