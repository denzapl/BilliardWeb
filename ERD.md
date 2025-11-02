# Draft ERD (Entity Relationship Diagram) - Textual

Entities:
- users (id, name, email, password, role)
- billiard_clubs (id, owner_name, club_name, club_member_count, address)
- tournaments (id, title, start_date, location)
- memberships (id, user_id -> users.id, club_id -> billiard_clubs.id, role)
- reviews (id, club_id -> billiard_clubs.id, user_id -> users.id, rating, comment)

Relationships:
- users 1..* memberships (one user can have many memberships)
- billiard_clubs 1..* memberships (one club can have many memberships)
- users 1..* reviews
- billiard_clubs 1..* reviews
- tournaments is standalone for now; future relation: tournaments may have many participants (memberships)


