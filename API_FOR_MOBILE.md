# IAZOS Mobile API

## Base URL
Va fi trimis prin tunnel sau prin domeniul final.

Exemplu:

`https://abc123.loca.lt/api/v1`

## Auth

- Tip autentificare: `Bearer Token`
- Login-ul si logout-ul folosesc Sanctum

## Sincronizare Web + Mobile

Web-ul si aplicatia mobila sunt deja sincronizate automat atunci cand folosesc aceste endpoint-uri, pentru ca:

- ambele citesc din aceeasi baza de date
- ambele scriu in aceeasi baza de date
- follow/unfollow seller se salveaza centralizat
- story-urile sellerilor se salveaza centralizat

Asta inseamna:

- daca userul da follow unui seller pe web, in mobil `is_following` va veni deja `true`
- daca userul da unfollow pe mobil, pe web butonul va aparea automat pe starea corecta
- daca sellerul publica story din mobil, el apare si pe site
- daca sellerul publica story din site, el apare si in mobil

## Endpoints

### Auth

- `POST /register`
- `POST /login`
- `POST /logout`
- `GET /me`

### Catalog / Categories

- `GET /catalog/tree`
- `GET /catalog/categories/{slug}`
- `GET /catalog/categories/{slug}/filters`
- `GET /catalog/brands`
- `GET /catalog/brands/{brand}/products`

### Products

- `GET /products`
- `GET /products/{id}`

`GET /products` suporta:

- `q`
- `category_id`
- `subcategory_id`
- `category_slug`
- `subcategory_slug`
- `brand`
- `sort=new|price_asc|price_desc`
- `filters[slug][]=value` pentru `select`, `multiselect`, `text`
- `filters[slug][min]=x`
- `filters[slug][max]=y`
- `filters[slug]=1` pentru `boolean`

### Sellers

- `GET /sellers`
- `GET /sellers/{id}`

Seller list / detail intorc inclusiv:

- `followers_count`
- `is_following`
- `avatar_url`
- `shop_name`
- `average_rating`
- `reviews_count`

### Follow Sellers

- `POST /sellers/{id}/follow`
- `DELETE /sellers/{id}/follow`
- `GET /me/followed-sellers/promos`

#### Follow response

Raspuns la follow/unfollow:

```json
{
  "ok": true,
  "message": "Seller urmarit cu succes.",
  "followers_count": 3
}
```

#### Promos pentru sellerii urmariti

`GET /me/followed-sellers/promos`

Intoarce:

- produsele promo ale sellerilor urmariti
- seller info in fiecare card
- imagine produs
- rating

### Seller Stories

- `GET /stories`
- `GET /sellers/{id}/stories`
- `POST /seller/stories`
- `DELETE /seller/stories/{storyId}`

## Payload-uri importante

### GET /me

```json
{
  "ok": true,
  "user": {
    "id": 12,
    "name": "Jony",
    "email": "jony@example.com",
    "role": "seller",
    "seller_status": "approved"
  }
}
```

### GET /sellers

```json
{
  "ok": true,
  "sellers": [
    {
      "id": 7,
      "name": "temporro",
      "shop_name": "temporro",
      "avatar_url": "https://.../media/public/seller/avatars/abc.webp",
      "legal_name": "Artur tuleaa",
      "pickup_address": "aaaaaaa",
      "seller_type": "individual",
      "delivery_type": "curier",
      "followers_count": 1,
      "is_following": true,
      "average_rating": 0,
      "reviews_count": 0,
      "public_products_count": 3
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

### GET /sellers/{id}

Intoarce:

- seller info complet
- `followers_count`
- `is_following`
- review-uri seller
- produse seller

### GET /stories

Grupeaza story-urile pe seller si pune sellerii urmariti primii.

```json
{
  "ok": true,
  "groups": [
    {
      "seller": {
        "id": 7,
        "name": "temporro",
        "shop_name": "temporro",
        "avatar_url": "https://.../avatar.webp",
        "followers_count": 1,
        "is_following": true
      },
      "is_followed_priority": true,
      "stories": [
        {
          "id": 5,
          "media_type": "video",
          "media_url": "https://.../media/public/stories/videos/xyz.mov",
          "thumbnail_url": "https://.../media/public/stories/thumbs/xyz.webp",
          "caption": "noutati",
          "expires_at": "2026-04-10T08:30:00+03:00",
          "created_at": "2026-04-09T08:30:00+03:00"
        }
      ]
    }
  ]
}
```

### GET /sellers/{id}/stories

Intoarce:

- seller info
- `is_following`
- toate story-urile active ale sellerului

### POST /seller/stories

Auth obligatoriu: seller logat, `approved`

Content-Type:

- `multipart/form-data`

Campuri:

- `media` required
- `caption` optional, max 280

Mimetypes acceptate:

- `image/jpeg`
- `image/png`
- `image/webp`
- `image/gif`
- `image/bmp`
- `video/mp4`
- `video/webm`
- `video/quicktime`

Observatie:

- in aplicatia mobila, butonul de adaugare story poate folosi camera telefonului sau galeria
- backend-ul nu are nevoie de endpoint separat pentru camera
- aplicatia doar trimite fisierul capturat prin `multipart/form-data` in campul `media`

### DELETE /seller/stories/{storyId}

- sterge story-ul sellerului logat
- daca story-ul nu apartine sellerului logat, raspunsul este `403`

## Ce poate face deja mobilul

### Pentru user normal

- vede lista sellerilor
- vede daca urmareste sau nu un seller
- da follow / unfollow
- vede numarul de followers
- vede produsele promo ale sellerilor urmariti
- vede stories feed public
- vede stories seller dedicat

### Pentru seller logat

- poate publica story foto
- poate publica story video
- poate sterge story
- poate folosi camera telefonului pentru media
- poate folosi galeria telefonului

## Note importante pentru Jony

- follow state trebuie citit din `is_following`, nu calculat local
- followers count trebuie citit din `followers_count`
- dupa follow/unfollow:
  - actualizeaza optimist butonul
  - apoi reseteaza seller detail / seller list / stories feed daca vrei consistenta perfecta
- stories sunt publice si pentru userii care nu urmaresc sellerul
- sellerii urmariti trebuie afisati primii in feed daca `is_followed_priority=true`
- la seller detail si sellers list trebuie folosit acelasi sistem de follow state
- la story create in mobil:
  - se poate deschide camera
  - sau galeria
  - rezultatul se trimite in `media`

## Erori posibile

- `401` daca userul nu este autentificat
- `403` daca incearca actiune de seller fara cont seller aprobat
- `404` daca sellerul nu exista sau nu este aprobat
- `409` daca migrations pentru stories nu sunt aplicate
- `503` daca migrations pentru followers nu sunt aplicate
