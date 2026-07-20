sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CountryController as CountryController
    participant Country as Country
    participant DB as Database
    
    C->>R: GET /resource/{id}
    R->>+CountryController: show(id)
    CountryController->>+Country: find(id) / findOrFail(id)
    Country->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-Country: Return record
    Country-->>-CountryController: Model instance
    CountryController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CountryController,Country: This sequence retrieves a specific resource by ID
  