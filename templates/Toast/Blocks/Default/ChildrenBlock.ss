<% if $Items.Count %>
    <section id="{$HTMLID}" class="default-children [ js-default-children ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-children__header">
                <div class="default-children__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>

        <div class="default-children__wrap lg-up-{$Columns}" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}_Text">
            <% loop $Items %>
                <div class="default-children-item [ js-in-view ]">
                    <a href="{$Link}" class="default-children-item__link">
                        <div class="default-children-item__media" data-src="{$Image.ScaleMaxWidth(800).URL}" data-equalize-watch="{$Top.HTMLID}_Media">
                            <img src="{$Image.ScaleWidth(8).URL}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                        </div>

                        <div class="default-children-item__details">
                            <div data-equalize-watch="{$Top.HTMLID}_Text">
                                <% if $Title %>
                                    <span class="default-children-item__title">{$Title.XML}</span>
                                <% end_if %>

                                <% if $Summary %>
                                    <p class="default-children-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>

                            <% if $Top.SecondaryColour.ID && $Top.SecondaryColour.ID != $Top.PrimaryColour.ID %>
                                <span class="default-children-item__button button colour--{$Top.SecondaryColour.ColourClasses}">Read more</span>
                            <% else %>
                                <span class="default-children-item__button button {$Top.getButtonClasses($Top.PrimaryColour.ID, 'primary')}">Read more</span>
                            <% end_if %>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>
