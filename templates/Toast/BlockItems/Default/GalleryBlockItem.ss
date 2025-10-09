<div id="{$BlockItemID}" class="default-gallery-item [ js-in-view ]">
    <button class="default-gallery-item__action [ js-default-gallery__action ] <% if $Video %>has-video<% end_if %>">
        <div class="default-gallery-item__media">
            <% if $Image %>
                <% with $Image %>
                    <picture>
                        <% if $Extension="svg" %>
                            <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                        <% else %>
                            <% with $Convert('webp') %>
                                <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(960).SizeAttr} style="object-position: {$FocusPosition}">
                            <% end_with %>
                        <% end_if %>
                    </picture>
                <% end_with %>
            <% else_if $Video %>
                <img loading="lazy" src="{$Video.ThumbnailURL('large')}" alt="{$Video.Title}" width="1920" height="1080">
            <% end_if %>

            <% if $Video %>
                <div class="default-gallery-item__video">
                    <div class="default-gallery-item__icon"></div>
                </div>
            <% end_if %>
        </div>
    </button>
</div>
