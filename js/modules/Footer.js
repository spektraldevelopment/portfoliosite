(function(window){

    function Footer(view)
    {
        console.log("Footer");

        var viewWidth = view.width;
        var viewHeight = view.height;

        var main = document.getElementById("mainContainer");

        var footerDiv = document.createElement("div");
        footerDiv.setAttribute("id", "footer");
        footerDiv.setAttribute("class", "footer");
        footerDiv.setAttribute("style", "top:" + viewHeight + "px");
        main.appendChild(footerDiv);

        var userInfo = document.createElement("div");
        userInfo.setAttribute("id", "userInfo");
        userInfo.setAttribute("class", "user");
        userInfo.innerHTML = "Browser: "  + browser + " Platform: " + platform;
        footerDiv.appendChild(userInfo);

        var copyRight = document.createElement("div");
        copyRight.setAttribute("id", "copyright");
        copyRight.setAttribute("class", "copyright");
        copyRight.innerHTML = "Copyright 2012 Dave Boyle. All other trademarks and copyrights are the property of their respective owners.";
        copyRight.setAttribute("style", "left:" + (viewWidth - 480) + "px");
        footerDiv.appendChild(copyRight);

        TweenLite.to(footerDiv, 1, {css:{top:viewHeight - 25}});
    }

    Footer.prototype.setY = function(newY)
    {
        var fD = document.getElementById("footer");
        fD.setAttribute("style", "top:" + newY + "px");
    }

    Footer.prototype.repositionCopyright = function(fWidth)
    {
        var cR = document.getElementById("copyright");
        cR.setAttribute("style", "left:" + (fWidth - 435) + "px");
    }

    window.Footer = Footer;

}(window))