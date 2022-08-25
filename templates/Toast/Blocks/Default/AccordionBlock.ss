<%------------------------------------------------------------------
Accordion Block
------------------------------------------------------------------%>

<section class="accordionBlock block">

    <div class="accordionBlock__wrap">

        <% loop $Items %>

            <%------------------------------------------------------------------
            Accordion Item
            ------------------------------------------------------------------%>

            <div class="accordionBlock__wrap__item [ js-accordion-item ]">

                <div href="#" class="accordionBlock__wrap__item__heading [ js-accordion-trigger ]">
                    <h5 class="accordionBlock__wrap__item__heading__title">$Title</h5>
                </div>

                <div class="accordionBlock__wrap__item__content [ js-accordion-target ]">
                    <div class="accordionBlock__wrap__item__content__inner">
                        $Content
                    </div>
                </div>

            </div>
            
        <% end_loop %>

    </div>
</section>