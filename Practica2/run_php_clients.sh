#!/usr/bin/env bash
# run-php-servers.sh
# Lanza 5 instancias de php main.php en diferentes puertos dentro de tmux,
# en paneles verticales con 0.5s de diferencia entre cada conexión.


# COMO USAR:
# ./run_php_clients.sh
# Requiere tener instalado tmux
# PARA NAVEGAR ENTRE PANELES: Ctrl+b y luego flechas abajo y arriba


SESSION="phpservers"
HOST="127.0.0.1"
BASE_PORT=5000
N=5

# Si ya existe la sesión, la matamos (para reiniciar limpia)
tmux has-session -t $SESSION 2>/dev/null
if [ $? -eq 0 ]; then
    tmux kill-session -t $SESSION
fi

# Crear nueva sesión con el primer comando
tmux new-session -d -s $SESSION "php main.php --ux-only $HOST:$((BASE_PORT))"

# Crear los demás paneles (división vertical con retraso de 0.5s)
for i in $(seq 2 $N); do
    sleep 0.5
    tmux split-window -h -t $SESSION "php main.php --ux-only $HOST:$((BASE_PORT))"
    tmux select-layout -t $SESSION even-vertical
done

# Adjuntar a la sesión
tmux attach -t $SESSION