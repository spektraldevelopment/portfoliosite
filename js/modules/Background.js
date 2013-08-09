(function(window){

    Background.prototype.mainStage;
    Background.prototype.bgContainer;
    Background.prototype.dotContainer;

    Background.prototype.container;

    ///////////////////////////
    ////Background
    ///////////////////////////
    function Background(theStage)
    {
        this.mainStage = theStage;

        createContainer(this.mainStage);
        createBG(this.mainStage);

        console.log("Background Created");
    }

    ///////////////////////////
    ////Create Container
    ///////////////////////////
    function createContainer(mainStage)
    {
        this.bgContainer = new Container();
        this.bgContainer.alpha = .5;
        this.bgContainer.x = (STAGE_WIDTH / 2) + 10;
        this.bgContainer.y = STAGE_HEIGHT / 2;
        this.bgContainer.regX = (STAGE_WIDTH) / 2;
        this.bgContainer.regY = (STAGE_HEIGHT) / 2;
        mainStage.addChild(this.bgContainer);
    }

    ///////////////////////////
    ////Create BG
    ///////////////////////////
    function createBG(mainStage)
    {
        for(var ex = 0; ex < SCREEN_WIDTH; ex+=20)
        {
            for(var why = 0; why < SCREEN_HEIGHT; why+=20)
            {
                var dot = new Graphics();
                dot.setStrokeStyle(.5);
                dot.beginStroke(Graphics.getRGB(3, 185, 255));
                dot.drawCircle(ex,why, Math.random() *.75);

                this.dotContainer = new Shape(dot);
                this.dotContainer.alpha = 1;
                this.bgContainer.addChild(this.dotContainer);
            }
        }
    }

    window.Background = Background;

}(window));