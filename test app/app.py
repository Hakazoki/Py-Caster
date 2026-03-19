from flask import Flask
from flask_socketio import SocketIO, emit
import eventlet

eventlet.monkey_patch()

app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app, async_mode='eventlet', cors_allowed_origins="*")

game_state = {
    'vie_ennemi': 100,
    'vie_joueur': 100
}

def print_succes(hp):
    print("\n" + r"""
       * * *
     * \  |  /   * 
       * -- * -- *
     * /  |  \   *
       * * *
    """ + f"\n>>> SORTILLEGE LANCER AVEC SUCCES ! VIE DU MONSTRE : {hp} <<<\n")

def print_echec(hp):
    print("\n" + r"""
          _ ._  _ , _ ._
        (_ ' ( `  )_  .__)
      ( (  (    )   `)  ) _)
     (__ (_   (_ . _) _) ,__)
         `~~`\ ' . /`~~`
              ;   ;
              /   \
_____________/_ __ \_____________
    """ + f"\n>>> ECHEC DU SORTILLEGE IL VOUS EXPLOSE EN MAIN ! VIE DU JOUEUR : {hp} <<<\n")

def print_restart():
    print("\n" + r"""
     ==========================
     |Redemarrage de la partie|
     ==========================
    """ + "\n")

@app.route('/')
def index():
    return "Serveur WebSocket actif."

@socketio.on('connect')
def connection():
    emit('sync_vie', game_state)

@socketio.on('reussite_sortilege')
def handle_incantation(data):
    global game_state
    
    reussite = data.get('reussite', False)
    
    if reussite:
        game_state['vie_ennemi'] = max(0, game_state['vie_ennemi'] - 20)
        print_succes(game_state['vie_ennemi'])
    else:
        game_state['vie_joueur'] = max(0, game_state['vie_joueur'] - 10)
        print_echec(game_state['vie_joueur'])
        
    socketio.emit('sync_vie', game_state)

@socketio.on('restart_game')
def restart_jeu():
    global game_state
    
    print_restart()
    game_state['vie_ennemi'] = 100
    game_state['vie_joueur'] = 100
    
    socketio.emit('sync_vie', game_state)

if __name__ == '__main__':
    socketio.run(app, host='192.168.10.210', port=5000)


