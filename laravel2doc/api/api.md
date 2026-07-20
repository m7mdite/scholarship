# API Documentation

## Project: laravel/laravel

Laravel Version: v12.58.0

Generated: 7/14/2026, 10:04:30 PM

## Table of Contents

- [api](#api)
- [web](#web)

## api

| Method | Endpoint | Handler | Description |
|--------|----------|---------|-------------|
| GET | /admin/stats | AdminStatsController::class, 'index' | List stats |
| GET | /hello | function () {
    return response()->json(['message' => 'Hello, World!' | List hello |
| POST | /register | AuthController::class, 'register' | Create a new register |
| POST | /login | AuthController::class, 'login' | Create a new login |
| GET | /scholarships | ScholarshipController::class, 'index' | List scholarships |
| GET | /scholarships/{id} | ScholarshipController::class, 'show' | Retrieve a specific {id} |
| GET | /top-scholarships | ScholarshipController::class, 'getTopScholarships' | List top-scholarships |
| GET | /scholarships/{id}/similar | ScholarshipController::class, 'getSimilarScholarships' | Retrieve a specific similar |
| GET | /scholarships/country/{countryId} | ScholarshipController::class, 'getByCountry' | Retrieve a specific {countryId} |
| GET | /me | AuthController::class, 'me' | List me |
| POST | /logout | AuthController::class, 'logout' | Create a new logout |
| POST | /favorites/{scholarship} | FavoriteScholarshipController::class, 'add' | Create a new {scholarship} |
| DELETE | /favorites/{scholarship} | FavoriteScholarshipController::class, 'remove' | Delete a specific {scholarship} |
| GET | /favorites | FavoriteScholarshipController::class, 'index' | List favorites |
| GET | /notifications | NotificationController::class, 'index' | List notifications |
| GET | /notifications/unread | NotificationController::class, 'unread' | List unread |
| PUT | /notifications/{id}/read | NotificationController::class, 'markAsRead' | Update a specific read |
| PUT | /notifications/read-all | NotificationController::class, 'markAllAsRead' | Update a specific read-all |
| DELETE | /notifications/{id} | NotificationController::class, 'destroy' | Delete a specific {id} |
| POST | /scholarships | ScholarshipController::class, 'store' | Create a new scholarships |
| PUT | /scholarships/{id} | ScholarshipController::class, 'update' | Update a specific {id} |
| DELETE | /scholarships/{id} | ScholarshipController::class, 'destroy' | Delete a specific {id} |
| POST | /notifications/send-to-all | NotificationController::class, 'sendToAll' | Create a new send-to-all |
| GET | /countries | CountryController::class, 'index' | List countries |
| GET | /countries/{id} | CountryController::class, 'show' | Retrieve a specific {id} |
| GET | /cities | CityController::class, 'index' | List cities |
| GET | /cities/{id} | CityController::class, 'show' | Retrieve a specific {id} |
| GET | /categories | CategoryController::class, 'index' | List categories |
| GET | /categories/{id} | CategoryController::class, 'show' | Retrieve a specific {id} |
| GET | /specializations | SpecializationController::class, 'index' | List specializations |
| GET | /specializations/{id} | SpecializationController::class, 'show' | Retrieve a specific {id} |
| POST | /chat | ChatController::class, 'handleChat' | Create a new chat |
| POST | /generate-cv | CvController::class, 'generateCV' | Create a new generate-cv |
| POST | /generate-motivation-letter | CvController::class, 'generateMotivationLetter' | Create a new generate-motivation-letter |
| POST | /generate-recommendation | CvController::class, 'generateRecommendationLetter' | Create a new generate-recommendation |
| GET | /test-pdf | function () {
    $pdf = Pdf::loadHTML('<h1>Hello</h1>');
    return $pdf->download('test.pdf');
});
// Route::get('/top-scholarships', [ScholarshipController::class, 'getTopScholarships' | List test-pdf |

### GET /admin/stats

**Handler:** AdminStatsController::class, 'index'

**Description:** List stats

---

### GET /hello

**Handler:** function () {
    return response()->json(['message' => 'Hello, World!'

**Description:** List hello

---

### POST /register

**Handler:** AuthController::class, 'register'

**Description:** Create a new register

---

### POST /login

**Handler:** AuthController::class, 'login'

**Description:** Create a new login

---

### GET /scholarships

**Handler:** ScholarshipController::class, 'index'

**Description:** List scholarships

---

### GET /scholarships/{id}

**Handler:** ScholarshipController::class, 'show'

**Description:** Retrieve a specific {id}

---

### GET /top-scholarships

**Handler:** ScholarshipController::class, 'getTopScholarships'

**Description:** List top-scholarships

---

### GET /scholarships/{id}/similar

**Handler:** ScholarshipController::class, 'getSimilarScholarships'

**Description:** Retrieve a specific similar

---

### GET /scholarships/country/{countryId}

**Handler:** ScholarshipController::class, 'getByCountry'

**Description:** Retrieve a specific {countryId}

---

### GET /me

**Handler:** AuthController::class, 'me'

**Description:** List me

---

### POST /logout

**Handler:** AuthController::class, 'logout'

**Description:** Create a new logout

---

### POST /favorites/{scholarship}

**Handler:** FavoriteScholarshipController::class, 'add'

**Description:** Create a new {scholarship}

---

### DELETE /favorites/{scholarship}

**Handler:** FavoriteScholarshipController::class, 'remove'

**Description:** Delete a specific {scholarship}

---

### GET /favorites

**Handler:** FavoriteScholarshipController::class, 'index'

**Description:** List favorites

---

### GET /notifications

**Handler:** NotificationController::class, 'index'

**Description:** List notifications

---

### GET /notifications/unread

**Handler:** NotificationController::class, 'unread'

**Description:** List unread

---

### PUT /notifications/{id}/read

**Handler:** NotificationController::class, 'markAsRead'

**Description:** Update a specific read

---

### PUT /notifications/read-all

**Handler:** NotificationController::class, 'markAllAsRead'

**Description:** Update a specific read-all

---

### DELETE /notifications/{id}

**Handler:** NotificationController::class, 'destroy'

**Description:** Delete a specific {id}

---

### POST /scholarships

**Handler:** ScholarshipController::class, 'store'

**Description:** Create a new scholarships

---

### PUT /scholarships/{id}

**Handler:** ScholarshipController::class, 'update'

**Description:** Update a specific {id}

---

### DELETE /scholarships/{id}

**Handler:** ScholarshipController::class, 'destroy'

**Description:** Delete a specific {id}

---

### POST /notifications/send-to-all

**Handler:** NotificationController::class, 'sendToAll'

**Description:** Create a new send-to-all

---

### GET /countries

**Handler:** CountryController::class, 'index'

**Description:** List countries

---

### GET /countries/{id}

**Handler:** CountryController::class, 'show'

**Description:** Retrieve a specific {id}

---

### GET /cities

**Handler:** CityController::class, 'index'

**Description:** List cities

---

### GET /cities/{id}

**Handler:** CityController::class, 'show'

**Description:** Retrieve a specific {id}

---

### GET /categories

**Handler:** CategoryController::class, 'index'

**Description:** List categories

---

### GET /categories/{id}

**Handler:** CategoryController::class, 'show'

**Description:** Retrieve a specific {id}

---

### GET /specializations

**Handler:** SpecializationController::class, 'index'

**Description:** List specializations

---

### GET /specializations/{id}

**Handler:** SpecializationController::class, 'show'

**Description:** Retrieve a specific {id}

---

### POST /chat

**Handler:** ChatController::class, 'handleChat'

**Description:** Create a new chat

---

### POST /generate-cv

**Handler:** CvController::class, 'generateCV'

**Description:** Create a new generate-cv

---

### POST /generate-motivation-letter

**Handler:** CvController::class, 'generateMotivationLetter'

**Description:** Create a new generate-motivation-letter

---

### POST /generate-recommendation

**Handler:** CvController::class, 'generateRecommendationLetter'

**Description:** Create a new generate-recommendation

---

### GET /test-pdf

**Handler:** function () {
    $pdf = Pdf::loadHTML('<h1>Hello</h1>');
    return $pdf->download('test.pdf');
});
// Route::get('/top-scholarships', [ScholarshipController::class, 'getTopScholarships'

**Description:** List test-pdf

---

### API Resource

| Method | Endpoint | Handler | Description |
|--------|----------|---------|-------------|
| GET | /countries | CountryController::class@index | List all countries |
| POST | /countries | CountryController::class@store | Store a new countrie |
| GET | /countries/{id} | CountryController::class@show | Show a specific countrie |
| PUT/PATCH | /countries/{id} | CountryController::class@update | Update a specific countrie |
| DELETE | /countries/{id} | CountryController::class@destroy | Delete a specific countrie |
| GET | /cities | CityController::class@index | List all cities |
| POST | /cities | CityController::class@store | Store a new citie |
| GET | /cities/{id} | CityController::class@show | Show a specific citie |
| PUT/PATCH | /cities/{id} | CityController::class@update | Update a specific citie |
| DELETE | /cities/{id} | CityController::class@destroy | Delete a specific citie |
| GET | /categories | CategoryController::class@index | List all categories |
| POST | /categories | CategoryController::class@store | Store a new categorie |
| GET | /categories/{id} | CategoryController::class@show | Show a specific categorie |
| PUT/PATCH | /categories/{id} | CategoryController::class@update | Update a specific categorie |
| DELETE | /categories/{id} | CategoryController::class@destroy | Delete a specific categorie |
| GET | /specializations | SpecializationController::class@index | List all specializations |
| POST | /specializations | SpecializationController::class@store | Store a new specialization |
| GET | /specializations/{id} | SpecializationController::class@show | Show a specific specialization |
| PUT/PATCH | /specializations/{id} | SpecializationController::class@update | Update a specific specialization |
| DELETE | /specializations/{id} | SpecializationController::class@destroy | Delete a specific specialization |

### GET /countries

**Handler:** CountryController::class@index

**Description:** List all countries

---

### POST /countries

**Handler:** CountryController::class@store

**Description:** Store a new countrie

---

### GET /countries/{id}

**Handler:** CountryController::class@show

**Description:** Show a specific countrie

---

### PUT/PATCH /countries/{id}

**Handler:** CountryController::class@update

**Description:** Update a specific countrie

---

### DELETE /countries/{id}

**Handler:** CountryController::class@destroy

**Description:** Delete a specific countrie

---

### GET /cities

**Handler:** CityController::class@index

**Description:** List all cities

---

### POST /cities

**Handler:** CityController::class@store

**Description:** Store a new citie

---

### GET /cities/{id}

**Handler:** CityController::class@show

**Description:** Show a specific citie

---

### PUT/PATCH /cities/{id}

**Handler:** CityController::class@update

**Description:** Update a specific citie

---

### DELETE /cities/{id}

**Handler:** CityController::class@destroy

**Description:** Delete a specific citie

---

### GET /categories

**Handler:** CategoryController::class@index

**Description:** List all categories

---

### POST /categories

**Handler:** CategoryController::class@store

**Description:** Store a new categorie

---

### GET /categories/{id}

**Handler:** CategoryController::class@show

**Description:** Show a specific categorie

---

### PUT/PATCH /categories/{id}

**Handler:** CategoryController::class@update

**Description:** Update a specific categorie

---

### DELETE /categories/{id}

**Handler:** CategoryController::class@destroy

**Description:** Delete a specific categorie

---

### GET /specializations

**Handler:** SpecializationController::class@index

**Description:** List all specializations

---

### POST /specializations

**Handler:** SpecializationController::class@store

**Description:** Store a new specialization

---

### GET /specializations/{id}

**Handler:** SpecializationController::class@show

**Description:** Show a specific specialization

---

### PUT/PATCH /specializations/{id}

**Handler:** SpecializationController::class@update

**Description:** Update a specific specialization

---

### DELETE /specializations/{id}

**Handler:** SpecializationController::class@destroy

**Description:** Delete a specific specialization

---

## web

| Method | Endpoint | Handler | Description |
|--------|----------|---------|-------------|
| GET | / | function () {
    return view('welcome');
});

Route::resource('scholarships', ScholarshipController::class);
Route::resource('countries', CountryController::class);
Route::resource('cities', CityController::class);
Route::resource('categories', CategoryController::class);
Route::resource('specializations', SpecializationController::class);

// Route::resource('scholarships', ScholarshipController::class);



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm' | List resource |
| POST | /login | AuthController::class, 'login' | Create a new login |
| GET | /register | AuthController::class, 'showRegisterForm' | List register |
| POST | /register | AuthController::class, 'register' | Create a new register |
| POST | /logout | AuthController::class, 'logout' | Create a new logout |
| GET | /dashboard | AuthController::class, 'dashboard' | List dashboard |

### GET /

**Handler:** function () {
    return view('welcome');
});

Route::resource('scholarships', ScholarshipController::class);
Route::resource('countries', CountryController::class);
Route::resource('cities', CityController::class);
Route::resource('categories', CategoryController::class);
Route::resource('specializations', SpecializationController::class);

// Route::resource('scholarships', ScholarshipController::class);



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'

**Description:** List resource

---

### POST /login

**Handler:** AuthController::class, 'login'

**Description:** Create a new login

---

### GET /register

**Handler:** AuthController::class, 'showRegisterForm'

**Description:** List register

---

### POST /register

**Handler:** AuthController::class, 'register'

**Description:** Create a new register

---

### POST /logout

**Handler:** AuthController::class, 'logout'

**Description:** Create a new logout

---

### GET /dashboard

**Handler:** AuthController::class, 'dashboard'

**Description:** List dashboard

---

### Resource

| Method | Endpoint | Handler | Description |
|--------|----------|---------|-------------|
| GET | /scholarships | ScholarshipController::class@index | List all scholarships |
| GET | /scholarships/create | ScholarshipController::class@create | Show form to create a new scholarship |
| POST | /scholarships | ScholarshipController::class@store | Store a new scholarship |
| GET | /scholarships/{id} | ScholarshipController::class@show | Show a specific scholarship |
| GET | /scholarships/{id}/edit | ScholarshipController::class@edit | Show form to edit scholarship |
| PUT/PATCH | /scholarships/{id} | ScholarshipController::class@update | Update a specific scholarship |
| DELETE | /scholarships/{id} | ScholarshipController::class@destroy | Delete a specific scholarship |
| GET | /countries | CountryController::class@index | List all countries |
| GET | /countries/create | CountryController::class@create | Show form to create a new countrie |
| POST | /countries | CountryController::class@store | Store a new countrie |
| GET | /countries/{id} | CountryController::class@show | Show a specific countrie |
| GET | /countries/{id}/edit | CountryController::class@edit | Show form to edit countrie |
| PUT/PATCH | /countries/{id} | CountryController::class@update | Update a specific countrie |
| DELETE | /countries/{id} | CountryController::class@destroy | Delete a specific countrie |
| GET | /cities | CityController::class@index | List all cities |
| GET | /cities/create | CityController::class@create | Show form to create a new citie |
| POST | /cities | CityController::class@store | Store a new citie |
| GET | /cities/{id} | CityController::class@show | Show a specific citie |
| GET | /cities/{id}/edit | CityController::class@edit | Show form to edit citie |
| PUT/PATCH | /cities/{id} | CityController::class@update | Update a specific citie |
| DELETE | /cities/{id} | CityController::class@destroy | Delete a specific citie |
| GET | /categories | CategoryController::class@index | List all categories |
| GET | /categories/create | CategoryController::class@create | Show form to create a new categorie |
| POST | /categories | CategoryController::class@store | Store a new categorie |
| GET | /categories/{id} | CategoryController::class@show | Show a specific categorie |
| GET | /categories/{id}/edit | CategoryController::class@edit | Show form to edit categorie |
| PUT/PATCH | /categories/{id} | CategoryController::class@update | Update a specific categorie |
| DELETE | /categories/{id} | CategoryController::class@destroy | Delete a specific categorie |
| GET | /specializations | SpecializationController::class@index | List all specializations |
| GET | /specializations/create | SpecializationController::class@create | Show form to create a new specialization |
| POST | /specializations | SpecializationController::class@store | Store a new specialization |
| GET | /specializations/{id} | SpecializationController::class@show | Show a specific specialization |
| GET | /specializations/{id}/edit | SpecializationController::class@edit | Show form to edit specialization |
| PUT/PATCH | /specializations/{id} | SpecializationController::class@update | Update a specific specialization |
| DELETE | /specializations/{id} | SpecializationController::class@destroy | Delete a specific specialization |
| GET | /scholarships | ScholarshipController::class@index | List all scholarships |
| GET | /scholarships/create | ScholarshipController::class@create | Show form to create a new scholarship |
| POST | /scholarships | ScholarshipController::class@store | Store a new scholarship |
| GET | /scholarships/{id} | ScholarshipController::class@show | Show a specific scholarship |
| GET | /scholarships/{id}/edit | ScholarshipController::class@edit | Show form to edit scholarship |
| PUT/PATCH | /scholarships/{id} | ScholarshipController::class@update | Update a specific scholarship |
| DELETE | /scholarships/{id} | ScholarshipController::class@destroy | Delete a specific scholarship |

### GET /scholarships

**Handler:** ScholarshipController::class@index

**Description:** List all scholarships

---

### GET /scholarships/create

**Handler:** ScholarshipController::class@create

**Description:** Show form to create a new scholarship

---

### POST /scholarships

**Handler:** ScholarshipController::class@store

**Description:** Store a new scholarship

---

### GET /scholarships/{id}

**Handler:** ScholarshipController::class@show

**Description:** Show a specific scholarship

---

### GET /scholarships/{id}/edit

**Handler:** ScholarshipController::class@edit

**Description:** Show form to edit scholarship

---

### PUT/PATCH /scholarships/{id}

**Handler:** ScholarshipController::class@update

**Description:** Update a specific scholarship

---

### DELETE /scholarships/{id}

**Handler:** ScholarshipController::class@destroy

**Description:** Delete a specific scholarship

---

### GET /countries

**Handler:** CountryController::class@index

**Description:** List all countries

---

### GET /countries/create

**Handler:** CountryController::class@create

**Description:** Show form to create a new countrie

---

### POST /countries

**Handler:** CountryController::class@store

**Description:** Store a new countrie

---

### GET /countries/{id}

**Handler:** CountryController::class@show

**Description:** Show a specific countrie

---

### GET /countries/{id}/edit

**Handler:** CountryController::class@edit

**Description:** Show form to edit countrie

---

### PUT/PATCH /countries/{id}

**Handler:** CountryController::class@update

**Description:** Update a specific countrie

---

### DELETE /countries/{id}

**Handler:** CountryController::class@destroy

**Description:** Delete a specific countrie

---

### GET /cities

**Handler:** CityController::class@index

**Description:** List all cities

---

### GET /cities/create

**Handler:** CityController::class@create

**Description:** Show form to create a new citie

---

### POST /cities

**Handler:** CityController::class@store

**Description:** Store a new citie

---

### GET /cities/{id}

**Handler:** CityController::class@show

**Description:** Show a specific citie

---

### GET /cities/{id}/edit

**Handler:** CityController::class@edit

**Description:** Show form to edit citie

---

### PUT/PATCH /cities/{id}

**Handler:** CityController::class@update

**Description:** Update a specific citie

---

### DELETE /cities/{id}

**Handler:** CityController::class@destroy

**Description:** Delete a specific citie

---

### GET /categories

**Handler:** CategoryController::class@index

**Description:** List all categories

---

### GET /categories/create

**Handler:** CategoryController::class@create

**Description:** Show form to create a new categorie

---

### POST /categories

**Handler:** CategoryController::class@store

**Description:** Store a new categorie

---

### GET /categories/{id}

**Handler:** CategoryController::class@show

**Description:** Show a specific categorie

---

### GET /categories/{id}/edit

**Handler:** CategoryController::class@edit

**Description:** Show form to edit categorie

---

### PUT/PATCH /categories/{id}

**Handler:** CategoryController::class@update

**Description:** Update a specific categorie

---

### DELETE /categories/{id}

**Handler:** CategoryController::class@destroy

**Description:** Delete a specific categorie

---

### GET /specializations

**Handler:** SpecializationController::class@index

**Description:** List all specializations

---

### GET /specializations/create

**Handler:** SpecializationController::class@create

**Description:** Show form to create a new specialization

---

### POST /specializations

**Handler:** SpecializationController::class@store

**Description:** Store a new specialization

---

### GET /specializations/{id}

**Handler:** SpecializationController::class@show

**Description:** Show a specific specialization

---

### GET /specializations/{id}/edit

**Handler:** SpecializationController::class@edit

**Description:** Show form to edit specialization

---

### PUT/PATCH /specializations/{id}

**Handler:** SpecializationController::class@update

**Description:** Update a specific specialization

---

### DELETE /specializations/{id}

**Handler:** SpecializationController::class@destroy

**Description:** Delete a specific specialization

---

### GET /scholarships

**Handler:** ScholarshipController::class@index

**Description:** List all scholarships

---

### GET /scholarships/create

**Handler:** ScholarshipController::class@create

**Description:** Show form to create a new scholarship

---

### POST /scholarships

**Handler:** ScholarshipController::class@store

**Description:** Store a new scholarship

---

### GET /scholarships/{id}

**Handler:** ScholarshipController::class@show

**Description:** Show a specific scholarship

---

### GET /scholarships/{id}/edit

**Handler:** ScholarshipController::class@edit

**Description:** Show form to edit scholarship

---

### PUT/PATCH /scholarships/{id}

**Handler:** ScholarshipController::class@update

**Description:** Update a specific scholarship

---

### DELETE /scholarships/{id}

**Handler:** ScholarshipController::class@destroy

**Description:** Delete a specific scholarship

---

