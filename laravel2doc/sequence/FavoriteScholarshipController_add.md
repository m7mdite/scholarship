sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant FavoriteScholarshipController as FavoriteScholarshipController
    participant V as Validator
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: POST /resource
    R->>+FavoriteScholarshipController: add(request)
    FavoriteScholarshipController->>+V: validate(request)
    V-->>-FavoriteScholarshipController: validated data
    FavoriteScholarshipController->>+Scholarship: create(data)
    Scholarship->>+DB: INSERT INTO table
    DB-->>-Scholarship: Return new record
    Scholarship-->>-FavoriteScholarshipController: New model instance
    FavoriteScholarshipController-->>-R: Return JSON response
    R-->>C: 201 Created with data
    
    Note over FavoriteScholarshipController,Scholarship: This sequence creates a new resource
  