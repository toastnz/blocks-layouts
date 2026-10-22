<div class="blocks-layouts-page-links">
    <% if $Groups %>
        <% loop $Groups %>
            <% if $Title %><h4>$Title</h4><% end_if %>
            <% loop $Pages %>
                <div class="blocks-layouts-page-links__item"><i class="$IconClass"></i><a href="$Link">$Title</a></div>
            <% end_loop %>
        <% end_loop %>
    <% else %>
        <p class="alert alert-warning blocks-layouts-page-links__empty">No pages are using this block.</p>
    <% end_if %>
</div>
