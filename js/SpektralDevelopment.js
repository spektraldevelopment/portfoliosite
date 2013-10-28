//Vars
var root;
var browser;
var platform;
var canvas;
var stage;
var viewport;

//String
var selectedSection;

//Number
var STAGE_WIDTH;
var STAGE_HEIGHT;

var SCREEN_WIDTH;
var SCREEN_HEIGHT;

var workWidth;
var startY;
var aboutY;
var contactY;

var sectionNum = 0;
var panelNum = 0;

//Divs
var centerContainer;
var panelContainer;

//Modules
var background;
var header;
var panel;
var about;
var contact;
var footer;

//XML
var xmlDoc;

//Array
var panelArray = new Array();
var panelXArray = new Array();

//PDF
var pD = PluginDetect;
var canShowPDF;

//Ease
var easeType = Expo.easeOut;


////////////////////////
////INIT
////////////////////////
function init()
{
    var contact = {
        email:"spektraldevelopment@gmail.com",
        phone:"(647) 477 - 3652",
        cell: "(416) 910 - 0839",
        site: "spektraldevelopment.com"
    }

    console.log("Contact: " + contact.site);

    root = document.location.host;

    console.log("root: " + root);

    browser = navigator.appCodeName;
    platform = navigator.platform;

    trackGA("Main", "userAgent: ", navigator.userAgent);

    SCREEN_WIDTH  = screen.width;
    SCREEN_HEIGHT = screen.height;

    canvas = document.getElementById("bgCanvas");
    stage = new Stage(canvas);

    background = new Background(stage);

    var main = document.getElementById("mainContainer");

    centerContainer = document.createElement("div");
    centerContainer.setAttribute("id", "centerContainer");
    centerContainer.setAttribute("class", "center");
    main.appendChild(centerContainer);

    //Nav arrows
    var rightArrow = document.createElement("div");
    rightArrow.setAttribute("id", "rightArrow");
    rightArrow.setAttribute("class", "arrow-right");
    centerContainer.appendChild(rightArrow);

    var leftArrow = document.createElement("div");
    leftArrow.setAttribute("id", "leftArrow");
    leftArrow.setAttribute("class", "arrow-left");
    leftArrow.setAttribute("style", "opacity:.25");
    centerContainer.appendChild(leftArrow);

    listenEvent(rightArrow, "click", onRightArrowClick);
    listenEvent(leftArrow,  "click", onLeftArrowClick);

    selectedSection = "Work";

    resizeCanvas();

    panelContainer = document.createElement("div");
    panelContainer.setAttribute("id", "panelContainer");
    panelContainer.setAttribute("class", "panelCont");
    centerContainer.appendChild(panelContainer);

    workWidth = parseFloat(getStyle(centerContainer, "width", "width"));

    loadXML();

    listenEvent(window, "resize", resizeCanvas);

    stage.update();

    console.log("Spektral Development");
}

