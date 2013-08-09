(function(window){

    //Divs
    Contact.prototype.mainDiv;
    Contact.prototype.panelDiv;
    Contact.prototype.titleDiv;
    Contact.prototype.contactForm;
    Contact.prototype.emailInput;
    Contact.prototype.nameInput;
    Contact.prototype.subjectInput;
    Contact.prototype.messageInput;
    Contact.prototype.titleDiv;
    Contact.prototype.submitButton;
    Contact.prototype.warningMessage;

    Contact.prototype.warningString;

    function Contact()
    {
        this.mainDiv = document.getElementById("centerContainer");

        //Panel
        this.panelDiv = document.createElement("div");
        this.panelDiv.setAttribute("id", "contactPanel");
        this.panelDiv.setAttribute("class", "panel");
        this.panelDiv.setAttribute("style", "top:1750px");//1800px
        this.mainDiv.appendChild(this.panelDiv);

        //Title
        this.titleDiv = document.createElement("div");
        this.titleDiv.setAttribute("id", "contactTitle");
        this.titleDiv.setAttribute("class", "sectionTitle");
        this.titleDiv.innerHTML = "Contact";
        this.panelDiv.appendChild(this.titleDiv);

        //Contact Form
        this.contactForm = document.createElement("form");
        this.contactForm.setAttribute("id", "contactForm");
        this.contactForm.setAttribute("class", "contact");
        this.panelDiv.appendChild(this.contactForm);

        //Title div
        this.titleDiv = document.createElement("div");
        this.titleDiv.setAttribute("id", "titleDiv");
        this.titleDiv.setAttribute("style", "position:absolute; top:80px; left:25px");
        this.panelDiv.appendChild(this.titleDiv);

        //Email title
        var emailTitle = document.createElement("div");
        emailTitle.setAttribute("id", "emailTitle");
        emailTitle.setAttribute("class", "contactTitle");
        emailTitle.innerHTML = "Email";
        this.titleDiv.appendChild(emailTitle);

        //Email input
        this.emailInput = document.createElement("input");
        this.emailInput.setAttribute("id", "email");
        this.emailInput.setAttribute("class", "contactField");
        this.emailInput.setAttribute("type", "text");
        this.emailInput.setAttribute("style", "width:200px")
        this.contactForm.appendChild(this.emailInput);

        //Name title
        var nameTitle = document.createElement("div");
        nameTitle.setAttribute("id", "nameTitle");
        nameTitle.setAttribute("class", "contactTitle");
        nameTitle.setAttribute("style", "top:50px");
        nameTitle.innerHTML = "Name";
        this.titleDiv.appendChild(nameTitle);

        //Name input
        this.nameInput = document.createElement("input");
        this.nameInput.setAttribute("id", "name");
        this.nameInput.setAttribute("class", "contactField");
        this.nameInput.setAttribute("type", "text");
        this.nameInput.setAttribute("style", "top:50px");
        this.contactForm.appendChild(this.nameInput);

        //Subject Title
        var subjectTitle = document.createElement("div");
        subjectTitle.setAttribute("id", "subjectTitle");
        subjectTitle.setAttribute("class", "contactTitle");
        subjectTitle.setAttribute("style", "top:100px");
        subjectTitle.innerHTML = "Subject";
        this.titleDiv.appendChild(subjectTitle);

        //Subject input
        this.subjectInput = document.createElement("input");
        this.subjectInput.setAttribute("id", "subject");
        this.subjectInput.setAttribute("class", "contactField");
        this.subjectInput.setAttribute("type", "text");
        this.subjectInput.setAttribute("style", "top:100px; width:400px");
        this.contactForm.appendChild(this.subjectInput);

        //Message title
        var messageTitle = document.createElement("div");
        messageTitle.setAttribute("id", "messageTitle");
        messageTitle.setAttribute("class", "contactTitle");
        messageTitle.setAttribute("style", "top:150px");
        messageTitle.innerHTML = "Message";
        this.titleDiv.appendChild(messageTitle);

        //MessageInput
        this.messageInput = document.createElement("input");
        this.messageInput.setAttribute("id", "message");
        this.messageInput.setAttribute("class", "contactField");
        this.messageInput.setAttribute("type", "textArea");
        this.messageInput.setAttribute("style", "top:150px; width:500px; height:100px");
        this.contactForm.appendChild(this.messageInput);

        //Submit button
        this.submitButton = document.createElement("input");
        this.submitButton.setAttribute("id", "submitButton");
        this.submitButton.setAttribute("class", "submit");
        this.submitButton.setAttribute("type", "submit");
        this.submitButton.setAttribute("value", "Submit");
        this.panelDiv.appendChild(this.submitButton);

        //Warning message
        this.warningMessage = document.createElement("div");
        this.warningMessage.setAttribute("id", "warningMessage");
        this.warningMessage.setAttribute("class", "warning");
        //this.warningMessage.innerHTML = "Warning message goes here";
        this.panelDiv.appendChild(this.warningMessage);

//        var underLine = document.createElement("div");
//        underLine.setAttribute("class", "underline");
//        // underLine.innerHTML = "underline";
//        this.panelDiv.appendChild(underLine);


        listenEvent(this.submitButton, "click", submitForm);

        console.log("Contact");
    }

    function submitForm()
    {
        var wM = document.getElementById("warningMessage");
        wM.innerHTML = "";

        var isEmailValid   = validateEmail();
        var isNameValid    = validateName();
        var isSubjectValid = validateSubject();
        var isMessageValid = validateMessage();

        if(isEmailValid && isNameValid && isSubjectValid && isMessageValid)
        {
            var submit = document.getElementById("submitButton");
            submit.setAttribute("style", "cursor:default");
            submit.disabled = true;

            wM.setAttribute("style", "color:#fff");
            wM.innerHTML = "Thank you for submitting your message. I will get back to you as soon as possible.";

            var e = document.forms["contactForm"].elements["email"].value;
            var n = document.forms["contactForm"].elements["name"].value;
            var s = document.forms["contactForm"].elements["subject"].value;
            var m = document.forms["contactForm"].elements["message"].value;

            var phpLoc = "http://spektraldevelopment.com/site/php/mail.php";
            var queryString = phpLoc + "?e=" + e + "&s=Name: " + n + ", Subject: " + s + "&m=" + m;

            console.log("Query String: " + queryString);

            var phphttp;

            if (window.XMLHttpRequest)
            {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                phphttp = new XMLHttpRequest();
            }
            else
            {
                // code for IE6, IE5
                phphttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            phphttp.open("POST", queryString ,true);
            phphttp.send();

            //console.log("email: " + email + " name: " + name + " subject: " + subject + " message: " + message);


            trackGA("Contact", "Form submitted", n);

            console.log("Submitting message");
        }
        else
        {
            var warning = document.createTextNode("Please fill out the following fields: ")

            wM.appendChild(warning);

            if(isEmailValid == false)
                throwWarning("Email");

            if(isNameValid == false)
                throwWarning("Name");

            if(isSubjectValid == false)
                throwWarning("Subject");

            if(isMessageValid == false)
                throwWarning("Message");
        }
        console.log("Submitting form: " + isEmailValid);
    }

    function validateEmail()
    {
        var valid;
        var email = document.forms["contactForm"].elements["email"].value;

        if(email == "")
        {
            valid = false;
        }
        else
        {
            var isValid = validateEmailString(email);

            console.log("Email isValid: " + isValid);

            if(isValid)
                valid = true;
            else
                valid = false;
        }

        return valid;
    }

    function validateName()
    {
        var valid;
        var name = document.forms["contactForm"].elements["name"].value;

        if(name == "")
        {
            valid = false;
        }
        else
        {
            valid = true;
        }

        return valid;
    }

    function validateSubject()
    {
        var valid;
        var subject = document.forms["contactForm"].elements["subject"].value;

        if(subject == "")
        {
            valid = false;
        }
        else
        {
            valid = true;
        }

        return valid;
    }

    function validateMessage()
    {
        var valid;
        var message = document.forms["contactForm"].elements["message"].value;

        if(message == "")
        {
            valid = false;
        }
        else
        {
            valid = true;
        }

        return valid;
    }

    function throwWarning(id)
    {
        var warning = document.createTextNode("(" + id + ")" + " ")

        var wM = document.getElementById("warningMessage");
        wM.appendChild(warning);

        console.log(id + " is invalid!");
    }

    function validateEmailString(email)
    {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    }


    window.Contact = Contact;

}(window))
