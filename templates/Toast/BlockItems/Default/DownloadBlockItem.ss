<a id="{$BlockItemID}" href="{$File.Link}" class="default-download-item [ js-in-view ]" download="{$Title.ATT}">
    <div class="default-download-item__background"></div>

    <div class="default-download-item__title">
        <span>{$Title}</span>
    </div>

    <div class="default-download-item__description">
        <span>{$Summary}</span>
    </div>

    <% with $File %>
        <div class="default-download-item__info">
            <span>{$Extension.UpperCase} {$Size}</span>
        </div>
    <% end_with %>

    <div class="default-download-item__icon" data-icon="{$Extension.LowerCase}">
        <span></span>
    </div>
</a>
