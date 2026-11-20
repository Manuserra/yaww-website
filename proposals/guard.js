/* Opened on its own (bookmark, old link)? Reopen inside the viewer so the
   bar with Board and the other options is always there. */
(function () {
  if (window.top !== window.self) return;
  var s = document.currentScript.src;
  var viewer = s.replace(/guard\.js.*$/, "view.html");
  location.replace(viewer + "?u=" + encodeURIComponent(location.pathname + location.search + location.hash));
})();
