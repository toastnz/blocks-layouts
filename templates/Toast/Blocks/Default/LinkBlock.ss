<% if $Items.Count %>
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
                <div class="default-link-item [ js-in-view ]">
                    <a href="{$Link.LinkURL}" {$Link.TargetAttr} class="default-link-item__link">
                        <div class="default-link-item__media" data-equalize-watch="{$Top.HTMLID}_Media">
                            <picture>
                                <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                                <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                                <img data-as="background" loading="lazy" src="{$Image.ScaleMaxWidth(960).URL}" alt="{$Image.Title.ATT}" width="{$FeaturedImage.Width}" height="{$FeaturedImage.Height}" style="object-fit: cover; object-position: {$getImageFocusPosition($Image.ID)}">
                            </picture>
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

                            <% if $Top.SecondaryColour.ID && $Top.SecondaryColour.ID != $Top.PrimaryColour.ID %>
                                <span class="default-link-item__button button colour--{$Top.SecondaryColour.ColourClasses}">{$Link.Title}</span>
                            <% else %>
                                <span class="default-link-item__button button {$Top.getButtonClasses($Top.PrimaryColour.ID, 'primary')}">{$Link.Title}</span>
                            <% end_if %>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
