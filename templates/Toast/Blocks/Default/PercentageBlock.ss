<section id="{$HTMLID}" class="default-percentage [ js-default-percentage ] background-colour--{$getColour($PrimaryColour, 'class, brightness')} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-percentage__wrap">
        <% loop $Items %>
            <div class="default-percentage-item percentage-{$Width}">
                <% if $Image %>
                    <div class="default-percentage-item__media" data-src="{$Image.FocusFill(640,480).URL}">
                        <img loading="lazy" src="{$Image.Fill(64,48).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}">
                    </div>
                <% else_if $Icon %>
                    <img src="{$Icon.URL}" class="default-percentage-item__icon">
                <% end_if %>

                <div class="default-percentage-item__details background-colour--{$getColour($Top.SecondaryColour, 'class, brightness')}">
                    <div data-equalize-watch="percentage-items">
                        <span class="default-percentage-item__title">{$Title}</span>
                        <p class="default-percentage-item__summary">{$Summary}</p>
                    </div>
                    <% if $LinkID %>
                        <a href="{$Link.LinkURL}" class="default-percentage-item__link" {$TargetAttr}>{$Link.Title}</a>
                    <% end_if %>
                </div>
            </div>
        <% end_loop %>
    </div>
</section>