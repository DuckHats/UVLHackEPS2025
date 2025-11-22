# 🏰 El Joc de Barris - Who Are You in the Realm?

**HackEPS 2025 Project by DuckHats**

> "The Old Gods and the New shall decide your dwelling."

**El Joc de Barris** is an immersive, AI-powered web application that acts as a "Sorting Hat" for the neighborhoods of Lleida. Users describe their personality, desires, and lifestyle, and our Maesters (powered by Google Gemini AI) analyze their decree to assign them their perfect "Realm" (neighborhood).

## ✨ Features

-   **🎙️ Voice-Activated Decrees**: Dictate your story using the Web Speech API. Supports **English**, **Spanish**, and **Catalan** with automatic language detection.
-   **🧠 AI Analysis**: Uses **Google Gemini 1.5 Flash** to analyze user input and match it with the unique characteristics of Lleida's neighborhoods.
-   **🗺️ Interactive Realm Map**: Visualizes the assigned neighborhood and heat zones using **Mapbox GL**.
-   **📜 Medieval Aesthetic**: A fully immersive UI with parchment textures, Cinzel typography, and magical animations.
-   **📤 Share Your Destiny**:
    -   **WhatsApp**: Share your result with a custom link (includes country code selector).
    -   **Email**: Send a "raven" with your decree.
    -   **PDF**: Download a parchment of your result.
-   **📱 Responsive Design**: Works on desktop and mobile devices.

## 🛠️ Tech Stack

-   **Backend**: Laravel 10 (PHP 8.2)
-   **Frontend**: Vue.js 3 (Composition API)
-   **Full Stack Glue**: Inertia.js
-   **Styling**: Tailwind CSS v4
-   **Maps**: Mapbox GL JS
-   **AI**: Google Gemini API
-   **Speech**: Web Speech API

## 🚀 Installation & Setup

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   PostgreSQL or MySQL

### Steps

1.  **Clone the Repository**

    ```bash
    git clone https://github.com/DuckHats/UVLHackEPS2025.git
    cd UVLHackEPS2025
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration**
    Copy the example env file and configure your database and API keys.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    **Required Keys in `.env`**:

    ```env
    # Database
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=your_db_name
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password

    # Google Gemini AI
    GEMINI_API_KEY=your_gemini_api_key

    # Mapbox
    VITE_MAPBOX_TOKEN=your_mapbox_public_token
    ```

4.  **Database Migration**

    ```bash
    php artisan migrate
    ```

5.  **Run the Application**
    Start the backend server and frontend bundler:

    ```bash
    # Terminal 1
    php artisan serve

    # Terminal 2
    npm run dev
    ```

6.  **Visit the Realm**
    Open your browser and go to `http://localhost:8000`.

## 📖 How It Works

1.  **Enter the Hall**: Land on the homepage and see the question "Who Are You in the Realm?".
2.  **Speak Your Truth**: Use the microphone button to dictate your personality and preferences, or type them manually.
3.  **Consult the Maesters**: Submit your form. The system sends your input to Gemini for analysis.
4.  **Receive Your Decree**: View your assigned neighborhood on the map, read the justification, and see your compatibility score.
5.  **Save & Share**: Use the "Save Results" button to share your destiny via WhatsApp, Email, or download it as a PDF.

## 👥 Team DuckHats

Built with ❤️ and ☕ for HackEPS 2025.
