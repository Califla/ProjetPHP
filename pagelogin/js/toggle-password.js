function togglePassword(inputId, btnEl) {
  var inp = document.getElementById(inputId);
  if (inp.type === 'password') {
    inp.type = 'text';
    btnEl.classList.add('visible');
  } else {
    inp.type = 'password';
    btnEl.classList.remove('visible');
  }
}
