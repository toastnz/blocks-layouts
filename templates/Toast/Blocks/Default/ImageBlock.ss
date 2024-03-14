<% if $Image %>
    <section id="{$HTMLID}" class="default-image [ js-default-image ] background-colour--{$PrimaryColour.getColourClasses} {$IncludeClasses} {$ExtraClasses}">
        <div class="default-image__wrap">
            <div class="default-image__media">
                <picture class="default-image__image">
                    <source media="(max-width: 767px)" srcset="{$Image.ScaleMaxWidth(768).URL}">
                    <source media="(max-width: 1439px)" srcset="{$Image.ScaleMaxWidth(1440).URL}">
                    <source media="(max-width: 1919px)" srcset="{$Image.ScaleMaxWidth(1920).URL}">
                    <img loading="lazy" src="{$Image.ScaleMaxWidth(1920).URL}" alt="{$Image.Title.ATT}" width="{$Image.getWidth()}" height="{$Image.getHeight()}" loading="lazy" alt="{$Image.Title.ATT}" style="object-fit: cover; object-position: {$getImageFocusPosition($Image.ID)}">
                </picture>
            </div>

            <% if $Caption %>
                <div class="default-image__content">
                    <p>{$Caption}</p>
                </div>
            <% end_if %>
        </div>
    </section>
<% end_if %>
