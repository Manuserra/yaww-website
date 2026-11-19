/* premium/ sits one folder below the official site and reuses its
   images, data and scripts in place (no copies). The data files hold
   asset paths relative to the site root ("images/…", "retreats/…"), so
   this walks the YAWW_* data globals once, right after they load and
   before any component renders, and points those paths one folder up. */
(function () {
  var ASSET = /^(images|retreats)\//;
  function fix(v, depth) {
    if (depth > 8 || v == null) return v;
    if (typeof v === "string") return ASSET.test(v) ? "../" + v : v;
    if (typeof v !== "object") return v;
    for (var k in v) if (Object.prototype.hasOwnProperty.call(v, k)) v[k] = fix(v[k], depth + 1);
    return v;
  }
  Object.keys(window).forEach(function (name) {
    if (/^YAWW_/.test(name) && !window["__fixed_" + name]) {
      fix(window[name], 0);
      window["__fixed_" + name] = true;
    }
  });
})();
