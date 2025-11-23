# El Joc de Barris - HackEPS 2025 🦆🎩

## 1. Explicació Tècnica 🛠️

### 🏗️ Obtenció de dades

-   **Font Principal**: Open Data LA.
-   **Datasets**: Hem utilitzat fitxers CSV amb dades reals sobre:
    -   Preu del lloguer (€/m²).
    -   Incidents i seguretat.
    -   Densitat de població.
    -   Oferta d'oci i restauració.
    -   Transport públic.

### 🧹 Qualitat de les dades

-   **Normalització**: Hem creat un script per estandarditzar els noms dels barris i creuar dades de diferents fonts.
-   **Escalat**: Totes les mètriques s'han convertit a una escala de KPI unificada (0-100) per poder comparar-les fàcilment.
-   **Neteja**: Eliminació de valors nuls i outliers que podien distorsionar el _matching_.

### ⚙️ El "Core" (Motor de Recomanació)

-   **Algorisme de Matching**:
    -   Cada "Arquetip" d'usuari (ex: _The Night Owl_, _The Family Guardian_) té uns pesos de preferència definits.
    -   Calculem la distància vectorial entre les preferències de l'usuari i els KPIs de cada barri.
    -   El barri amb la menor distància (o major puntuació de similitud) és el guanyador.
-   **Intel·ligència Artificial (Gemini)**:
    -   Utilitzem l'API de Google Gemini per generar el "Veredicte".
    -   L'IA no només justifica l'elecció amb dades, sinó que ho fa adoptant una personalitat èpica/medieval ("El Decret Reial").

### 💻 Stack Tecnològic

-   **Backend**: Laravel 10 (PHP).
-   **Frontend**: Vue.js 3 (Composition API) + Inertia.js.
-   **Estils**: TailwindCSS (Disseny _Dark/Gold Premium_).
-   **Mapes**: Mapbox GL JS + GeoJSON de barris.
-   **IA**: Google Gemini API.
-   **Imatges**: Wikimedia Commons API (Proxy propi per evitar CORS).

### 🧠 Aprenentatge

-   Integració de models LLM per generar contingut dinàmic i personalitzat en temps real.
-   Gestió de dades geoespacials i visualització interactiva en mapes.
-   Importància de l'UX/UI per convertir dades fredes en una experiència immersiva ("Gamificació").

---

## 2. Demo en Viu 🚀

### 🎭 Flux de l'Usuari

1. **Landing Page**: Introducció immersiva.
2. **Selecció d'Arquetip**: L'usuari tria el seu perfil (ex: _The Nature Lover_).
3. **Processament**: Càlcul de puntuacions i generació de text amb IA.
4. **Resultat (El Decret)**:
    - **Mapa Tèrmic**: Visualització del barri guanyador.
    - **Carrusel d'Imatges**: Fotos reals del barri (Wikimedia).
    - **Estadístiques**: Gràfics de seguretat, preu, vida nocturna, etc.
    - **Comparador**: Eina per comparar el barri recomanat amb qualsevol altre del mapa.
    - **Exportació**: Generació de PDF ("Pergamí") i compartir per WhatsApp.

### 🎁 Client Sorpresa (Integració)

> _Espai reservat per explicar com hem adaptat l'algorisme per al nou perfil anunciat diumenge a les 09:00h._

-   **Nou Arquetip**: [Nom del Client]
-   **Ajustos**: Modificació dels pesos dels KPIs per prioritzar [Necessitat Clau del Client].

---

_Gràcies per la vostra atenció!_ 🦆
