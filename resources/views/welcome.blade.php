<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NjangiMarket — La Marketplace Locale du Cameroun</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { font-family: 'Inter', sans-serif; }
    .navbar-nj { background: rgba(255,255,255,.96); backdrop-filter: blur(8px); border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 1000; }
    .hero { background: linear-gradient(135deg, #0f172a 0%, #064e3b 55%, #16a34a 100%); padding: 100px 0 80px; overflow: hidden; position: relative; }
    .hero::after { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); pointer-events:none; }
    .hero-badge { display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); color:#fff; padding:.375rem 1rem; border-radius:99px; font-size:.8rem; font-weight:600; margin-bottom:1.5rem; }
    .stat-pill { background:#fff; border-radius:1rem; padding:1.25rem 1.5rem; text-align:center; box-shadow:0 4px 24px rgba(0,0,0,.08); }
    .stat-pill-val { font-size:1.6rem; font-weight:800; color:#0f172a; }
    .stat-pill-label { font-size:.78rem; color:#64748b; margin-top:.25rem; }
    .feature-card { background:#fff; border-radius:1.25rem; padding:2rem; border:1px solid #f1f5f9; transition:all .2s; }
    .feature-card:hover { box-shadow:0 8px 32px rgba(0,0,0,.08); transform:translateY(-3px); }
    .feature-icon { width:52px; height:52px; border-radius:.875rem; display:flex; align-items:center; justify-content:center; font-size:1.375rem; margin-bottom:1.25rem; }
    .step-num { width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg,#16a34a,#064e3b); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.25rem; font-weight:800; margin:0 auto 1rem; }
    .cta-section { background:linear-gradient(135deg,#064e3b,#16a34a); }
    .role-card { background:#fff; border-radius:1.25rem; padding:2rem 1.5rem; text-align:center; border:2px solid #f1f5f9; transition:all .2s; cursor:pointer; }
    .role-card:hover { border-color:#16a34a; box-shadow:0 8px 24px rgba(22,163,74,.15); }
    .role-card-icon { font-size:2.5rem; margin-bottom:1rem; }
  </style>
</head>
<body style="background:#f8fafc;">

{{-- Navbar --}}
<nav class="navbar-nj">
  <div class="container d-flex align-items-center py-3">
    <a href="/" class="text-decoration-none d-flex align-items-center gap-2">
      <span style="font-size:1.5rem;">🛒</span>
      <span style="font-size:1.25rem; font-weight:800; color:#0f172a;">Njangi<span style="color:#16a34a;">Market</span></span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-2">
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm fw-600">Mon espace</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm fw-600">Connexion</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm fw-600">S'inscrire</a>
      @endauth
    </div>
  </div>
</nav>

{{-- Hero --}}
<section class="hero">
  <div class="container text-center text-white position-relative" style="z-index:1;">
    <div class="hero-badge">
      <i class="bi bi-lightning-charge-fill" style="color:#fbbf24;"></i>
      Nouvelle génération de commerce local au Cameroun
    </div>
    <h1 style="font-size:clamp(2.25rem,5vw,3.75rem); font-weight:800; line-height:1.15; margin-bottom:1.25rem;">
      La marketplace qui <br>
      <span style="color:#4ade80;">digitalise</span> vos marchés locaux
    </h1>
    <p style="font-size:1.125rem; opacity:.8; max-width:600px; margin:0 auto 2.5rem;">
      Vendez, achetez et livrez dans votre quartier avec géolocalisation, tontines digitales et paiement Mobile Money.
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap mb-5">
      <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-700 px-5 py-3" style="border-radius:.875rem;">
        <i class="bi bi-shop me-2"></i>Commencer gratuitement
      </a>
      <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3" style="border-radius:.875rem;">
        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
      </a>
    </div>

    {{-- Stats rapides --}}
    <div class="row g-3 justify-content-center" style="max-width:700px; margin:0 auto;">
      @foreach([
        ['70%','Échanges informels au Cameroun'],
        ['15%','Économie via tontines'],
        ['23.7%','Croissance e-commerce/an'],
      ] as [$val,$label])
      <div class="col-4">
        <div style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); border-radius:1rem; padding:1rem;">
          <div style="font-size:1.5rem; font-weight:800;">{{ $val }}</div>
          <div style="font-size:.7rem; opacity:.7; margin-top:.25rem;">{{ $label }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Rôles --}}
<section class="py-5" style="background:#fff;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="font-size:1.875rem; font-weight:800; color:#0f172a;">Choisissez votre rôle</h2>
      <p style="color:#64748b;">Une plateforme, trois façons de participer</p>
    </div>
    <div class="row g-4 justify-content-center">
      @foreach([
        ['🛍️','Client','Acheteur','Découvrez des produits frais de votre marché local, payez avec Mobile Money et suivez vos livraisons.','#16a34a'],
        ['🏪','Vendeur','Commerçant','Créez votre boutique en ligne, géolocalisez vos produits et atteignez des clients dans tout votre quartier.','#2563eb'],
        ['🏍️','Livreur','Livreur','Livrez des commandes près de chez vous, gagnez des commissions et des bonus de satisfaction.','#d97706'],
      ] as [$emoji,$titre,$role,$desc,$color])
      <div class="col-md-4">
        <div class="role-card" onclick="window.location='{{ route('register') }}'">
          <div class="role-card-icon">{{ $emoji }}</div>
          <h5 style="font-weight:700; color:#0f172a; margin-bottom:.5rem;">{{ $titre }}</h5>
          <div style="display:inline-block; background:{{ $color }}18; color:{{ $color }}; font-size:.75rem; font-weight:600; padding:.25rem .75rem; border-radius:99px; margin-bottom:1rem;">{{ $role }}</div>
          <p style="color:#64748b; font-size:.9rem; line-height:1.6; margin:0;">{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Fonctionnalités --}}
<section class="py-5" style="background:#f8fafc;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="font-size:1.875rem; font-weight:800; color:#0f172a;">Pourquoi NjangiMarket ?</h2>
      <p style="color:#64748b;">Conçu pour les réalités africaines</p>
    </div>
    <div class="row g-3">
      @foreach([
        ['bi-geo-alt-fill','#16a34a','#f0fdf4','Géolocalisation','Trouvez les vendeurs et suivez les livreurs près de chez vous avec OpenStreetMap.'],
        ['bi-people-fill','#7c3aed','#f5f3ff','Tontines Njangi','Achetez en groupe, cotisez ensemble et économisez jusqu\'à 15% sur tous vos achats.'],
        ['bi-phone-fill','#f59e0b','#fffbeb','Mobile Money','Orange Money & MTN MoMo via l\'API Campay. Simple, rapide, sécurisé.'],
        ['bi-truck','#2563eb','#eff6ff','Livraison rapide','Suivi temps réel de vos livreurs avec bonus ponctualité (+10%) et satisfaction (+5%).'],
        ['bi-shop','#ef4444','#fef2f2','Marchés informels','Même les commerçants de rue peuvent créer leur boutique et toucher plus de clients.'],
        ['bi-shield-check','#0f766e','#f0fdfa','Paiement sécurisé','Transactions protégées, remboursement garanti en cas de problème avec votre commande.'],
      ] as [$icon,$color,$bg,$title,$desc])
      <div class="col-md-4">
        <div class="feature-card">
          <div class="feature-icon" style="background:{{ $bg }};color:{{ $color }};"><i class="bi {{ $icon }}"></i></div>
          <h6 style="font-weight:700; color:#0f172a; margin-bottom:.5rem;">{{ $title }}</h6>
          <p style="color:#64748b; font-size:.875rem; margin:0; line-height:1.6;">{{ $desc }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Comment ça marche --}}
<section class="py-5" style="background:#fff;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="font-size:1.875rem; font-weight:800; color:#0f172a;">Comment ça marche ?</h2>
    </div>
    <div class="row g-4 text-center">
      @foreach([
        ['1','bi-person-plus','Créez votre compte','Inscrivez-vous gratuitement et choisissez votre rôle en moins de 2 minutes.'],
        ['2','bi-search','Explorez','Trouvez des produits près de chez vous grâce à la géolocalisation.'],
        ['3','bi-phone','Payez','Orange Money ou MTN MoMo — ou rejoignez une tontine pour économiser.'],
        ['4','bi-truck','Recevez','Suivi en temps réel et livraison rapide dans votre quartier.'],
      ] as [$num,$icon,$title,$desc])
      <div class="col-6 col-md-3">
        <div class="step-num">{{ $num }}</div>
        <i class="bi {{ $icon }}" style="font-size:1.5rem;color:#16a34a;margin-bottom:.75rem;display:block;"></i>
        <h6 style="font-weight:700; color:#0f172a;">{{ $title }}</h6>
        <p style="color:#64748b; font-size:.85rem;">{{ $desc }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- CTA final --}}
<section class="cta-section py-5 text-white text-center">
  <div class="container">
    <h2 style="font-size:2rem; font-weight:800; margin-bottom:.75rem;">Prêt à rejoindre NjangiMarket ?</h2>
    <p style="opacity:.8; margin-bottom:2rem; font-size:1.05rem;">
      Des milliers de Camerounais commercent déjà en ligne de manière inclusive
    </p>
    <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-700 px-5 py-3" style="border-radius:.875rem;">
      <i class="bi bi-arrow-right-circle me-2"></i>S'inscrire gratuitement
    </a>
  </div>
</section>

<footer style="background:#0f172a; color:#94a3b8;" class="py-4 text-center">
  <p class="mb-0 small">© {{ date('Y') }} NjangiMarket · Plateforme de commerce local au Cameroun</p>
</footer>

</body>
</html>
