<%------------------------------------------------------------------
Testimonial block
------------------------------------------------------------------%>

<% if $Testimonials.Count %>
    <section class="testimonialBlock contentBlock">
        
        <div class="testimonialBlock__wrap">

            <%------------------------------------------------------------------
            Testimonial slider
            ------------------------------------------------------------------%>
            <div class="<% if $ShowSlider %>[ js-slider--testimonials ] slider<% else %>stack<% end_if %>">
                <% loop $Testimonials %>

                    <%------------------------------------------------------------------
                    Testimonial item
                    ------------------------------------------------------------------%>
                    <div class="testimonialBlock__wrap__item item">

                        <div class="testimonialBlock__wrap__item__quote">
                            <p>{$Testimonial.XML}</p>
                        </div>
                        <div class="testimonialBlock__wrap__item__credit">
                            <% if $Up.ShowNameAndLocation %>
                                <p><span>{$Title.XML}<% if $Location %>,<% end_if %></span> {$Location.XML}</p>
                            <% end_if %>
                        </div>
                    </div>
                <% end_loop %>

            </div>

        </div>

    </section>
<% end_if %>