////////////////////////
////LOAD XML
////////////////////////
function loadXML()
{
    var xmlLoc;

    if(root == "localhost")
        xmlLoc = "xml/content.xml";//Local
    else
        xmlLoc = "site/xml/content.xml";//Live

    var xmlhttp;

    if (window.XMLHttpRequest)
    {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("GET", xmlLoc,false);
    xmlhttp.send();

    xmlDoc = xmlhttp.responseXML;

    var xmlSuccess;

    if(xmlDoc != null)
        xmlSuccess = true;
    else
        xmlSuccess = false;

    trackGA("Main", "XML success: ", xmlSuccess);

    //Check if browser can display pdfs
    var pdfLoc = xmlDoc.getElementsByTagName("pdf")[0].childNodes[0].nodeValue;
    pD.onDetectionDone('PDFReader', canDisplayPDF, pdfLoc);

    initModules();

    //console.log("panelXArray: " + panelXArray);
}

function onRightArrowClick()
{
    panelNum++;

    if(panelNum >= xmlDoc.getElementsByTagName("panel").length)
    {
        panelNum = xmlDoc.getElementsByTagName("panel").length - 1;
    }

    workMoveTo(panelNum);

    trackGA("Work", "Clicked: ", "onRightArrowClick");
}

function onLeftArrowClick()
{
    panelNum--;

    if(panelNum < 0)
    {
        panelNum = 0;
    }

    workMoveTo(panelNum);

    trackGA("Work", "Clicked: ", "onLeftArrowClick");
}

//////////////////////////
////WORK MOVE TO
/////////////////////////
function workMoveTo(num)
{
    panelNum = num;

    var rA = document.getElementById("rightArrow");
    var lA = document.getElementById("leftArrow");

    if(num >= xmlDoc.getElementsByTagName("panel").length - 1)
    {
       rA.setAttribute("style", "opacity:.25");
    }
    else
    {
        rA.setAttribute("style", "opacity:1");
    }

    if(num <= 0)
    {
        lA.setAttribute("style", "opacity:.25");
    }
    else
    {
        lA.setAttribute("style", "opacity:1");
    }

    scrollToWork();

    var moveX = -(panelXArray[num]);

    TweenLite.to(panelContainer, 2, {css:{left:moveX},  ease:easeType});

    header.highlightThumb(num);
    highlightPanel(num);

    var label = "workMoveTo: " + num;

    trackGA("Work", "Selected: ", label);
}

function scrollToWork()
{
    selectedSection = "Work";
    TweenLite.to(centerContainer, 1, {css:{top:startY}, ease:easeType});
    header.highlightLink(selectedSection);
    trackGA("Work", "Selected: ", "scrollToWork")
}

function scrollToAbout()
{
    selectedSection = "About";
    TweenLite.to(centerContainer, 1, {css:{top:-(aboutY)}, ease:easeType});//550
    header.highlightLink(selectedSection);
    trackGA("About", "Selected: ", "scrollToAbout")
}

function scrollToContact()
{
    selectedSection = "Contact";
    TweenLite.to(centerContainer, 1, {css:{top:-(contactY)}, ease:easeType});//1450
    header.highlightLink(selectedSection);
    trackGA("Contact", "Selected: ", "scrollToContact");
}

function highlightPanel(num)
{
    for(var i = 0; i < panelArray.length; i++)
    {
        if(i == num)
        {

           panelArray[i].fadeIn();
        }
        else
        {
            panelArray[i].fadeOut();
        }
    }

    if(num == null)
        panelArray[0].fadeIn();
}

////////////////////////
////INIT MODULES
////////////////////////
function initModules()
{
    //Header
    header = new Header();
    header.setData(xmlDoc.getElementsByTagName("header")[0]);

    //Work Panels
    for(i =0; i < xmlDoc.getElementsByTagName("panel").length; i++)
    {
        panel = new Panel(i);
        panel.setData(xmlDoc.getElementsByTagName("panel")[i]);

        panelArray.push(panel);
        panelXArray.push(parseFloat(panel.getX()));
    }

    highlightPanel(null);

    //About
    about = new About(xmlDoc.getElementsByTagName("about")[0]);

    //Contact
    contact = new Contact();

    footer = new Footer(viewport);

    listenEvent(document, "keydown", onKeyPress);

    //Mousewheel setup
    var mousewheelevt = (/Firefox/i.test(navigator.userAgent))? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x

    trackGA("Main", "MouseWheel available: ", mousewheelevt);
    //console.log("mousewheelevt: " + mousewheelevt);

    if (document.attachEvent) //if IE (and Opera depending on user setting)
        document.attachEvent("on"+ mousewheelevt, onMouseWheel);
    else if (document.addEventListener) //WC3 browsers
        document.addEventListener(mousewheelevt, onMouseWheel, false)
}

/////////////////////////////////////////
////ON MOUSE WHEEL
/////////////////////////////////////////
function onMouseWheel(e)
{
    var evt = window.event || e; //equalize event object
    var delta = evt.detail ? evt.detail*(-120) : evt.wheelDelta; //check for detail first so Opera uses that instead of wheelDelta

    if(delta >= 120)
    {
        sectionNum--;

        if(sectionNum <=0)
            sectionNum = 0;
    }

    if(delta <= -120)
    {
        sectionNum++;

        if(sectionNum > 2)
            sectionNum = 2;
    }

    if(sectionNum == 0)
        scrollToWork();

    if(sectionNum == 1)
        scrollToAbout();

    if(sectionNum == 2)
        scrollToContact();
}

//////////////////////////////////////
////ON KEY PRESS
//////////////////////////////////////
function onKeyPress(e)
{
    var key = e.keyCode;

    if(key == 38)
    {
        sectionNum--;

        if(sectionNum <=0)
            sectionNum = 0;

        if(sectionNum == 0)
            scrollToWork();

        if(sectionNum == 1)
            scrollToAbout();

        if(sectionNum == 2)
            scrollToContact();

        trackGA("Main", "Keyboard: ", "UP");
        console.log("UP");
    }

    if(key == 40)
    {
        sectionNum++;

        if(sectionNum > 2)
            sectionNum = 2;

        if(sectionNum == 0)
            scrollToWork();

        if(sectionNum == 1)
            scrollToAbout();

        if(sectionNum == 2)
            scrollToContact();

        trackGA("Main", "Keyboard: ", "DOWN");
        console.log("DOWN");
    }

    if(key == 37)
    {
        sectionNum = 0;

        panelNum--;

        if(panelNum < 0)
        {
            panelNum = 0;
        }

        workMoveTo(panelNum);

        trackGA("Main", "Keyboard: ", "LEFT");
        console.log("LEFT");
    }

    if(key == 39)
    {
        sectionNum = 0;

        panelNum++;

        if(panelNum >= xmlDoc.getElementsByTagName("panel").length)
        {
            panelNum = xmlDoc.getElementsByTagName("panel").length - 1;
        }

        workMoveTo(panelNum);

        trackGA("Main", "Keyboard: ", "RIGHT");
        console.log("RIGHT");
    }
}

//////////////////////////////
////RESIZE CANVAS
//////////////////////////////
function resizeCanvas()
{
    viewport = getBrowserSize();

    try
    {
        footer.setY((viewport.height - 25));
    }
    catch(e){}

    try
    {
        footer.repositionCopyright(viewport.width);
    }
    catch(e){}

    try
    {
        header.reposElements(viewport.width);
    }
    catch(e){}

    startY = (viewport.height / 2);
    aboutY = (875 - startY);
    contactY = (1750 - startY);

    var cC = document.getElementById("centerContainer");

    if(selectedSection == "Work")
        cC.setAttribute("style", "top:" + startY + "px");

    if(selectedSection == "About")
        cC.setAttribute("style", "top:" + -(aboutY) + "px");

    if(selectedSection == "Contact")
        cC.setAttribute("style", "top:" + -(contactY) + "px");

    if (canvas.width  < viewport.width)
    {
        canvas.width  = viewport.width;
    }

    if (canvas.height < viewport.height)
    {
        canvas.height = viewport.height;
    }

    STAGE_WIDTH  = viewport.width  + 15;
    STAGE_HEIGHT = viewport.height + 15;

    stage.width  = STAGE_WIDTH;
    stage.height = STAGE_HEIGHT;

    stage.update();

    //console.log("stage.width: " + stage.width + " stage.height: " + stage.height + " window.innerWidth: " + window.innerWidth +  " window.innerHeight: " + window.innerHeight);
}

//////////////////////////////////
////PDF
/////////////////////////////////
function showPDF()
{
    trackGA("Main", "ShowPDF canShowPDF: ", canShowPDF);

    var pdfLoc = xmlDoc.getElementsByTagName("pdf")[0].childNodes[0].nodeValue;

    var overlay = document.createElement("div");
    overlay.setAttribute("id", "overlay");
    overlay.setAttribute("class", "overlay");
    document.body.appendChild(overlay);

    var bg = document.createElement("div");
    bg.setAttribute("id", "overlayBG");
    bg.setAttribute("class", "overlayBg");
    overlay.appendChild(bg);

    listenEvent(bg, "click", hidePDF);

    var pdfFallback = document.createElement("p");
    pdfFallback.setAttribute("id", "pdfFallback");
    pdfFallback.setAttribute("class", "pdfFallback");
    ///pdfFallback.innerHTML = "It appears your Web browser is not configured to display PDF files.";
    overlay.appendChild(pdfFallback);

    var fallbackClose = document.createElement("a");
    fallbackClose.setAttribute("class", "pdfClose");
    fallbackClose.setAttribute("style", "top:65px; left:388px");
    fallbackClose.innerHTML = "Close";
    pdfFallback.appendChild(fallbackClose);

    listenEvent(fallbackClose, "click", hidePDF);

    var txt = document.createTextNode("It appears your Web browser is not configured to display PDF files. No problem ");
    pdfFallback.appendChild(txt);

    var pdfLink = document.createElement("a");
    pdfLink.setAttribute("href", pdfLoc);
    pdfLink.setAttribute("style", "color:#fff");
    pdfLink.innerHTML = "click here to download the PDF."
    pdfFallback.appendChild(pdfLink);

    console.log("canSHowPdf: " + canShowPDF);
    ////////////////*************************************************
    if(canShowPDF == true)
    {
        var pdfCont = document.createElement("div");
        pdfCont.setAttribute("id", "pdfCont");
        pdfCont.setAttribute("class", "pdfContainer");
        overlay.appendChild(pdfCont);

        var pdfObj = document.createElement("object");
        pdfObj.setAttribute("id", "pdf");
        pdfObj.setAttribute("class", "pdf");
        pdfObj.setAttribute("data", pdfLoc);
        pdfObj.setAttribute("type", "application/pdf");
        pdfCont.appendChild(pdfObj);

        var pdfWidth = parseFloat(getStyle(pdfCont, "width", "width"));

        var pdfClose = document.createElement("a");
        pdfClose.setAttribute("class", "pdfClose");
        pdfClose.setAttribute("style", "left:" + (pdfWidth - 100) + "px; top:25px");
        pdfClose.innerHTML = "Close";
        pdfCont.appendChild(pdfClose);

        listenEvent(pdfClose, "click", hidePDF);
    }
}

function hidePDF()
{
    document.body.removeChild(document.getElementById("overlay"));
    trackGA("Main", "Hide PDF", null);
}


////////////////////////////////////
////HELPER FUNCTIONS**************************
////////////////////////////////////


//////////////////
////GET STYLE
/////////////////
function getStyle(elem, ie, other)
{
    if(elem.currentStyle)
    {
        return elem.currentStyle[ie];
    }
    else if (document.defaultView && document.defaultView.getComputedStyle)
    {
        return document.defaultView.getComputedStyle(elem, null).getPropertyValue(other);
    }
    else
    {
        return null;
    }
}

//////////////////
////GET BROWSER SIZE
/////////////////
function getBrowserSize()
{
    var wdth = 0;
    var hth = 0;

    if(!window.innerWidth)
    {
        wdth = (document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body.clientWidth);
        hth = (document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight);
    }
    else
    {
        wdth = window.innerWidth;
        hth = window.innerHeight;
    }

    return {width:wdth, height:hth};
}

//////////////////
////LISTEN EVENT
/////////////////
function listenEvent(eventTarget, eventType, eventHandler)
{
    if(eventTarget.addEventListener)
    {
        eventTarget.addEventListener(eventType, eventHandler, false)
    }
    else if(eventTarget.attachEvent)
    {
        eventType = "on" + eventType;
        eventTarget.attachEvent(eventType, eventHandler)
    }
    else
    {
        eventTarget["on" + eventType] = eventHandler;
    }
}

function canDisplayPDF(pD)
{
    if(pD.hasMimeType("application/pdf") == null)
        canShowPDF = false;
    else
        canShowPDF = true;
}

function trackGA(category, action, label)
{
    //_gaq.push(['_trackEvent', category, action, label]);
    //_gaq.push(['_trackEvent', 'Videos', 'Play', 'Gone With the Wind']);
}
