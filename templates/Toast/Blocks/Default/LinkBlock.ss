<section id="{$BlockID}" tabIndex="0" class="default-link [ js-default-link ] theme-colour--{$PrimaryColour.ColourCustomID} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-link__header">
            <div class="default-link__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-link__wrap lg-up-{$Columns}" data-match-height="{$BlockID}_Media" data-equalize="{$BlockID}_Text">
        <% if $Items.Count %>
            <% loop $Items.Sort('SortOrder') %>
                <div id="{$BlockItemID}" class="default-link-item [ js-in-view ]">
                    <a href="{$Link.LinkURL}" {$Link.TargetAttr} class="default-link-item__link">
                        <div class="default-link-item__media" data-equalize-watch="{$Top.BlockID}_Media">
                            <% with $Image %>
                                <picture>
                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                    <img loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-position: {$FocusPosition}">
                                </picture>
                            <% end_with %>
                        </div>

                        <div class="default-link-item__details">
                            <div data-equalize-watch="{$Top.BlockID}_Text">
                                <% if $Title %>
                                    <span class="default-link-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-link-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>

                            <span class="default-link-item__button read-more">{$Link.Title}</span>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        <% end_if %>
    </div>

    {$ExtraRequirements}
</section>
