// Global JS for ôplani
document.addEventListener('DOMContentLoaded',()=>{
  const yearSpan=document.querySelector('#year');
  if(yearSpan) yearSpan.textContent=new Date().getFullYear();
});


const toggle=document.getElementById('themeToggle');
if(toggle){
  const applyIcon=()=>{
    toggle.innerHTML=document.body.classList.contains('dark')
      ?'<i class="bi-sun"></i>'
      :'<i class="bi-moon"></i>';
  };
  if(localStorage.getItem('theme')==='dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('theme'))){
      document.body.classList.add('dark');
  }
  applyIcon();
  toggle.addEventListener('click',()=>{
    document.body.classList.toggle('dark');
    localStorage.setItem('theme',document.body.classList.contains('dark')?'dark':'light');
    applyIcon();
  });
}
