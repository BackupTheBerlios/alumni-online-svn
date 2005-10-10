// PHP Layers Menu 3.0.1 (C) 2001-2003 Marco Pratesi (marco at telug dot it)

useTimeouts = 1;
timeoutLength = 1000;	// time in ms; not significant if useTimeouts = 0;
shutdownOnClick = 0;

menuLeftShift = 6;
menuRightShift = 10;

loaded = 0;
layersMoved = 0;
layerPoppedUp = "";

timeoutFlag = 0;
if (Konqueror21 || Opera56 || IE4) {
	useTimeouts = 0;
}
if (NS4 || Opera56 || IE4) {
	shutdownOnClick = 1;
}

currentY = 0;
function grabMouse(e) {	// for NS4
	currentY = e.pageY;
}
if (NS4) {
	document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE);
	document.onmousemove = grabMouse;
}

function shutdown() {
	for (i=1; i<=numl; i++) {
		LMPopUpL(listl[i], false);
	}
	layerPoppedUp = "";
}
if (shutdownOnClick) {
	if (NS4) {
		document.onmousedown = shutdown;
	} else {
		document.onclick = shutdown;
	}
}

function setLMTO() {
	if (useTimeouts) {
		timeoutFlag = setTimeout('shutdown()', timeoutLength);
	}
}

function clearLMTO() {
	if (useTimeouts) {
		clearTimeout(timeoutFlag);
	}
}

function moveLayerX(menuName) {
	if (!loaded || (isVisible(menuName) && menuName != layerPoppedUp)) {
		return;
	}
	if (father[menuName] != "") {
		if (!Opera5 && !IE4) {
			width0 = lwidth[father[menuName]];
			width1 = lwidth[menuName];
		} else if (Opera5) {
			// Opera 5 stupidly and exaggeratedly overestimates layers widths
			// hence we consider a default value equal to $abscissaStep
			width0 = abscissaStep;
			width1 = abscissaStep;
		} else if (IE4) {
			width0 = getOffsetWidth(father[menuName]);
			width1 = getOffsetWidth(menuName);
		}
		onLeft = getOffsetLeft(father[menuName]) - width1 + menuLeftShift;
		onRight = getOffsetLeft(father[menuName]) + width0 - menuRightShift;
		windowWidth = getWindowWidth();
		windowXOffset = getWindowXOffset();
//		if (NS4 && !DOM) {
//			windowXOffset = 0;
//		}
		if (onLeft < windowXOffset && onRight + width1 > windowWidth + windowXOffset) {
			if (onRight + width1 - windowWidth - windowXOffset > windowXOffset - onLeft) {
				onLeft = windowXOffset;
			} else {
				onRight = windowWidth + windowXOffset - width1;
			}
		}
		if (back[father[menuName]]) {
			if (onLeft < windowXOffset) {
				back[menuName] = 0;
			} else {
				back[menuName] = 1;
			}
		} else {
//alert(onRight + " - " + width1 + " - " +  windowWidth + " - " + windowXOffset);
			if (onRight + width1 > windowWidth + windowXOffset) {
				back[menuName] = 1;
			} else {
				back[menuName] = 0;
			}
		}
		if (back[menuName]) {
			setLeft(menuName, onLeft);
		} else {
			setLeft(menuName, onRight);
		}
	}
}

function moveLayerY(menuName, ordinateMargin) {
	if (!loaded || (isVisible(menuName) && menuName != layerPoppedUp)) {
		return;
	}
	if (!layersMoved) {
		moveLayers();
		layersMoved = 1;
	}
	if (!NS4) {
		newY = getOffsetTop("ref" + menuName);
	} else {
		newY = currentY;
	}
	newY -= ordinateMargin;
	layerHeight = getOffsetHeight(menuName);
	windowHeight = getWindowHeight();
	windowYOffset = getWindowYOffset();
	if (newY + layerHeight > windowHeight + windowYOffset) {
		if (layerHeight > windowHeight) {
			newY = windowYOffset;
		} else {
			newY = windowHeight + windowYOffset - layerHeight;
		}
	}
	if (Math.abs(getOffsetTop(menuName) - newY) > thresholdY) {
		setTop(menuName, newY);
	}
	moveLayerX(menuName);	// workaround needed for Mozilla < 1.4 for MS Windows
}

function LMPopUpL(menuName, on) {
	if (!loaded) {
		return;
	}
	if (!layersMoved) {
		moveLayers();
		layersMoved = 1;
	}
	setVisibility(menuName, on);
}

function LMPopUp(menuName, isCurrent) {
	if (!loaded || menuName == layerPoppedUp || (isVisible(menuName) && !isCurrent)) {
		return;
	}
	if (menuName == father[layerPoppedUp]) {
		LMPopUpL(layerPoppedUp, false);
	} else if (father[menuName] == layerPoppedUp) {
		LMPopUpL(menuName, true);
	} else {
		shutdown();
		foobar = menuName;
		do {
			LMPopUpL(foobar, true);
			foobar = father[foobar];
		} while (foobar != "")
	}
	layerPoppedUp = menuName;
}

function resizeHandler() {
	if (NS4) {
		window.location.reload();
	}
	shutdown();
	for (i=1; i<=numl; i++) {
		setLeft(listl[i], 0);
		setTop(listl[i], 0);
	}
//	moveLayers();
	layersMoved = 0;
}
window.onresize = resizeHandler;

function yaresizeHandler() {
	if (window.innerWidth != origWidth || window.innerHeight != origHeight) {
		if (Konqueror2 || Opera5) {
			window.location.reload();	// Opera 5 often fails this
		}
		origWidth  = window.innerWidth;
		origHeight = window.innerHeight;
		resizeHandler();
	}
	setTimeout('yaresizeHandler()', 500);
}
function loadHandler() {
	if (Konqueror2 || Opera56) {
		origWidth  = window.innerWidth;
		origHeight = window.innerHeight;
		yaresizeHandler();
	}
}
window.onload = loadHandler;

function fixieflm(menuName) {
	if (DOM) {
		setWidth(menuName, "100%");
	} else {	// IE4 IS SIMPLY A BASTARD !!!
		document.write("</div>");
		document.write("<div id=\"IE4" + menuName + "\" style=\"position: relative; width: 100%; visibility: visible;\">");
	}
}