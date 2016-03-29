<% if $Playlist.MoreThanOnePage %>
	<% if $Playlist.NotFirstPage %>
        <a class="prev" href="$Playlist.PrevLink">Prev</a>
	<% end_if %>
	<% loop $Playlist.Pages %>
		<% if $CurrentBool %>
			$PageNum
		<% else %>
			<% if $Link %>
                <a href="$Link">$PageNum</a>
			<% else %>
                ...
			<% end_if %>
		<% end_if %>
	<% end_loop %>
	<% if $Playlist.NotLastPage %>
        <a class="next" href="$Playlist.NextLink">Next</a>
	<% end_if %>
<% end_if %>