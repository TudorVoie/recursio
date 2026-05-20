<?php
session_start();

$lang = $_SESSION['lang'] ?? 'ro';
if (!is_file(__DIR__ . "/{$lang}.php")) {
    $lang = 'ro';
}
$t = require __DIR__ . "/{$lang}.php";

// Current language display
$langNames = [
    'en' => '',
    'ro' => '',
    'hu' => ''
];
$currentLangDisplay = $langNames[$lang] ?? 'Română';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['page_title'] ?></title>
    <link rel="icon" type="image/png" href="Recursio-logo-Spiral.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="recursivitate.css">
</head>

<body class="bg-slate-950 text-slate-200">

    <!-- Clean Header -->
    <div class="max-w-screen-xl mx-auto px-8 pt-8 pb-4 relative">
        <div class="flex items-center justify-between">

            <!-- BUTON ÎNAPOI -->
            <a href="index.php"
                class="flex items-center gap-x-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 rounded-2xl text-sm font-medium transition-all active:scale-[0.985]">
                <i class="fa-solid fa-arrow-left mr-1"></i>
                <span><?= $t['back_button'] ?></span>
            </a>

            <!-- LOGO CENTRU -->
            <div class="absolute left-1/2 transform -translate-x-1/2 mt-8">
                <div class="logo-container flex items-center">
                    <img src="Recursio-logo-dark-bg-2-2.png" alt="Recursio Logo" class="site-logo">
                </div>
            </div>

            <!-- LANGUAGE DROPDOWN -->
            <div class="relative ml-auto">
                <button onclick="toggleLanguageDropdown()"
                    class="flex items-center gap-x-3 px-5 py-3 bg-slate-800 hover:bg-slate-700 rounded-2xl text-sm font-medium transition-all active:scale-[0.985]">

                    <img id="current-lang-flag" src="<?= $lang ?>.png" alt="<?= $lang ?>"
                        class="w-5 h-5 rounded-sm object-cover border border-slate-600">

                    <span><?= $langNames[$lang] ?? 'Română' ?></span>

                    <i class="fa-solid fa-chevron-down text-xs transition-transform" id="lang-chevron"></i>
                </button>

                <div id="lang-dropdown" class="lang-dropdown hidden">
                    <a href="set_lang.php?lang=en"
                        class="lang-option flex items-center gap-x-3 px-4 py-3 hover:bg-slate-700 <?= $lang === 'en' ? 'active' : '' ?>">
                        <img src="en.png" class="w-5 h-5 rounded-sm object-cover" alt="English">
                        <span>English</span>
                    </a>
                    <a href="set_lang.php?lang=ro"
                        class="lang-option flex items-center gap-x-3 px-4 py-3 hover:bg-slate-700 <?= $lang === 'ro' ? 'active' : '' ?>">
                        <img src="ro.png" class="w-5 h-5 rounded-sm object-cover" alt="Română">
                        <span>Română</span>
                    </a>
                    <a href="set_lang.php?lang=hu"
                        class="lang-option flex items-center gap-x-3 px-4 py-3 hover:bg-slate-700 <?= $lang === 'hu' ? 'active' : '' ?>">
                        <img src="hu.png" class="w-5 h-5 rounded-sm object-cover" alt="Magyar">
                        <span>Magyar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero -->
    <div class="hero-section max-w-screen-xl mx-auto px-8 pt-12 pb-16">
        <div class="max-w-3xl">
            <h1 class="hero-title text-6xl font-display font-semibold tracking-tighter leading-none">
                <?= $t['hero_title'] ?>
            </h1>
            <p class="hero-subtitle mt-5 text-xl text-slate-400 leading-relaxed">
                <?= $t['hero_subtitle'] ?>
            </p>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-8 pb-20">

        <!-- HOW TO THINK RECURSIVELY -->
        <div class="mb-16">
            <div class="mb-6">
                <span class="text-indigo-400 text-sm font-semibold tracking-widest"><?= $t['core_concept'] ?></span>
                <h2 class="text-3xl font-display font-semibold tracking-tight"><?= $t['how_to_think_recursively'] ?>
                </h2>
                <p class="text-slate-400 mt-2 max-w-xl"><?= $t['how_to_think_recursively_desc'] ?></p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Base Case -->
                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div
                            class="w-9 h-9 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-stop-circle text-xl"></i>
                        </div>
                        <div class="font-semibold text-lg"><?= $t['base_case_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['base_case_desc'] ?><br><br>
                        <span class="font-mono text-emerald-400">if (n == 0) return 1;</span><br><br>
                        <span class="text-xs text-slate-400"><?= $t['base_case_explanation'] ?></span>
                    </div>
                </div>

                <!-- Recursive Case -->
                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div
                            class="w-9 h-9 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-sync text-xl"></i>
                        </div>
                        <div class="font-semibold text-lg"><?= $t['recursive_case_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['recursive_case_desc'] ?><br><br>
                        <span class="font-mono text-emerald-400">return n * factorial(n - 1);</span><br><br>
                        <span class="text-xs text-slate-400"><?= $t['recursive_case_explanation'] ?></span>
                    </div>
                </div>

                <!-- Progress -->
                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div
                            class="w-9 h-9 bg-purple-500/10 text-purple-400 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-arrow-down text-xl"></i>
                        </div>
                        <div class="font-semibold text-lg"><?= $t['progress_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['progress_desc'] ?><br><br>
                        <?= $t['progress_patterns'] ?><br><br>
                        <span class="text-xs text-emerald-400"><?= $t['progress_warning'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Thinking Framework -->
            <div class="mt-8 glass p-8 rounded-3xl">
                <div class="font-semibold mb-4 flex items-center gap-x-2">
                    <i class="fa-solid fa-brain text-indigo-400"></i>
                    <span><?= $t['step_by_step_thinking'] ?></span>
                </div>

                <div class="grid md:grid-cols-5 gap-4 text-sm">
                    <div class="info-box p-4 rounded-2xl">
                        <div class="font-medium text-emerald-400 mb-1"><?= $t['step1'] ?></div>
                        <div><?= $t['step1_text'] ?></div>
                    </div>
                    <div class="info-box p-4 rounded-2xl">
                        <div class="font-medium text-emerald-400 mb-1"><?= $t['step2'] ?></div>
                        <div><?= $t['step2_text'] ?></div>
                    </div>
                    <div class="info-box p-4 rounded-2xl">
                        <div class="font-medium text-emerald-400 mb-1"><?= $t['step3'] ?></div>
                        <div><?= $t['step3_text'] ?></div>
                    </div>
                    <div class="info-box p-4 rounded-2xl">
                        <div class="font-medium text-emerald-400 mb-1"><?= $t['step4'] ?></div>
                        <div><?= $t['step4_text'] ?></div>
                    </div>
                    <div class="info-box p-4 rounded-2xl">
                        <div class="font-medium text-emerald-400 mb-1"><?= $t['step5'] ?></div>
                        <div><?= $t['step5_text'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RETURN TYPES -->
        <div class="mb-16">
            <div class="mb-6">
                <span
                    class="text-indigo-400 text-sm font-semibold tracking-widest"><?= $t['return_types_label'] ?></span>
                <h2 class="text-3xl font-display font-semibold tracking-tight"><?= $t['return_types_title'] ?></h2>
                <p class="text-slate-400 mt-2 max-w-xl"><?= $t['return_types_desc'] ?></p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div
                            class="w-9 h-9 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-sync text-xl"></i>
                        </div>
                        <div class="font-semibold text-lg"><?= $t['returning_value_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['returning_value_desc'] ?><br><br>
                        <span class="font-mono text-emerald-400">int factorial(int n) {<br>&nbsp;&nbsp;if (n == 0)
                            return 1;<br>&nbsp;&nbsp;return n * factorial(n-1);<br>}</span><br><br>
                        <span class="text-xs text-emerald-400"><?= $t['returning_value_note'] ?></span>
                    </div>
                </div>

                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div
                            class="w-9 h-9 bg-orange-500/10 text-orange-400 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-times text-xl"></i>
                        </div>
                        <div class="font-semibold text-lg"><?= $t['void_recursion_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['void_recursion_desc'] ?><br><br>
                        <span class="font-mono text-orange-400">void printNumbers(int n) {<br>&nbsp;&nbsp;if (n == 0)
                            return;<br>&nbsp;&nbsp;printNumbers (n-1);<br>&nbsp;&nbsp;cout << n << " ";<br>}</span><br><br>
                        <span class="text-xs text-orange-400"><?= $t['void_recursion_note'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LINEAR VS TREE RECURSION -->
        <div class="mb-16">
            <div class="mb-6">
                <span
                    class="text-indigo-400 text-sm font-semibold tracking-widest"><?= $t['recursion_types_label'] ?></span>
                <h2 class="text-3xl font-display font-semibold tracking-tight"><?= $t['recursion_types_title'] ?></h2>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="glass p-7 rounded-3xl modern-card">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div class="px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-xl text-xs font-semibold">
                            LINEAR</div>
                        <div class="font-semibold text-lg"><?= $t['linear_recursion_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['linear_recursion_desc'] ?><br><br>
                        <span class="font-mono text-emerald-400">factorial(n) → factorial(n-1)</span><br><br>
                        <span class="text-xs text-emerald-400"><?= $t['linear_recursion_examples'] ?></span>
                    </div>
                </div>

                <div class="glass p-7 rounded-3xl modern-card border border-purple-500/30">
                    <div class="flex items-center gap-x-3 mb-4">
                        <div class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-xl text-xs font-semibold">TREE
                        </div>
                        <div class="font-semibold text-lg"><?= $t['tree_recursion_title'] ?></div>
                    </div>
                    <div class="text-sm text-slate-300 leading-relaxed">
                        <?= $t['tree_recursion_desc'] ?><br><br>
                        <span class="font-mono text-purple-400">fib(n) → fib(n-1) + fib(n-2)</span><br><br>
                        <span class="text-xs text-purple-400"><?= $t['tree_recursion_examples'] ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-6 glass p-6 rounded-3xl">
                <div class="text-sm text-slate-300">
                    <strong class="text-white"><?= $t['tree_recursion_warning_title'] ?></strong><br>
                    <?= $t['tree_recursion_warning_desc'] ?>
                </div>
            </div>
        </div>

        <!-- CALL STACK -->
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <span
                        class="text-indigo-400 text-sm font-semibold tracking-widest"><?= $t['internal_mechanics'] ?></span>
                    <h2 class="text-3xl font-display font-semibold tracking-tight"><?= $t['call_stack_explained'] ?>
                    </h2>
                </div>
                <button onclick="demoCallStack()"
                    class="px-5 py-2.5 text-sm flex items-center gap-x-2 bg-slate-800 hover:bg-slate-700 rounded-2xl transition-colors">
                    <i class="fa-solid fa-play mr-1.5"></i> <span><?= $t['watch_animation'] ?></span>
                </button>
            </div>

            <div class="glass p-8 rounded-3xl">
                <div class="grid md:grid-cols-5 gap-8">
                    <div class="md:col-span-3 text-sm text-slate-300">
                        <p class="mb-5 leading-relaxed"><?= $t['call_stack_description'] ?></p>
                        <ul class="space-y-3 ml-1">
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-1">•</span>
                                <span><?= $t['call_stack_item_1'] ?></span></li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-1">•</span>
                                <span><?= $t['call_stack_item_2'] ?></span></li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 mt-1">•</span>
                                <span><?= $t['call_stack_item_3'] ?></span></li>
                        </ul>
                        <div class="mt-6 text-xs bg-slate-900 p-4 rounded-2xl border border-slate-700 leading-relaxed">
                            <?= $t['stack_limit_warning'] ?>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="text-xs text-slate-400 mb-3 flex items-center gap-x-2">
                            <span><?= $t['live_call_stack'] ?></span>
                            <div class="flex-1 h-px bg-slate-700"></div>
                        </div>
                        <div id="stack-visual"
                            class="min-h-[230px] bg-slate-900 border border-slate-700 rounded-2xl p-5 flex flex-col-reverse gap-2.5 overflow-hidden text-sm">
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button onclick="pushCall()"
                                class="flex-1 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-700 rounded-2xl font-medium"><?= $t['push_call'] ?></button>
                            <button onclick="popCall()"
                                class="flex-1 py-2.5 text-xs bg-slate-700 hover:bg-slate-600 rounded-2xl font-medium"><?= $t['pop_return'] ?></button>
                            <button onclick="resetStack()"
                                class="px-4 py-2.5 text-xs bg-slate-800 hover:bg-slate-700 rounded-2xl"><?= $t['reset'] ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUIZ (4 întrebări) -->
        <div>
            <div class="glass p-10 rounded-3xl">
                <div class="text-center mb-8">
                    <div
                        class="mx-auto w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-graduation-cap text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold"><?= $t['test_your_understanding'] ?></h3>
                    <p class="text-slate-400 text-sm mt-1">4 questions about recursive functions</p>
                </div>

                <div id="quiz-container" class="max-w-xl mx-auto"></div>

                <div class="text-center mt-8">
                    <button onclick="submitQuiz()" id="quiz-submit-btn"
                        class="px-10 py-3.5 bg-white text-slate-950 font-semibold rounded-3xl hover:bg-slate-100 transition-all hidden">
                        <?= $t['submit_answers'] ?>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function initTailwind() { }

        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('lang-dropdown');
            const chevron = document.getElementById('lang-chevron');
            dropdown.classList.toggle('hidden');
            dropdown.classList.toggle('show');
            chevron.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('lang-dropdown');
            const button = event.target.closest('button');
            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('show');
                document.getElementById('lang-chevron').style.transform = 'rotate(0deg)';
            }
        });

        // Quiz - 4 întrebări
        const quizQuestions = [
            { q: "<?= $t['quiz_q1'] ?>", options: ["<?= $t['quiz_q1_opt1'] ?>", "<?= $t['quiz_q1_opt2'] ?>", "<?= $t['quiz_q1_opt3'] ?>", "<?= $t['quiz_q1_opt4'] ?>"], correct: 1 },
            { q: "<?= $t['quiz_q2'] ?>", options: ["<?= $t['quiz_q2_opt1'] ?>", "<?= $t['quiz_q2_opt2'] ?>", "<?= $t['quiz_q2_opt3'] ?>", "<?= $t['quiz_q2_opt4'] ?>"], correct: 2 },
            { q: "<?= $t['quiz_q4'] ?>", options: ["<?= $t['quiz_q4_opt1'] ?>", "<?= $t['quiz_q4_opt2'] ?>", "<?= $t['quiz_q4_opt3'] ?>", "<?= $t['quiz_q4_opt4'] ?>"], correct: 1 },
            { q: "<?= $t['quiz_q5'] ?>", options: ["<?= $t['quiz_q5_opt1'] ?>", "<?= $t['quiz_q5_opt2'] ?>", "<?= $t['quiz_q5_opt3'] ?>", "<?= $t['quiz_q5_opt4'] ?>"], correct: 2 }
        ];

        let userAnswers = [];

        function initQuiz() {
            const container = document.getElementById('quiz-container');
            container.innerHTML = '';
            userAnswers = new Array(quizQuestions.length).fill(null);

            quizQuestions.forEach((q, i) => {
                const div = document.createElement('div');
                div.className = 'mb-7';
                div.innerHTML = `
                    <div class="font-medium mb-3">${i + 1}. ${q.q}</div>
                    <div class="space-y-2">
                        ${q.options.map((opt, j) => `
                            <label class="flex items-center gap-x-3 p-3.5 rounded-2xl hover:bg-slate-800 cursor-pointer border border-transparent has-[:checked]:border-indigo-500 has-[:checked]:bg-slate-800">
                                <input type="radio" name="q${i}" value="${j}" class="accent-indigo-500" onchange="userAnswers[${i}] = ${j}">
                                <span class="text-sm">${opt}</span>
                            </label>
                        `).join('')}
                    </div>
                `;
                container.appendChild(div);
            });
            document.getElementById('quiz-submit-btn').classList.remove('hidden');
        }

        function submitQuiz() {
            let score = 0;
            quizQuestions.forEach((q, i) => { if (userAnswers[i] === q.correct) score++; });

            const container = document.getElementById('quiz-container');
            container.innerHTML = `
                <div class="text-center py-6">
                    <div class="text-6xl mb-3">${score >= 3 ? '🎉' : '📖'}</div>
                    <div class="text-4xl font-semibold mb-1">Score: <span class="text-emerald-400">${score}/4</span></div>
                    <div class="max-w-xs mx-auto mt-4 text-sm text-slate-400">
                        ${score >= 3 ? '<?= $t['quiz_excellent'] ?>' : '<?= $t['quiz_keep_practicing'] ?>'}
                    </div>
                    <button onclick="initQuiz()" class="mt-7 px-8 py-2.5 border border-slate-600 hover:bg-slate-800 rounded-2xl text-sm font-medium"><?= $t['try_again'] ?></button>
                </div>
            `;
            document.getElementById('quiz-submit-btn').classList.add('hidden');
        }

// ==================== CALL STACK ====================
let stackItems = [];

function pushCall() {
    const container = document.getElementById('stack-visual');
    const level = stackItems.length + 1;
    const div = document.createElement('div');
    div.className = `stack-item px-4 py-3 bg-slate-800 border border-indigo-500/60 rounded-2xl flex justify-between items-center text-sm`;
    div.innerHTML = `
        <div class="flex items-center gap-x-3">
            <i class="fa-solid fa-layer-group text-indigo-400"></i>
            <span>factorial(${level})</span>
        </div>
        <div class="text-xs px-2.5 py-0.5 bg-indigo-500/10 text-indigo-300 rounded-xl">FRAME</div>`;
    container.appendChild(div);
    stackItems.push(div);
}

function popCall() {
    if (stackItems.length === 0) return;
    const last = stackItems.pop();
    last.style.transition = 'all 0.35s ease';
    last.style.opacity = '0';
    last.style.transform = 'translateX(30px)';
    setTimeout(() => last.remove(), 320);
}

function resetStack() {
    document.getElementById('stack-visual').innerHTML = '';
    stackItems = [];
}

function demoCallStack() {
    resetStack();
    setTimeout(() => pushCall(), 80);
    setTimeout(() => pushCall(), 650);
    setTimeout(() => pushCall(), 1250);
    setTimeout(() => {
        const msg = document.createElement('div');
        msg.className = 'text-center text-xs text-emerald-400 py-2';
        msg.innerHTML = 'Base case reached → unwinding begins';
        document.getElementById('stack-visual').appendChild(msg);
    }, 1850);
    setTimeout(() => popCall(), 2500);
    setTimeout(() => popCall(), 3050);
    setTimeout(() => popCall(), 3550);
}

        function initialize() {
            initTailwind();
            initQuiz();
            console.log('%c[Recursio] Full version loaded (4 questions + centered logo)', 'color:#64748b');
        }

        window.onload = initialize;
    </script>
</body>

</html>