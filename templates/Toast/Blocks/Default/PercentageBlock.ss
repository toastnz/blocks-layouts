<section id="{$HTMLID}" class="default-percentage [ js-default-percentage ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-percentage__wrap" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}__Text">
        <% loop $Items.Sort('SortOrder') %>
            <div class="default-percentage-item percentage-{$Width} [ js-in-view ] <% if not $Image && not $Title && not $Summary && not $LinkID %>default-percentage-item--space<% end_if %>">
                <% if $Image %>
                    <div class="default-percentage-item__media" data-equalize-watch="{$Top.HTMLID}_Media">
                        <picture>
                            <source media="(max-width: 479px)" srcset="{$Image.ScaleMaxWidth(480).URL}">
                            <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                            <img data-as="background" loading="lazy" src="{$Image.ScaleMaxWidth(960).URL}" alt="{$Image.Title.ATT}" width="{$Image.Width}" height="{$Image.Height}">
                        </picture>
                    </div>
                <% end_if %>

                <div class="default-percentage-item__details">
                    <div data-equalize-watch="{$Top.HTMLID}__Text">
                        <% if $Title %>
                            <span class="default-percentage-item__title">{$Title.XML}</span>
                        <% end_if %>

                        <% if $Summary %>
                            <p class="default-percentage-item__summary">{$Summary}</p>
                        <% end_if %>
                    </div>

                    <% if $LinkID %>
                        <% if $Top.SecondaryColour.ID && $Top.SecondaryColour.ID != $Top.PrimaryColour.ID %>
                            <a href="{$Link.LinkURL}" class="default-percentage-item__link button colour--{$Top.SecondaryColour.ColourClasses}" {$Link.TargetAttr}>{$Link.Title}</a>
                        <% else %>
                            <a href="{$Link.LinkURL}" class="default-percentage-item__link button {$Top.getButtonClasses($Top.PrimaryColour.ID, 'primary')}" {$Link.TargetAttr}>{$Link.Title}</a>
                        <% end_if %>
                    <% end_if %>
                </div>
            </div>
        <% end_loop %>
    </div>
</section>
