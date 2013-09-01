

<ul class="breadcrumb">
	<li>
		<a href="%appurl%">Forums</a> <span class="divider">/</span>
	</li>
	<li class="active"><?=$board['title'];?></li>
</ul>

<table id="messagelist" class="table" width="100%">
	<tr>
		<th>Popularity</th>
		<th>Title</th>
		<th>OP</th>
		<th>Last Post</th>
		<th><div align="right">Date</div></th>
	</tr>
<?php foreach($threads as $thread) { ?>
	<tr>
		<td><a href="YAY"><i class="icon-thumbs-up"></i></a><a href="BOO"><i class="icon-thumbs-down"></i></a> 1337</td>
		<td><a href="%appurl%thread/<?=$thread['id'];?>/"><?=$thread['subject'];?></a></td>
		<td><?=$thread['user'];?></td>
		<td><?=$thread['content'];?></td>
		<td>March 1, 2012</td>
	</tr>
<?php } ?>
</table>

<hr>
	<form action="%post%add/thread/<?=$board['id'];?>/" method="post">
	
		<input class="span12" type="text" placeholder="Subject" id="subject" name="subject"/>
		
		<textarea class="span12" name="msg" id="" rows="6"></textarea>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Post</button>
			<button class="btn">Discard</button>
		</div>
		
	</form>


</div>