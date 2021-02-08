<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
    <article>
        <h1>$Title</h1>
        <div class="content">
			<% if $Playlist %>
			<ul>
			<% loop $Playlist %>
                <li class="unit size1of4<% if $MultipleOf(4,4) %> clearfix<% end_if %>">
					<% if $Thumbnail %>
                        <a href="$URL" target="_blank" title="$Title">
                            <img src="$Thumbnail" alt="$Title" width="$ThumbnailWidth" height="$ThumbnailHeight" />
                        </a>
					<% end_if %>
                    <p class="black">$Title</p>
                    <ul class="reset margin">
                        <li><a href="$URL" target="_blank" class="view-gallery">&gt; View video</a></li>
                    </ul>
                    <div class="clearfix"></div>
                </li>
			<% end_loop %>
            </ul>
			<% include Pagination %>
			<% else %>
			<p>There are currently no videos in this playlist.</p>
			<% end_if %>
		</div>
    </article>
	$Form
	$CommentsForm
</div>