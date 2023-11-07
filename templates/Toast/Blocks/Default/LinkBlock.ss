<section id="{$HTMLID}" class="default-link [ js-default-link ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <% if $Content %>
        <div class="default-link__header">
            <div class="default-link__content">
                {$Content}
            </div>
        </div>
    <% end_if %>

    <div class="default-link__wrap lg-up-{$Columns}" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}_Text">
        <% loop $Items.Sort('SortOrder') %>
            <div class="default-link-item">
                <a href="{$Link.LinkURL}" class="default-link-item__link">
                    <div class="default-link-item__media" data-src="{$Image.ScaleMaxWidth(800).URL}" data-equalize-watch="{$Top.HTMLID}_Media">
                        <img src="{$Image.ScaleWidth(8).URL}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                        <div class="default-link-item__icon" data-src="{$Icon.URL}"></div>
                    </div>

                    <div class="default-link-item__details">
                        <div data-equalize-watch="{$Top.HTMLID}_Text">
                            <% if $Title %>
                                <span class="default-link-item__title">{$Title.XML}</span>
                            <% end_if %>

                            <% if $Summary %>
                                <p class="default-link-item__summary">{$Summary.XML}</p>
                            <% end_if %>
                        </div>

                        <span class="default-link-item__button colour--{$Top.SecondaryColour.ColourClasses}">{$Link.Title}</span>
                    </div>
                </a>
            </div>
        <% end_loop %>
    </div>
</section>
