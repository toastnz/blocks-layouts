<div class="default-gallery-modal__item">
    <div class="default-gallery-modal__media">
        <div class="default-gallery-modal__image <% if $Video %>has-video<% end_if %>">
            <% if $Image %>
                <% with $Image %>
                    <picture>
                        <% if $Extension="svg" %>
                            <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                        <% else %>
                            <% with $Convert('webp') %>
                                <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                                <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" loading="lazy" alt="{$Title.ATT}" style="object-position: {$FocusPosition}">
                            <% end_with %>
                        <% end_if %>
                    </picture>
                <% end_with %>
            <% else_if $Video %>
                <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
            <% end_if %>

            <% if $Video %>
                <div class="default-gallery-modal__video" data-video="{$Video.IframeURL}">
                    <div class="default-gallery-modal__icon"></div>
                </div>
            <% end_if %>
        </div>
    </div>
</div>
