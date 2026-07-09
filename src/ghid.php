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
if (!in_array($lang, $allowed, true)) {
    $lang = 'ro';
}

$T = [
    'ro' => [
        'title' => 'Recursio — Prezentare',
        'hero_h' => 'Recursivitatea prinde viață',
        'hero_p' => 'Recursio transformă o funcție recursivă într-un arbore animat: fiecare apel devine un nod, iar tu urmărești cum se construiește arborele și cum se întorc rezultatele — pas cu pas.',
        'what_e' => 'Pe scurt',
        'what_h' => 'Ce este recursivitatea?',
        'what_p' => 'O funcție recursivă rezolvă o problemă apelându-se pe ea însăși cu o versiune mai mică a ei, până ajunge la un caz simplu pe care îl știe direct. Are nevoie de exact două lucruri:',
        'base_h' => 'Cazul de bază',
        'base_p' => 'Condiția de oprire — situația cea mai simplă, în care răspunsul se știe imediat. Fără el, recursivitatea nu se termină niciodată.',
        'rec_h' => 'Cazul recursiv',
        'rec_p' => 'Apelul funcției către ea însăși, dar cu o problemă puțin mai mică. Fiecare pas trebuie să se apropie de cazul de bază.',
        'cmt_base' => '// cazul de bază',
        'cmt_rec' => '// cazul recursiv',
        'viz_e' => 'Ghid vizual',
        'viz_h' => 'Ce reprezintă elementele din vizualizator',
        'viz_p' => 'Când rulezi o funcție, Recursio desenează arborele de apeluri. Iată ce înseamnă fiecare element:',
        'fig_cap' => 'Exemplu: arborele pentru fibonacci(6)',
        'ph1' => 'Aici va apărea imaginea adnotată a arborelui',
        'ph2' => '(o adaug când îmi trimiți captura din vizualizator)',
        'leg1_h' => 'Parametru funcție',
        'leg1_p' => 'Fiecare cerc este un apel al funcției. Numărul din interior este valoarea parametrului cu care a fost apelată.',
        'leg2_h' => 'Linie de conexiune',
        'leg2_p' => 'Leagă un apel de sub-apelurile pe care le declanșează, formând ramurile arborelui.',
        'leg3_h' => 'Direcție',
        'leg3_p' => 'Indică sensul în care avansează recursivitatea, de la apelul inițial spre cazul de bază.',
        'leg4_h' => 'Valoare returnată',
        'leg4_p' => 'Numărul colorat arată ce returnează un apel după ce s-a terminat.',
        'leg5_h' => 'Valoare returnată finală',
        'leg5_p' => 'Rezultatul întors de apelul de la rădăcină — răspunsul final al recursivității.',
        'ai_e' => 'Asistent AI',
        'ai_h' => 'Învață cu un asistent AI alături',
        'ai_p' => 'Recursio are un asistent AI integrat, ca un tutor mereu la îndemână. Nu îți dă doar răspunsul — te ajută să înțelegi ce se întâmplă în spatele recursivității.',
        'ai1_h' => 'Îți explică propriul cod',
        'ai1_p' => 'Când rulezi o funcție, Recursio îi cere AI-ului să descrie ce face codul tău, pe limbajul tău — fără să sapi prin documentații.',
        'ai2_h' => 'Răspunde la întrebările tale',
        'ai2_p' => 'Deschide asistentul din bara laterală și întreabă orice despre codul tău sau despre recursivitate. Ține minte conversația, așa că poți continua cu întrebări.',
        'ai3_h' => 'Te ajută să înțelegi și să depanezi',
        'ai3_p' => 'Blocat la un stack overflow sau nu găsești cazul de bază? Cere-i AI-ului să urmărească apelurile pas cu pas și să-ți arate unde e problema.',
        'ai_tip_h' => 'Idei de întrebări:',
        'ai_q1' => 'De ce dă funcția mea stack overflow?',
        'ai_q2' => 'Explică-mi pas cu pas ce face fibonacci(5).',
        'ai_q3' => 'Care e cazul de bază aici și de ce e nevoie de el?',
        'cta_h' => 'Gata de încercat?',
        'cta_p' => 'Scrie-ți propria funcție recursivă și urmărește arborele construindu-se în timp real.',
        'cta_btn' => 'Deschide vizualizatorul',
    ],
    'en' => [
        'title' => 'Recursio — Overview',
        'hero_h' => 'Recursion comes to life',
        'hero_p' => 'Recursio turns a recursive function into an animated tree: every call becomes a node, and you watch the tree build up and the results come back — step by step.',
        'what_e' => 'In short',
        'what_h' => 'What is recursion?',
        'what_p' => 'A recursive function solves a problem by calling itself on a smaller version of it, until it reaches a simple case it knows directly. It needs exactly two things:',
        'base_h' => 'The base case',
        'base_p' => 'The stopping condition — the simplest situation, where the answer is known at once. Without it, recursion never ends.',
        'rec_h' => 'The recursive case',
        'rec_p' => 'The function calling itself, but on a slightly smaller problem. Every step must move closer to the base case.',
        'cmt_base' => '// base case',
        'cmt_rec' => '// recursive case',
        'viz_e' => 'Visual guide',
        'viz_h' => 'What the visualizer elements mean',
        'viz_p' => 'When you run a function, Recursio draws the call tree. Here is what each element means:',
        'fig_cap' => 'Example: the tree for fibonacci(6)',
        'ph1' => 'The annotated tree image will go here',
        'ph2' => '(I will add it when you send the screenshot from the visualizer)',
        'leg1_h' => 'Function parameter',
        'leg1_p' => 'Each circle is a function call. The number inside is the parameter value it was called with.',
        'leg2_h' => 'Connection line',
        'leg2_p' => 'Links a call to the sub-calls it triggers, forming the branches of the tree.',
        'leg3_h' => 'Direction',
        'leg3_p' => 'Shows the way recursion moves, from the initial call down to the base case.',
        'leg4_h' => 'Returned value',
        'leg4_p' => 'The colored number shows what a call returns once it has finished.',
        'leg5_h' => 'Final returned value',
        'leg5_p' => 'The result returned by the root call — the final answer of the recursion.',
        'ai_e' => 'AI assistant',
        'ai_h' => 'Learn with an AI assistant by your side',
        'ai_p' => 'Recursio has a built-in AI assistant, like a tutor always within reach. It does not just hand you the answer — it helps you understand what is happening behind the recursion.',
        'ai1_h' => 'Explains your own code',
        'ai1_p' => 'When you run a function, Recursio asks the AI to describe what your code does, in plain language — no digging through docs.',
        'ai2_h' => 'Answers your questions',
        'ai2_p' => 'Open the assistant in the sidebar and ask anything about your code or recursion. It remembers the conversation, so you can keep asking follow-ups.',
        'ai3_h' => 'Helps you understand and debug',
        'ai3_p' => 'Stuck on a stack overflow or cannot find the base case? Ask the AI to trace the calls step by step and point out where things go wrong.',
        'ai_tip_h' => 'Questions you could ask:',
        'ai_q1' => 'Why does my function cause a stack overflow?',
        'ai_q2' => 'Walk me through what fibonacci(5) does, step by step.',
        'ai_q3' => 'What is the base case here and why is it needed?',
        'cta_h' => 'Ready to try it?',
        'cta_p' => 'Write your own recursive function and watch the tree build up in real time.',
        'cta_btn' => 'Open the visualizer',
    ],
    'hu' => [
        'title' => 'Recursio — Bemutató',
        'hero_h' => 'A rekurzió életre kel',
        'hero_p' => 'A Recursio egy rekurzív függvényt animált fává alakít: minden hívás egy csomópont lesz, te pedig végignézed, ahogy a fa felépül és az eredmények visszatérnek — lépésről lépésre.',
        'what_e' => 'Röviden',
        'what_h' => 'Mi a rekurzió?',
        'what_p' => 'A rekurzív függvény úgy old meg egy feladatot, hogy önmagát hívja meg annak egy kisebb változatával, amíg el nem ér egy egyszerű esethez, amelyet közvetlenül ismer. Pontosan két dologra van szüksége:',
        'base_h' => 'Az alapeset',
        'base_p' => 'A leállási feltétel — a legegyszerűbb helyzet, ahol a válasz azonnal ismert. Nélküle a rekurzió sosem ér véget.',
        'rec_h' => 'A rekurzív eset',
        'rec_p' => 'A függvény önmagát hívja, de egy kicsit kisebb feladattal. Minden lépésnek közelednie kell az alapesethez.',
        'cmt_base' => '// alapeset',
        'cmt_rec' => '// rekurzív eset',
        'viz_e' => 'Vizuális útmutató',
        'viz_h' => 'Mit jelentenek a vizualizáló elemei',
        'viz_p' => 'Amikor lefuttatsz egy függvényt, a Recursio megrajzolja a hívási fát. Íme, mit jelent minden elem:',
        'fig_cap' => 'Példa: a fibonacci(6) fája',
        'ph1' => 'Ide kerül a fa feliratozott képe',
        'ph2' => '(hozzáadom, amint elküldöd a képernyőképet a vizualizálóból)',
        'leg1_h' => 'Függvényparaméter',
        'leg1_p' => 'Minden kör egy függvényhívás. A benne lévő szám az a paraméterérték, amellyel meghívták.',
        'leg2_h' => 'Összekötő vonal',
        'leg2_p' => 'Egy hívást összeköt az általa kiváltott alhívásokkal, így alakulnak ki a fa ágai.',
        'leg3_h' => 'Irány',
        'leg3_p' => 'Megmutatja, merre halad a rekurzió, a kezdeti hívástól az alapeset felé.',
        'leg4_h' => 'Visszatérési érték',
        'leg4_p' => 'A színes szám azt mutatja, mit ad vissza egy hívás, miután befejeződött.',
        'leg5_h' => 'Végső visszatérési érték',
        'leg5_p' => 'A gyökérhívás által visszaadott eredmény — a rekurzió végső válasza.',
        'ai_e' => 'AI asszisztens',
        'ai_h' => 'Tanulj egy AI asszisztenssel az oldaladon',
        'ai_p' => 'A Recursio beépített AI asszisztenssel rendelkezik, mint egy mindig kéznél lévő korrepetitor. Nemcsak megadja a választ — segít megérteni, mi történik a rekurzió mögött.',
        'ai1_h' => 'Elmagyarázza a saját kódodat',
        'ai1_p' => 'Amikor lefuttatsz egy függvényt, a Recursio megkéri az AI-t, hogy egyszerű nyelven leírja, mit csinál a kódod — nem kell dokumentációkban keresgélned.',
        'ai2_h' => 'Válaszol a kérdéseidre',
        'ai2_p' => 'Nyisd meg az asszisztenst az oldalsávban, és kérdezz bármit a kódodról vagy a rekurzióról. Megjegyzi a beszélgetést, így folytathatod a kérdéseket.',
        'ai3_h' => 'Segít megérteni és hibát keresni',
        'ai3_p' => 'Elakadtál egy stack overflow-nál, vagy nem találod az alapesetet? Kérd meg az AI-t, hogy lépésről lépésre kövesse a hívásokat, és mutassa meg, hol a hiba.',
        'ai_tip_h' => 'Kérdésötletek:',
        'ai_q1' => 'Miért okoz a függvényem stack overflow-t?',
        'ai_q2' => 'Vezess végig lépésről lépésre, mit csinál a fibonacci(5).',
        'ai_q3' => 'Mi itt az alapeset, és miért van rá szükség?',
        'cta_h' => 'Kipróbálod?',
        'cta_p' => 'Írd meg a saját rekurzív függvényedet, és nézd, ahogy a fa valós időben felépül.',
        'cta_btn' => 'Vizualizáló megnyitása',
    ],
];

$t = $T[$lang];
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
                <?php foreach (['ro' => 'Română', 'en' => 'English', 'hu' => 'Magyar'] as $code => $name): ?>
                    <a class="lang-opt <?= $lang === $code ? 'active' : '' ?>" href="ghid.php?lang=<?= $code ?>">
                        <img src="<?= $code ?>.png" alt="<?= $code ?>"><span><?= $name ?></span>
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