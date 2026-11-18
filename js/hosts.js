/* ============================================================
   YAWW — "Your hosts"

   One slot, two people, a segmented control between them. The markup
   ships with the teacher already in the slot, so the block is complete
   and readable before this file runs — and stays readable if it never
   does. Each host's content lives in a <template> next to the tabs;
   pressing a tab swaps the slot's contents and replays a short
   crossfade.

   Progressive enhancement is the whole reason for the <template>
   approach rather than building the pair in JS: with scripting off the
   reader still gets the teacher in full, which is the half that
   matters most.
   ============================================================ */
(function (window, document) {
  "use strict";

  function init(root) {
    var tabs = Array.prototype.slice.call(root.querySelectorAll("[data-host-tab]"));
    var slot = root.querySelector("[data-host-slot]");
    if (!tabs.length || !slot) return;

    function show(who) {
      var tpl = root.querySelector('template[data-host="' + who + '"]');
      if (!tpl) return;
      tabs.forEach(function (t) {
        var on = t.getAttribute("data-host-tab") === who;
        t.setAttribute("aria-selected", on ? "true" : "false");
        // Only the selected tab stays in the tab order, which is how a
        // tablist is meant to behave.
        t.tabIndex = on ? 0 : -1;
      });
      slot.classList.remove("is-swapping");
      slot.innerHTML = "";
      slot.appendChild(tpl.content.cloneNode(true));
      void slot.offsetWidth;          // restart the animation
      slot.classList.add("is-swapping");
    }

    tabs.forEach(function (tab, i) {
      tab.addEventListener("click", function () {
        if (tab.getAttribute("aria-selected") === "true") return;
        show(tab.getAttribute("data-host-tab"));
      });
      /* Arrow keys move between tabs, which is what a tablist owes a
         keyboard. Home/End jump to the ends. */
      tab.addEventListener("keydown", function (e) {
        var next = null;
        if (e.key === "ArrowRight") next = tabs[(i + 1) % tabs.length];
        else if (e.key === "ArrowLeft") next = tabs[(i - 1 + tabs.length) % tabs.length];
        else if (e.key === "Home") next = tabs[0];
        else if (e.key === "End") next = tabs[tabs.length - 1];
        if (!next) return;
        e.preventDefault();
        next.focus();
        show(next.getAttribute("data-host-tab"));
      });
    });
  }

  function boot() { document.querySelectorAll("[data-hosts]").forEach(init); }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();

})(window, document);
