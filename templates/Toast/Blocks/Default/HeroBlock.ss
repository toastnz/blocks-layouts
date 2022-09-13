<section class="default-hero background-colour--{$BGColourClassName} {$getLightOrDark($BGColourClassName)} {$ExtraClasses}">
    <div class="default-hero__container">
        <div class="default-hero__wrap <% if $Width=='full' %>full<% end_if %>">
            <div class="default-hero__content align--{$VerticalPosition}">
                <div class="default-hero__text">
                    {$Content}
                </div>
            </div>
        </div>

        <% if $BackgroundImage %>
            <div class="default-hero__background" data-src="{$BackgroundImage.ScaleMaxWidth(1920).URL}">
                <img src="{$BackgroundImage.ScaleMaxWidth(20).URL}" alt="{$Image.Title.ATT}">
            </div>
        <% end_if %>
    </div>
</section>