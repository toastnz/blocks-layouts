<div class="toast-block-icon<% if $IconURL %> toast-block-icon--image<% end_if %>" data-block-id="$BlockID" title="$Name">
    <% if $IconURL %>
        <img class="toast-block-icon__image" src="$IconURL" alt="">
    <% else %>
        <span class="toast-block-icon__media $IconClass"></span>
    <% end_if %>
</div>
<span class="toast-block-title">$Name</span>
