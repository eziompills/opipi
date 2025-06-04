// Global JS for ôplani
document.addEventListener('DOMContentLoaded',()=>{
  const yearSpan=document.querySelector('#year');
  if(yearSpan) yearSpan.textContent=new Date().getFullYear();

  // Auto dismiss flash messages after 5s
  document.querySelectorAll('.alert').forEach(el=>{
    const bsAlert=bootstrap.Alert.getOrCreateInstance(el);
    setTimeout(()=>bsAlert.close(),5000);
  });
});


const toggle=document.getElementById('themeToggle');
if(toggle){
  const applyIcon=()=>{
    toggle.innerHTML=document.body.classList.contains('dark')
      ?'<i class="bi-sun"></i>'
      :'<i class="bi-moon"></i>';
  };
  const prefersDark=window.matchMedia('(prefers-color-scheme: dark)').matches;
  if(localStorage.getItem('theme')==='dark' || (prefersDark && !localStorage.getItem('theme'))){
    document.documentElement.classList.add('dark');
    document.body.classList.add('dark');
  }
  applyIcon();
  toggle.addEventListener('click',()=>{
    document.documentElement.classList.toggle('dark');
    document.body.classList.toggle('dark');
    localStorage.setItem('theme',document.body.classList.contains('dark')?'dark':'light');
    applyIcon();
  });
}

// Bouton retour haut de page
const backTop=document.getElementById('backToTop');
if(backTop){
  window.addEventListener('scroll',()=>{
    backTop.style.display=window.scrollY>200?'block':'none';
  });
}
