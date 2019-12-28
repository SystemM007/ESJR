/* 
* Override sommige functies in swfupload
*/

/*
* Mogelijkheid gemaakt om met behulp van de custom setting target de locatie van 
* het flash object aan te geven
*/

// Private: loadFlash generates the HTML tag for the Flash
// It then adds the flash to the body
SWFUpload.prototype.appendFlash = function () {
	var targetElement, container;

	// Make sure an element with the ID we are going to use doesn't already exist
	if (document.getElementById(this.movieName) !== null) 
	{
		throw "ID " + this.movieName + " is already in use. The Flash Object could not be added";
	}

	// Get the body tag where we will be adding the flash movie
	if(this.settings.custom_settings.target)
	{
		targetElement = this.settings.custom_settings.target;
	}
	else
	{
		targetElement = document.getElementsByTagName("body")[0];
	}

	if (targetElement == undefined) {
		throw "Could not find the 'target' element.";
	}

	// Append the container and load the flash
	container = document.createElement("div");
	container.style.width = "1px";
	container.style.height = "1px";

	targetElement.appendChild(container);
	container.innerHTML = this.getFlashHTML();	// Using innerHTML is non-standard but the only sensible way to dynamically add Flash in IE (and maybe other browsers)
};


/*
* Beetje irritant dat deze functie  ook wordt aangeroepen als this.setting.debug false is
*/

// oorspronkelijke functie van naam veranderen
SWFUpload.prototype.displayDebugInfoSuper = SWFUpload.prototype.displayDebugInfo

// functie overschrijven, en conditioneel de super functie aanroepen
SWFUpload.prototype.displayDebugInfo = function () {
	if(this.settings.debug) this.displayDebugInfoSuper();
};

/*
* SWFUpload.Console door sturen naar de firebug console
*/

SWFUpload.Console = {};
SWFUpload.Console.writeLine = console.log;