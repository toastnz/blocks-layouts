<section class="heroBlock contentBlock">
    <div class="heroBlock__wrap">

        <% if $BackgroundImage %>
            <div class="heroBlock__wrap__background" style="background-image: url('{$BackgroundImage.FocusFill(1920,1080).URL}');"></div>
        <% end_if %>

        <div class="heroBlock__wrap__content">
            $Content
        </div>

    </div>
</section>
