<%----------------------------------------------------------------
Video Block
----------------------------------------------------------------%>

<section class="videoBlock block">
    <a class="videoBlock__wrap  [ js-video-modal ]" data-video-id="$Video" data-video-type="youtube">
        <div href="#" class="videoBlock__wrap__background" style="background-image: url('<% if $ThumbnailID %>$Thumbnail.focusFill(1920,1080).URL<% else %>$Video.ThumbnailURL<% end_if %>');"></div>
        <div class="videoBlock__wrap__icon">$SVG('play')</div>
    </a>
</section>