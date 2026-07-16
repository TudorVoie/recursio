<?php
session_start();

$allowed = ['ro', 'en', 'hu'];

// permite deep-link cu ?lang= (limba se moștenește din sesiune / index.php)
if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ghid.php');
    exit;
}

$lang = $_SESSION['lang'] ?? 'ro';

if (!is_file(__DIR__ . "/{$lang}.php")) {
    $lang = 'ro';
}

$t = require __DIR__ . "/{$lang}.php";

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['title']) ?></title>
    <link rel="icon" type="image/png" href="Recursio-logo-Spiral.png">
    <link rel="stylesheet" href="ghid.css">
</head>

<body>

    <!-- Logo sus (titlul Recursio) + dropdown limba in dreapta -->
    <div class="brand">
        <img src="Recursio-logo-dark-bg-2-2.png" alt="Recursio">
        <div class="brand-lang" tabindex="0">
            <button type="button" class="lang-toggle" aria-label="Language">
                <img src="<?= $lang ?>.png" alt="<?= $lang ?>">
                <span class="chev">&#9660;</span>
            </button>
            <div class="lang-menu">
                <?php foreach (['ro', 'en', 'hu'] as $code): ?>
    <a class="lang-opt <?= $lang === $code ? 'active' : '' ?>"
       href="set_lang.php?lang=<?= urlencode($code) ?>&redirect=ghid.php">
        <img src="<?= $code ?>.png" alt="<?= strtoupper($code) ?>">
    </a>
