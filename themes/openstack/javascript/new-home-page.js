$(document).ready(function() {
  var p1 = $(".clamp1");
  p1.attr('title', 'read more');

  var p2 = $(".clamp2");
  p2.attr('title', 'read more');

  $clamp(
        p1[0],
        {clamp: 3, useNativeClamp: true}
  );

  $clamp(
      p2[0],
      {clamp: 3, useNativeClamp: true}
  );

  $(p1[0]).click(function(e){
    e.stopPropagation()
    var p = $(p1[0]);
    p.attr("style", "");
    return false;
  });

  $(p2[0]).click(function(e){
    e.stopPropagation()
    var p = $(p2[0]);
    p.attr("style", "");
    return false;
  });
});