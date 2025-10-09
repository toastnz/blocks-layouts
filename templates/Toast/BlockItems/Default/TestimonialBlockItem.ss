<div id="{$BlockItemID}" class="default-testimonial-item">
    <div class="default-testimonial-item__wrap">
        <div class="default-testimonial-item__content">
            <b>&#8220;</b>
            <p>{$Summary}&#8221;</p>
        </div>

        <div class="default-testimonial-item__details">
            <% if $Image %>
                <% with $Image %>
                    <% if $Extension="svg" %>
                        <img loading="lazy" src="{$URL}" alt="{$Title.ATT}" width="100" height="100" loading="lazy" alt="{$Title.ATT}">
                    <% else %>
                        <img class="default-testimonial-item__image" src="{$FocusFill(200,200).Convert('webp').URL}" width="100" height="100" loading="lazy" alt="{$Title.ATT}">
                    <% end_if %>
                <% end_with %>
            <% end_if %>

            <% if $Name %>
                <span class="default-testimonial-item__name">
                    <% if $Position %>
                        {$Name}, {$Position}
                    <% else %>
                        {$Name}
                    <% end_if %>
                </span>
            <% end_if %>
        </div>
    </div>
</div>
