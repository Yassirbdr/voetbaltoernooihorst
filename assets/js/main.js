// Alle scripts van Voetbaldagen Horst
document.documentElement.classList.add('js');

// Header: transparant bovenaan, solide na scrollen + mobiel menu
(function () {
  const header = document.getElementById('header');
  const knop = document.getElementById('menuKnop');
  const nav = document.getElementById('nav');
  if (!header) return;
  const check = () => header.classList.toggle('solide', window.scrollY > 40 || header.dataset.solide === '1');
  check();
  window.addEventListener('scroll', check, { passive: true });
  if (knop) knop.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    knop.setAttribute('aria-expanded', open);
    if (open) header.classList.add('solide');
  });
})();

// Onderdelen verschijnen bij scrollen
(function () {
  const els = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('zichtbaar')); return; }
  const io = new IntersectionObserver(items => items.forEach(i => {
    if (i.isIntersecting) { i.target.classList.add('zichtbaar'); io.unobserve(i.target); }
  }), { threshold: 0.12 });
  els.forEach(e => io.observe(e));
})();

// Bevestigingsvraag bij verwijderen
document.querySelectorAll('form[data-bevestig]').forEach(f => {
  f.addEventListener('submit', e => { if (!confirm(f.dataset.bevestig)) e.preventDefault(); });
});

// Confetti op de bevestigingspagina
(function () {
  if (!document.querySelector('[data-confetti]')) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const kleuren = ['#FF7A1A', '#FFC93C', '#22C55E', '#ffffff', '#1F4FD8'];
  const wrap = document.createElement('div');
  wrap.className = 'confetti';
  for (let i = 0; i < 60; i++) {
    const s = document.createElement('i');
    s.style.left = Math.random() * 100 + 'vw';
    s.style.background = kleuren[i % kleuren.length];
    s.style.animationDelay = Math.random() * 1.2 + 's';
    s.style.animationDuration = 2.5 + Math.random() * 1.5 + 's';
    wrap.appendChild(s);
  }
  document.body.appendChild(wrap);
  setTimeout(() => wrap.remove(), 5500);
})();

// Vrijwilligersformulier: minstens 1 dag kiezen
(function () {
  const f = document.getElementById('vrijwForm');
  if (!f) return;
  f.addEventListener('submit', e => {
    const ok = f.querySelector('input[name="dagen[]"]:checked');
    const m = document.getElementById('dagenFout');
    m.hidden = !!ok;
    if (!ok) { e.preventDefault(); m.scrollIntoView({ block: 'center', behavior: 'smooth' }); }
  });
})();

