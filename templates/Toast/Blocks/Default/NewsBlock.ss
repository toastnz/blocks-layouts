<% if $Items.Count %>
    <section class="newsBlock contentBlock" data-equalize>
        <div class="newsBlock__header row">
            <div class="column">
                $Content
            </div>
        </div>
        <div class="newsBlock__wrap row sm-up-2 md-up-3">
            <% loop $Items.Sort('SortOrder') %>
                <% if $Link %>
                    <a href="$Link.LinkURL" $Link.TargetAttr class="newsBlock__wrap__item column">
                        <div class="newsBlock__wrap__item__media" style="background-image: url('{$FeaturedImage.Fill(640,640).URL}');"></div>
                        <div class="newsBlock__wrap__item__details" data-equalize-watch>
                            <h5>$Title.XML</h5>
                            
                            <% if $Image %>
                                $Image.Fill(100,100)
                            <% end_if %>

                            $Content
                        </div>
                        <p><span class="newsBlock__wrap__item__link link">Read more</span></p>
                    </a>
                <% end_if %>
            <% end_loop %>
        </div>
    </section>
<% end_if %>