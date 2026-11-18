(() => {
  const body = document.body;
  const themeButton = document.querySelector('.theme-toggle');
  const menuButton = document.querySelector('.menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  const fontDecrease = document.querySelector('.font-decrease');
  const fontIncrease = document.querySelector('.font-increase');

  const applyTheme = (dark) => {
    body.classList.toggle('dark', dark);
    if (themeButton) themeButton.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
  };

  const savedTheme = localStorage.getItem('tpjn-theme');
  applyTheme(savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches));

  if (themeButton) {
    themeButton.addEventListener('click', () => {
      const dark = !body.classList.contains('dark');
      localStorage.setItem('tpjn-theme', dark ? 'dark' : 'light');
      applyTheme(dark);
    });
  }

  let fontSize = Number(localStorage.getItem('tpjn-font-size') || 0);
  const setFontSize = (size) => {
    fontSize = Math.max(-1, Math.min(1, size));
    body.classList.toggle('font-small', fontSize === -1);
    body.classList.toggle('font-large', fontSize === 1);
    localStorage.setItem('tpjn-font-size', String(fontSize));
  };
  setFontSize(fontSize);

  if (fontDecrease) fontDecrease.addEventListener('click', () => setFontSize(fontSize - 1));
  if (fontIncrease) fontIncrease.addEventListener('click', () => setFontSize(fontSize + 1));

  if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', () => {
      const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
      menuButton.setAttribute('aria-expanded', String(!isOpen));
      mobileMenu.hidden = !isOpen;
    });

    mobileMenu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
      mobileMenu.hidden = true;
      menuButton.setAttribute('aria-expanded', 'false');
    }));
  }
})();

