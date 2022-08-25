<% if $Items.Count %>
    <section class="linkBlock block">

        <div class="linkBlock__wrap linkBlock__wrap--{$Columns}">
            
            <% loop $Items.Sort('SortOrder') %>
                <a href="$Link.LinkURL" class="linkBlock__wrap__item <% if not $Icon && not $image %>bordered <% end_if %>[ js-in-view ]">

                    <% if $Icon %>
                        <div class="linkBlock__wrap__item__icon">
                            <img src="$Icon.URL">
                        </div>
                    <% else_if $Image %>
                        <div class="linkBlock__wrap__item__image" style="background-image:url({$Image.FocusFill(600,300).URL}); background-position: $Image.PercentageX% $Image.PercentageY%;"></div>
                    <% end_if %>

                    <div class="linkBlock__wrap__item__content">
                        <div class="linkBlock__wrap__item__content__heading">
                            <h6>$Title.XML</h6>
                        </div>
                        <p>$Summary.XML</p>
                        <p class="linkBlock__wrap__item__content__link">Read More $SVG('arrow-right')</p>
                    </div>

                </a>
            <% end_loop %>

        </div>

    </section>
<% end_if %>