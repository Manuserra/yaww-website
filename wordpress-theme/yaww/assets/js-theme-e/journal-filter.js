/* Journal topic filter and the tab bar's sliding indicator, copied from journal.html's inline script.
   One grid, filtered in place: order is never touched, only visibility. */
(function () {
  var tabs  = document.querySelectorAll('.main-journal .tab');
  var cards = document.querySelectorAll('.journal-grid .journal');
  var empty = document.querySelector('.journal-empty');
  if (!tabs.length || !cards.length) return;

  function apply(topic) {
    var shown = 0;
    cards.forEach(function (card) {
      var on = topic === 'all' ||
               card.dataset.cats.split(' ').indexOf(topic) > -1;
      card.hidden = !on;
      if (on) shown++;
    });
    if (empty) empty.hidden = shown > 0;
  }

  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      tabs.forEach(function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      apply(btn.dataset.tab);
    });
  });

  // A category archive preselects its topic (home.php); the Journal itself starts on All.
  var first = document.querySelector('.main-journal .tab.is-active');
  apply(first ? first.dataset.tab : 'all');
})();

/* Tabs: sliding indicator. Reads the clicked tab's own box. */
(function () {
  var BARS = [
    { bar: '.tabs', item: '.tabs__item', cls: 'tabs__indicator' },
    { bar: '.tabbar .container', item: '.tab', cls: 'tabbar__indicator' }
  ];
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  BARS.forEach(function (kind) {
    document.querySelectorAll(kind.bar).forEach(function (bar) {
      var tabs = bar.querySelectorAll(kind.item);
      if (!tabs.length) return;

      var ind = document.createElement('span');
      ind.className = kind.cls;
      ind.setAttribute('aria-hidden', 'true');
      bar.appendChild(ind);
      bar.classList.add('is-sliding');

      function move(tab, animate) {
        if (!tab) return;
        bar.classList.toggle('is-sliding-ready', !!animate);
        ind.style.width = tab.offsetWidth + 'px';
        ind.style.transform = 'translate(' + tab.offsetLeft + 'px, ' +
                              (tab.offsetTop + tab.offsetHeight - 1) + 'px)';
      }
      function active() { return bar.querySelector(kind.item + '.is-active'); }

      move(active(), false);
      bar.addEventListener('click', function (e) {
        var t = e.target.closest(kind.item);
        if (t && bar.contains(t)) move(t, !reduce);
      });
      var ro = window.ResizeObserver
        ? new ResizeObserver(function () { move(active(), false); })
        : null;
      if (ro) ro.observe(bar);
      window.addEventListener('resize', function () { move(active(), false); });
      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { move(active(), false); });
      }
    });
  });
})();
