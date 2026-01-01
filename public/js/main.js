function showAlert(msg, type='success'){
  const d = document.createElement('div');
  d.className = 'alert ' + type;
  d.innerText = msg;
  document.body.prepend(d);
  setTimeout(()=> d.remove(), 4000);
}

document.addEventListener('submit', function(e){
  const f = e.target;
  if (f.matches('form')) {
    const req = f.querySelectorAll('[required]');
    for (let i=0;i<req.length;i++){
      if (!req[i].value.trim()) {
        e.preventDefault();
        showAlert('Isi semua field yang wajib.', 'error');
        return;
      }
    }
  }
});
