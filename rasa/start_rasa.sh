#!/bin/bash

echo "Starting Rasa Chatbot Server..."
cd "$(dirname "$0")"

# Kiểm tra virtual environment
if [ ! -d "venv" ]; then
    echo "Creating virtual environment..."
    python3 -m venv venv
    echo "Installing Rasa..."
    source venv/bin/activate
    pip install -r requirements.txt
fi

# Kích hoạt virtual environment
source venv/bin/activate

# Kiểm tra model đã train chưa
if [ ! -d "models" ]; then
    echo "Training Rasa model..."
    rasa train
fi

# Chạy Rasa server
echo "Starting Rasa server on port 5005..."
rasa run --enable-api --cors "*" --port 5005

