from flask import Flask, jsonify, request
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
import pandas as pd
from prophet import Prophet
import json
import os

app = Flask(__name__)
CORS(app)

# Database configuration for MySQL
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/brgy_bangkal_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)

# Define your database models
class MaleFemale(db.Model):
    __tablename__ = 'v1_male_female'
    age = db.Column(db.Integer, primary_key=True)
    year_2019 = db.Column("2019", db.Integer)
    year_2020 = db.Column("2020", db.Integer)
    year_2021 = db.Column("2021", db.Integer)
    year_2022 = db.Column("2022", db.Integer)
    year_2023 = db.Column("2023", db.Integer)
    year_2024 = db.Column("2024", db.Integer)
    community_services = db.Column(db.String(512))
    gender = db.Column(db.String(512))

class Male(db.Model):
    __tablename__ = 'v2_male'
    age = db.Column(db.Integer, primary_key=True)
    year_2019 = db.Column("2019", db.Integer)
    year_2020 = db.Column("2020", db.Integer)
    year_2021 = db.Column("2021", db.Integer)
    year_2022 = db.Column("2022", db.Integer)
    year_2023 = db.Column("2023", db.Integer)
    year_2024 = db.Column("2024", db.Integer)
    community_services = db.Column(db.String(512))  # Add this line
    gender = db.Column(db.String(1), default='M')  # Add this line

class Female(db.Model):
    __tablename__ = 'v3_female'
    age = db.Column(db.Integer, primary_key=True)
    year_2019 = db.Column("2019", db.Integer)
    year_2020 = db.Column("2020", db.Integer)
    year_2021 = db.Column("2021", db.Integer)
    year_2022 = db.Column("2022", db.Integer)
    year_2023 = db.Column("2023", db.Integer)
    year_2024 = db.Column("2024", db.Integer)
    community_services = db.Column(db.String(512))  # Add this line
    gender = db.Column(db.String(1), default='F')  # Add this line

def forecast_table(records, next_year, table_name):
    data = {
        "AGE": [],
        "2019": [],
        "2020": [],
        "2021": [],
        "2022": [],
        "2023": [],
        "2024": [],
        "Community_Services": [],
        "Gender": []  # Add Gender here
    }

    for record in records:
        data["AGE"].append(record.age)
        data["2019"].append(record.year_2019)
        data["2020"].append(record.year_2020)
        data["2021"].append(record.year_2021)
        data["2022"].append(record.year_2022)
        data["2023"].append(record.year_2023)
        data["2024"].append(record.year_2024)
        
        # Fetch Community_Services and Gender
        data["Community_Services"].append(record.community_services)  # No placeholder needed
        data["Gender"].append(record.gender)  # Add Gender from the record

    df = pd.DataFrame(data)
    predictions = []

    for index, row in df.iterrows():
        df_prophet = pd.DataFrame({
            'ds': pd.date_range(start='2019-01-01', periods=6, freq='Y'),
            'y': row[1:7].values
        })

        model = Prophet()
        model.fit(df_prophet)

        future = model.make_future_dataframe(periods=1, freq='Y')
        forecast = model.predict(future)

        prediction_next_year = forecast[forecast['ds'].dt.year == next_year]['yhat'].values[0]

        predictions.append({
            'AGE': int(row['AGE']),
            'Community_Services': row['Community_Services'],  # Include this field
            'Gender': row['Gender'],  # Include Gender here
            str(next_year): int(round(prediction_next_year))
        })

        setattr(record, f'year_{next_year}', prediction_next_year)

    db.session.commit()

    output_folder = 'forecasts'
    os.makedirs(output_folder, exist_ok=True)

    output_file = os.path.join(output_folder, f'forecast_{table_name}_{next_year}.json')

    with open(output_file, 'w') as f:
        json.dump(predictions, f, indent=4)

@app.route('/forecast', methods=['GET'])
def forecast():
    current_year = request.args.get('year', type=int)

    if current_year is None or current_year < 2024:
        return jsonify({"error": "Year must be 2024 or later to forecast."}), 400

    next_year = current_year + 1

    # Forecast for MaleFemale
    records_mf = MaleFemale.query.all()
    forecast_table(records_mf, next_year, "v1_male_female")

    # Forecast for Male
    records_m = Male.query.all()
    forecast_table(records_m, next_year, "v2_male")

    # Forecast for Female
    records_f = Female.query.all()
    forecast_table(records_f, next_year, "v3_female")

    return jsonify({"message": "Forecasts generated successfully."})

if __name__ == '__main__':
    app.run(debug=True)
