# 🏰 Game of Neighborhoods - Who Are You in the Realm?

**HackEPS 2025 Project by DuckHats**

> "The Old Gods and the New shall decide your dwelling."

**Game of Neighborhoods** is an immersive, AI-powered web application that acts as a "Sorting Hat" for the neighborhoods of Los Angeles. Users describe their personality, desires, and lifestyle, and our Maesters (powered by **Google Gemini AI**) analyze their decree to assign them their perfect "Realm" (neighborhood).

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

-   **Framework**: Laravel 12 (PHP 8.2)
-   **AI**: Google Gemini API custom LLM's with **Dynamic Prompting**
-   **Data Sources**: Overpass API (OpenStreetMap), Open Data LA
-   **Database**: MySQL
-   **Deployment**: Docker engine

### Frontend (The Court)

-   **Framework**: Vue.js 3 (Composition API)
-   **Routing**: Inertia.js (SPA experience with Laravel backend)
-   **Styling**: Tailwind CSS v4
-   **Maps**: Mapbox GL JS
-   **Animations**: Framer Motion Vue
-   **Speech**: Web Speech API

## 🚀 Installation & Setup

### Steps

1.  **Clone the Repository and prepare the environment**

    ```bash
    git clone https://github.com/DuckHats/UVLHackEPS2025.git
    cd UVLHackEPS2025
    cp .env.example .env
    ```

    > [!IMPORTANT]  
    > You need to configure your database and API keys in the `.env` file.

2.  **Config docker and install Dependencies**

    ```bash
    cp docker-compose-example.yml docker-compose.yml
    docker network create game_of_neighborhoods
    docker compose build
    docker compose up -d
    docker exec -it -u root <container_name> bash
    ```

3.  **Install Dependencies**
    Copy the example env file and configure your database and API keys.

    ```bash
    composer install
    npm install
    npm run build
    php artisan key:generate
    ```

4.  **Database Migration**

    ```bash
    php artisan migrate
    ```

5.  **Visit the Realm**
    Open your browser and go to `http://localhost:8010`.

## 🏗️ Architecture

### How it works

-   **[📄 Technical Overview](TECHNICAL_OVERVIEW.md)**: A detailed explanation of the internal data flow and logic.

## 🎨 Design Philosophy

The application embraces a **medieval/fantasy aesthetic** inspired by Game of Thrones:

-   Parchment-style backgrounds
-   Cinzel font for headers (medieval feel)
-   Gold/yellow accent colors (#fbbf24)
-   Dark theme with warm tones
-   Smooth animations and transitions
-   Immersive language ("Maesters", "Decree", "Realm")

## 👥 Team DuckHats

Built with ❤️ and ☕ in 24 hours by DuckHats
