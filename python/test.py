import csv
import json

# Load the CSV file
with open(r'C:\Users\Pau Thing\Downloads\dataset.csv', newline='') as csvfile:
    reader = csv.reader(csvfile)
    data = [row for row in reader]

# Convert the data to JSON
json_data = json.dumps(data)

print(json_data)
