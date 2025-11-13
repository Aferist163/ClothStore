# ClothStore - Projekt Full-Stack E-commerce

To jest kompletny projekt full-stack e-commerce, stworzony od zera jako projekt akademicki. Demonstruje on pełny cykl rozwojowy: od lokalnego środowiska XAMPP do wdrożenia na "żywo" w infrastrukturze chmurowej (Render + TiDB).

Projekt zbudowany jest w oparciu o architekturę "odseparowaną" (decoupled): natywne PHP REST API jako backend oraz dynamiczny JavaScript (Fetch API) jako frontend.

**Wersja produkcyjna projektu, wdrożona na platformie Render:**

### (https://clothstore-nolk.onrender.com/)

---

## ✨ Stos Technologiczny

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-TiDB_Cloud-F29111?style=for-the-badge&logo=mysql)
![Render](https://img.shields.io/badge/Render-Deploy-46E3B7?style=for-the-badge&logo=render)
![Docker](https://img.shields.io/badge/Docker-Runtime-2496ED?style=for-the-badge&logo=docker)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

* **Frontend:** HTML5, CSS3, JavaScript (Fetch API)
* **Backend:** Natywny PHP 8.2 (REST API)
* **Baza Danych (Dev):** XAMPP (MySQL)
* **Baza Danych (Production):** TiDB Cloud (Kompatybilna z MySQL)
* **Hosting i CI/CD:** Render (Używający `Dockerfile` do konfiguracji środowiska PHP)
* **Narzędzia:** Git, GitHub, VS Code, Postman

---

## 🛠️ Funkcjonalności

* **Uwierzytelnianie:** Pełny system rejestracji, logowania, wylogowywania oraz zmiany hasła.
* **Bezpieczeństwo:** Haszowanie haseł (`password_hash`), ochrona przed SQL Injection (`bind_param`), weryfikacja sesji.
* **Role Użytkowników:** Rozróżnienie uprawnień "user" i "admin".
* **Katalog Produktów:** Dynamiczne ładowanie produktów z bazy danych.
* **Filtrowanie i Sortowanie:** Po stronie klienta (JavaScript) dla natychmiastowego filtrowania (po kategoriach) i sortowania (po cenie/nazwie).
* **Koszyk:** Dodawanie produktów, przeglądanie koszyka, obliczanie sumy całkowitej.
* **Zamówienia:** Pełny proces "Checkout", który tworzy rekordy w tabelach `orders` i `order_items` oraz czyści koszyk.
* **Panel Administratora:**
    * Zabezpieczony endpoint (dostępny tylko dla roli `admin`).
    * Pełny **CRUD** (Create, Read, Update, Delete) do zarządzania produktami.

---

## ⚙️ Instalacja i Uruchomienie (Lokalne)

Jak uruchomić ten projekt na Twoim lokalnym komputerze (np. przy użyciu XAMPP).

1.  **Sklonuj repozytorium:**
    ```bash
    git clone [https://github.com/JlemypShaman/ClothStore.git](https://github.com/JlemypShaman/ClothStore.git)
    ```

2.  **Przenieś projekt:**
    * Przenieś cały folder `ClothStore` do swojego katalogu roboczego serwera WWW (np. `C:/xampp/htdocs/`).

3.  **Skonfiguruj bazę danych:**
    * Uruchom serwer Apache i MySQL (poprzez Panel Kontrolny XAMPP).
    * Otwórz `phpMyAdmin` (zazwyczaj `http://localhost/phpmyadmin`).
    * Stwórz nową bazę danych o nazwie `clothstore` (użyj kodowania `utf8mb4_general_ci`).
    * Wybierz bazę `clothstore`.
    * Przejdź do zakładki "Import".
    * Kliknij "Wybierz plik" i wskaż plik `database/clothstore.sql` z folderu projektu.
    * Kliknij "Wykonaj" (Go).

4.  **Uruchom stronę:**
    * Otwórz przeglądarkę i przejdź pod adres:
    * **`http://localhost/ClothStore/`**

Strona powinna działać.

---

## 👥 Autorzy

* **JlemypShaman** (Timur Tkachov)
* **Aferist163** (Andrii Struk)
* **Vlad Kostyna**

* *Wsparcie i konsultacje: Gemini AI*