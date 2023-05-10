<section id="{$HTMLID}" class="default-hero [ js-default-hero ] background-colour--{$getColour($PrimaryColour, 'class, brightness')} {$IncludeClasses} {$ExtraClasses}">
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
                <img class="" src="{$BackgroundImage.ScaleMaxWidth(192).URL}" width="{$BackgroundImage.getWidth()}" height="{$BackgroundImage.getHeight()}" loading="lazy" alt="{$BackgroundImage.Title.ATT}">
            </div>
        <% end_if %>
    </div>
</section>