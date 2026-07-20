erDiagram
  applicationcriteria {
    string requirment_type
    string application_criteria_value
    string application_criteria_description
    int scholarship_id FK "References scholarship"
    int id PK "Primary key"
  }
  category {
    string category_name
    int id PK "Primary key"
  }
  city {
    string city_name
    int country_id FK "References country"
    int id PK "Primary key"
  }
  country {
    float country_name
    float country_rate
    int id PK "Primary key"
  }
  favoritescholarship {
    int scholarship_id FK "References scholarship"
    int user_id FK "References user"
    int id PK "Primary key"
  }
  howtoapply {
    string how_to_apply_description
    int id PK "Primary key"
    int scholarship_id FK "References scholarship"
  }
  notification {
    int id PK "Primary key"
    int user_id FK "References user"
    string type
    string title
    string body
    json data
    datetime read_at
    datetime created_at
    datetime updated_at
  }
  personalexperience {
    string personal_experiences_description
    int id PK "Primary key"
    int scholarship_id FK "References scholarship"
  }
  photo {
    string image_path
    int city_id FK "References city"
    int id PK "Primary key"
    int scholarship_id FK "References scholarship"
  }
  review {
    int id PK "Primary key"
    int scholarship_id FK "References scholarship"
    string reviewer_name
    string review
    string rating
    datetime created_at
    datetime updated_at
  }
  scholarship {
    string scholarship_name
    string degree
    string finance
    string scholarship_description
    string donar
    datetime finished_date
    datetime start_date
    string scholarship_language
    int country_id FK "References country"
    int city_id FK "References city"
    int specialization_id FK "References specialization"
    int category_id FK "References category"
    string scholarship_link
    int id PK "Primary key"
    int scholarship_id FK "References scholarship"
    datetime created_at
    datetime updated_at
  }
  specialization {
    string specialization_name
    int category_id FK "References category"
    int id PK "Primary key"
  }
  user {
    string name
    string email
    string password
    int id PK "Primary key"
    int user_id FK "References user"
    int scholarship_id FK "References scholarship"
    datetime created_at
    datetime updated_at
  }
  applicationcriteria }|--|| scholarship : "scholarship"
  city }|--|| country : "country"
  favoritescholarship }|--|| scholarship : "scholarship"
  favoritescholarship }|--|| user : "user"
  howtoapply }|--|| scholarship : "scholarship"
  notification }|--|| user : "user"
  personalexperience }|--|| scholarship : "scholarship"
  photo }|--|| city : "city"
  photo }|--|| scholarship : "scholarship"
  review }|--|| scholarship : "scholarship"
  scholarship ||--o| howtoapply : "howToApply"
  scholarship ||--|{ notification : "notifications"
  scholarship ||--|{ photo : "photos"
  scholarship ||--|{ applicationcriteria : "applicationCriteria"
  scholarship ||--|{ favoritescholarship : "favoriteByUsers"
  scholarship ||--|{ personalexperience : "personalExperiences"
  scholarship ||--|{ review : "reviews"
  scholarship }|--|| country : "country"
  scholarship }|--|| city : "city"
  scholarship }|--|| specialization : "specialization"
  scholarship }|--|| category : "category"
  specialization }|--|| category : "category"
  user ||--|{ notification : "notifications"
  user }|--|{ scholarship : "favoriteScholarships"
