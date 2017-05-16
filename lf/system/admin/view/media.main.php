<h2><i class="fa fa-picture-o"></i> Media</h2>
<?=notice();?>

<?php
$parts = $this->getSubdirParts();
$breadcount = count($parts);
$counter = 1;


echo '<a href="cdparent/'.$breadcount.'">Media</a> > ';

foreach($parts as $part)
{
	if( $counter != 1 )
		echo ' > ';
	
	echo '<a href="cdparent/'.($breadcount - $counter).'">'.$part.'</a>';
	
	$counter++;
}


// <?=implode(' > ', $this->getSubdirParts());



?>



<div class="row">
	<div class="col-9">
		<h3 class="no_martop">Folder Contents</h3>
		<ul class="efvlist">
		<?php if( $this->getSubdirDepth() > 0 ): ?>
			<li>
				<a href="<?=\lf\requestGet('ActionUrl');?>cdparent">..</a>
			</li>
		<?php endif;

		foreach($files as $file): 
			if($file == '.gitignore') continue;
		?>
			<li>	
				<?php 
				if( is_dir( $this->getFullDir().'/'.$file ) ): 
					$file .= '/';
					$function = 'chdir';
				else:
					$function = 'open';
				endif; ?>
				<a href="<?=\lf\requestGet('AdminUrl').'media/'.$function.'/'.$file;?>">
					
					
					
					
					<?=$file;?>
				</a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	<div class="col-3">
		<h3 class="no_martop">Upload</h3>
		<form action="<?=\lf\requestGet('ActionUrl');?>upload" method="post" enctype="multipart/form-data">
			<ul class="vlist">
				<li>
					<input name="uploadedFile" class="blue button" type="file">
				</li>
				<li>
					<input type="submit">
				</li>
			</ul>
		</form>
	
	
	
	
	
	
	
	
		TODO: DRAGON DROP
		// ty http://tutorialzine.com/2011/09/html5-file-upload-jquery-php/
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	</div>
</div>

