<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Passkey Management</h3>
    <button id="registerPasskey" class="btn btn-success">Enable New Passkey</button>
</div>
<table class="table table-bordered bg-white">
    <thead><tr><th>ID</th><th>Created</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($credentials as $cred): ?>
        <tr>
            <td><?= esc(substr($cred['credential_id'], 0, 20)) ?>...</td>
            <td><?= esc($cred['created_at']) ?></td>
            <td>
                <form method="post" action="/passkeys/<?= (int) $cred['id'] ?>/delete">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
const b64ToBuf = (b64) => Uint8Array.from(atob(b64), c => c.charCodeAt(0));
const bufToB64 = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf)));

document.getElementById('registerPasskey').addEventListener('click', async () => {
  const optRes = await fetch('/passkeys/options', {method:'POST'});
  const options = await optRes.json();
  options.challenge = b64ToBuf(options.challenge);
  options.user.id = b64ToBuf(options.user.id);
  options.excludeCredentials = (options.excludeCredentials || []).map(c => ({...c, id: b64ToBuf(c.id)}));

  const cred = await navigator.credentials.create({publicKey: options});
  const payload = new URLSearchParams({
    clientDataJSON: bufToB64(cred.response.clientDataJSON),
    attestationObject: bufToB64(cred.response.attestationObject),
    transports: JSON.stringify(cred.response.getTransports ? cred.response.getTransports() : [])
  });
  const save = await fetch('/passkeys/register', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload});
  if (!save.ok) { alert('Passkey registration failed'); return; }
  window.location.reload();
});
</script>
<?= $this->endSection() ?>
