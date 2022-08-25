<% if $Tabs %>
<section class="tabBlock block [ js-tabs ]">
    <div class="tabBlock__wrap">

        <div class="tabBlock__wrap__tabs">
            
            <div class="tabBlock__wrap__tabs__indicator [ js-tabbed-indicator ]"></div>
            
            <% loop $Tabs %>
                <a href="#" class="tabBlock__wrap__tabs__item [ js-tabs-link ] <% if $First %>active<% end_if %>">
                    <h6>$Title.XML</h6>
                </a>
            <% end_loop %>
        </div>

        <div class="tabBlock__wrap__content">
            <% loop $Tabs %>
            <div class="tabBlock__wrap__content__item [ js-tabs-item ] <% if $First %>active<% end_if %>">
                $Content
            </div>
            <% end_loop %>
        </div>
    </div>
</section>
<% end_if %>