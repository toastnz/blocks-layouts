<% if $Posts.Count %>
    <section id="{$HTMLID}" class="default-blog [ js-default-blog ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <% if $Content %>
            <div class="default-blog__header">
                <div class="default-blog__content">
                    {$Content}
                </div>
            </div>
        <% end_if %>
    
        <div class="default-blog__wrap lg-up-{$Columns}" data-equalize="{$HTMLID}">
            <% loop $Posts %>
                <div class="default-blog-item">
                    <a href="{$Link}" class="default-blog-item__link">
                        <div class="default-blog-item__media" data-src="{$Image.FocusFill(500,600).URL}" style="background-position: {$getImageFocusPosition($Image.ID)}">
                            <img src="{$Image.Fill(5,6).URL}" width="5" height="6" loading="lazy" alt="{$Image.Title.ATT}">

                            <div class="default-blog-item__icon" data-src="{$Icon.URL}"></div>
                        </div>
    
                        <div class="default-blog-item__details background-colour--{$Top.SecondaryColour.getColourClasses}">
                            <div data-equalize-watch="{$Top.HTMLID}">
                                <% if $Title %>
                                    <span class="default-blog-item__title">{$Title.XML}</span>
                                <% end_if %>
        
                                <% if $Summary %>
                                    <p class="default-blog-item__summary">{$Summary.XML}</p>
                                <% end_if %>
                            </div>
    
                            <span class="default-blog-item__button">Read more</span>
                        </div>
                    </a>
                </div>
            <% end_loop %>
        </div>
    </section>
<% end_if %>