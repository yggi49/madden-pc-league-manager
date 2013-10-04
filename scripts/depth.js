function showhide(node) {

  var info = node.childNodes[1];

  if (info.style.display == '') {

    var divNodes = document.getElementsByTagName('div');

    for (i = 0; i < divNodes.length; i++) {
      if (divNodes[i].className == 'info' && divNodes[i] != info) {
        divNodes[i].style.display = '';
      }
    }

    info.style.display = 'block';
  } else {
    info.style.display = '';
  }
} // showhide(node)
