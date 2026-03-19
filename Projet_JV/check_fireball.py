import sys
import subprocess
import ast
import json
import random

# ---------------------------------------------------------
# 1. Chargement du code joueur
# ---------------------------------------------------------

# Nom du fichier contenant le code Python du joueur
filename = sys.argv[1]

# Vérification du puzzle_id sans try/except
if len(sys.argv) < 3:
    print("ERREUR : puzzle_id invalide.")
    sys.exit(0)

arg = sys.argv[2]

# Accepte les entiers positifs et négatifs
if not arg.lstrip("-").isdigit():
    print("ERREUR : puzzle_id invalide.")
    sys.exit(0)

puzzle_id = int(arg)

# Lecture du code joueur
with open(filename, "r", encoding="utf-8") as f:
    code = f.read()

# ---------------------------------------------------------
# 2. Vérification syntaxique
# ---------------------------------------------------------

syntax_check = subprocess.run(
    [sys.executable, "-m", "py_compile", filename],
    capture_output=True,
    text=True
)

if syntax_check.returncode != 0:
    print("ERREUR : Code invalide.\n--- DETAILS PYTHON ---")
    print(syntax_check.stderr)
    sys.exit(0)

# ---------------------------------------------------------
# 3. Analyse AST (sécurisée)
# ---------------------------------------------------------

tree = ast.parse(code)

class PuzzleValidator(ast.NodeVisitor):
    allowed = {
        ast.Module, ast.FunctionDef, ast.Return,
        ast.Expr, ast.BinOp, ast.Add, ast.Sub, ast.Mult, ast.Div,
        ast.UnaryOp, ast.UAdd, ast.USub,
        ast.Num, ast.Constant, ast.arguments, ast.Name, ast.Load,
        ast.Assign, ast.Store, ast.List, ast.Call, ast.Attribute,
        ast.Subscript
    }

    def visit(self, node):
        if type(node) not in self.allowed:
            print("ERREUR : Instruction interdite :", type(node).__name__)
            print("Vous subissez {} degats.".format(random.randint(5, 15)))
            sys.exit(0)
        super().visit(node)

PuzzleValidator().visit(tree)

# ---------------------------------------------------------
# 4. Extraction de compute()
# ---------------------------------------------------------

def extract_compute(tree):
    for node in tree.body:
        if isinstance(node, ast.FunctionDef) and node.name == "compute":
            return node
    return None

compute_node = extract_compute(tree)

if compute_node is None:
    print("ERREUR : compute() manquante.\nVous subissez {} degats.".format(random.randint(5, 15)))
    sys.exit(0)

# ---------------------------------------------------------
# 5. Extraction du return et évaluation contrôlée
# ---------------------------------------------------------

returns = [node for node in compute_node.body if isinstance(node, ast.Return)]

if not returns:
    print("ERREUR : compute() doit contenir un return.\nVous subissez {} degats.".format(random.randint(5, 15)))
    sys.exit(0)

expr = returns[-1].value
expr = ast.fix_missing_locations(expr)

safe_globals = {
    "__builtins__": {},
    "len": len,
    "sum": sum,
    "str": str
}

safe_locals = {}

# Exécute tout le code (définitions, variables, etc.)
exec(compile(tree, filename, "exec"), safe_globals, safe_locals)

# Vérifie que compute existe
if "compute" not in safe_locals:
    print("ERREUR : compute() manquante.")
    sys.exit(0)

# Appelle compute()
result = safe_locals["compute"]()


# ---------------------------------------------------------
# 6. Chargement du puzzle et vérification
# ---------------------------------------------------------

with open("question.json", "r", encoding="utf-8") as f:
    puzzles = json.load(f)

puzzle = puzzles[puzzle_id]
ptype = puzzle["type"]

def check_positive(x): return isinstance(x, (int, float)) and x > 0
def check_even(x):     return isinstance(x, int) and x % 2 == 0
def check_gt100(x):    return isinstance(x, (int, float)) and x > 100
def check_string_upper(x): return isinstance(x, str) and x == "DRAGON"

checks = {
    "positive": check_positive,
    "even": check_even,
    "gt100": check_gt100,
    "string_upper": check_string_upper
}

# ---------------------------------------------------------
# 7. Résultat final
# ---------------------------------------------------------

if ptype not in checks:
    print("ERREUR : Type de puzzle inconnu :", ptype)
    sys.exit(0)

if checks[ptype](result):
    dmg = random.randint(10, 25)
    print("SUCCES : Puzzle reussi !\nVotre boule de feu touche l'ennemi.\nDegats infliges :", dmg)
else:
    dmg = random.randint(5, 15)
    print("ECHEC : Mauvaise reponse.\nVous subissez {} degats.".format(dmg))