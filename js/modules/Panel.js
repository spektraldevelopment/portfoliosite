(function(window){

    //Static
    Panel.prototype.panelID;
    Panel.prototype.panel;

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
        this.name = "Panel" + id;

        this.panelID = id;

        this.padding = 25;

        this.imageArray = new Array();

        this.delayOffset = 0;//panelID

        this.mainDiv = document.getElementById("panelContainer");

        //Create Panel
        this.panelDiv = document.createElement("div");
        this.panelDiv.setAttribute("id", "panel" + this.panelID);
        this.panelDiv.setAttribute("class", "panel");
        this.panelDiv.setAttribute("style", "left:" + ((this.panelID * (800 + this.padding))) + "px; opacity:0")
        this.mainDiv.appendChild(this.panelDiv);

        //Add Title
        this.titleDiv = document.createElement("div");
        this.titleDiv.setAttribute("id", "p" + this.panelID + "Title");
        this.titleDiv.setAttribute("class", "title");
        this.panelDiv.appendChild(this.titleDiv);

        //Add Client
        this.clientDiv = document.createElement("div");
        this.clientDiv.setAttribute("id", "p" + this.panelID + "Client");
        this.clientDiv.setAttribute("class", "client");
        this.panelDiv.appendChild(this.clientDiv);

        //Add Description
        this.descDiv = document.createElement("div");
        this.descDiv.setAttribute("id", "p" + this.panelID + "Desc");
        this.descDiv.setAttribute("class", "desc");
        this.panelDiv.appendChild(this.descDiv);

        //Add demo link
        this.demoDiv = document.createElement("div");
        this.demoDiv.setAttribute("id", "p" + this.panelID + "Demo");
        this.demoDiv.setAttribute("class", "demo");
        this.panelDiv.appendChild(this.demoDiv);

        setPanel(this);
    }

    Panel.prototype.getX = function()
    {
        var p = document.getElementById("panel" + this.panelID);
        return getStyle(p, "left", "left");
    }

    Panel.prototype.setData = function(data)
    {
       var p = getPanel();

       setTitle(data.getElementsByTagName("title")[0].childNodes[0].nodeValue);
       setClient(data.getElementsByTagName("client")[0].childNodes[0].nodeValue);
       setDesc (data.getElementsByTagName("desc")[0].childNodes[0].nodeValue);
       setDemo (data.getElementsByTagName("link")[0].childNodes[0].nodeValue);

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

    function setPanel(panel)
    {
        this.panel = panel;
    }

    function getPanel()
    {
        return this.panel;
    }

    function setTitle(title)
    {
        var p = getPanel();

        var t = document.getElementById("p" + p.panelID + "Title");
        t.innerHTML = title;
    }

    function setClient(client)
    {
        var p = getPanel();

        var c = document.getElementById("p" + p.panelID + "Client");
        c.innerHTML = "Client: " + client;

        var t = document.getElementById("p" + p.panelID + "Title");
        var tY = getStyle(t, "top", "top");
        var tH = getStyle(t, "height", "height");

        var cY = (parseFloat(tY) + parseFloat(tH));

        //var c = document.getElementById("p" + this.panelID + "Client");
        c.setAttribute("style", "top:" + (cY + 5) + "px");
    }

    function setDesc(desc)
    {
        var p = getPanel();

        var d = document.getElementById("p" + p.panelID + "Desc");
        d.innerHTML = desc;

        var c = document.getElementById("p" + p.panelID + "Client");
        var cY = getStyle(c, "top", "top");
        var cH = getStyle(c, "height", "height");

        var dY = (parseFloat(cY) + parseFloat(cH));

        d.setAttribute("style", "top:" + (dY + 10) + "px");
    }

    function setDemo(link)
    {
        var p = getPanel();

        p.linkToDemo = link;

        var demoLink = document.createElement("a");
        demoLink.setAttribute("id", "demoLink" + p.panelID);
        demoLink.setAttribute("class", "demolink");
        demoLink.setAttribute("href", p.linkToDemo);
        demoLink.setAttribute("target", "_blank")
        demoLink.innerHTML = "Demo";
        p.demoDiv.appendChild(demoLink);

        var d = document.getElementById("p" + p.panelID + "Desc");
        var dY = getStyle(d, "top", "top");
        var dH = getStyle(d, "height", "height");

        var demoY = (parseFloat(dY) + parseFloat(dH));

        var demo = document.getElementById("p" + p.panelID + "Demo");
        demo.setAttribute("style", "top:" + (demoY + 10) + "px");

        listenEvent(demoLink, "click", onDemoClick);
    }

    function onDemoClick(e)
    {
        //console.log("Demo click: " + this.id);
        trackGA("Work", "Demo clicked: ", this.id);
    }

    function setPic(picID, picLoc, length)
    {
        var p = getPanel();

        p.imgDiv = document.createElement("div");
        p.imgDiv.setAttribute("id", "image" + picID);
        p.imgDiv.setAttribute("class", "image");

        var image = document.createElement("img");
        image.setAttribute("src", picLoc);
        p.imgDiv.appendChild(image);

        p.panelDiv.appendChild(p.imgDiv);

        p.imageArray.push(p.imgDiv);

        if(picID == (length - 1))
           initSlideShow();
    }

    function initSlideShow()
    {
        var p = getPanel();

        p.resetIndex = (p.imageArray.length - 1);
        p.imageIndex = p.resetIndex;

        fadeOut(p);
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