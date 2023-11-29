<% if $Items.Count %>
    <section id="{$HTMLID}" class="default-testimonial [ js-default-testimonial ] background-colour--c-white {$IncludeClasses} {$ExtraClasses}">
        <div class="default-testimonial__wrap">
            <div class="default-testimonial__list colour--{$SecondaryColour.ColourClasses}">
                <div class="default-testimonial__slider [ js-default-testimonial__container ]">
                    <% loop $Items.Sort('SortOrder') %>
                        <div class="default-testimonial-item background-colour--{$Top.PrimaryColour.ColourClasses}">
                            <div class="default-testimonial-item__wrap">
                                <div class="default-testimonial-item__content">
                                    <b>&#8220;</b>
                                    <p class="colour--{$Top.SecondaryColour.ColourClasses}">{$Summary}&#8221;</p>
                                </div>

                                <div class="default-testimonial-item__details">
                                    <% if $Image %>
                                        <img class="default-testimonial-item__image" src="{$Image.FocusFill(100,100).URL}" width="100" height="100" loading="lazy" alt="{$Image.Title.ATT}">
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
                </div>
            </div>
        </div>
    </section>
<% end_if %>
