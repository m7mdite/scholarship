sequenceDiagram
    autonumber
    participant C as Client
    participant R as Route
    participant NotificationController as NotificationController
    participant User as User
    participant DB as Database
    
    C->>R: DELETE /resource/{id}
    R->>+NotificationController: destroy(id)
    NotificationController->>+User: find(id)
    User->>+DB: SELECT * FROM table WHERE id = ?
    DB-->>-User: Return record
    User-->>-NotificationController: Model instance
    NotificationController->>+User: delete()
    User->>+DB: DELETE FROM table WHERE id = ?
    DB-->>-User: Success
    User-->>-NotificationController: Success
    NotificationController-->>-R: Return JSON response
    R-->>C: 204 No Content
    
    Note over NotificationController,User: This sequence removes a resource
  