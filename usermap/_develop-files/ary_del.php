<?php

$my_ary = json_decode('{"DE":{"29378":[2,4,6],"29379":[3],"34613":[7]},"AT":{"4014":[5]}}', true);print_r($my_ary);echo '<br>';
remove_doubles_value($my_ary, 4, 'DE', '29378');
print_r($my_ary);echo '<br>';
remove_doubles_value($my_ary, 6, 'DE', '29378');
print_r($my_ary);echo '<br>';
remove_doubles_value($my_ary, 2, 'DE', '29378');
print_r($my_ary);echo '<br>';

function remove_doubles_value(&$doubles, $user_id, $country, $zip_code)
	{
	// does this zipcode array really exist?
	if (!array_key_exists($zip_code, $doubles[$country]))
	{
		return;		// no, it doesn't exist, there is no known user, so we leave the function
	}
	// first we check whether there is a single user for this country / zipcode pair
	if (sizeof($doubles[$country][$zip_code]) == 1)
	{
		if ($user_id <> $doubles[$country][$zip_code][0])	// is this really the user we want to remove?
		{
			return;											// no, leave the function
		}
		else
		{
			unset($doubles[$country][$zip_code]);			// YES, delete the user array for this zipcode
		}
	}
	else	// there is more than one user at this zipcode
	{
		// if all other users have been deleted earlier we can simply delete the user array for this zipcode
		$deletable = true;
		foreach ($doubles[$country][$zip_code] as $value)
		{
			if ($value <> 0 or $value <> $user_id)
			{
				$deletable = false;
			}
		}
		if ($deletable)	// $user_id is the last remaining user, delete the array
		{
			unset($doubles[$country][$zip_code]);	// delete the user array for this zipcode
		}
		else		// there are other users, so we have to set the entry to 0 which signales an empty value
		{
			$size = sizeof($doubles[$country][$zip_code]);
			$i = 0;
			foreach ($doubles[$country][$zip_code] as $value)
			{
				if ($value == $user_id)
				{
					if ($i == ($size - 1)) // if this is the last entry in the array, dump it
					{
						unset($doubles[$country][$zip_code][$i]);					// last entry, remove it ...
					remove_doubles_value($doubles, 0, $country, $zip_code);	// and check, whether there are deleted entries before this one (user_id set to 0)
					}
					else
					{
						$doubles[$country][$zip_code][$i] = 0;		// not the last entry, set it to Zero (no valid user_id)
					}
				}
				$i++;
			}
		}
	}
}

?>
