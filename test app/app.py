from flask import Flask, render_template, session, request, redirect, url_for
from flask_socketio import SocketIO, emit, join_room, leave_room, send


app = Flask(__name__)
app.config['SECRET_KEY'] = 'secret!'
socketio = SocketIO(app)


@app.route('/')
def index():
    return "Bienvenue"

@app.route('/socket')
def socket_page():
    return render_template('socket.html')

@app.route('/client')
def client_page():
    return render_template('client.html')

@app.route('/mobile')
def AR_page():
    return render_template('mobile.html')

def messageReceived(methods=['GET', 'POST']):
    print('message reçus')

@socketio.on('message')
def handle_message(message):
    print('Received message: ' + message)

    socketio.emit('response', 'Réponse serveur: Bonjour!')


if __name__ == '__main__':
    socketio.run(app, debug=True)