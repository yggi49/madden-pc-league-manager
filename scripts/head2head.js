function showh2h(node) {

  var info = node;

  while (info.nextSibling) {
    info = info.nextSibling;

    if (info.nodeName == 'ul') {
      break;
    }
  }

  if (info.nodeName != 'ul') {
    return;
  }

  if (info.style.display == '') {
    info.style.display = 'block';
  } else {
    info.style.display = '';
  }
} // showhide(node)
