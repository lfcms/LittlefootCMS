<h2>Users</h2>
<table cellspacing=5 style="width: 100%;">
	<tr style="text-align:left;">
		<th><?=$headers;?></th>
		<th>Delete</th>
		<th>Modify</th>
	</tr>
<?=$userlist;?>
</table>
<h3><?=ucfirst($action).' User'.$link;?></h3><form action="%baseurl%users/<?=$action;?>/" method="post">	<ul>		<li><input type="text" name="user" <?=$values[0];?>'/> Username</li>		<li><input type="password" name="pass" /> Password</li>		<li><input type="text" name="email" <?=$values[1];?>.'/> Email</li>		<li><input type="text" name="nick" <?=$values[2];?>/> Nick</li>		<li><input type="text" name="group" <?=$values[3];?>/> Group</li>		<li><?=$id;?><input type="submit" value="<?=$action;?>" name="action" /></li>	</ul></form>