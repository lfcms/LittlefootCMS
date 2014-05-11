<?php

$count = 0;

$template = array(
        'html' => '<td>(<a href="%href%"%js%>%text%</a>)</td>',
        'replace' => array( '%href%', '%text%', '%js%')
);

$userlist = '';
$save = array();
do
{
        if(!isset($row_id)) { $row_id = $row['id']; unset($row['id']); }

        if(isset($vars[1]) && $row_id == $vars[1])
        {
                $save = $row;
                $save['id'] = $row_id;
        }

        $row['email'] = '<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>';

        $rm = array(
                '%baseurl%users/rm/'.$row_id.'/',
                'delete',
				jsprompt('Are you sure?')
        );
        $edit = array(
                '%baseurl%users/edit/'.$row_id.'/',
                'edit',
				''
        );
        $tools =
                str_replace(
                        $template['replace'],
                        $rm,
                        $template['html']
                ).
                str_replace(
                        $template['replace'],
                        $edit,
                        $template['html']
                )
        ;

        $userlist .= '
                <tr>
                        <td>'.implode('</td><td>', $row).'</td>
                        <td><a href="%appurl%edit/'.$row_id.'">edit</a></td>
                        <td><a '.jsprompt('Are you sure?').'  href="%appurl%rm/'.$row_id.'" class="delete_item">delete</a></td>
                </tr>
        ';
        unset($row_id);
		$count++;
}
while ($row = $this->db->fetch($result));

?>