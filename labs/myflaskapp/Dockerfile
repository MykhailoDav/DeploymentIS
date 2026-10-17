# Використовуємо базовий образ з Python
FROM python:3.11-slim
RUN apt-get update && apt-get install -y --no-install-recommends iputils-ping && rm -rf /var/lib/apt/lists/*
# Встановлюємо робочу директорію всередині контейнера
WORKDIR /app
# Копіюємо файли проєкту у контейнер
COPY requirements.txt .
COPY app.py .
# Встановлюємо залежності
RUN pip install --no-cache-dir -r requirements.txt
# Відкриваємо порт 5000
EXPOSE 5000
# Команда запуску
CMD ["python", "app.py"]