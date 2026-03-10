# 🍼 Żłobki Polska — Motyw WordPress

> Nowoczesna wyszukiwarka żłobków i klubów dziecięcych w Polsce, oparta na danych z oficjalnego rejestru Ministerstwa Rodziny i Polityki Społecznej (MRiPS).

---

## 📋 Spis treści

- [O projekcie](#o-projekcie)
- [Wymagania](#wymagania)
- [Struktura plików](#struktura-plików)
- [Custom Post Type i Taksonomie](#custom-post-type-i-taksonomie)
- [Pola meta (custom fields)](#pola-meta-custom-fields)
- [Szablony](#szablony)
- [Filtry i wyszukiwarka](#filtry-i-wyszukiwarka)
- [AJAX Search](#ajax-search)
- [Schema.org / SEO](#schemaorg--seo)
- [Customizer (Hero)](#customizer-hero)
- [Stylowanie](#stylowanie)
- [JavaScript](#javascript)
- [Funkcje pomocnicze](#funkcje-pomocnicze)
- [Integracja z Rank Math](#integracja-z-rank-math)

---

## O projekcie

**Żłobki Polska** to dedykowany motyw WordPress służący jako wyszukiwarka placówek opieki nad dziećmi w Polsce. Dane importowane są z publicznego rejestru żłobków i klubów dziecięcych prowadzonego przez MRiPS.

**Główne funkcje:**
- Przeglądanie i filtrowanie bazy ponad 6 500 placówek
- Filtrowanie po województwie, powiecie, typie, cenie, dostępności
- Strona szczegółowa każdej placówki z mapą Google
- SEO: Schema.org (ChildCare, BreadcrumbList, WebSite + SearchAction)
- Customizer WordPress do edycji sekcji Hero bez kodowania
- Responsywny design (mobile-first)
- Integracja z Rank Math SEO

---

## Wymagania

| Wymaganie | Wersja |
|-----------|--------|
| WordPress | ≥ 6.4 |
| PHP | ≥ 8.1 |
| MySQL | ≥ 5.7 |
| Rank Math SEO | zalecane |

---

## Struktura plików

```
zlobki-polska/
├── style.css                        # Wymagany przez WP nagłówek motywu
├── theme.json                       # Paleta kolorów i typografia (Gutenberg)
├── functions.php                    # Główna logika motywu
├── index.php                        # Fallback template
├── front-page.php                   # Strona główna
├── archive-zlobek.php               # Lista/wyszukiwarka placówek
├── single-zlobek.php                # Strona szczegółowa placówki
├── page.php                         # Szablon stron statycznych
├── header.php                       # Nagłówek serwisu (nav, logo)
├── footer.php                       # Stopka serwisu
├── template-parts/
│   └── card-nursery.php             # Karta placówki (używana w gridzie)
└── assets/
    ├── css/
    │   └── main.css                 # Główny arkusz stylów
    └── js/
        └── main.js                  # Główny skrypt JS
```

---

## Custom Post Type i Taksonomie

Zarejestrowane w `functions.php` → `add_action('init', ...)`.

### Post Type: `zlobek`

| Parametr | Wartość |
|----------|---------|
| Slug CPT | `zlobek` |
| Slug archiwum | `/zlobki/` |
| REST API | tak |
| Obsługa | title, editor, custom-fields, thumbnail |

### Taksonomie

| Taksonomia | Slug | Opis |
|------------|------|------|
| `wojewodztwo` | `/wojewodztwo/` | 16 województw Polski |
| `typ_instytucji` | `/typ/` | Żłobek / Klub dziecięcy |
| `powiat` | `/powiat/` | Powiaty (hierarchia pod województwem) |

Wszystkie taksonomie są hierarchiczne, publiczne i dostępne przez REST API.

---

## Pola meta (custom fields)

Zarejestrowane przez `register_post_meta()` z `show_in_rest: true`.

### Adres i lokalizacja

| Klucz | Typ | Opis |
|-------|-----|------|
| `miejscowosc` | string | Miejscowość |
| `ulica` | string | Ulica |
| `nr_domu` | string | Numer domu |
| `nr_lokalu` | string | Numer lokalu |
| `kod_pocztowy` | string | Kod pocztowy |
| `gmina` | string | Gmina |
| `lat` | string | Szerokość geograficzna |
| `lng` | string | Długość geograficzna |

### Kontakt

| Klucz | Typ | Opis |
|-------|-----|------|
| `telefon` | string | Numer telefonu |
| `email` | string | Adres e-mail |
| `www` | string | Strona WWW |

### Dane operacyjne

| Klucz | Typ | Opis |
|-------|-----|------|
| `liczba_miejsc` | integer | Łączna liczba miejsc |
| `liczba_dzieci` | integer | Liczba aktualnie zapisanych dzieci |
| `opłata_miesięczna` | number | Miesięczna opłata (PLN) |
| `opłata_godzinowa` | number | Opłata godzinowa >10h (PLN) |
| `opłata_wyżywienie_m` | number | Wyżywienie miesięcznie (PLN) |
| `opłata_wyżywienie_d` | number | Wyżywienie dziennie (PLN) |
| `znizki` | string | Opis dostępnych zniżek |
| `godziny_otwarcia` | string | Godziny pracy |
| `dostosowany_niepelnosp` | boolean | Dostosowanie dla niepełnosprawnych |
| `zawieszona_dzialalnosc` | boolean | Czy działalność jest zawieszona |

### Podmiot prowadzący

| Klucz | Typ | Opis |
|-------|-----|------|
| `podmiot_nazwa` | string | Nazwa podmiotu |
| `podmiot_nip` | string | NIP |
| `podmiot_regon` | string | REGON |
| `podmiot_www` | string | Strona podmiotu |
| `numer_rejestru` | string | Numer w rejestrze MRiPS |
| `zlobek_id` | string | ID z rejestru MRiPS |

---

## Szablony

### `front-page.php` — Strona główna
- Sekcja Hero z wyszukiwarką (pole tekstowe + dropdown województwa + chipy filtrów)
- Stats bar (liczba instytucji, miejsc, województw)
- Grid 6 ostatnio dodanych placówek
- Siatka województw z liczbą placówek
- Sekcja 6 kart „Dlaczego warto"

### `archive-zlobek.php` — Wyszukiwarka / Archiwum
Obsługuje trzy konteksty URL:
1. `/zlobki/` — archiwum CPT
2. `/wojewodztwo/mazowieckie/` — taksonomia
3. `/zlobki/?woj=mazowieckie&typ=zlobek&s=...` — filtrowanie przez GET

**Dostępne filtry GET:**

| Parametr | Opis |
|----------|------|
| `s` | Szukaj po nazwie / miejscowości |
| `woj` | Slug województwa |
| `typ` | Slug typu instytucji |
| `powiat` | Slug powiatu |
| `max_price` | Maksymalna opłata miesięczna (PLN) |
| `niepelnosprawni` | `1` = tylko dostosowane |
| `zawieszone` | `1` = pokaż zawieszone |
| `sort` | `title` / `price_asc` / `price_desc` / `capacity` / `newest` |
| `paged` | Numer strony paginacji |

### `single-zlobek.php` — Szczegóły placówki
- Hero z nazwą, typem i adresem
- Sekcja dostępności miejsc z paskiem
- Sekcja opłat (miesięczna, godzinowa, wyżywienie, zniżki)
- Godziny otwarcia
- Dane lokalizacyjne
- Informacje o podmiocie prowadzącym
- Sidebar: mapa Google (iframe embed), dane kontaktowe, skrót informacji, linki do taksonomii

### `page.php` — Strony statyczne
- Breadcrumb
- Układ dwukolumnowy: treść Gutenberg + sidebar
- Sidebar: nawigacja podstron, CTA wyszukiwarki, link do kontaktu
- Obsługa paginacji treści (`<!--nextpage-->`)

### `template-parts/card-nursery.php` — Karta placówki
Wielokrotnie używany komponent wyświetlający:
- Ikonę i typ placówki (badge)
- Nazwę z linkiem
- Lokalizację
- Grid 4 metadanych: miejsca / wolne / opłata / zapełnienie
- Pasek dostępności
- Przyciski kontaktowe (tel, email, www) + przycisk „Szczegóły →"

---

## Filtry i wyszukiwarka

Filtrowanie działa przez standardowe GET-parametry — brak AJAX na liście (pełne przeładowanie strony). Dzięki temu:
- Filtry są indeksowalne przez Google
- Każdy widok ma unikalny, udostępnialny URL
- Działa bez JavaScript

Sidebar filtrów (`filters-panel`) jest sticky na desktopie, ukryty na mobile i przełączany przyciskiem „🎛️ Filtry".

---

## AJAX Search

Endpoint: `admin-ajax.php?action=zlobki_search`

Zarejestrowany dla zalogowanych i niezalogowanych użytkowników. Przyjmuje parametry POST:

| Parametr | Opis |
|----------|------|
| `search` | Tekst wyszukiwania |
| `wojewodztwo` | Slug województwa |
| `typ` | Slug typu |
| `max_price` | Maks. cena |
| `niepelnosprawni` | `1` / pusty |
| `wolne_miejsca` | `1` / pusty |
| `sort` | Jak w GET |
| `page` | Numer strony |
| `nonce` | `zlobki_search` |

Zwraca JSON: `{ success: true, data: { html, found, max_pages, paged } }`

---

## Schema.org / SEO

Trzy bloki JSON-LD wstrzykiwane przez `wp_head` (priorytet 5):

### 1. WebSite + SearchAction (tylko strona główna)
Umożliwia Google wyświetlenie Sitelinks Searchbox w wynikach wyszukiwania.

### 2. ChildCare (strony pojedynczych placówek)
Zawiera: nazwę, adres, geo, telefon, email, URL, liczbę miejsc, ofertę cenową (UnitPriceSpecification z `MON` — miesięcznie).

### 3. BreadcrumbList (archiwum, taksonomie, strony statyczne)
Generowany automatycznie na podstawie kontekstu szablonu. Rank Math obsługuje breadcrumbs dla postów — ten blok pokrywa resztę.

---

## Customizer (Hero)

Panel: **Wygląd → Dostosuj → 🏠 Sekcja Hero**

Edytowalne pola:

| ID ustawienia | Opis |
|---------------|------|
| `zlobki_hero_badge` | Tekst odznaki nad tytułem |
| `zlobki_hero_title` | Pierwsza linia tytułu |
| `zlobki_hero_title_em` | Wyróżnione słowo/fraza |
| `zlobki_hero_subtitle` | Podtytuł (textarea, obsługa HTML) |
| `zlobki_hero_btn` | Tekst przycisku Szukaj |
| `zlobki_hero_search_ph` | Placeholder pola tekstowego |
| `zlobki_hero_chip_np` | Tekst chipa „dla niepełnosprawnych" |
| `zlobki_hero_chip_klub` | Tekst chipa „tylko kluby" |
| `zlobki_hero_chip_zlobek` | Tekst chipa „tylko żłobki" |

Wszystkie pola używają `transport: postMessage` — podgląd aktualizuje się na żywo bez przeładowania strony.

---

## Stylowanie

Plik: `assets/css/main.css`

**Paleta kolorów (CSS Variables):**

| Zmienna | Wartość | Opis |
|---------|---------|------|
| `--color-primary` | `#2D7D7B` | Głęboki teal |
| `--color-accent` | `#F4845F` | Koral |
| `--color-bg` | `#FFFAF6` | Tło krem |
| `--color-text` | `#1E2B2A` | Tekst główny |
| `--color-success` | `#4CAF76` | Zielony (wolne miejsca) |
| `--color-warning` | `#F4A35F` | Pomarańczowy (zawieszone) |

**Typografia:**
- Nagłówki wyświetlane: `Playfair Display` (Google Fonts)
- Treść: `Nunito` (Google Fonts)

**Breakpointy:**

| Breakpoint | Zmiany |
|------------|--------|
| `≤ 1100px` | Sidebar filtrów chowany, single grid jednokolumnowy |
| `≤ 768px` | Karty jednokolumnowe, nawigacja hamburger |
| `≤ 480px` | Regiony 1 kolumna, meta kart 1 kolumna |

---

## JavaScript

Plik: `assets/js/main.js`

| Funkcja | Opis |
|---------|------|
| Mobile menu | Toggle nav + zamknięcie po kliknięciu poza |
| Counter animation | Animowane liczniki w sekcji Hero/Stats (IntersectionObserver) |
| Availability bar | Animacja paska dostępności przy wejściu w viewport |
| Card entrance | Fade-in + slide-up dla kart (staggered delay) |
| Filters toggle | Przełącznik panelu filtrów na mobile |
| Price range | Live update etykiety zakresu cenowego |
| Sticky header shrink | Klasa `.scrolled` po przewinięciu 50px |

Wszystko opakowane w IIFE (`(function(){ 'use strict'; })()`), brak zewnętrznych zależności.

---

## Funkcje pomocnicze

Zdefiniowane w `functions.php`:

| Funkcja | Opis |
|---------|------|
| `zlobki_ucwords_pl($str)` | Zamiana na Title Case z obsługą polskich znaków UTF-8 (mb_strtolower + regex) |
| `zlobki_get_address($post_id)` | Zwraca sformatowany pełny adres placówki |
| `zlobki_format_price($value)` | Formatuje cenę: `1 200 zł` lub `—` gdy brak |
| `zlobki_availability($post_id)` | Zwraca procent zapełnienia (0–100) |
| `zlobki_parse_geo($geo)` | Parsuje string `"lng;lat"` → `['lat' => ..., 'lng' => ...]` |
| `zlobki_format_hours($raw)` | Usuwa prefiks „Godziny pracy: " i sanitizuje |
| `zlobki_type_icon($type)` | Zwraca emoji: `🎯` dla klubu, `🍼` dla żłobka |

---

## Integracja z Rank Math

Motyw rejestruje CPT i taksonomie w sitemapie Rank Math:

```php
add_filter('rank_math/sitemap/post_type', ...)      // dodaje 'zlobek'
add_filter('rank_math/sitemap/taxonomies', ...)      // dodaje wojew., typ, powiat
add_filter('rank_math/titles/pt_zlobek_title', ...)  // domyślny pattern tytułu
add_filter('rank_math/titles/pt_zlobek_description', ...) // domyślny opis
```

Rank Math zarządza: meta title/description, og/twitter tags, canonical URL, sitemap XML.
Motyw zarządza: Schema.org ChildCare, BreadcrumbList na archiwach i taksonomach, WebSite SearchAction.

---

## Kolumny admina

W widoku listy CPT `zlobek` dodane kolumny:
- **Typ** — `typ_instytucji_label`
- **Województwo** — `wojewodztwo_label`
- **Miejsca** — `liczba_miejsc` (+ liczba zapisanych)
- **Opłata/mc** — sformatowana przez `zlobki_format_price()`

---

## Stałe i wersjonowanie

```php
define('ZLOBKI_VERSION', '1.0.0');   // używana do cache-bustingu CSS/JS
define('ZLOBKI_DIR',     get_template_directory());
define('ZLOBKI_URI',     get_template_directory_uri());
define('ZLOBKI_SLUG',    'zlobek');  // slug CPT
```

---

*Dane pochodzą z: [Rejestr żłobków i klubów dziecięcych MRiPS](https://www.gov.pl/web/rodzina/rejestr-zlobkow-i-klubow-dzieciecych)*
