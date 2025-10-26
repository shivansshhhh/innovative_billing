# 💊 Pharmacy Billing and Inventory System

A real-time pharmacy billing and inventory management system built with **PHP** for backend operations and **Java** for the customer-facing Android application.

## 🧩 Features

- 🔄 **Real-Time Inventory Sync**: Stock data updates live between the billing system and the customer app.
- 📱 **Customer Android App**: Built with Java, allows users to check medicine availability at nearby pharmacies.
- 🧾 **Billing Module**: Fast and user-friendly interface for pharmacists to generate and manage bills.
- 📦 **Stock Management**: Add, update, and track medicine quantities, expiry dates, and purchase history.
- 🔍 **Search & Filter**: Quick search for medicines, sorted by availability and pharmacy location.
- 🔐 **Secure Access**: Role-based login system for pharmacists and customers.

## 🛠️ Technologies Used

| Component         | Technology      |
|------------------|-----------------|
| Backend (Billing)| PHP + MySQL     |
| Customer App     | Java (Android)  |
| API Communication| REST (JSON)     |

## 📱 Android App (Java)

The Android app connects directly to the backend via RESTful APIs and allows:
- Checking availability of medicines
- Viewing pharmacy location details
- Making medicine requests or reservations

## 🖥️ Billing System (PHP)

The web-based dashboard allows pharmacists to:
- View and manage stock in real time
- Generate customer bills and apply discounts
- Update medicine records efficiently

## 🔧 Setup Instructions

### 📌 Backend (PHP)

1. Clone the repository.
2. Import the database from `database.sql`.
3. Configure your database credentials in `config.php`.
4. Host it using Apache/XAMPP/Laragon.

### 📌 Android App (Flutter)

1. Open the `/pharmacy_customer_app` folder in Flutter set in VS Code or Android Studio.
2. Update the base API URL in the app’s constants file. (Ngrok URL)
3. Build and run on an emulator or Android device.
      flutter clean
      flutter pub get
      flutter run
   
## 🚀 Future Enhancements

- Online ordering & delivery tracking
- Admin dashboard with analytics
- QR code scanning for billing

### Not a MIT Licensed project copyrights are priserved copying or stealing repo is prohabited.

### Still in the Work.....

**Developed by Shivansh Panwar and Kunal Kumar Dev**  
