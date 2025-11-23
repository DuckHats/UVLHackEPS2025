# 🏰 El Joc de Barris - Who Are You in the Realm?

**HackEPS 2025 Project by DuckHats**

> "The Old Gods and the New shall decide your dwelling."

**El Joc de Barris** is an immersive, AI-powered web application that acts as a "Sorting Hat" for the neighborhoods of LA. Users describe their personality, desires, and lifestyle, and our Maesters (powered by Google Gemini AI) analyze their decree to assign them their perfect "Realm" (neighborhood).

## ✨ Features

-   **🎙️ Voice-Activated Decrees**: Dictate your story using the Web Speech API. Supports **English**, **Spanish**, and **Catalan** with automatic language detection.
-   **🧠 AI Analysis**: Uses **Google Gemini 1.5 Pro** to analyze user input and match it with the unique characteristics of LA's neighborhoods.
-   **🗺️ Interactive Realm Map**: Visualizes the assigned neighborhood and heat zones using **Mapbox GL**.
-   **📊 Real-time Amenities**: Displays local amenities (restaurants, parks, etc.) using **Overpass API** data.
-   **⚖️ Neighborhood Comparison**: Click any neighborhood on the map to compare it with your recommended match.
-   **📜 Medieval Aesthetic**: A fully immersive UI with parchment textures, Cinzel typography, and magical animations.
-   **📤 Share Your Destiny**:
    -   **WhatsApp**: Share your result with a custom link (includes country code selector).
    -   **Email**: Send a "raven" with your decree.
    -   **PDF**: Download a beautifully formatted parchment of your result.
-   **📱 Responsive Design**: Works seamlessly on desktop and mobile devices.

## 🛠️ Tech Stack

### Backend

-   **Framework**: Laravel 10 (PHP 8.2)
-   **AI**: Google Gemini API (1.5 Pro)
-   **Data Sources**: Overpass API (OpenStreetMap), Open Data LA
-   **Architecture**: Service-oriented with Form Requests and dedicated service classes

### Frontend

-   **Framework**: Vue.js 3 (Composition API)
-   **Routing**: Inertia.js (SPA experience with Laravel backend)
-   **Styling**: Tailwind CSS v4
-   **Maps**: Mapbox GL JS
-   **Animations**: Framer Motion Vue
-   **Speech**: Web Speech API

### Code Quality

-   **Constants Management**: Centralized `AppConstants` class
-   **Reusable Components**: `AmenityCard`, `AmenityGrid`, `StatCard`
-   **Composables**: `useTheme` for consistent styling
-   **Form Validation**: Laravel Form Requests
-   **Service Layer**: Dedicated services for business logic

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
    GEMINI_ENABLED=true

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
3.  **Consult the Maesters**: Submit your form. The system:
    -   Analyzes your input using Gemini AI
    -   Extracts your personality archetype and KPIs
    -   Filters top candidate neighborhoods
    -   Scores each neighborhood based on your preferences
    -   Enriches data with real-time amenities from Overpass API
4.  **Receive Your Decree**: View your assigned neighborhood on the map, read the AI-generated justification, and see your compatibility score.
5.  **Explore & Compare**: Click other neighborhoods on the map to compare them with your match.
6.  **Save & Share**: Use the "Save Results" button to share your destiny via WhatsApp, Email, or download it as a PDF.

## 🏗️ Architecture

### Backend Services

-   **GeminiService**: Handles all AI interactions (profile analysis, justification generation)
-   **NeighborhoodService**: Manages neighborhood data, scoring, and matching logic
-   **OverpassService**: Fetches real-time amenity data from OpenStreetMap
-   **ResultProcessingService**: Orchestrates the complete analysis workflow
-   **PdfService**: Generates beautiful PDF reports
-   **EmailService** & **WhatsAppService**: Handle sharing functionality

### Frontend Components

-   **Pages**: `Home.vue`, `Result.vue`, `Contact.vue`
-   **Layouts**: `GameLayout.vue` (medieval theme wrapper)
-   **Components**: `Map.vue`, `AmenityCard.vue`, `AmenityGrid.vue`, `StatCard.vue`
-   **Composables**: `useTheme.js` (centralized styling constants)

### Data Flow

```
User Input → AnalyzeProfileRequest (validation)
          → ResultProcessingService
          → GeminiService (AI analysis)
          → NeighborhoodService (matching)
          → OverpassService (amenities)
          → Result Page (Inertia.js)
```

## 🎨 Design Philosophy

The application embraces a **medieval/fantasy aesthetic** inspired by Game of Thrones:

-   Parchment-style backgrounds
-   Cinzel font for headers (medieval feel)
-   Gold/yellow accent colors (#fbbf24)
-   Dark theme with warm tones
-   Smooth animations and transitions
-   Immersive language ("Maesters", "Decree", "Realm")

## 👥 Team DuckHats

Built with ❤️ and ☕ for HackEPS 2025.

---

## 📝 Recent Improvements

### Code Refactoring (November 2025)

-   ✅ Centralized constants in `AppConstants` class
-   ✅ Extracted fallback data to `FallbackNeighborhoodProvider`
-   ✅ Created reusable Vue components (reduced Result.vue by 6%)
-   ✅ Implemented Form Request validation
-   ✅ Moved business logic to service layer
-   ✅ Added comprehensive PHPDoc and JSDoc comments
-   ✅ Improved error handling and logging throughout
