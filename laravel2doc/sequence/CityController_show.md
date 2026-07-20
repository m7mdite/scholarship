sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant CityController as CityController
    participant City as City
    participant DB as Database
    
    C->>R: GET /resource/{id}
    R->>+CityController: show(id)
    CityController->>+City: find(id) / findOrFail(id)
    City->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-City: Return record
    City-->>-CityController: Model instance
    CityController-->>-R: Return JSON response
    R-->>C: 200 OK with data
    
    Note over CityController,City: This sequence retrieves a specific resource by ID
  