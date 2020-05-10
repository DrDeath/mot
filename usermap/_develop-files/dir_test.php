<?php

$my_ary = dir_counter('language/');
$my_ary[] = 'fr';
echo json_encode($my_ary) . '<br>';
$nr = array_search('de_x_sie', $my_ary);
if ($nr !== false)
{
	echo $nr . '<br>';
	array_splice($my_ary, $nr, 1);
}
echo json_encode($my_ary) . '<br>';
echo sizeof($my_ary) . '<br><br>';

$nr = array_search('de', $my_ary);
if ($nr !== false)
{
	echo $nr . '<br>';
	array_splice($my_ary, $nr, 1);
}
echo json_encode($my_ary) . '<br>';
echo sizeof($my_ary) . '<br><br>';

$nr = array_search('en', $my_ary);
if ($nr !== false)
{
	echo $nr . '<br>';
	array_splice($my_ary, $nr, 1);
}
echo json_encode($my_ary) . '<br>';
echo sizeof($my_ary) . '<br><br>';

$nr = array_search('es', $my_ary);
if ($nr !== false)
{
	echo $nr . '<br>';
	array_splice($my_ary, $nr, 1);
}
else
{
	echo "Language 'es' fehlt!<br>";
}
echo json_encode($my_ary) . '<br>';
echo sizeof($my_ary) . '<br><br>';

function dir_counter($dir)
{
    $rtn = array();
	$path = scandir($dir);

    foreach($path as $element)
	{
        if($element != '.' && $element != '..' && is_dir($dir.'/'.$element))
		{
			$rtn[] = $element;
        }
    }

    return $rtn;
}
?>
