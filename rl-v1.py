import mysql.connector
import time
import random
import numpy as np
import gym
from gym import spaces

# Koneksi ke Database A dan B
def connect_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="maria",
        database="db_A",
        port=3307,
        charset="utf8mb4",
        collation="utf8mb4_general_ci"
    ), mysql.connector.connect(
        host="localhost",
        user="root",
        password="maria",
        database="db_B",
        port=3307,
        charset="utf8mb4",
        collation="utf8mb4_general_ci"
    )

# Parameter RL dan Sharding
MAX_VIEWS_A = 10
RESET_TIME = 10  # Reset setiap 3 detik di DB B
ALPHA = 0.1
GAMMA = 0.9
EPSILON = 0.2

# Inisialisasi Q-table
Q_table = {}

class ShardingEnv(gym.Env):
    def __init__(self):
        super(ShardingEnv, self).__init__()
        self.db_A, self.db_B = connect_db()

        # Action space: 0 = pindahkan artikel, 1 = biarkan artikel tetap di DB A, 2 = reset artikel
        self.action_space = spaces.Discrete(3)
        
        # Observasi: total views di DB A
        self.observation_space = spaces.Discrete(16)  # Bisa disesuaikan sesuai dengan total state yang diperlukan
    
    def reset(self):
        # Memuat tabel Q dan mengatur ulang kondisi
        self.load_q_table()
        return random.choice(range(16))  # Mengembalikan state acak saat reset
    
    def step(self, action):
        cursor_A = self.db_A.cursor()
        cursor_B = self.db_B.cursor()

        # Ambil total views di DB A
        cursor_A.execute("SELECT SUM(views) FROM articles")
        total_views_A = cursor_A.fetchone()[0] or 0
        reward = 10 if total_views_A <= MAX_VIEWS_A else -10

        if action == 0 and total_views_A > MAX_VIEWS_A:
            # Pindahkan artikel dengan views tertinggi
            cursor_A.execute("SELECT id, title, content, views, modified FROM articles ORDER BY views DESC LIMIT 1")
            article = cursor_A.fetchone()
            if article:
                article_id, title, content, views, modified = article
                cursor_B.execute("INSERT INTO articles (id, title, content, views, modified) VALUES (%s, %s, %s, %s, %s)", 
                 (article_id, title, content, views, modified))
                self.db_B.commit()
                cursor_A.execute("DELETE FROM articles WHERE id = %s", (article_id,))
                self.db_A.commit()
                self.update_q_table(views, action, reward, views + 1)
        
        cursor_A.close()
        cursor_B.close()
        self.save_q_table()
        
        # Return state baru (total views di DB A) dan reward
        return total_views_A, reward, False, {}

    def render(self):
        # Render informasi saat dibutuhkan (misalnya, untuk debug)
        print("Rendering environment...")

    def load_q_table(self):
        cursor = self.db_A.cursor()
        if not self.table_exists(cursor, "q_table"):
            print("Tabel q_table tidak ditemukan, membuat baru...")
            cursor.execute("""
                CREATE TABLE q_table (
                    state INT PRIMARY KEY,
                    action0 FLOAT DEFAULT 0,
                    action1 FLOAT DEFAULT 0,
                    action2 FLOAT DEFAULT 0
                )
            """)
            self.db_A.commit()

        cursor.execute("SELECT * FROM q_table")
        rows = cursor.fetchall()
        for row in rows:
            state, action0, action1, action2 = row
            Q_table[state] = [action0, action1, action2]

        # Tambahkan state default jika kosong
        for i in range(16):
            if i not in Q_table:
                Q_table[i] = [0, 0, 0]
        cursor.close()

    def save_q_table(self):
        cursor = self.db_A.cursor()
        for state, actions in Q_table.items():
            cursor.execute("""
                INSERT INTO q_table (state, action0, action1, action2)
                VALUES (%s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE action0=VALUES(action0), action1=VALUES(action1), action2=VALUES(action2)
            """, (state, actions[0], actions[1], actions[2]))
        self.db_A.commit()
        cursor.close()

    def table_exists(self, cursor, table_name):
        cursor.execute(f"SHOW TABLES LIKE '{table_name}'")
        return cursor.fetchone() is not None

    def update_q_table(self, state, action, reward, next_state):
        if next_state not in Q_table:
            Q_table[next_state] = [0, 0, 0]

        old_value = Q_table[state][action]
        future_value = max(Q_table[next_state])
        Q_table[state][action] = old_value + ALPHA * (reward + GAMMA * future_value - old_value)

    def select_action(self, state):
        if state not in Q_table:
            Q_table[state] = [0, 0, 0]
        if random.uniform(0, 1) < EPSILON:
            return random.choice([0, 1, 2])
        return np.argmax(Q_table[state])

    def reset_views_in_db_b(self):
        # Reset views di DB B
        cursor_B = self.db_B.cursor()
        cursor_B.execute("UPDATE articles SET views = views - 1 WHERE views > 0")
        self.db_B.commit()

        # Pindahkan artikel dari DB B ke DB A jika views sudah 0
        cursor_B.execute("SELECT * FROM articles WHERE views = 0")
        articles_B = cursor_B.fetchall()

        for article in articles_B:
            article_id, title, content, views, modified = article
            cursor_A = self.db_A.cursor()
            cursor_A.execute("INSERT INTO articles (id, title, content, views, modified) VALUES (%s, %s, %s, %s, %s)", 
                 (article_id, title, content, 0, modified))
            self.db_A.commit()
            cursor_A.close()
            
            cursor_B.execute("DELETE FROM articles WHERE id = %s", (article_id,))
            self.db_B.commit()

            # Print artikel yang dipindahkan ke DB A
            print(f"Artikel ID={article_id}, Judul={title} dipindahkan ke DB A")

        cursor_B.close()

# Environment
env = ShardingEnv()

# Timer untuk eksperimen RL
start_time = time.time()
while True:
    state = env.reset()
    action = env.select_action(state)  # Memilih action berdasarkan state dan Q-table
    next_state, reward, done, _ = env.step(action)  # Menjalankan langkah di environment
    env.update_q_table(state, action, reward, next_state)  # Update Q-table
    state = next_state  # Set state baru

    # Hitung waktu berjalan sejak terakhir reset
    elapsed_time = time.time() - start_time

    # Jika waktu reset telah tercapai, reset views di DB B
    if elapsed_time >= RESET_TIME:
        # print("Reset views di DB B...")
        env.reset_views_in_db_b()

        # Reset timer setelah reset
        start_time = time.time()
