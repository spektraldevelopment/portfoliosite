(function(window){

    //Divs
//    About.prototype.mainDiv;
//    About.prototype.panelDiv;
//    About.prototype.titleDiv;
//    About.prototype.profileImage;
//    About.prototype.aboutParagraph;

    function About(data)
    {
        this.mainDiv = document.getElementById("centerContainer");

        //Panel
        this.panelDiv = document.createElement("div");
        this.panelDiv.setAttribute("id", "aboutPanel");
        this.panelDiv.setAttribute("class", "panel");
        this.panelDiv.setAttribute("style", "top:875px");//900px
        this.mainDiv.appendChild(this.panelDiv);

        //Title
        this.titleDiv = document.createElement("div");
        this.titleDiv.setAttribute("id", "aboutTitle");
        this.titleDiv.setAttribute("class", "sectionTitle");
        this.titleDiv.innerHTML = "About";
        this.panelDiv.appendChild(this.titleDiv);

        //Paragraph
        this.aboutParagraph = document.createElement("p");
        this.aboutParagraph.setAttribute("id", "aboutParagraph");
        this.aboutParagraph.setAttribute("class", "aboutContent");
        this.panelDiv.appendChild(this.aboutParagraph);

        //Profile Image
        this.profileImage = document.createElement("img");
        this.profileImage.setAttribute("id", "profileImage");
        this.profileImage.setAttribute("class", "profile");

        if(root == "localhost")
            this.profileImage.setAttribute("src", "images/profilePic.jpg");
        else
            this.profileImage.setAttribute("src", "images/profilePic.jpg");

        this.aboutParagraph.appendChild(this.profileImage);

        var txt = document.createTextNode(data.childNodes[0].nodeValue);
        this.aboutParagraph.appendChild(txt);

        console.log("About");
    }

    window.About = About;

}(window));