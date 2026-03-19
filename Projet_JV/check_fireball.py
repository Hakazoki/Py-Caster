import sys
import ast
import json
import random

# --- Récupération des arguments ---
filename = sys.argv[1]      # fichier contenant le code du joueur
puzzle_id = int(sys.argv[2])  # ID du puzzle choisi par le JS

# --- Lecture du code Python du joueur ---
with open(filename, "r") as f:
    code = f.read()

# --- Vérification syntaxique ---
try:
    ast.parse(code)
except SyntaxError:
    print("ERREUR : Code invalide.\nVous subissez {} degats.".format(random.randint(5, 15)))
    sys.exit(0)

# --- Chargement des puzzles depuis le JSON ---
with open("question.json", "r", encoding="utf-8") as f:
    puzzles = json.load(f)

# On récupère le puzzle correspondant
puzzle = puzzles[puzzle_id]
puzzle_type = puzzle["type"]

# --- Exécution contrôlée ---
local_env = {}
try:
    exec(code, {}, local_env)
    compute = local_env.get("compute")
except Exception as e:
    print("ERREUR à l'execution : {}.\nVous subissez {} degats.".format(e, random.randint(5, 15)))
    sys.exit(0)

# Vérifier que compute existe
if compute is None or not callable(compute):
    print("ERREUR : La fonction compute() est manquante.\nVous subissez {} degats.".format(random.randint(5, 15)))
    sys.exit(0)

# Appel de compute()
try:
    result = compute()
except Exception as e:
    print("ERREUR dans compute() : {}.\nVous subissez {} degats.".format(e, random.randint(5, 15)))
    sys.exit(0)

# --- Vérifications selon le type de puzzle ---
def check_positive(x):
    return isinstance(x, (int, float)) and x > 0

def check_even(x):
    return isinstance(x, int) and x % 2 == 0

def check_gt100(x):
    return isinstance(x, (int, float)) and x > 100

# Table de correspondance
checks = {
    "positive": check_positive,
    "even": check_even,
    "gt100": check_gt100
}

# Vérification finale
if checks[puzzle_type](result):
    dmg = random.randint(10, 25)
    print("SUCCES : Puzzle reussi !\nVotre boule de feu touche l'ennemi.\nDegats infliges : {}.".format(dmg))
else:
    dmg = random.randint(5, 15)
    print("ECHEC : Mauvaise reponse.\nVous subissez {} degats.".format(dmg))