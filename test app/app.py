from flask import Flask, render_template
from flask_socketio import SocketIO, send


app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app)


@app.route('/')
def index():
    return "Bienvenue"

@app.route('/socket')
def socket_page():
    return render_template('socket.html')

@socketio.on('message')
def handle_message(message):
    print('Received message: ' + message)

    socketio.emit('response', 'Réponse serveur: Bonjour!')


if __name__ == '__main__':
    socketio.run(app, debug=True)