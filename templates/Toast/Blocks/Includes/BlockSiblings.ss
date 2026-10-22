<div class="content-block-siblings">
    <% loop $Blocks %>
        <a <% if $Active %>class="active"<% end_if %> href="$Link" data-block-id="$BlockID">$Icon</a>
    <% end_loop %>
</div>
