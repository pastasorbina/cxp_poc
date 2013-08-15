
//var canvasId = '';
//var canvasObj = new Object;

var initUrl; 
var stackArr = new Array();
  

function Stacklet(cId) {
    var canvasId = cId;
    var canvasObj = $(cId);
     
    
    var stack = {
        data : new Array(), 
        push : function(url){ 
          this.data.push(url);
        },
        pop : function(url){ 
          this.data.pop();
        }, 
        getLast : function(){ 
            return this.data[(this.data.length-1)];
        },
        getFirst : function(){ 
            return this.data[0];
        },
        getPrevious : function(){
            if(this.data[(this.data.length-2)] != undefined) {
                return this.data[(this.data.length-2)];
            } else {
              die();  
            } 
        },
        empty : function(){ 
            this.data = new Array();
        }
    };
    
    
    canvasId = cId;
    //console.log('initiate canvas '+canvasId);
      
    var gorn = function() {
       //console.log('gornnnn !!');
       //console.log(stack.data);
    };
    
    var loading = function() {
        $(canvasId).html('loading ...');
    };
    
    var loadHTML = function(url, params) {
        loading();
        $.post(url, params, function(data) {
            $(canvasId).html(renderHTML(data));
        },'html'); 
    };
    
    var renderHTML = function(html) {
        var htmlHead = '<div class="inner">';
        var htmlFoot = '</div>';
        return htmlHead+html+htmlFoot;
    };
    
    this.justRender = function(data) { 
        $(function() {
            loading();
            $(canvasId).html(renderHTML(data) ); 
        }); 
    };
    
    /** Function Startup */
    this.start = function( url='',params='' ){ 
        $(function() {
            loading();
            $.post(url, params, function(data) { 
                $(canvasId).html(renderHTML(data) );
            },'html').complete(function(){ }); 
            stack.push(url);  
        });
         
    };
    
    this.reset = function() { 
        var url = stack.getFirst();
        //console.log(url);
        stack.empty();
        loading();
        $.post(url, {}, function(data) {
            $(canvasId).html(renderHTML(data));
            
        },'html');
        stack.push(url);  
    };
    
    this.refresh = function() { 
        var url = stack.getLast();
        loading();
        $.post(url, {}, function(data) { 
            $(canvasId).html(renderHTML(data));
        },'html'); 
    };
    
    
    /** Function Startup
     */
    this.stackNext = function( 
                            url='',
                            params={}
                            ) {
        $(function() { 
            loadHTML(url, params);
            stack.push(url);
            //console.log(stack.data);
        }); 
    };
    
    this.stackPrev = function() {
        $(function() {
            var prev = stack.getPrevious();
            if(prev != undefined) { 
                loadHTML(prev);
                stack.pop(); 
            }
            //console.log(stack.data);
        }); 
    };
    
    
}





