<a id="{$BlockItemID}" href="{$File.Link}" class="default-resources-item [ js-in-view ]" download="{$Title.ATT}">
    <div class="default-resources-item__background"></div>

    <div class="default-resources-item__title">
        <span>{$Title}</span>
    </div>

    <div class="default-resources-item__description">
        <span>{$Summary}</span>
    </div>

    <% with $File %>
        <div class="default-resources-item__info">
            <span>{$Extension.UpperCase} {$Size}</span>
        </div>
    <% end_with %>

    <div class="default-resources-item__icon" data-icon="{$Extension.LowerCase}">
        <span></span>
    </div>
</a>
