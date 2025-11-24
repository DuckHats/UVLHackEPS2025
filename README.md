# 🏰 El Joc de Barris - Who Are You in the Realm?

**HackEPS 2025 Project by DuckHats**

> "The Old Gods and the New shall decide your dwelling."

**El Joc de Barris** is an immersive, AI-powered web application that acts as a "Sorting Hat" for the neighborhoods of Los Angeles. Users describe their personality, desires, and lifestyle, and our Maesters (powered by **Google Gemini AI**) analyze their decree to assign them their perfect "Realm" (neighborhood).

## ✨ Features

-   **🎙️ Voice-Activated Decrees**: Dictate your story using the Web Speech API. Supports **English**, **Spanish**, and **Catalan** with automatic language detection.
-   **📜 Scrolls of Wisdom (Dynamic AI)**: Our Maesters use **Dynamic Prompts** stored in the ancient archives (`storage/prompts/`) to analyze your soul. The system adapts to new archetypes and KPIs without needing a code deploy.
-   **🗺️ Interactive Realm Map**: Visualizes the assigned neighborhood and heat zones using **Mapbox GL**.
-   **🌾 Bounty of the Land (Real-time Amenities)**: Displays local amenities (taverns, healers, markets) using **Overpass API** data. We fetch real-time data to ensure your realm has everything you need.
-   **⚖️ The Great Debate (Comparison)**: Click any neighborhood on the map to compare it with your recommended match. See how your assigned realm stacks up against others in terms of safety, nature, and commerce.
-   **🏰 Medieval Aesthetic**: A fully immersive UI with parchment textures, Cinzel typography, and magical animations.
-   **🦅 Raven's Whisper (Sharing)**:
    -   **WhatsApp**: Send a direct message via API to your allies.
    -   **Email**: Dispatch a digital raven with your decree.
    -   **PDF**: Download a beautifully formatted parchment of your result.
-   **📱 Responsive Design**: Works seamlessly on desktop and mobile devices.

## 🛠️ Tech Stack

### Backend (The Keep)

-   **Framework**: Laravel 10 (PHP 8.2)
-   **AI**: Google Gemini API (1.5 Pro) with **Dynamic Prompting**
-   **Data Sources**: Overpass API (OpenStreetMap), Open Data LA
-   **Architecture**: Service-oriented with `GeminiService`, `OverpassService`, and `NeighborhoodService`.

### Frontend (The Court)

-   **Framework**: Vue.js 3 (Composition API)
-   **Routing**: Inertia.js (SPA experience with Laravel backend)
-   **Styling**: Tailwind CSS v4
-   **Maps**: Mapbox GL JS
-   **Animations**: Framer Motion Vue
-   **Speech**: Web Speech API
-   **Deployment**: Docker

### Code Quality (The Laws)

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
    Open your browser and go to `http://localhost:8010`.

## 📖 How It Works

1.  **Enter the Hall**: Land on the homepage and see the question "Who Are You in the Realm?".
2.  **Speak Your Truth**: Use the microphone button to dictate your personality and preferences, or type them manually.
3.  **Consult the Maesters**: Submit your form. The system:
    -   **Analyzes** your input using Gemini AI and the `analyze_profile.md` prompt.
    -   **Extracts** your personality archetype and KPIs.
    -   **Scores** neighborhoods based on your preferences.
    -   **Enriches** data with real-time amenities from Overpass API.
4.  **Receive Your Decree**: View your assigned neighborhood on the map, read the AI-generated justification, and see your compatibility score.
5.  **Explore & Compare**: Click other neighborhoods on the map to compare them with your match.
6.  **Save & Share**: Use the "Save Results" button to share your destiny via WhatsApp, Email, or download it as a PDF.

## 🏗️ Architecture

### Backend Services

-   **GeminiService**: Handles all AI interactions, reading prompts from `storage/prompts/`.
-   **NeighborhoodService**: Manages neighborhood data, scoring, and matching logic.
-   **OverpassService**: Fetches real-time amenity data (pharmacies, schools, parks) from OpenStreetMap.
-   **ResultProcessingService**: Orchestrates the complete analysis workflow.
-   **PdfService**: Generates beautiful PDF reports.
-   **EmailService** & **WhatsAppService**: Handle sharing functionality.
-   **[📄 Technical Overview](TECHNICAL_OVERVIEW.md)**: A detailed explanation of the internal data flow and logic.

### Frontend Components

-   **Pages**: `Home.vue`, `Result.vue`, `Contact.vue`
-   **Layouts**: `GameLayout.vue` (medieval theme wrapper)
-   **Components**: `Map.vue`, `AmenityCard.vue`, `AmenityGrid.vue`, `StatCard.vue`
-   **Composables**: `useTheme.js` (centralized styling constants)

## 🎨 Design Philosophy

The application embraces a **medieval/fantasy aesthetic** inspired by Game of Thrones:

-   Parchment-style backgrounds
-   Cinzel font for headers (medieval feel)
-   Gold/yellow accent colors (#fbbf24)
-   Dark theme with warm tones
-   Smooth animations and transitions
-   Immersive language ("Maesters", "Decree", "Realm")

## 📜 Chronicles of Updates (Recent Changes)

### The Age of Refactoring (November 2025)

-   ✅ **Dynamic Prompts**: Externalized AI logic to Markdown files for easier tuning.
-   ✅ **Overpass API Integration**: Real-time data fetching for amenities (Pharmacies, Hospitals, Schools).
-   ✅ **Realm Comparison**: Added ability to compare the recommended neighborhood with others on the map.
-   ✅ **Raven's Whisper**: Improved WhatsApp sharing with direct API integration.
-   ✅ **Code Quality**: Centralized constants, improved service layer, and better error handling.

## 👥 Team DuckHats

Built with ❤️ and ☕ for HackEPS 2025.
