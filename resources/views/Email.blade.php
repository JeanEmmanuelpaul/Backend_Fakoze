<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reçu de don</title>
  <style>
    body { margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrapper { max-width: 580px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header  { background: #009650; padding: 32px 40px; text-align: center; }
    .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 800; }
    .header p  { color: rgba(255,255,255,.8); margin: 6px 0 0; font-size: 14px; }
    .body    { padding: 32px 40px; }
    .amount-box { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px; }
    .amount-box .label { color: #166534; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
    .amount-box .value { color: #009650; font-size: 36px; font-weight: 900; margin: 4px 0 0; }
    .details { border-top: 1px solid #e2e8f0; padding-top: 20px; margin-bottom: 24px; }
    .detail-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .detail-row .key   { color: #64748b; }
    .detail-row .val   { color: #1e293b; font-weight: 600; }
    .message-box { background: #f8fafc; border-left: 3px solid #009650; border-radius: 4px; padding: 12px 16px; font-size: 14px; color: #475569; font-style: italic; margin-bottom: 24px; }
    .footer { background: #f8fafc; padding: 20px 40px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    .footer a { color: #009650; text-decoration: none; }
  </style>
</head>
<body>
  <div class="wrapper">

    <!-- Header -->
    <div class="header">
      <h1>💚 Merci pour votre don !</h1>
      <p>Votre générosité fait une vraie différence en Haïti.</p>
    </div>

    <!-- Body -->
    <div class="body">

      <!-- Montant -->
      <div class="amount-box">
        <div class="label">Montant du don</div>
        <div class="value">{{ number_format($montant, 0, ',', ' ') }} HTG</div>
      </div>

      <!-- Détails -->
      <div class="details">
        <div class="detail-row">
          <span class="key">Numéro de reçu</span>
          <span class="val">#{{ str_pad($donId, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
          <span class="key">Date</span>
          <span class="val">{{ $date }}</span>
        </div>
        <div class="detail-row">
          <span class="key">Type</span>
          <span class="val">{{ $frequence === 'mensuel' ? '🔄 Don mensuel' : '💚 Don unique' }}</span>
        </div>
        <div class="detail-row">
          <span class="key">Paiement</span>
          <span class="val">Stripe (carte bancaire)</span>
        </div>
      </div>

      <!-- Message -->
      @if($message)
      <div class="message-box">
        "{{ $message }}"
      </div>
      @endif

      <p style="color: #475569; font-size: 14px; line-height: 1.7;">
        Grâce à votre contribution, nous pouvons continuer nos missions d'éducation,
        de solidarité et de développement communautaire. Ce reçu vous est fourni
        à titre de confirmation de votre don.
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>© {{ date('Y') }} — Votre organisation • Haïti</p>
      <p>Pour toute question : <a href="mailto:contact@votreorg.ht">contact@votreorg.ht</a></p>
      <p style="margin-top: 8px; font-size: 11px;">
        Ce reçu a été généré automatiquement. Conservez-le pour vos archives.
      </p>
    </div>

  </div>
</body>
</html>
