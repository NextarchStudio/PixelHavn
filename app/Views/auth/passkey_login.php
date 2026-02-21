<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body">
<h3>Passkey Login</h3>
<div class="mb-3"><label class="form-label">Username</label><input id="username" class="form-control"></div>
<button id="start" class="btn btn-primary">Login with Passkey</button>
</div></div></div></div>
<script>
const b64ToBuf = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
const bufToB64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf)));

document.getElementById('start').addEventListener('click', async () => {
  const username = document.getElementById('username').value;
  const optRes = await fetch('/auth/passkey/options', {method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams({username})});
  const options = await optRes.json();
  if (!optRes.ok) { alert(options.error || 'Failed'); return; }
  options.challenge = b64ToBuf(options.challenge);
  options.allowCredentials = (options.allowCredentials || []).map(c => ({...c, id: b64ToBuf(c.id)}));
  const assertion = await navigator.credentials.get({publicKey: options});
  const payload = new URLSearchParams({
    credentialId: bufToB64(assertion.rawId),
    clientDataJSON: bufToB64(assertion.response.clientDataJSON),
    authenticatorData: bufToB64(assertion.response.authenticatorData),
    signature: bufToB64(assertion.response.signature)
  });
  const verifyRes = await fetch('/auth/passkey/verify', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload});
  const data = await verifyRes.json();
  if (!verifyRes.ok) { alert(data.error || 'Login failed'); return; }
  window.location.href = data.redirect;
});
</script>
<?= $this->endSection() ?>
