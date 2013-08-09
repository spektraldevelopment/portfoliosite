(function(window){

    //Static
    Panel.main;
    Panel.prototype.panelID;

    //String
    Panel.prototype.linkToDemo;

    //Divs
    Panel.prototype.mainDiv;
    Panel.prototype.panelDiv;
    Panel.prototype.titleDiv;
    Panel.prototype.descDiv;
    Panel.prototype.imgDiv;
    Panel.prototype.clientDiv;
    Panel.prototype.demoDiv;

    //Numbers
    Panel.prototype.padding;
    Panel.prototype.resetIndex;
    Panel.prototype.imageIndex;
    Panel.prototype.delayOffset;

    //Array
    Panel.prototype.imageArray;

    function Panel(id)
    {
        main = this;
        main.name = "Panel" + id;

        main.panelID = id;

        main.padding = 25;

        main.imageArray = new Array();

        main.delayOffset = 0;//panelID

        main.mainDiv = document.getElementById("panelContainer");

        //Create Panel
        main.panelDiv = document.createElement("div");
        main.panelDiv.setAttribute("id", "panel" + main.panelID);
        main.panelDiv.setAttribute("class", "panel");
        main.panelDiv.setAttribute("style", "left:" + ((main.panelID * (800 + main.padding))) + "px; opacity:0")
        main.mainDiv.appendChild(main.panelDiv);

        //Add Title
        main.titleDiv = document.createElement("div");
        main.titleDiv.setAttribute("id", "p" + main.panelID + "Title");
        main.titleDiv.setAttribute("class", "title");
        main.panelDiv.appendChild(main.titleDiv);

        //Add Client
        main.clientDiv = document.createElement("div");
        main.clientDiv.setAttribute("id", "p" + main.panelID + "Client");
        main.clientDiv.setAttribute("class", "client");
        main.panelDiv.appendChild(main.clientDiv);

        //Add Description
        main.descDiv = document.createElement("div");
        main.descDiv.setAttribute("id", "p" + main.panelID + "Desc");
        main.descDiv.setAttribute("class", "desc");
        main.panelDiv.appendChild(main.descDiv);

        //Add demo link
        main.demoDiv = document.createElement("div");
        main.demoDiv.setAttribute("id", "p" + main.panelID + "Demo");
        main.demoDiv.setAttribute("class", "demo");
        main.panelDiv.appendChild(main.demoDiv);

//        main.getMain = function()
//        {
//            return main;
//        }
    }

    Panel.prototype.getThisMain = function()
    {
        return main;
    }

    Panel.prototype.getX = function()
    {
        var p = document.getElementById("panel" + this.panelID);
        return getStyle(p, "left", "left");
    }

    Panel.prototype.setData = function(data)
    {
       setTitle(data.getElementsByTagName("title")[0].childNodes[0].nodeValue);
       setClient(data.getElementsByTagName("client")[0].childNodes[0].nodeValue);
       setDesc (data.getElementsByTagName("desc")[0].childNodes[0].nodeValue);
       setDemo ();
       main.linkToDemo = data.getElementsByTagName("link")[0].childNodes[0].nodeValue;

        //console.log("main.linkToDemo: " + main.linkToDemo);

       for(j = 0; j < data.getElementsByTagName("image").length; j++)
       {
            setPic(j, data.getElementsByTagName("image")[j].childNodes[0].nodeValue, data.getElementsByTagName("image").length);
       }
    }

    Panel.prototype.getPanelID = function()
    {
        return this.panelID;
    }

    Panel.prototype.fadeOut = function()
    {
        var pc = document.getElementById("panel" + this.panelID);
        TweenLite.to(pc,1, {css:{opacity:0}});
    }

    Panel.prototype.fadeIn = function()
    {
        var pc = document.getElementById("panel" + this.panelID);
        TweenLite.to(pc,1, {css:{opacity:1}});
    }

    function onDemoClick()
    {
        var m = this.main;

        console.log("Demo Link: " + m.name);
    }

    function setTitle(title)
    {
        main.titleDiv.innerHTML = title;
    }

    function setClient(client)
    {
        main.clientDiv.innerHTML = "Client: " + client;

        var t = document.getElementById("p" + main.panelID + "Title");
        var tY = getStyle(t, "top", "top");
        var tH = getStyle(t, "height", "height");

        var cY = (parseFloat(tY) + parseFloat(tH));

        var c = document.getElementById("p" + main.panelID + "Client");
        c.setAttribute("style", "top:" + (cY + 5) + "px");
    }

    function setDesc(desc)
    {
        main.descDiv.innerHTML = desc;

        var c = document.getElementById("p" + main.panelID + "Client");
        var cY = getStyle(c, "top", "top");
        var cH = getStyle(c, "height", "height");

        var dY = (parseFloat(cY) + parseFloat(cH));

        var d = document.getElementById("p" + main.panelID + "Desc");
        d.setAttribute("style", "top:" + (dY + 10) + "px");
    }

    function setDemo()
    {
        var demoLink = document.createElement("a");
        demoLink.setAttribute("id", "demoLink");
        demoLink.setAttribute("class", "demolink");
        demoLink.innerHTML = "Demo";
        main.demoDiv.appendChild(demoLink);

        var d = document.getElementById("p" + main.panelID + "Desc");
        var dY = getStyle(d, "top", "top");
        var dH = getStyle(d, "height", "height");

        var demoY = (parseFloat(dY) + parseFloat(dH));

        var demo = document.getElementById("p" + main.panelID + "Demo");
        demo.setAttribute("style", "top:" + (demoY + 10) + "px");

        listenEvent(demoLink, "click", onDemoClick);
    }

    function setPic(picID, picLoc, length)
    {
        main.imgDiv = document.createElement("div");
        main.imgDiv.setAttribute("id", "image" + picID);
        main.imgDiv.setAttribute("class", "image");

        var image = document.createElement("img");
        image.setAttribute("src", picLoc);
        main.imgDiv.appendChild(image);

        main.panelDiv.appendChild(main.imgDiv);

        main.imageArray.push(main.imgDiv);

        if(picID == (length - 1))
           initSlideShow();
    }

    function initSlideShow()
    {
        main.resetIndex = (main.imageArray.length - 1);
        main.imageIndex = main.resetIndex;

        fadeOut(main);
    }

    function fadeOut(main)
    {
        TweenLite.to(main.imageArray[main.imageIndex],  1, {css:{opacity:0}, delay:(5 + main.delayOffset), onComplete:fadeOut, onCompleteParams:[main]})

        main.imageIndex--;

        if(main.imageIndex < 0)
        {
            main.imageIndex = main.resetIndex;

            resetSlideShow(main);
        }
    }

    function resetSlideShow(main)
    {
        TweenLite.to(main.imageArray[main.imageIndex], 1, {css:{opacity:1}, delay:(5 + main.delayOffset), onComplete:reset, onCompleteParams:[main]});
    }

    function reset(main)
    {
        for(i = 0; i < main.resetIndex; i++)
        {
            main.imageArray[i].style.opacity = 1;
        }
    }

    window.Panel = Panel;

}(window))