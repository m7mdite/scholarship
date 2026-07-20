sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant FavoriteScholarshipController as FavoriteScholarshipController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: GET /resource
    R->>+FavoriteScholarshipController: index()
    FavoriteScholarshipController->>+Scholarship: all() / get() / paginate()
    Scholarship->>+DB: SELECT * FROM table
    DB-->>-Scholarship: Return records
    Scholarship-->>-FavoriteScholarshipController: Collection of models
    FavoriteScholarshipController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over FavoriteScholarshipController,Scholarship: This sequence retrieves a list of resources
  