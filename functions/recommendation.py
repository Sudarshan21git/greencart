import math
import mysql.connector
from flask import Flask, request, jsonify

app = Flask(__name__)

# Connect to the MySQL Database
def connect_to_db():
    conn = mysql.connector.connect(
        host='localhost',         # e.g., 'localhost'
        user='root',              # e.g., 'root'
        password='',              # your password
        database='greencart'      # e.g., 'plant_recommendations'
    )
    return conn

# Fetch User Ratings Data from MySQL Database
def fetch_user_ratings():
    conn = connect_to_db()
    cursor = conn.cursor(dictionary=True)
    
    cursor.execute('SELECT user_id, product_id, rating FROM reviews')  # Replace with your table and column names
    data = cursor.fetchall()
    
    conn.close()
    
    user_item_matrix = {}
    for row in data:
        user_id = row['user_id']
        item_id = row['product_id']
        rating = row['rating']
        
        if user_id not in user_item_matrix:
            user_item_matrix[user_id] = {}
        
        user_item_matrix[user_id][item_id] = rating
    
    return user_item_matrix

# Manually Compute Cosine Similarity Between Two Vectors
def cosine_similarity_manual(vector_a, vector_b):
    dot_product = sum(a * b for a, b in zip(vector_a, vector_b))
    magnitude_a = math.sqrt(sum(a**2 for a in vector_a))
    magnitude_b = math.sqrt(sum(b**2 for b in vector_b))
    
    if magnitude_a == 0 or magnitude_b == 0:
        return 0  # No similarity if one vector is all zeros
    
    return dot_product / (magnitude_a * magnitude_b)

# Compute User-User Similarity Matrix
def compute_user_similarity_matrix(user_item_matrix):
    users = list(user_item_matrix.keys())
    similarity_matrix = {}
    
    for user1 in users:
        similarity_matrix[user1] = {}
        for user2 in users:
            if user1 == user2:
                similarity_matrix[user1][user2] = 1  # Perfect similarity with self
            else:
                ratings_user1 = [user_item_matrix[user1].get(item, 0) for item in user_item_matrix[user1]]
                ratings_user2 = [user_item_matrix[user2].get(item, 0) for item in user_item_matrix[user2]]
                
                similarity_matrix[user1][user2] = cosine_similarity_manual(ratings_user1, ratings_user2)
    
    return similarity_matrix

# Recommend Products Based on Similar Users' Ratings
def recommend_products(user_id, user_item_matrix, similarity_matrix, top_n=4):
    if user_id not in user_item_matrix:
        return {}
    
    user_ratings = user_item_matrix[user_id]
    sim_scores = similarity_matrix[user_id]
    
    recommended_items = {}
    
    # Recommend products rated by similar users (without predicting ratings)
    for other_user, similarity in sim_scores.items():
        if other_user == user_id:
            continue
        
        # Get items rated by similar user
        other_user_ratings = user_item_matrix[other_user]
        
        for item, rating in other_user_ratings.items():
            if item not in user_ratings:  # Recommend only items that the user has not rated
                if item not in recommended_items:
                    recommended_items[item] = 0
                # Add product to recommendations based on the similarity score
                recommended_items[item] += similarity  # Use similarity as the weight
    
    # Sort the recommended items by their weighted score
    sorted_recommendations = dict(sorted(recommended_items.items(), key=lambda x: x[1], reverse=True)[:top_n])
    
    return sorted_recommendations

@app.route('/recommendations', methods=['POST'])
def recommend():
    user_id = request.form.get('user_id')  # Get the user_id from the POST request
    if not user_id:
        return jsonify({"error": "No user ID provided"}), 400
    
    user_id = int(user_id)  # Convert user_id to integer
    
    # Fetch data from the database
    user_item_matrix = fetch_user_ratings()
    
    # Compute the similarity matrix
    similarity_matrix = compute_user_similarity_matrix(user_item_matrix)
    
    # Get recommendations for the user
    recommendations = recommend_products(user_id, user_item_matrix, similarity_matrix)
    
    return jsonify(recommendations)

if __name__ == "__main__":
    app.run(debug=True)
