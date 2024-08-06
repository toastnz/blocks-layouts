<% if $Items.Count %>
    <section id="{$HTMLID}" tabIndex="0" class="default-children [ js-default-children ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
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
                        <div class="default-children-item__media" data-equalize-watch="{$Top.HTMLID}_Media">
                            <% with $Image %>
                                <picture>
                                    <source media="(max-width: 479px)" srcset="{$ScaleMaxWidth(480).URL}">
                                    <source media="(max-width: 767px)" srcset="{$ScaleMaxWidth(768).URL}">
                                    <img data-as="background" loading="lazy" src="{$ScaleMaxWidth(960).URL}" alt="{$Title.ATT}" width="{$Width}" height="{$Height}" style="object-fit: cover; object-position: {$FocusPosition}">
                                </picture>
                            <% end_with %>
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
