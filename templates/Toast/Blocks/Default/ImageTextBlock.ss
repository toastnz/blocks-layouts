<section class="default-image-text content-block [ js-in-view ]">
    <div class="default-image-text__wrap <% if $Width=='full' %>full<% end_if %>">
        <div class="default-image-text__media align-{$ImageAlignment}">
            <% if $Video %>
                <div class="default-image-text__video" data-video-modal="{$Video.IframeURL}" data-src="{$Image.ScaleWidth(1200).URL}">
                    <img src="{$Image.ScaleWidth(120).URL}" alt="{$Image.Title}">
                </div>
            <% else %>
                <div class="default-image-text__image" data-src="{$Image.ScaleWidth(1200).URL}">
                    <img src="{$Image.ScaleWidth(120).URL}" alt="{$Image.Title}">
                </div>
            <% end_if %>
        </div>

        <div class="default-image-text__content">
            <div class="default-image-text__text {$ExtraClasses}">

                {$Content}

                <% if $CTALink %>
                    <a href="{$CTALink.LinkURL}" {$CTALink.TargetAttr} class="default-image-text__link">{$CTALink.Title}</a>
                <% end_if %>
            </div>
        </div>
    </div>
</section>