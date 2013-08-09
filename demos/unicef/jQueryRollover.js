$(document).ready(function(){
	$("img").hover(
		function(event){
			if (this.id){
				this.src = "img/" + this.id + "_on.jpg";
			}
		}, 
		function(event){
			if(this.id){
				this.src = "img/" + this.id + "_off.jpg";
			}
		}
	);
});