<?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="wrap">

        <!-- HERO -->
        <header class="hero">
            <h1><?= htmlspecialchars($t['hero_h']) ?></h1>
            <p><?= htmlspecialchars($t['hero_p']) ?></p>
        </header>

        <!-- CE ESTE RECURSIVITATEA -->
        <section class="section">
            <span class="eyebrow"><?= htmlspecialchars($t['what_e']) ?></span>
            <h2 class="h2"><?= htmlspecialchars($t['what_h']) ?></h2>
            <p class="lead"><?= htmlspecialchars($t['what_p']) ?></p>
            <div class="grid grid-2">
                <div class="card">
                    <div class="phone_padding">
                        <div class="card-head">
                            <div class="badge blue">1</div>
                            <div class="card-title"><?= htmlspecialchars($t['base_h']) ?></div>
                        </div>
                        <p><?= htmlspecialchars($t['base_p']) ?></p>
                        <div class="code"><span class="k">if</span> (n == <span class="n">0</span>) <span
                                class="k">return</span> <span class="n">1</span>; <span
                                class="c"><?= htmlspecialchars($t['cmt_base']) ?></span></div>
                    </div>
                </div>
                <div class="card">
                    <div class="phone_padding">
                        <div class="card-head">
                            <div class="badge purple">2</div>
                            <div class="card-title"><?= htmlspecialchars($t['rec_h']) ?></div>
                        </div>
                        <p><?= htmlspecialchars($t['rec_p']) ?></p>
                        <div class="code"><span class="k">return</span> n * <span class="f">factorial</span>(n - <span
                                class="n">1</span>); <span class="c"><?= htmlspecialchars($t['cmt_rec']) ?></span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CE REPREZINTĂ ELEMENTELE DIN VIZUALIZATOR -->
        <section class="section">
            <span class="eyebrow"><?= htmlspecialchars($t['viz_e']) ?></span>
            <h2 class="h2"><?= htmlspecialchars($t['viz_h']) ?></h2>
            <p class="lead"><?= htmlspecialchars($t['viz_p']) ?></p>

            <div class="viz">
                <!-- Salveaza screenshot-ul arborelui ca tree.png in acest folder.
                     Daca fisierul lipseste, se afiseaza automat placeholder-ul de mai jos. -->
                <figure class="figure">
                    <a class="figure-link" href="tree.png" onclick="openLightbox(event, 'tree.png')">
                        <img src="tree.png" alt="<?= htmlspecialchars($t['fig_cap']) ?>"
                            onerror="this.closest('.figure-link').style.display='none'; this.closest('figure').querySelector('.ph').style.display='flex';">
                    </a>
                    <div class="ph" style="display:none;">
                        <div class="ph-ico">&#128202;</div>
                        <div><?= htmlspecialchars($t['ph1']) ?></div>
                        <div style="font-size:0.8rem;opacity:0.75;"><?= htmlspecialchars($t['ph2']) ?></div>
                    </div>
                    <figcaption><?= htmlspecialchars($t['fig_cap']) ?></figcaption>
                </figure>

                <!-- Legenda: 2 stanga, al 5-lea centrat, 2 dreapta (culorile exacte din vizualizator) -->
                <div class="legend">
                    <div class="legend-col">
                        <div class="leg-item">
                            <div class="leg-icon">
                                <div class="v-node">4</div>
                            </div>
                            <div>
                                <div class="leg-title"><?= htmlspecialchars($t['leg1_h']) ?></div>
                                <div class="leg-desc"><?= htmlspecialchars($t['leg1_p']) ?></div>
                            </div>
                        </div>

                        <div class="leg-item">
                            <div class="leg-icon">
                                <svg width="22" height="40" viewBox="0 0 22 40">
                                    <line x1="11" y1="5" x2="11" y2="35" stroke="#ffffff" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <div class="leg-title"><?= htmlspecialchars($t['leg2_h']) ?></div>
                                <div class="leg-desc"><?= htmlspecialchars($t['leg2_p']) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="legend-col">
                        <div class="leg-item">
                            <div class="leg-icon">
                                <svg width="28" height="26" viewBox="0 0 28 26">
                                    <polygon points="2,3 23,7 14,21" fill="#133168" stroke="#005cd4"
                                        stroke-width="1.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="leg-title"><?= htmlspecialchars($t['leg3_h']) ?></div>
                                <div class="leg-desc"><?= htmlspecialchars($t['leg3_p']) ?></div>
                            </div>
                        </div>

                        <div class="leg-item">
                            <div class="leg-icon">
                                <span class="v-val ret">5</span>
                            </div>
                            <div>
                                <div class="leg-title"><?= htmlspecialchars($t['leg4_h']) ?></div>
                                <div class="leg-desc"><?= htmlspecialchars($t['leg4_p']) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="legend-bottom">
                        <div class="leg-item">
                            <div class="leg-icon">
                                <span class="v-val final">8</span>
                            </div>
                            <div>
                                <div class="leg-title"><?= htmlspecialchars($t['leg5_h']) ?></div>
                                <div class="leg-desc"><?= htmlspecialchars($t['leg5_p']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ASISTENT AI: cum îl folosești educativ -->
        <section class="section">
            <span class="eyebrow"><?= htmlspecialchars($t['ai_e']) ?></span>
            <h2 class="h2"><?= htmlspecialchars($t['ai_h']) ?></h2>
            <p class="lead"><?= htmlspecialchars($t['ai_p']) ?></p>

            <div class="grid">
                <div class="card">
                    <div class="phone_padding">
                        <div class="card-head">
                            <div class="card-title"><?= htmlspecialchars($t['ai1_h']) ?></div>
                        </div>
                        <p><?= htmlspecialchars($t['ai1_p']) ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="phone_padding">
                        <div class="card-head">
                            <div class="card-title"><?= htmlspecialchars($t['ai2_h']) ?></div>
                        </div>
                        <p><?= htmlspecialchars($t['ai2_p']) ?></p>
                    </div>
                </div>
                <div class="card">
                    <div class="phone_padding">
                        <div class="card-head">
                            <div class="card-title">
                                <?= htmlspecialchars($t['ai3_h']) ?>
                            </div>
                        </div>
                        <p><?= htmlspecialchars($t['ai3_p']) ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA: butonul de mers la vizualizator (păstrat) -->
        <section class="cta">
            <h2><?= htmlspecialchars($t['cta_h']) ?></h2>
            <p><?= htmlspecialchars($t['cta_p']) ?></p>
            <a href="index.php" class="btn"><?= htmlspecialchars($t['cta_btn']) ?> &rarr;</a>
        </section>

    </div>

    <!-- Lightbox: click pe imagine -> se deschide mare, in pagina -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <button type="button" class="lightbox-close" aria-label="Close" onclick="closeLightbox()">&times;</button>
        <img id="lightbox-img" src="" alt="" onclick="event.stopPropagation()">
    </div>

    <script>
        function openLightbox(e, src) {
            if (e) e.preventDefault();
            const lb = document.getElementById('lightbox');
            document.getElementById('lightbox-img').src = src;
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>

</body>

</html>