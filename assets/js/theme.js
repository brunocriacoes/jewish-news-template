document.addEventListener('DOMContentLoaded', () => {
  const body=document.body, toggle=document.querySelector('.theme-toggle'), menu=document.querySelector('.menu-toggle'), mobile=document.querySelector('.mobile-menu');
  if (localStorage.getItem('jnt-theme')==='dark') body.classList.add('dark');
  toggle?.addEventListener('click',()=>{body.classList.toggle('dark');localStorage.setItem('jnt-theme',body.classList.contains('dark')?'dark':'light');});
  menu?.addEventListener('click',()=>{const open=menu.getAttribute('aria-expanded')==='true';menu.setAttribute('aria-expanded',String(!open));mobile.hidden=open;});
  let size=Number(localStorage.getItem('jnt-font-size')||0);
  const setSize=()=>{body.classList.toggle('font-small',size===-1);body.classList.toggle('font-large',size===1);localStorage.setItem('jnt-font-size',String(size));};
  document.querySelector('.font-decrease')?.addEventListener('click',()=>{size=Math.max(-1,size-1);setSize();});
  document.querySelector('.font-increase')?.addEventListener('click',()=>{size=Math.min(1,size+1);setSize();}); setSize();
});