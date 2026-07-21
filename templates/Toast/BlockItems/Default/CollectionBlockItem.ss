<div class="default-collection-item [ js-in-view ]">
    <a href="{$Link}" class="default-collection-item__link">
        <% if $FeaturedImage %>
            <div class="default-collection-item__media">
                <% with $FeaturedImage %>
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

        <div class="default-collection-item__details">
            <% if $Title %>
                <% if $Parent.Heading %>
                    <h3 class="default-collection-item__title">{$Title}</h3>
                <% else %>
                    <h2 class="default-collection-item__title">{$Title}</h2>
                <% end_if %>
            <% end_if %>

            <% if $Summary %>
                <p class="default-collection-item__summary">{$Summary}</p>
            <% end_if %>

            <span class="default-collection-item__button read-more">Read more</span>
        </div>
    </a>
</div>
