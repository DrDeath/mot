<?php

$ary = array();
echo build_doubles($ary, 2, 'DE', '29378') . '<br>';
echo build_doubles($ary, 3, 'DE', '29379') . '<br>';
echo build_doubles($ary, 4, 'DE', '29378') . '<br>';
echo build_doubles($ary, 5, 'AT', '4014') . '<br>';
echo build_doubles($ary, 6, 'DE', '29378') . '<br>';
echo build_doubles($ary, 7, 'DE', '34613') . '<br>';
print_r($ary);
echo '<br>';
echo json_encode($ary) . '<br>';
$ary = json_decode('{"DE":{"29378":[2,0,6],"29379":[3],"34613":[7]},"AT":{"4014":[5]}}', true);
echo build_doubles($ary, 9, 'DE', '29378') . '<br>';
print_r($ary);
echo '<br>';
echo json_encode($ary);



function build_doubles(&$doubles, $user_id, $country, $zip_code)
{
	if (array_key_exists($country, $doubles))						// do we already have this country code?
	{
		if (array_key_exists($zip_code, $doubles[$country]))		// yes, country code already exists, now we check for existence of the zip code
		{
			// yes, country code and zip code already exist, now we have to check for empty entry (user_id equals 0)
			$i = 0;
			$size = sizeof($doubles[$country][$zip_code])-1;
			while ($i <= $size)										// for all stored user_ids:
			{
				if ($doubles[$country][$zip_code][$i] == 0)			// do we have user_id = 0 (earlier deleted user)?
				{
					$doubles[$country][$zip_code][$i] = $user_id;	// yes, overwrite it with this user
					return $i;										// and return the offset
				}
				$i++;
			}
			array_push($doubles[$country][$zip_code], $user_id);	// if we get here there was no empty slot and we have to add the user at the end
			return sizeof($doubles[$country][$zip_code])-1;			// and return the new offset
		}
		else
		{
			$doubles[$country][$zip_code] = array();				// no, zip code doesn't exist within in this country code, so we generate it . . .
			array_push($doubles[$country][$zip_code], $user_id);	// and save the user id
			return 0;												// first user with this zip code, so the offset will be zero
		}
	}
	else
	{
		$doubles[$country] = array();								// country code doesn't exist
		$doubles[$country][$zip_code] = array();					// and the zip code
		array_push($doubles[$country][$zip_code], $user_id);		// and save the user id
		return 0;													// first user with this zip code, so the offset will be zero
	}
}

?>
