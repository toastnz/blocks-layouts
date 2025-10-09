<% with $File %>
    <a id="{$Up.BlockItemID}" href="{$Link}" class="default-download-item [ js-in-view ]" download="{$Up.Title.ATT}">
        <div class="default-download-item__background"></div>

        <div class="default-download-item__title">
            <span>{$Up.Title}</span>
        </div>

        <div class="default-download-item__description">
            <span>{$Up.Summary}</span>
        </div>

        <div class="default-download-item__info">
            <span>{$Extension.upperCase} {$Size}</span>
        </div>

        <div class="default-download-item__icon">
            <span></span>
        </div>
    </a>
<% end_with %>
