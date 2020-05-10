<?php

$cc = $zc = '';
$my_ary = json_decode('{"DE":{"29378":[2,4,6],"29379":[3],"34613":[7]},"AT":{"4014":[5]}}', true);print_r($my_ary);echo '<br>';
$result = check_user_id($my_ary, 7, $cc, $zc);
if ($result)
{
	echo 'Ergebnis: ' . $cc . ' - ' . $zc;
}
else
{
	echo 'Kein Ergebnis!';
}

function check_user_id ($array2check, $user_id, &$key_cc, &$key_zc)
{
	$val_cc = '';
	$val_zc = '';
	foreach ($array2check as $key_cc => $val_cc)
	{
		foreach ($val_cc as $key_zc => $val_zc)
		{
			$i = 0;
			foreach ((array) $array2check[$key_cc][$key_zc] as $val)
			{
				if ($val == $user_id)
				{
					return true;
				}
				$i++;
			}
		}
	}
	return false;
}
?>
