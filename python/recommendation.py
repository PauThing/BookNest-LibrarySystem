import json
import pyodbc
import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.metrics import pairwise_distances
from sklearn.metrics import mean_squared_error
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics.pairwise import cosine_similarity

#connect to your SQL Server database
conn = pyodbc.connect(
    f'DRIVER=ODBC Driver 17 for SQL Server;'
    f'SERVER=LAPTOP-FOGH91GN;'
    f'DATABASE=LibrarySystem;'
    f'UID=sa;'
    f'PWD=sqlserverPT2001'
)

#query to retrieve the borrowing history data
query = "SELECT user_id, ISBN FROM borrowinghistory"

#execute the SQL query and fetch data into a pandas DataFrame
df = pd.read_sql(query, conn)

#close the database connection
conn.close()

#create the User-Item Interaction Matrix
interaction_matrix = pd.pivot_table(df, values=None, index='user_id', columns='ISBN', aggfunc='size', fill_value=0)

#replace NaN (no interactions) with 0
interaction_matrix.fillna(0, inplace=True)

#calculate book similarities using cosine similarity
book_similarity = cosine_similarity(interaction_matrix.T)

#create a DataFrame to store item similarities
book_similarity_df = pd.DataFrame(book_similarity, index=interaction_matrix.columns, columns=interaction_matrix.columns)

#calculate User Similarity Matrix based on user interactions
user_similarity_matrix = cosine_similarity(interaction_matrix)

#get the number of users
unique_user_ids = df['user_id'].unique()

#number of similar users to consider
N = 100

#create a dictionary to store recommendations for each user
recommendations_dict = {}

#reset the index to ensure it's continuous
df.reset_index(drop=True, inplace=True)

#create a DataFrame to store user indices
user_indices_df = df.groupby('user_id').head(1).reset_index(drop=True)

for target_user_id in unique_user_ids:
    #find the target user's index in the DataFrame
    target_user_indices = user_indices_df.index[user_indices_df['user_id'] == target_user_id]
    if not target_user_indices.empty:
        target_user_index = target_user_indices[0]

    #find the most similar users to the target user
    similar_users = user_similarity_matrix[target_user_index].argsort()[::-1]

    #get the top N most similar users (excluding the target user itself)
    top_similar_users = similar_users[1:N+1]

    #generate recommendations for the target user based on similar users' interactions
    target_user_interactions = interaction_matrix.iloc[target_user_index]

    #sum the interactions of similar users for each book
    recommendations = (interaction_matrix.iloc[top_similar_users] * target_user_interactions).sum()

    #sort the recommendations by score
    sorted_recommendations = recommendations.sort_values(ascending=False)

    #store the recommendations in the dictionary
    recommendations_dict[target_user_id] = sorted_recommendations

#convert Series to dictionary
for user_id, recommendations in recommendations_dict.items():
    recommendations_dict[user_id] = recommendations.to_dict()

#convert the recommendation data to JSON format
# recommendations_json = json.dumps(recommendations_dict)

with open('recommendations.json', 'w') as json_file:
    json.dump(recommendations_dict, json_file)
