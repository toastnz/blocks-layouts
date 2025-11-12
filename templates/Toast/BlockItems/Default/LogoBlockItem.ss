<div id="{$BlockItemID}" class="default-logo-item [ js-in-view ]" data-title="{$Title.ATT}">
    <% if $BrandLink %>
        <a href="{$BrandLink}" class="default-logo-item__link" target="_blank" aria-label="Visit the website for {$Title.ATT}. (Opens in a new tab)">
    <% end_if %>

    <picture class="default-logo-item__picture" data-title="{$Title.ATT}">
        <% with $Image %>
            <% if $Extension="svg" %>
                <img class="[ js-default-logo__image ]" loading="lazy" src="{$URL}" {$SizeAttr} loading="lazy" alt="{$Up.Title.ATT}">
            <% else %>
                <source media="(max-width: 639px)" srcset="{$ScaleMaxWidth(640).Convert('webp').URL}">
                <img class="[ js-default-logo__image ]" loading="lazy" src="{$ScaleMaxWidth(960).Convert('webp').URL}" alt="{$Up.Title.ATT}" {$ScaleMaxWidth(960).SizeAttr}>
            <% end_if %>
        <% end_with %>
    </picture>

    <% if $BrandLink %>
        </a>
    <% end_if %>
</div>
