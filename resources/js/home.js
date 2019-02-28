$(function(){
  $("#add").click(function(){
    element =1;
  });
  $("#in").click(function(){
    element =2;
  });
  $("#calc").click(function(){
    set = 2;
    var shapes = stage.find('Circle');
    shapes.forEach(function(shape) {
      shape.fill('#101010');
    });
      layer.draw();
  });
  $("#out").click(function(){
    var kolo=[];
    jazli.forEach(function(jazol) {
      if(jazol.children.length == 2){
        var name = jazol.name().split('_').filter(v => v);
        for (var i = 0; i < name.length; i+=2)
          if(node = stage.find('.'+name[i]+'_'+(name[i+1] == 0 ? '2':'0'))[0])
            node.children[0].name(jazol.id().toString());
      }
    });
    resistors.forEach(function(resistor) {
      if(resistor.children.length == 4) kolo.push(resistor.children[2].children[0].name(),resistor.name(),resistor.children[3].children[0].name());
    });
    for (var i = 0; i < kolo.length; i+=3) {
      if(kolo[i] == 0 || kolo[i+2] == 0){
        temp = [kolo[0],kolo[1],kolo[2]];
        kolo[0]=kolo[i];
        kolo[1]=kolo[i+1];
        kolo[2]=kolo[i+2];
        kolo[i] = temp[0];
        kolo[i+1] = temp[1];
        kolo[i+2] = temp[2];
      }
    }
    console.log(kolo.join('_'));
    $.get("/electro/public/calc",{kolo:kolo.join('_')},function(data){
      if(data.length < 100)
       alert(data);
       else{
         $(window).scrollTop( 300 );
       $('.result').html(data);
        }
    });
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
    if(otpornik.children[2].name() != 'none')
      updateLine(otpornik.children[2]);
    if (otpornik.children[3].name() != 'none')
         updateLine(otpornik.children[3]);
    layer.draw();
  });
  $("#edit-rr").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.rotate(10);
    if(otpornik.children[2].name() != 'none')
      updateLine(otpornik.children[2]);
    if (otpornik.children[3].name() != 'none')
         updateLine(otpornik.children[3]);
    layer.draw();
  });
  $("#edit-delete").click(function(){
    var otpornik = stage.find('#'+editValue.attr('name'))[0];
    otpornik.destroy();
    layer.draw();
    edit.fadeOut();
  });
  //edit objekts
  var element;
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
  var tempId='';
  var resistors=[];
  var jazli=[];
  var set;
  var stage = new Konva.Stage({
    container: 'container',
    width: width,
    height: height,
    draggable: true
  });
  stage.on('click  tap',function(){
    switch (element) {
      case 1:
        addOtpornik(stage.getPointerPosition());
        break;
        case 2:
          addJazol(stage.getPointerPosition());
          break;
    }
    element=0;
  });
  var layer = new Konva.Layer();
  stage.add(layer);

  function addJazol(pos) {
    var jazol = new Konva.Group({
        x: (pos.x-20)/stage.scaleX() - stage.x()/stage.scaleX(),
        y: (pos.y-20)/stage.scaleX() -stage.y()/stage.scaleX(),
        id:i,
        name:'',
        draggable: true
     });
    var circle = new Konva.Circle({ x: 20, y: 20, radius: 7, fill: '#343a40'});
    var hitbox = new Konva.Rect({ x:0, y:0, width:40, height:40 });
    jazol.add(circle,hitbox);

   jazol.on('dblclick  dbltap',function(){
     jazol.destroy();
     layer.draw();
   });
   jazol.on('dragmove', function () {
      if(jazol.name() != ''){
       var par = jazol.name().split('_').filter(v => v);
       for (var i = 0; i < par.length; i+=2) {
         var lines = stage.find('.'+par[i]);
         lines.forEach(function(line){
           if(line.className == 'Line'){
             var boundingBox = jazol.children[0].getClientRect({ relativeTo: stage });
             var points = line.points();
             points[par[i+1]]=boundingBox.x+7;
             points[1+parseInt(par[i+1])]=boundingBox.y+7;
           }
         });
       }
    layer.draw();
    }
  });
   jazol.on('click  tap',function(){
   conect(jazol,true);
   });
    i++;
    layer.add(jazol);
    jazli[i]=jazol;
    layer.draw();
  }

  function addOtpornik(pos) {
      var group = new Konva.Group({
           x: (pos.x-70)/stage.scaleX() - stage.x()/stage.scaleX(),
           y: (pos.y-15)/stage.scaleX() -stage.y()/stage.scaleX(),
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
        stroke: '#343a40',
        strokeWidth: 4,
      });
      var leftNode = new Konva.Group({x: -13, y: -5,name:'none'});
      var circle = new Konva.Circle({ x: 20, y: 20, radius: 7, fill: '#343a40'});
      var hitbox = new Konva.Rect({ x:0, y:0, width:40, height:40});
      leftNode.add(circle,hitbox);

      var rightNode = new Konva.Group({x:108, y:-5, name:'none'});
      var circle = new Konva.Circle({ x: 20, y: 20, radius: 7, fill: '#343a40'});
      var hitbox = new Konva.Rect({ x:0, y:0, width:40, height:40});
      rightNode.add(circle,hitbox);
      var vrednost = new Konva.Text({
         x: 47,
         y: 5,
         text: '10Ω',
         fontSize: 22,
         fontStyle:'bold',
         fontFamily: 'Calibri',
         fill: '#343a40'
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
     rightNode.on('click  tap',function(){conect(rightNode);});
     leftNode.on('click  tap',function(){conect(leftNode);});
     group.on('dragmove', function () {
       if(leftNode.name() != 'none')
         updateLine(leftNode);
       if (rightNode.name() != 'none')
            updateLine(rightNode);
    });
      i++;
      layer.add(group);
      layer.draw();
      resistors[i]=group;
    }

  function updateLine(node){
    var par = node.name().split('_');
    var lines = stage.find('.'+par[0]);
    lines.forEach(function(line){
        if(line.className == 'Line'){
          var boundingBox = node.children[0].getClientRect({ relativeTo: stage });
          var points = line.points();
          points[par[1]]=boundingBox.x;
          points[1+parseInt(par[1])]=boundingBox.y;
          layer.draw();
        }
    });
  }

  function conect(node,jazol=false) {
   if(set>0){
     node.children[0].name(set == 2 ? '0':'100');
     node.children[0].fill('#007bff');
     set--;
     layer.draw();
     return 1;
   }
   if(!jazol)node.children[0].radius(0);
  var boundingBox = node.children[0].getClientRect({ relativeTo: stage });
  if(line){
    jazol?xy.push(boundingBox.x+7 ,boundingBox.y+7):xy.push(boundingBox.x ,boundingBox.y);
    var conLine = new Konva.Line({
      points: [xy[0],xy[1],xy[2],xy[3]],
      stroke: '#343a40',
      strokeWidth: 4,
      name: lineId.toString(),
      id:'_'+tempId
    });
    conLine.on('dblclick  dbltap',function(){
      lineId--;
      conLine.destroy();
      if(conLine.id()!='_'){
          var type=1;
          var jazliId = conLine.id().split('_').filter(v => v);
          jazliId.forEach(function(jazol){
            var node = stage.find('#'+jazol)[0];
            var name = node.name().split('_').filter(v => v);
            for (var i = 0; i < name.length; i+=2) if(name[i] == conLine.name()){ type = name[i+1];name.splice(i,2); break;}
            node.name(name.join('_'));
          });
          if(type == 0 || type == 2){
            type = type == 0 ? 2:0;
            if(node = stage.find('.'+conLine.name()+'_'+type)[0]){
              node.add(new Konva.Rect({ x:0, y:0, width:40, height:40})).name('none');
              node.children[0].radius(7);
              node.children[0].name('');
            }
          }
      }else{
        var node = stage.find('.'+conLine.name()+'_0')[0];
        node.add(new Konva.Rect({ x:0, y:0, width:40, height:40})).name('none');
        node.children[0].radius(7);
        node.children[0].name('');
        node = stage.find('.'+conLine.name()+'_2')[0];
        node.add(new Konva.Rect({ x:0, y:0, width:40, height:40})).name('none');
        node.children[0].radius(7);
        node.children[0].name('');
      }
      tempId='';
      layer.draw();
    });
    if(jazol){
      conLine.id(conLine.id()+'_'+node.id());
      node.name(node.name()+'_'+lineId+'_2');
    }else{
      node.name(lineId+'_2');
      node.children[0].name(lineId.toString());
      node.children[1].destroy();
    }
    layer.add(conLine);
    xy = [];
    lineId++;
    line = false;
    if(tempId!='') stage.find('#'+tempId).setListening(true);
    var shapes = stage.find('Circle');
    shapes.forEach(function(shape) {
      shape.fill('#343a40');
    });
  }else{
    if(jazol){
      tempId = node.id();
      node.name(node.name()+'_'+lineId+'_0');
      node.setListening(false);
    }else{
    node.name(lineId+'_0');
    node.children[0].name(lineId.toString());
    node.children[1].destroy();
    }
    line = true;
    jazol?xy.push(boundingBox.x+7 ,boundingBox.y+7):xy.push(boundingBox.x ,boundingBox.y);
    var shapes = stage.find('Circle');
    shapes.forEach(function(shape) {
      shape.fill('#101010');
    });
  }
    layer.draw();
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
  function getDistance(p1, p2) {
      return Math.sqrt(Math.pow((p2.x - p1.x), 2) + Math.pow((p2.y - p1.y), 2));
  }
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
