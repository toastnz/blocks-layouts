<section id="{$HTMLID}" class="default-percentage [ js-default-percentage ] background-colour--{$PrimaryColour.ColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-percentage__wrap" data-match-height="{$HTMLID}_Media" data-equalize="{$HTMLID}__Text">
        <% loop $Items %>
            <div class="default-percentage-item percentage-{$Width} [ js-in-view ]">
                <% if $Image %>
                    <div class="default-percentage-item__media" data-src="{$Image.ScaleMaxWidth(800).URL}" data-equalize-watch="{$Top.HTMLID}_Media">
                        <img loading="lazy" src="{$Image.ScaleWidth(8).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                    </div>
                <% end_if %>

                <div class="default-percentage-item__details">
                    <div data-equalize-watch="{$Top.HTMLID}__Text">
                        <span class="default-percentage-item__title">{$Title.XML}</span>
                        <p class="default-percentage-item__summary">{$Summary}</p>
                    </div>

                    <% if $LinkID %>
                        <a href="{$Link.LinkURL}" class="default-percentage-item__link colour--{$Top.SecondaryColour.ColourClasses}" {$Link.TargetAttr}>{$Link.Title}</a>
                    <% end_if %>
                </div>
            </div>
        <% end_loop %>
    </div>
</section>
