<section id="{$HTMLID}" class="default-percentage [ js-default-percentage ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-percentage__wrap" data-equalize="{$HTMLID}__items">
        <% loop $Items %>
            <div class="default-percentage-item percentage-{$Width}">
                <% if $Image %>
                    <div class="default-percentage-item__media" data-src="{$Image.FocusFill(640,480).URL}">
                        <img loading="lazy" src="{$Image.Fill(64,48).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                    </div>
                <% else_if $Icon %>
                    <img src="{$Icon.URL}" class="default-percentage-item__icon">
                <% end_if %>

                <div class="default-percentage-item__details background-colour--{$Top.SecondaryColour.getColourClasses}">
                    <div data-equalize-watch="{$Top.HTMLID}__items">
                        <span class="default-percentage-item__title">{$Title}</span>
                        <div class="default-percentage-item__summary">{$Summary}</div>
                    </div>
                    <% if $LinkID %>
                        <a href="{$Link.LinkURL}" class="default-percentage-item__link" {$TargetAttr}>{$Link.Title}</a>
                    <% end_if %>
                </div>
            </div>
        <% end_loop %>
    </div>
</section>