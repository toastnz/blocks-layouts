<div id="{$BlockItemID}" class="default-link-item [ js-in-view ]" role="listitem">
    <a href="{$Link.URL}" {$Link.LinkAttributes} class="default-link-item__link">
        <% if $Image %>
            <div class="default-link-item__media">
                <% with $Image %>
                    <picture>
                        <% if $Extension="svg" %>
                            <img loading="lazy" src="{$URL}" alt="<% if not $Up.Title %>{$Title.ATT}<% end_if %>" width="960" height="960">
                        <% else %>
                            <source media="(max-width: 479px)" srcset="{$FocusFillMax(480,480).Convert('webp').URL}">
                            <img loading="lazy" src="{$FocusFillMax(960,960).Convert('webp').URL}" alt="<% if not $Up.Title %>{$Title.ATT}<% end_if %>" width="960" height="960" style="object-position: {$FocusPosition}">
                        <% end_if %>
                    </picture>
                <% end_with %>
            </div>
        <% end_if %>

        <div class="default-link-item__details">
            <% if $Title %>
                <% if $Parent.Heading %>
                    <h3 class="default-link-item__title">{$Title}</h3>
                <% else %>
                    <h2 class="default-link-item__title">{$Title}</h2>
                <% end_if %>
            <% end_if %>

            <% if $Summary %>
                <p class="default-link-item__summary">{$Summary}</p>
            <% end_if %>

            <span class="default-link-item__button read-more">{$Link.Title}</span>
        </div>
    </a>
</div>
