<div id="{$BlockItemID}" class="default-percentage-item percentage-{$Width} [ js-in-view ] <% if not $Image && not $Title && not $Summary && not $LinkID %>default-percentage-item--space<% end_if %>">
    <% if $Image %>
        <div class="default-percentage-item__media">
            <% with $Image %>
                <picture>
                    <% if $Extension="svg" %>
                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="960" height="960" loading="lazy" alt="{$Title.ATT}">
                    <% else %>
                        <source media="(max-width: 639px)" srcset="{$FocusFillMax(640,640).Convert('webp').URL}">
                        <img loading="lazy" src="{$FocusFillMax(960,960).Convert('webp').URL}" alt="{$Title.ATT}" width="960" height="960" style="object-position: {$FocusPosition}">
                    <% end_if %>
                </picture>
            <% end_with %>
        </div>
    <% end_if %>

    <div class="default-percentage-item__details">
        <% if $Title %>
            <% if $Top.Heading %>
                <h3 class="default-percentage-item__title">{$Title}</h3>
            <% else %>
                <h2 class="default-percentage-item__title">{$Title}</h2>
            <% end_if %>
        <% end_if %>

        <% if $Summary %>
            <p class="default-percentage-item__summary">{$Summary}</p>
        <% end_if %>

        <% if $LinkID %>
            <a href="{$Link.LinkURL}" class="default-percentage-item__link read-more" {$Link.LinkAttributes}>{$Link.Title}</a>
        <% end_if %>
    </div>
</div>
