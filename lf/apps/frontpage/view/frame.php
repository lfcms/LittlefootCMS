<!-- Main conainter that holds all the widgets -->
<section>
<!-- Top widgets -->
	<!-- left side - info -->
	<aside id="info">
		{slideshow}
	</aside>
	<!-- right side - campus talent -->
	<aside id="campus-talent">
		{campuspicks#box}
	</aside>

	<!-- Main content -->
	<section id="latest-news">
		<h2>Latest News</h2>
		{blog#latest?inst=Latest News&cat=Entertainment}
		{blog#latest?inst=Latest News&cat=Sports}
		{blog#latest?inst=Latest News&cat=Technology}
	</section>

	<section id="latest-blog">
		{blog#sidebar?inst=Blog}
	</section>

	<section id="videos">
		<h2>Videos</h2>
		{campuspicks#videobox}
		<a href="%baseurl%talent/">More</a>
	</section>

	<!-- More widgets -->
	<section id="trending-events">
		{calendar#trending}
	</section>

	<section id="campus-hotties">
		<h2>Campus Hotties</h2>
		{campuspicks#hotties}
	</section>
</section>