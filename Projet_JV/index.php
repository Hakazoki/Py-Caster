<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <title>Projet JV</title>
    <style>
        body {
            background-image: url("image/fond_dragon.png");
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
    </style>
</head>

<body>
    <div class="collapse navbar-dark bg-dark" id="navbarToggleExternalContent" data-bs-theme="dark">
        <div class="bg-dark p-4">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">Règles</a>
                </li>
            </ul>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">Règles</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="titre" style="display: block; width: auto; height: auto;">
        <p>Cyber Dragon</p>
    </div>
    <div class="play" style="display: block; width: auto; height: auto;">
        <div class="jeu">
            <form method="post">
                <button id="btnPlay" type="button" name="choice" value="Jouer" class="btn btn-darkred btn-xyl">
                    <p>▶</p>
                    <p>Jouer</p>
                </button>
                <div id="terminalWrapper" class="d-none">
                    <div class="terminal-header">
                        <button id="resetBtn" type="submit" name="restart">Rejouer ⭯</button>
                        <button id="closeTerminal" type="button" class="close-btn">Fermer ✖</button>
                    </div>
                    <textarea id="terminal" class="form-control terminal" rows="10"></textarea>
                </div>
            </form>

        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Règle du jeu</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <?php include 'consign.php' ?>
            </div>
        </div>
    </div>

    <script>
        const btnPlay = document.getElementById('btnPlay');
        const terminalWrapper = document.getElementById('terminalWrapper');
        const terminal = document.getElementById('terminal');
        const closeTerminal = document.getElementById('closeTerminal');
        const resetBtn = document.getElementById('resetBtn');
        let puzzleMode = false;

        const commands = {

            help: () => {
                return "Sorts dispo : help, fireball, soin, slay, foudre, glagla, histo";
            },

            fireball: async () => {
                puzzleMode = true;
                const questions = await fetch("question.json").then(r => r.json());
                const q = questions[Math.floor(Math.random() * questions.length)];
                window.currentPuzzle = q.id;
                terminal.value +=
                    `\n=== PUZZLE PYTHON : Sort "fireball" ===
${q.text}

${q.template}

Écris ton code ci-dessus puis tape "run" sur une nouvelle ligne pour valider.`;
            },


            _resolvePuzzle: async () => {

                // 1. Récupérer toutes les lignes du terminal
                const allLines = terminal.value.split("\n");

                // 2. Trouver le DERNIER "def compute()"
                let startIndex = -1;
                for (let i = allLines.length - 1; i >= 0; i--) {
                    if (allLines[i].trim().startsWith("def compute")) {
                        startIndex = i;
                        break;
                    }
                }

                if (startIndex === -1) {
                    terminal.value += "\nERREUR : Impossible de trouver compute().\n$ ";
                    return;
                }

                // 3. Trouver le DERNIER "run" APRÈS ce compute()
                let runIndex = -1;
                for (let i = allLines.length - 1; i > startIndex; i--) {
                    if (allLines[i].trim() === "run") {
                        runIndex = i;
                        break;
                    }
                }

                if (runIndex === -1) {
                    terminal.value += "\nERREUR : Tape 'run' pour valider.\n$ ";
                    return;
                }

                // 4. Extraire toutes les lignes entre compute() et run
                const rawBlock = allLines.slice(startIndex, runIndex);

                // 5. Filtrer pour ne garder QUE les lignes Python valides
                const codeLines = rawBlock.filter(l => {
                    const t = l.trim();
                    return (
                        t.startsWith("def ") ||
                        t.startsWith("return ") ||
                        t.includes("=") ||
                        t.startsWith("#") ||
                        l.startsWith("    ") ||
                        t === ""
                    );
                });

                // 6. Reconstituer le code propre
                const code = codeLines.join("\n");

                // 7. Envoyer au serveur
                const result = await fetch("cmd_fireball.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "code=" + encodeURIComponent(code) +
                        "&puzzle_id=" + window.currentPuzzle
                }).then(r => r.text());

                terminal.value += "\n" + result + "\n$ ";
            },

            soin: () => {
                return fetch("soin.php")
                    .then(r => r.text())
                    .then(number => {
                        return "Vous vous soignez !\nPV récupérés : " + number;
                    });
            },
            slay: () => {
                return fetch("cmd_action.php")
                    .then(r => r.text())
                    .then(number => {
                        return "Votre ennemi est lacéré !\nDégâts infligés : " + number;
                    });
            },
            foudre: () => {
                return fetch("cmd_action.php")
                    .then(r => r.text())
                    .then(number => {
                        return "Des éclairs frappent l'adversaire !\nDégâts infligés : " + number;
                    });
            },
            glagla: () => {
                return fetch("cmd_action.php")
                    .then(r => r.text())
                    .then(number => {
                        return "Un blizzard se lève !\nDégâts infligés : " + number;
                    });
            },
            histo: () => {
                return fetch("voir_histo.php")
                    .then(r => r.text())
                    .then(data => {
                        terminal.value += "\n" + data;
                        terminal.scrollTop = terminal.scrollHeight;
                        return "";
                    });
            }
        }
        btnPlay.addEventListener('click', () => {
            terminalWrapper.classList.remove('d-none');
            terminal.focus();
            terminal.value += "\n$ ";
        });

        closeTerminal.addEventListener('click', () => {
            terminalWrapper.classList.add('d-none');
        });

        resetBtn.addEventListener("click", function() {
            fetch("reset_histo.php")
        });
        terminal.addEventListener("keydown", async (e) => {
            if (e.key !== "Enter") return;

            const lines = terminal.value.split("\n");
            const lastLine = lines[lines.length - 1].trim();

            if (puzzleMode) {
                if (lastLine === "run") {
                    e.preventDefault();
                    puzzleMode = false;
                    await commands._resolvePuzzle();
                }
                return;
            }

            const cmd = lastLine.replace(/^\$/, "").trim();

            if (commands[cmd]) {
                const output = commands[cmd]();

                if (output instanceof Promise) {
                    const text = await output;
                    if (text !== undefined) {
                        terminal.value += "\n" + text + "\n$ ";
                    }
                } else {
                    if (output !== undefined) {
                        terminal.value += "\n" + output + "\n$ ";
                    }
                }

            } else {
                terminal.value += "\nCommande inconnue : " + cmd + "\n$ ";
            }
        });

        terminal.addEventListener("keydown", function(e) {
            if (e.key === "Tab") {
                if (!puzzleMode) return;
                e.preventDefault();
                const start = terminal.selectionStart;
                const end = terminal.selectionEnd;
                const before = terminal.value.substring(0, start);
                const after = terminal.value.substring(end);
                const tab = "    ";
                terminal.value = before + tab + after;
                terminal.selectionStart = terminal.selectionEnd = start + tab.length;
            }
        });
    </script>

    <script>
        const socket = io('http://192.168.10.210:5000');

        function Resultat(success) {
            socket.emit('reussite_sortilege', {
                reussite: success
            });
        }

        document.getElementById('btn-valider-sort').addEventListener('click', () => {
            Resultat(true);
        });

        function relancerPartie() {
            socket.emit('restart_game');
        }

        document.getElementById('btn-restart').addEventListener('click', () => {
            relancerPartie();
        });
        
        socket.on('connect', () => {
            console.log("✔️ Connecté au serveur Socket.io");
        });

        socket.on('connect_error', (err) => {
            console.error("❌ Erreur de connexion :", err);
        });

        socket.on('disconnect', () => {
            console.warn("⚠️ Déconnecté du serveur");
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

</body>

</html>