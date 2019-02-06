$(function(){
  $("#add").click(function(){
    addOtpornik();
  });
  $("#in").click(function(){
    addJazol();
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
  var lineId=1;
  var lastDist = 0;
   var startScale = 1;

   function getDistance(p1, p2) {
       return Math.sqrt(Math.pow((p2.x - p1.x), 2) + Math.pow((p2.y - p1.y), 2));
   }

  var stage = new Konva.Stage({
    container: 'container',
    width: width,
    height: height,
    draggable: true
  });
  var layer = new Konva.Layer();
  stage.add(layer);

  function addJazol() {
    var circle = new Konva.Circle({
     x: Math.random() * stage.getWidth() / 2,
     y: 40,
     radius: 10,
     fill: '#000',
     id:i,
     name:'0',
     draggable: true
   });
   circle.on('dblclick  dbltap',function(){circle.destroy();});
   circle.on('dragmove', function () {
     var lines = stage.find('.'+circle.name());
     if(lines.length < 1 || lines == undefined)return 0;
      lines.each(function(line) {
        if(line.className == 'Line')
        line.destroy();
        else
        line.name(' ');
      });
     layer.draw();
  });
   circle.on('click  tap',function(){
   conect(circle);
   });
    i++;
    layer.add(circle);
    layer.draw();
  }

  function addOtpornik() {
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
         radius: 10,
         fill: '#000'
       });
       var rightNode = new Konva.Circle({
        x: 135,
        y: 15,
        radius: 10,
        fill: '#000'
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
     conect(rightNode);
     });
     leftNode.on('click  tap',function(){
       conect(leftNode);
     });
     group.on('dragmove', function () {
       var lines = stage.find('.'+leftNode.name());
       if(lines.length < 1 || lines == undefined){
        lines = stage.find('.'+rightNode.name());
        if(lines.length < 1 || lines == undefined) return 0;
      }
        lines.each(function(line) {
          if(line.className == 'Line')
          line.destroy();
          else
          line.name(' ');
        });
       layer.draw();
    });
      i++;
      layer.add(group);
      layer.draw();
    }

  function conect(node) {
  var boundingBox = node.getClientRect({ relativeTo: stage });
  if(line){
    xy.push(boundingBox.x + 10 ,boundingBox.y + 10);

    var conLine = new Konva.Line({
      points: [xy[0],xy[1],xy[2],xy[3]],
      stroke: '#000',
      strokeWidth: 4,
      name: lineId.toString()
    });
  /*  var boundingBox = conLine.getClientRect({ relativeTo: stage });
    var box = new Konva.Rect({
             x: boundingBox.x-5,   za podobro klikanje
             y: boundingBox.y,
             width: boundingBox.width +10,
             height: boundingBox.height,
             stroke: 'red',
             strokeWidth: 1
         });*/
  //  box.on('dblclick  dbltap',function(){conLine.destroy(); box.destroy();});
  conLine.on('dblclick  dbltap',function(){conLine.destroy();});
    node.name(lineId.toString());
    layer.add(conLine);
    layer.draw();
    xy = [];
    lineId++;
   line = false;
  }else{
    line = true;
    node.name(lineId.toString());
    xy.push(boundingBox.x + 10 ,boundingBox.y + 10);
  }
}

  //zomm in/out for mobile
  stage.getContent().addEventListener('touchmove', function(evt) {
         var touch1 = evt.touches[0];
         var touch2 = evt.touches[1];
         if(touch1 && touch2) {
             var dist = getDistance({
                 x: touch1.clientX,
                 y: touch1.clientY
             }, {
                 x: touch2.clientX,
                 y: touch2.clientY
             });

             if(!lastDist) {
                 lastDist = dist;
             }
             var scale = stage.getScaleX() * dist / lastDist;
             stage.scaleX(scale);
             stage.scaleY(scale);
             stage.draw();
             lastDist = dist;
         }
     }, false);
  stage.getContent().addEventListener('touchend', function() { lastDist = 0; }, false);
  //zomm in/out for desktop
  var scaleBy = 1.2;
   stage.on('wheel', e => {
     e.evt.preventDefault();
     var oldScale = stage.scaleX();
     var mousePointTo = {
       x: stage.getPointerPosition().x / oldScale - stage.x() / oldScale,
       y: stage.getPointerPosition().y / oldScale - stage.y() / oldScale
     };
     var newScale =
       e.evt.deltaY > 0 ? oldScale * scaleBy : oldScale / scaleBy;
     stage.scale({ x: newScale, y: newScale });
     var newPos = {
       x:
         -(mousePointTo.x - stage.getPointerPosition().x / newScale) *
         newScale,
       y:
         -(mousePointTo.y - stage.getPointerPosition().y / newScale) *
         newScale
     };
     stage.position(newPos);
     stage.batchDraw();
   });

});
