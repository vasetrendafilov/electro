$(function(){
  $("#add").click(function(){
    addOtpornik(layer, stage);
  });
  $("#in").click(function(){
    addJazol(layer, stage);
  });
  $("#edit-check").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.name(editValue.val());
    otpornik.children[0].text(editValue.val()+'Ω');
    edit.fadeOut();
    layer.draw();
  });
  $("#edit-rl").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.rotate(-10);
    layer.draw();
  });
  $("#edit-rr").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.rotate(10);
    layer.draw();
  });
  $("#edit-delete").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.destroy();
    layer.draw();
    edit.fadeOut();
  });
  //edit objekts
  var edit = $(".edit");
  var editValue = $("#edit-value");

  //edit objekts

  var container = $("#container");
  var width = container.width();
  var height = container.height();
  var tween = null;
  var line = false;
  var xy=[];
  var i=1;

  var stage = new Konva.Stage({
    container: 'container',
    width: width,
    height: height,
    draggable: true
  });
  var layer = new Konva.Layer();
  stage.add(layer);

  function addJazol(layer,stage) {
    var circle = new Konva.Circle({
     x: Math.random() * stage.getWidth() / 2,
     y: 40,
     radius: 10,
     fill: '#ffa700',
     id:i,
     name:'0',
     draggable: true
   });
   circle.on('dblclick  dbltap',function(){
     var pos = stage.getPointerPosition();
     if(pos.x  + 132 > window.screen.width) edit.css({top: pos.y + 30 , left: window.screen.width - 240});
      else if(pos.x  - 132 < 0) edit.css({top: pos.y + 30 , left: 10});
      else  edit.css({top: pos.y + 30 , left: pos.x -100});
      editValue.fadeOut(1);
      edit.fadeIn();
      editValue.val('0');
      editValue.prop('name', circle.id());
   })
   circle.on('click  tap',function(){
     if(line){
       xy.push(circle.x(),circle.y());
       var redLine = new Konva.Line({
         points: [xy[0],xy[1],xy[2],xy[3]],
         stroke: '#000',
         strokeWidth: 4,
         lineCap: 'round',
         lineJoin: 'round'
       });
         layer.add(redLine);
           layer.draw();
        xy = [];
        line = false;
     }else{
       line = true;
      xy.push(circle.x(),circle.y());
    }
   });
    i++;
    layer.add(circle);
    layer.draw();
  }

  function addOtpornik(layer, stage) {
    var group = new Konva.Group({
                 x: Math.random() * stage.getWidth()/2,
                 y: Math.random() * stage.getHeight()/2,
                 id:i,
                 name:'10',
                 draggable: true
             });

        var otpornik = new Konva.Shape({
          sceneFunc: function (context, shape) {
          context.beginPath();
          context.moveTo(0, 15);
          context.lineTo(30,15);
          context.moveTo(95, 15);
          context.lineTo(125,15);
          context.rect(30,0,65,30);
          context.fillStrokeShape(shape);
        },
          x:5,
          y:0,
          fill: 'transparent',
          stroke: '#000',
          strokeWidth: 4,
        });
        var leftNode = new Konva.Circle({
         x: 0,
         y: 15,
         radius: 6,
         fill: '#000',
         name:'0'
       });
       var rightNode = new Konva.Circle({
        x: 135,
        y: 15,
        radius: 6,
        fill: '#000',
        name:'0'
      });
      var vrednost = new Konva.Text({
         x: 47,
         y: 5,
         text: '10Ω',
         fontSize: 22,
         fontStyle:'bold',
         fontFamily: 'Calibri',
         fill: '#000'
       });

        group.add(vrednost,otpornik,leftNode,rightNode);
     otpornik.on('dblclick  dbltap',function(){
       console.log(group.position());
       var pos = stage.getPointerPosition();
       if(pos.x  + 132 > window.screen.width) edit.css({top: pos.y + 30 , left: window.screen.width - 240});
        else if(pos.x  - 132 < 0) edit.css({top: pos.y + 30 , left: 10});
        else  edit.css({top: pos.y + 30 , left: pos.x -100});
        editValue.fadeIn(1);
        edit.fadeIn();
        editValue.val(group.name());
        editValue.prop('name', group.id());
     });
     rightNode.on('click  tap',function(){
       if(line){
         xy.push(group.x()+115,group.y()+15);
         var redLine = new Konva.Line({
           points: [xy[0],xy[1],xy[2],xy[3]],
           stroke: '#000',
           strokeWidth: 4,
           lineCap: 'round',
           lineJoin: 'round'
         });
           layer.add(redLine);
             layer.draw();
          xy = [];
          line = false;
       }else{
         line = true;
        xy.push(group.x() + 115 ,group.y() + 15);
      }
     });
     leftNode.on('click  tap',function(){
       if(line){
         xy.push(group.x(),group.y()+ 15);
         var redLine = new Konva.Line({
           points: [xy[0],xy[1],xy[2],xy[3]],
           stroke: '#000',
           strokeWidth: 4,
           lineCap: 'round',
           lineJoin: 'round'
         });
           layer.add(redLine);
             layer.draw();
          xy = [];
          line = false;
       }else{
         line = true;
        xy.push(group.x(),group.y()+ 15);
      }
     });
      i++;
      layer.add(group);
      layer.draw();
    }
});
