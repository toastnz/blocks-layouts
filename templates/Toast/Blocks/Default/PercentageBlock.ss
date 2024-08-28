<colour-block id="{$BlockID}" tabIndex="0" class="default-percentage [ js-default-percentage ] {$IncludeClasses} {$ExtraClasses}">
    <section>
        <div class="default-percentage__wrap" data-match-height="{$BlockID}_Media" data-equalize="{$BlockID}__Text">
            <% if $Items.Count %>
                <% loop $Items.Sort('SortOrder') %>
                    <div id="{$BlockItemID}" class="default-percentage-item percentage-{$Width} [ js-in-view ] <% if not $Image && not $Title && not $Summary && not $LinkID %>default-percentage-item--space<% end_if %>">
                        <% if $Image %>
                            <div class="default-percentage-item__media" data-equalize-watch="{$Top.BlockID}_Media">
                                <% with $Image %>
                                    <picture>
                                        <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                        <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                        <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                    </picture>
                                <% end_with %>
                            </div>
                        <% end_if %>

                        <div class="default-percentage-item__details">
                            <div data-equalize-watch="{$Top.BlockID}__Text">
                                <% if $Title %>
                                    <span class="default-percentage-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-percentage-item__summary">{$Summary}</p>
                                <% end_if %>
                            </div>

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
