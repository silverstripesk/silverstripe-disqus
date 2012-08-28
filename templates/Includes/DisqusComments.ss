<% if SyncDisqus %>
	<div id="disqus_local">
			<h2><% _t("Disqus.COMMENTSHEADING-JSOFF","Comments") %></h2>
			<% if LocalComments %>
				<% loop LocalComments %>
					<div class="disqus_comment_local">
						<h3>{$author_name}:</h3>
						<div>$message</div>
					</div>
				<% end_loop %>
			<% else %>
				<p><% _t("Disqus.NOCOMMENTS-JSOFF","No comments on this article.") %></p>
			<% end_if %>
	</div>
<% end_if %>
<div id="disqus_thread">

</div>
<noscript>Povoľte si javascript, ak chcete pridať príspevok do diskusie alebo nám napíšte e-mail ;)</noscript>
<p><a href="http://disqus.com" class="dsq-brlink">komentárový systém: <span class="logo-disqus">Disqus</span></a></p>