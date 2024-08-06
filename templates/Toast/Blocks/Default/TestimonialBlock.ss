<% if $Items.Count %>
    <section id="{$HTMLID}" tabIndex="0" class="default-testimonial [ js-default-testimonial ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-testimonial__wrap">
            <div class="default-testimonial__list">
                <div class="[ js-default-testimonial__container ]">
                    <% loop $Items.Sort('SortOrder') %>
                        <div class="default-testimonial-item">
                            <div class="default-testimonial-item__wrap">
                                <div class="default-testimonial-item__content">
                                    <b>"</b>
                                    <p>{$Summary}</p>
                                    <b>"</b>
                                </div>

                                <div class="default-testimonial-item__details">
                                    <% if $Image %>
                                        <img class="default-testimonial-item__image" src="{$Image.FocusFill(100,100).URL}" width="100" height="100" loading="lazy" alt="{$Image.Title.ATT}">
                                    <% end_if %>

                                    <% if $Name %>
                                        <span class="default-testimonial-item__name">{$Name}</span>
                                    <% end_if %>

                                    <% if $Position %>
                                        <span class="default-testimonial-item__position">{$Position}</span>
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
