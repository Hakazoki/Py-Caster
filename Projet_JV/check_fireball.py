import sys
import ast
import random

filename = sys.argv[1]

with open(filename, "r") as f:
    code = f.read()

try:
    ast.parse(code)
except SyntaxError:
    print("ERREUR : Code invalide.\nVous subissez {} degats.".format(random.randint(5, 15)))
    sys.exit(0)

local_env = {}
try:
    exec(code, {}, local_env)
    result = local_env["compute"]()
except Exception as e:
    print("ERREUR à l'exécution : {}.\nVous subissez {} degats.".format(e, random.randint(5, 15)))
    sys.exit(0)

if isinstance(result, (int, float)) and result > 0:
    dmg = random.randint(10, 25)
    print("SUCCÈS : Puzzle reussi !\nVotre boule de feu touche l'ennemi.\nDégâts infligés : {}.".format(dmg))
else:
    dmg = random.randint(5, 15)
    print("ÉCHEC : compute() ne renvoie pas un nombre positif.\nVous subissez {} dégâts.".format(dmg))

print("DEBUG:", repr(code))