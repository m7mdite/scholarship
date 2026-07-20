sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant FavoriteScholarshipController as FavoriteScholarshipController
    participant Scholarship as Scholarship
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+FavoriteScholarshipController: remove(id)
    FavoriteScholarshipController->>+Scholarship: find(id)
    Scholarship->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Scholarship: Return record
    Scholarship-->>-FavoriteScholarshipController: Model instance
    FavoriteScholarshipController->>+Scholarship: delete()
    Scholarship->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-Scholarship: Success
    Scholarship-->>-FavoriteScholarshipController: Success
    FavoriteScholarshipController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over FavoriteScholarshipController,Scholarship: This sequence removes a resource
  