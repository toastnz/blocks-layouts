
<section id="{$BlockID}" tabIndex="0" class="default-children [ js-default-children ] ThemeColour_{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-children__header">
            <div class="default-children__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-children__wrap lg-up-{$Columns}" data-match-height="{$BlockID}_Media" data-equalize="{$BlockID}_Text">
        <% if $Items.Count %>
            <% loop $Items %>
                <div class="default-children-item [ js-in-view ]">
                    <a href="{$Link}" class="default-children-item__link">
                        <div class="default-children-item__media" data-equalize-watch="{$$Top.BlockID}_Media">
                            <% with $Image %>
                                <picture>
                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                    <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                </picture>
                            <% end_with %>
                        </div>

                        <div class="default-children-item__details">
                            <div data-equalize-watch="{$$Top.BlockID}_Text">
                                <% if $Title %>
                                    <span class="default-children-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-children-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>

                            <span class="default-children-item__button read-more">Read more</span>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        <% end_if %>
    </div>

    {$ExtraRequirements}
</section>
