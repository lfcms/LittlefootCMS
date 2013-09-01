<html>
	<head>
		<title>test</title>
	</head>
	<body>
		<h1>View_ListAll</h1>
		<p>%baseurl%</p>
		<table>
			<tr>
				<th>ID</th><th>Note</th>
			</tr>
			<?php foreach($data as $row) { ?>
			<tr>
				<td><?php echo $row['id'] ?></td><td><?php echo $row['note'] ?></td>
			</tr>
			<?php } ?>
		</table>
	</body>
</html>