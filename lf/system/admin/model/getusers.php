<?php

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
                'X',
				jsprompt('Are you sure?')
        );
        $edit = array(
                '%baseurl%users/edit/'.$row_id.'/',
                '[=]',
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
                        '.$tools.'
                        <td>'.implode('</td><td>', $row).'</td>
                </tr>
        ';
        unset($row_id);
}
while ($row = mysql_fetch_assoc($result));

?>