<section id="{$HTMLID}" class="default-hero [ js-default-hero ] background-colour--{$SecondaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
    <div class="default-hero__container">
        <div class="default-hero__wrap align--{$ContentAlignment}">
            <div class="default-hero__content align--{$ContentPosition}">
                <div class="default-hero__text">
                    {$Content}
                </div>
            </div>
        </div>

        
        <% if $BackgroundImage %>
            <div class="default-hero__background" data-src="{$BackgroundImage.ScaleMaxWidth(1920).URL}" style="background-position: {$getImageFocusPosition($BackgroundImage.ID)}">
                <div class="default-hero__background-colour background-colour--{$PrimaryColour.getColourClasses}"></div>
                <img src="{$BackgroundImage.ScaleMaxWidth(192).URL}" width="{$BackgroundImage.getWidth()}" height="{$BackgroundImage.getHeight()}" loading="lazy" alt="{$BackgroundImage.Title.ATT}">
            </div>
        <% end_if %>
    </div>
</section>