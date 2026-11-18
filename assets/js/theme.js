const body = document.body;
const themeButton = document.querySelector('.theme-toggle');
const menuButton = document.querySelector('.menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
const heroImage = document.querySelector('.hero-story img');
const headerLogo = document.querySelector('.brand img');
const logoSource = document.querySelector('.brand source');
const fontDecrease = document.querySelector('.font-decrease');
const fontIncrease = document.querySelector('.font-increase');
document.querySelectorAll('a').forEach(link => {
  if (!link.closest('.nav, .mobile-menu, .site-footer, .newsletter, .section-heading, .breaking')) link.setAttribute('href', 'single.html');
});
document.querySelectorAll('.hero-story, .compact-story, .text-story, .image-story, .photo-card, .opinion article').forEach(card => {
  card.tabIndex = 0;
  card.setAttribute('role', 'link');
  card.addEventListener('click', event => { if (!event.target.closest('a, button')) window.location.href = 'single.html'; });
  card.addEventListener('keydown', event => { if (event.key === 'Enter') window.location.href = 'single.html'; });
});

const publicationDates = {
  'Hoje, 08:40': '11 de agosto de 2026',
  'Hoje, 07:15': '11 de agosto de 2026',
  'Ontem, 18:10': '10 de agosto de 2026',
  '2 hours ago': 'August 11, 2026',
  '3 hours ago': 'August 11, 2026',
  '5 hours ago': 'August 11, 2026'
};
document.querySelectorAll('.byline').forEach(item => {
  Object.entries(publicationDates).forEach(([time, date]) => {
    item.textContent = item.textContent.replace(time, date);
  });
});

document.querySelector('.hero-side').insertAdjacentHTML('beforeend', `
  <article class="compact-story">
    <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=700&q=80" alt="People talking at a community gathering">
    <div><span class="kicker">FEATURES</span><h2>Stories connecting generations of the Portuguese community</h2><span class="byline">August 9, 2026</span></div>
  </article>
  <article class="compact-story">
    <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=700&q=80" alt="Livros em uma biblioteca">
    <div><span class="kicker">MUSEUMS</span><h2>A new perspective on Portuguese Jewish heritage</h2><span class="byline">August 8, 2026</span></div>
  </article>
`);

document.querySelector('.opinion .read-more').insertAdjacentHTML('beforebegin', `
  <article><span class="kicker">COMMUNITIES</span><h3>Memory, encounter and future in every community</h3><p>By the PJN team</p></article>
`);

const opinionSummaries = [
  'A reflection on listening and collective building.',
  'Uma leitura breve sobre preservar o que importa.',
  'Ideias para compreender melhor o presente.',
  'Stories connecting the past and the future.'
];
document.querySelectorAll('.opinion article').forEach((article, index) => {
  article.insertAdjacentHTML('beforeend', `<p class="opinion-summary">${opinionSummaries[index]}</p><a class="opinion-link" href="#">Read article →</a>`);
});

document.querySelector('.culture-grid').insertAdjacentHTML('beforeend', `
  <article class="photo-card"><img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=500&q=80" alt="Open book"><span class="kicker">HISTORY</span><h3>New perspectives on Portuguese heritage</h3></article>
`);

document.querySelectorAll('.photo-card h3').forEach(title => {
  title.innerHTML = `<a class="article-title-link" href="#">${title.innerHTML}</a>`;
});

heroImage.addEventListener('error', () => {
  heroImage.src = 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1300&q=85';
  heroImage.alt = 'Paisagem portuguesa ao entardecer';
}, { once: true });

const savedTheme = localStorage.getItem('tpjn-theme');
if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) body.classList.add('dark');

function syncLogo() {
  logoSource.media = 'not all';
  headerLogo.src = 'logo-light.png';
}
syncLogo();

let fontSize = Number(localStorage.getItem('tpjn-font-size') || 0);
function setFontSize(size) {
  fontSize = Math.max(-1, Math.min(1, size));
  body.classList.remove('font-small', 'font-large');
  if (fontSize === -1) body.classList.add('font-small');
  if (fontSize === 1) body.classList.add('font-large');
  localStorage.setItem('tpjn-font-size', String(fontSize));
}
setFontSize(fontSize);
fontDecrease.addEventListener('click', () => setFontSize(fontSize - 1));
fontIncrease.addEventListener('click', () => setFontSize(fontSize + 1));

function setTheme() {
  const isDark = body.classList.toggle('dark');
  localStorage.setItem('tpjn-theme', isDark ? 'dark' : 'light');
  themeButton.setAttribute('aria-label', isDark ? 'Ativar modo claro' : 'Ativar modo escuro');
  syncLogo();
}
themeButton.addEventListener('click', setTheme);

menuButton.addEventListener('click', () => {
  const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
  menuButton.setAttribute('aria-expanded', String(!isOpen));
  mobileMenu.hidden = isOpen;
});
mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => { mobileMenu.hidden = true; menuButton.setAttribute('aria-expanded', 'false'); }));

document.querySelector('.newsletter form').addEventListener('submit', event => {
  event.preventDefault();
  const button = event.currentTarget.querySelector('button');
  button.textContent = 'Obrigado!';
  button.disabled = true;
});

