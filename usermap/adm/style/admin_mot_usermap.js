/*
* Define the search patterns for lat(itude) as a 2-digit floating point value with a possible leading minus (-)dd.d and for lon(gitude) as a 3-digit value (-)ddd.d
*/
var latMatch = /-?\d{1,2}\.*\d*/;
var lonMatch = /-?\d{1,3}\.*\d*/;

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
*		matchString: string, contains the pre-defined search pattern
*		defaultValue: value to use in case the provided value isn't valid
*		minValue: lowest value allowed
*		maxValue: highest value allowed
*
* @return:	writes either the default value or - if it matches the pattern and is within the boundaries - th given value into the DOM element's value
*/
function chkUsermapCoords(inputName, matchString, defaultValue, minValue, maxValue) {

	var domElement = document.getElementById(inputName);
	var elementValue = domElement.value;
	var result = elementValue.match(matchString);
	if (result == null) {
		domElement.value = defaultValue;		// input doesn't match the pattern, we use the default value
	} else {
		if ((result[0] < minValue) || (result[0] > maxValue)) {
			domElement.value = defaultValue;	// input matches the search pattern but is outside the given boundaries, we use the default value
		} else {
			domElement.value = result[0];		// input matches th search pattern und is within the boundaries, we use it
		}
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
		elementValue = elementValue.replace(/,{2,}/g, ",");			// delete multiple commas,
		elementValue = elementValue.replace(/^,*/, "");				// erase all leading commas
		elementValue = elementValue.replace(/,*$/, "");				// erase all trailing commas
		domElement.value = elementValue;
	}
}
