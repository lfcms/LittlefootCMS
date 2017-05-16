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
				<label>Copy URL</label>
				<input class="" type="text" value="<?=$imgurl;?>">
			</li>
			<li>
				<label>Copy HTML</label>
				<textarea name="" id="" cols="30" rows="5"><img src="<?=$imgurl;?>" /></textarea>
			</li>
			<li>
				<label>Image Preview</label>
				<a href="<?=$imgurl;?>"><img src="<?=$imgurl;?>" alt=""></a>
			</li>
			<li>
				<a href="delete" class="red button"><i class="fa fa-trash-o"></i> Delete</a>
			</li>
		</ul>
		
	</div>
</div>