// Inschrijfformulier met stappen, spelerskaarten en live ticket
(function () {
  const f = document.getElementById('inschrijfForm');
  if (!f) return;
  const MIN = +f.dataset.min, MAX = +f.dataset.max, LMIN = +f.dataset.lmin, LMAX = +f.dataset.lmax;
  const stappen = [...f.querySelectorAll('.stap')];
  const dots = [...document.querySelectorAll('.stepper li')];
  const lijst = document.getElementById('spelerLijst');
  const teller = document.getElementById('spelerTeller');
  const sel = document.getElementById('aantalSel');
  const btnPlus = document.getElementById('spelerPlus');
  const OUD = window.OUD || {};
  let huidig = 0;

  const el = (tag, cls, txt) => {
    const x = document.createElement(tag);
    if (cls) x.className = cls;
    if (txt !== undefined) x.textContent = txt;
    return x;
  };
  const idx = type => stappen.findIndex(s => s.dataset.type === type);

  // ----- foutmeldingen bij velden -----
  function zetFout(input, tekst) {
    const v = input.closest('.veld');
    v.classList.add('fout');
    let m = v.querySelector('.fout-tekst');
    if (!m) { m = el('small', 'fout-tekst'); v.appendChild(m); }
    m.textContent = tekst;
  }
  function wis(input) {
    const v = input.closest('.veld');
    v.classList.remove('fout');
    const m = v.querySelector('.fout-tekst');
    if (m) m.remove();
  }

  // ----- spelerskaarten -----
  function voegSpeler(naam, leeftijd) {
    const k = el('div', 'speler-kaart');
    k.innerHTML =
      '<div class="shirt">00</div>' +
      '<div class="speler-velden">' +
        '<div class="veld"><label>Naam speler</label><input type="text" name="speler_naam[]" maxlength="60" placeholder="Volledige naam" required></div>' +
        '<div class="veld"><label>Leeftijd</label><input type="number" name="speler_leeftijd[]" min="' + LMIN + '" max="' + LMAX + '" placeholder="' + LMIN + '-' + LMAX + '" required></div>' +
      '</div>' +
      '<button type="button" class="speler-weg" aria-label="Speler verwijderen">✕</button>';
    const [n, l] = k.querySelectorAll('input');
    n.value = naam || '';
    l.value = leeftijd || '';
    k.querySelector('.speler-weg').addEventListener('click', () => {
      if (lijst.children.length > MIN) { k.remove(); nummers(); }
    });
    lijst.appendChild(k);
  }
  function nummers() {
    const n = lijst.children.length;
    [...lijst.children].forEach((k, i) => { k.querySelector('.shirt').textContent = String(i + 1).padStart(2, '0'); });
    teller.textContent = n + (n === 1 ? ' speler' : ' spelers') + ' toegevoegd';
    sel.value = n;
    btnPlus.disabled = n >= MAX;
    lijst.querySelectorAll('.speler-weg').forEach(b => { b.disabled = n <= MIN; });
    samenvatting();
  }
  btnPlus.addEventListener('click', () => { if (lijst.children.length < MAX) { voegSpeler(); nummers(); } });
  sel.addEventListener('change', () => {
    const w = +sel.value;
    while (lijst.children.length < w) voegSpeler();
    while (lijst.children.length > w) lijst.lastElementChild.remove();
    nummers();
  });

  // ----- live ticket -----
  function samenvatting() {
    const t = f.teamnaam.value.trim();
    document.getElementById('sumTeam').textContent = t || '–';
    const k = f.querySelector('input[name="keuze"]:checked');
    const sk = document.getElementById('sumKeuze');
    if (sk) sk.textContent = k ? k.value : '–';
    document.getElementById('sumSpelers').textContent = lijst.children.length;
    const chip = document.getElementById('gekozenChip');
    if (chip) { chip.hidden = !k; chip.textContent = k ? '✓ Gekozen: ' + k.value : ''; }
  }
  f.addEventListener('input', samenvatting);
  f.addEventListener('change', samenvatting);

  // ----- zoeken in clubs / landen -----
  const zoek = document.getElementById('zoek');
  if (zoek) zoek.addEventListener('input', () => {
    const q = zoek.value.trim().toLowerCase();
    f.querySelectorAll('.keuze-kaart').forEach(k => { k.hidden = q !== '' && !k.dataset.naam.includes(q); });
  });

  // ----- stappen -----
  function toon(n, scroll) {
    huidig = n;
    stappen.forEach((s, i) => { s.hidden = i !== n; });
    dots.forEach((d, i) => { d.classList.toggle('actief', i === n); d.classList.toggle('klaar', i < n); });
    if (stappen[n].dataset.type === 'review') vulReview();
    if (scroll) document.querySelector('.stepper').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  function valideer(n) {
    const s = stappen[n], type = s.dataset.type;
    let ok = true;
    if (type === 'team') {
      s.querySelectorAll('input[required]').forEach(i => {
        wis(i);
        if (!i.value.trim() || !i.checkValidity()) {
          ok = false;
          zetFout(i, i.type === 'email' ? 'Vul een geldig e-mailadres in.' : i.name === 'telefoon' ? 'Vul een geldig telefoonnummer in (minimaal 8 cijfers).' : 'Dit veld is verplicht.');
        }
      });
    }
    if (type === 'keuze') {
      const gekozen = f.querySelector('input[name="keuze"]:checked');
      document.getElementById('keuzeFout').hidden = !!gekozen;
      if (!gekozen) ok = false;
    }
    if (type === 'spelers') {
      lijst.querySelectorAll('input').forEach(i => {
        wis(i);
        if (!i.value.trim() || !i.checkValidity()) {
          ok = false;
          zetFout(i, i.type === 'number' ? 'Leeftijd ' + LMIN + ' t/m ' + LMAX + '.' : 'Vul een naam in.');
        }
      });
      const n = lijst.children.length;
      document.getElementById('spelersFout').hidden = n >= MIN && n <= MAX;
      if (n < MIN || n > MAX) ok = false;
    }
    if (type === 'review') {
      const c = document.getElementById('akkoord');
      document.getElementById('akkoordFout').hidden = c.checked;
      if (!c.checked) ok = false;
    }
    if (!ok) {
      const eerste = s.querySelector('.veld.fout input, .fout-melding:not([hidden])');
      if (eerste) eerste.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }
    return ok;
  }
  f.querySelectorAll('[data-volgende]').forEach(b => b.addEventListener('click', () => { if (valideer(huidig)) toon(huidig + 1, true); }));
  f.querySelectorAll('[data-vorige]').forEach(b => b.addEventListener('click', () => toon(huidig - 1, true)));

  // Enter mag het formulier niet per ongeluk versturen
  f.addEventListener('keydown', e => {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'checkbox' && e.target.type !== 'radio') {
      e.preventDefault();
      const nb = stappen[huidig].querySelector('[data-volgende]');
      if (nb) nb.click();
    }
  });

  // ----- controleren -----
  function blok(titel, stapIndex, regels) {
    const b = el('div', 'review-blok');
    const kop = el('div', 'review-kop');
    kop.append(el('h3', null, titel));
    const w = el('button', 'btn btn-grijs btn-klein', 'Wijzig');
    w.type = 'button';
    w.addEventListener('click', () => toon(stapIndex, true));
    kop.append(w);
    b.append(kop);
    regels.forEach(([l, v]) => { const r = el('div', 'rij'); r.append(el('span', null, l), el('strong', null, v)); b.append(r); });
    return b;
  }
  function vulReview() {
    const r = document.getElementById('reviewInhoud');
    r.textContent = '';
    r.append(blok('Teamgegevens', idx('team'), [
      ['Teamnaam', f.teamnaam.value], ['Teamcaptain', f.captain.value], ['E-mail', f.email.value], ['Telefoon', f.telefoon.value],
    ]));
    const k = f.querySelector('input[name="keuze"]:checked');
    if (idx('keuze') >= 0) r.append(blok(f.dataset.keuzelabel, idx('keuze'), [[f.dataset.keuzelabel, k ? k.value : '–']]));
    const sp = [];
    lijst.querySelectorAll('.speler-kaart').forEach((c, i) => {
      const [n, l] = c.querySelectorAll('input');
      sp.push([(i + 1) + '. ' + n.value, l.value + ' jaar']);
    });
    r.append(blok('Spelers (' + sp.length + ')', idx('spelers'), sp));
  }

  // ----- versturen -----
  f.addEventListener('submit', e => {
    for (let i = 0; i < stappen.length; i++) {
      if (!valideer(i)) { e.preventDefault(); toon(i, true); return; }
    }
    const b = f.querySelector('[type="submit"]');
    b.disabled = true;
    b.textContent = 'Bezig met verzenden...';
  });

  // ----- opstarten (ook na een foutmelding van de server) -----
  const oud = OUD.spelers && OUD.spelers.length ? OUD.spelers : null;
  const aantal = oud ? oud.length : MIN;
  for (let i = 0; i < aantal; i++) voegSpeler(oud ? oud[i][0] : '', oud ? oud[i][1] : '');
  nummers();
  toon(0, false);
})();