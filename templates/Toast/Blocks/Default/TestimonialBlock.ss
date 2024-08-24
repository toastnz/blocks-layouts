<colour-block id="{$BlockID}" tabIndex="0" class="default-testimonial [ js-default-testimonial ] theme-colour--{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-testimonial__wrap [ js-default-testimonial__wrap ]">
            <div class="default-testimonial__list [ js-default-testimonial__list ]">
                <div class="default-testimonial__slides  [ js-default-testimonial__slides ]">
                    <% if $Items.Count %>
                        <% loop $Items.Sort('SortOrder') %>
                            <div id="{$BlockItemID}" class="default-testimonial-item">
                                <div class="default-testimonial-item__wrap">
                                    <div class="default-testimonial-item__content">
                                        <b>&#8220;</b>
                                        <p>{$Summary}&#8221;</p>
                                    </div>

                                    <div class="default-testimonial-item__details">
                                        <% if $Image %>
                                            <% with $Image %>
                                                <img class="default-testimonial-item__image" src="{$FocusFill(100,100).URL}" width="100" height="100" loading="lazy" alt="{$Title.ATT}">
                                            <% end_with %>
                                        <% end_if %>

                                        <% if $Name %>
                                            <span class="default-testimonial-item__name">
                                                <% if $Position %>
                                                    {$Name}, {$Position}
                                                <% else %>
                                                    {$Name}
                                                <% end_if %>
                                            </span>
                                        <% end_if %>
                                    </div>
                                </div>
                            </div>
                        <% end_loop %>
                    <% end_if %>
                </div>
            </div>
        </div>

        {$ExtraRequirements}
    </section>
</colour-block>
