<?php
require_once '../inc/header.php';
require_role(['admin']);
?>
<h1 class="mb-4">Tableau de bord Admin</h1>

<div class="row" id="statCards"></div>

<div class="row">
  <div class="col-12">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <h5 class="card-title">Revenu mensuel global</h5>
        <canvas id="chartRevenueAdmin" height="90"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('api/stats.php')
  .then(r=>r.json())
  .then(data=>{
     const statCards=document.getElementById('statCards');
     const cards=[
       {key:'users',label:'Utilisateurs'},
       {key:'salons',label:'Salons'},
       {key:'bookings',label:'Réservations'}
     ];
     cards.forEach(c=>{
       const div=document.createElement('div');
       div.className='col-md-4';
       div.innerHTML=`<div class="card shadow-sm border-0 mb-3">
         <div class="card-body">
           <h2 class="display-5">${data.totals[c.key]}</h2>
           <p class="text-muted mb-0">${c.label}</p>
         </div>
       </div>`;
       statCards.appendChild(div);
     });

     // Revenue chart
     const labels=Object.keys(data.revenue);
     const values=labels.map(l=>data.revenue[l]);
     new Chart(document.getElementById('chartRevenueAdmin').getContext('2d'),{
       type:'line',
       data:{labels,datasets:[{label:'€',data:values,fill:false}]},
       options:{responsive:true,maintainAspectRatio:false}
     });
  })
  .catch(console.error);
</script>
<?php require_once '../inc/footer.php'; ?>
