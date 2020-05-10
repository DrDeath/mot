/*
* Checks if the field for the geonames.org username is not empty when the form is sent. In case it's empty we provide an error message and give the focus back to this field
* @params:	errorMsg:	string with the error message, provided by the language object
*
* @return:	false if field is empty to prevent the form from being sent
*/
function chkUsermapEmptyUser(errorMsg)
{
	var domElement = document.getElementById('mot_usermap_geonamesuser');
	if (domElement.value ==	 '') {
		alert(errorMsg);
		domElement.focus();
		return (false);
	}
}

/*
* Checks the value of an input element with a regular expression to make certain we get the value we want
*
* @params:	inputName:	string, name of the DOM element we want to check
*		defaultValue: value to use in case the provided value isn't valid
*		minValue: lowest value allowed
*		maxValue: highest value allowed
*
*/
function chkUsermapCoords(inputName, defaultValue, minValue, maxValue) {
	var domElement = document.getElementById(inputName);
	var elementValue = domElement.value;
	elementValue = elementValue.replace(/,{1,10}/g, ".");		// replace all commas with a fullstop
	elementValue = elementValue.replace(/[^\d/.-]/g, "");		// delete everything except numbers, fullstops and dashes
	elementValue = elementValue.replace(/-{2,10}/g, "-");		// replace multible dashes with one dash
	elementValue = elementValue.replace(/-*$/, "");				// erase all trailing dashes
	elementValue = elementValue.replace(/\.*$/, "");			// erase all trailing fullstops
	if (elementValue != '') {
		if ((elementValue < minValue) || (elementValue > maxValue)) {
			domElement.value = defaultValue;
		} else {
			domElement.value = elementValue;
		}
	} else {
		domElement.value = defaultValue;		//  no input at all, we use the default
	}
}

/*
* Cleans the geonames.org username of some superfluous charachters
* @params:	inputName:	string with the id of the input text field
*
* @return:	Sets the value of the given field
*/
function cleanUsermapUser(inputName)
{
	var domElement = document.getElementById(inputName);
	var elementValue = domElement.value;
	if (elementValue != '') {
		elementValue = elementValue.replace(/[;:\.-]/g, ",");		// replace some characters with a comma (in case someone fooled while typing) (dashes are not allowed in Geonames user names)
		elementValue = elementValue.replace(/,{2,10}/g, ",");		// delete multiple commas,
		elementValue = elementValue.replace(/^,*/, "");				// erase all leading commas
		elementValue = elementValue.replace(/,*$/, "");				// erase all trailing commas
		domElement.value = elementValue;
	}
}
