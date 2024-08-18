<div id="BlockPreview">
    {$Preview}
</div>

<style>
    body {
        height: auto;
        <%-- background-color: transparent; --%>
    }

    *:not(#BlockPreview *) {
        display: none;
    }

    *:has(#BlockPreview) {
        display: block !important;
    }

    #BlockPreview {
        display: block!important;
        <%-- background-color: #fff; --%>
    }
</style>
