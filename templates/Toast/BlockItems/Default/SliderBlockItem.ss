<div class="default-slider-item">
    <div class="default-slider-item__layout">
        <div class="default-slider-item__media">
            <picture>
                <% if $Extension="svg" %>
                    <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" {$SizeAttr} loading="lazy" alt="{$Title.ATT}">
                <% else %>
                    <% with $Convert('webp') %>
                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                        <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                        <source media="(max-width: 1439px)" srcset="{$ScaleMaxWidth(1440).URL}">
                        <img loading="lazy" src="{$ScaleMaxWidth(1920).URL}" alt="{$Title.ATT}" {$ScaleMaxWidth(1920).SizeAttr} style="object-position: {$FocusPosition}">
                    <% end_with %>
                <% end_if %>
            </picture>
        </div>
    </div>
</div>
