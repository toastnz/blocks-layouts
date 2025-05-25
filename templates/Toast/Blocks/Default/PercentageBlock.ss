<colour-block id="{$BlockID}" tabIndex="0" class="default-percentage [ js-default-percentage ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <% if $Heading || $Content %>
            <div class="default-percentage__header">
                <div class="default-percentage__content">
                    <% if $Heading %>
                        <h2 class="default-percentage__heading">{$Heading}</h2>
                    <% end_if %>

                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-percentage__wrap">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <div id="{$BlockItemID}" class="default-percentage-item percentage-{$Width} [ js-in-view ] <% if not $Image && not $Title && not $Summary && not $LinkID %>default-percentage-item--space<% end_if %>">
                        <% if $Image %>
                            <div class="default-percentage-item__media">
                                <% with $Image %>
                                    <picture>
                                        <% if $Extension="svg" %>
                                            <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="960" height="960" loading="lazy" alt="{$Title.ATT}">
                                        <% else %>
                                            <source media="(max-width: 479px)" srcset="{$FocusFillMax(480,480).Convert('webp').URL}">
                                            <img loading="lazy" src="{$FocusFillMax(960,960).Convert('webp').URL}" alt="{$Title.ATT}" width="960" height="960" style="object-position: {$FocusPosition}">
                                        <% end_if %>
                                    </picture>
                                <% end_with %>
                            </div>
                        <% end_if %>

                        <div class="default-percentage-item__details">
                            <% if $Title %>
                                <span class="default-percentage-item__title">{$Title}</span>
                            <% end_if %>

                            <% if $Summary %>
                                <p class="default-percentage-item__summary">{$Summary}</p>
                            <% end_if %>

                            <% if $LinkID %>
                                <a href="{$Link.LinkURL}" class="default-percentage-item__link read-more" {$Link.LinkAttributes}>{$Link.Title}</a>
                            <% end_if %>
                        </div>
                    </div>
                <% end_loop %>
            <% end_if %>
        </div>
    </section>

    {$ExtraRequirements}
</colour-block>
