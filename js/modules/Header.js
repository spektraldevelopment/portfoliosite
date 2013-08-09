(function(window){

    Header.prototype.header;

    Header.prototype.logo;
    Header.prototype.linkContainer;

    Header.prototype.workLink;
    Header.prototype.aboutLink;
    Header.prototype.contactLink;
    Header.prototype.cvLink;

    Header.prototype.thumbContainer;

    //Number
    Header.prototype.headerWidth;
    Header.prototype.thumbContainerWidth;

    //Array
    Header.prototype.thumbArray;
    Header.prototype.thumbXArray;

    //XmlDoc
    Header.prototype.xmlDoc;

    ///////////////////////////
    ////Header
    ///////////////////////////
    function Header()
    {
        console.log("Header");

        header = this;

        var main = document.getElementById("mainContainer");

        //Header Container
        var headerDiv = document.createElement("div");
        headerDiv.setAttribute("id", "header");
        headerDiv.setAttribute("class", "header");
        main.appendChild(headerDiv);

        this.headerWidth = getStyle(headerDiv, "width", "width");

        //Logo
        this.logo = document.createElement("div");
        this.logo.setAttribute("id", "logo");
        this.logo.setAttribute("class", "logo");
        this.logo.innerHTML = "Spektral Development";
        headerDiv.appendChild(this.logo);

        //Link Container
        this.linkContainer = document.createElement("div");
        this.linkContainer.setAttribute("id", "linkContainer");
        this.linkContainer.setAttribute("class", "linkCont");
        headerDiv.appendChild(this.linkContainer);

        //WorkLink
        this.workLink = document.createElement("div");
        this.workLink.setAttribute("id", "workLink");
        this.workLink.setAttribute("class", "headerLink");
        this.workLink.setAttribute("style", "color:#fff")
        this.workLink.innerHTML = "Work";
        this.linkContainer.appendChild(this.workLink);

        //About Link
        this.aboutLink = document.createElement("div");
        this.aboutLink.setAttribute("id", "aboutLink");
        this.aboutLink.setAttribute("class", "headerLink");
        this.aboutLink.setAttribute("style", "left:65px");
        this.aboutLink.innerHTML = "About";
        this.linkContainer.appendChild(this.aboutLink);

        //Contact Link
        this.contactLink = document.createElement("div");
        this.contactLink.setAttribute("id", "contactLink");
        this.contactLink.setAttribute("class", "headerLink");
        this.contactLink.setAttribute("style", "left:135px");
        this.contactLink.innerHTML = "Contact";
        this.linkContainer.appendChild(this.contactLink);

        //CV Link
        this.cvLink = document.createElement("div");
        this.cvLink.setAttribute("id", "cvLink");
        this.cvLink.setAttribute("class", "headerLink");
        this.cvLink.setAttribute("style", "left:220px");
        this.cvLink.innerHTML = "CV";
        this.linkContainer.appendChild(this.cvLink);

        var linkX = parseFloat(getStyle(headerDiv, "width", "width")) - parseFloat(getStyle(this.linkContainer, "width", "width"));
        this.linkContainer.setAttribute("style", "left:" + linkX + "px");

        TweenLite.to(headerDiv, 1, {css:{top:0}});

        this.thumbArray = new Array();
        this.thumbXArray = new Array();

        //Thumb Container
        this.thumbContainer = document.createElement("div");
        this.thumbContainer.setAttribute("id", "thumbContainer");
        this.thumbContainer.setAttribute("class", "thumbCont");
        headerDiv.appendChild(this.thumbContainer);

        //Event Listeners
        listenEvent(this.workLink, "click", scrollToWork);
        listenEvent(this.aboutLink, "click", scrollToAbout);
        listenEvent(this.contactLink, "click", scrollToContact);
        listenEvent(this.cvLink, "click", showPDF);
    }

    Header.prototype.setData = function(data)
    {
        this.xmlDoc = data;

        var spacing = 60;

        for(var i = 0; i < data.getElementsByTagName("thumb").length; i++)
        {
            var thumb = document.createElement("img");
            thumb.setAttribute("id", "thumb" + i);
            thumb.setAttribute("class", "thumb");
            thumb.setAttribute("style", "left:" + (i * spacing) + "px");

            if(i == 0)
                thumb.setAttribute("style", "border-color:#fff");

            thumb.setAttribute("src", data.getElementsByTagName("thumb")[i].childNodes[0].nodeValue);

            this.thumbArray.push(thumb);
            this.thumbXArray.push((i * spacing));

            listenEvent(thumb, "click", onThumbClick);
            this.thumbContainer.appendChild(thumb);

            this.thumbContainerWidth = (i * spacing) + spacing;
        }

        //Center Thumb Container
        var thumbContX = (parseFloat(this.headerWidth) / 2) - (this.thumbContainerWidth / 2);
        this.thumbContainer.setAttribute("style", "left:" + thumbContX + "px");
    }

    Header.prototype.highlightLink = function(link)
    {
        if(link == "Work")
            this.workLink.setAttribute("style", "color:#fff");
        else
            this.workLink.setAttribute("style", "color:#cccccc");

        if(link == "About")
            this.aboutLink.setAttribute("style", "left:65px; color:#fff");
        else
            this.aboutLink.setAttribute("style", "left:65px; color:#cccccc");

        if(link == "Contact")
            this.contactLink.setAttribute("style", "left:135px; color:#fff");
        else
            this.contactLink.setAttribute("style", "left:135px; color:#cccccc");
    }

    //function highlightThumb(thumb)
    Header.prototype.highlightThumb = function(thumb)
    {
        for(var i = 0; i < this.xmlDoc.getElementsByTagName("thumb").length; i++)
        {
            if(i == thumb)
            {
               header.thumbArray[i].setAttribute("style", "left:" + header.thumbXArray[i] + "px; border-color:#fff");
            }
            else
            {
               header.thumbArray[i].setAttribute("style", "left:" + header.thumbXArray[i] + "px; border-color:#ccc");
            }
        }
    }

    function onThumbClick(e)
    {
        var thumbId = this.id;
        var thumbNum = thumbId.replace("thumb", "");

        workMoveTo(thumbNum);

        header.highlightThumb(thumbNum);

        trackGA("Header", "Thumb Clicked: ", thumbId);
    }

    Header.prototype.reposElements = function(hWidth)
    {
        var tC = document.getElementById("thumbContainer");

        var thumbContX = (parseFloat(hWidth) / 2) - (this.thumbContainerWidth / 2);
        tC.setAttribute("style", "left:" + thumbContX + "px");

        var lC = document.getElementById("linkContainer");
        var linkX = hWidth - parseFloat(getStyle(lC, "width", "width"));
        lC.setAttribute("style", "left:" + linkX + "px");

        //console.log("hWidth: " + hWidth);
    }

    window.Header = Header;

}(window))
