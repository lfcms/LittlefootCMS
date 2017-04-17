<h2><i class="fa fa-picture-o"></i> Media</h2>

<?php
$parts = $this->getSubdirParts();
$breadcount = count($parts);
$counter = 1;

echo '<a href="'.\lf\requestGet('ActionUrl').'cdparent/'.$breadcount.'">Media</a> > ';

foreach($parts as $part)
{
	if( $counter != 1 )
		echo ' > ';
	
	echo '<a href="'.\lf\requestGet('ActionUrl').'cdparent/'.($breadcount - $counter).'">'.$part.'</a>';
	
	$counter++;
}


// <?=implode(' > ', $this->getSubdirParts());



		$imgurl = \lf\requestGet('Subdir').'lf/media/'.$this->getSubDir().'/'.$filename;
?>

<h3>File Viewer</h3>

<div class="row">
	<div class="col-12">
		<ul class="vlist">
			<li>
				Copy URL
				<input class="" type="text" value="<?=$imgurl;?>">
				Or img tag
				<textarea name="" id="" cols="30" rows="10"><img src="<?=$imgurl;?>" /></textarea>
			</li>
			<li>
				Image Preview <br>
				<a href="<?=$imgurl;?>"><img src="<?=$imgurl;?>" alt=""></a>
			</li>
			<li>
				<a href="delete" class="red button">DELETE</a>
			</li>
		</ul>
		
	</div>
</div>
