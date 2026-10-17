import pymysql
from flask import Flask

app = Flask(__name__)

@app.route("/")
def hello():
    return "<h1>Hello from Flask v3.0!</h1>"

@app.route("/db")
def db():
    try:
        conn = pymysql.connect(host="db", user="root", password="1234", connect_timeout=3)
        with conn.cursor() as cur:
            cur.execute("SELECT VERSION()")
            version = cur.fetchone()[0]
        conn.close()
        return f"<h1>Connected to MySQL {version} at host 'db'</h1>"
    except Exception as e:
        return f"<h1>DB connection failed: {e}</h1>", 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
