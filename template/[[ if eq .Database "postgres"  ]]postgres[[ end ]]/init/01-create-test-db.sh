#!/usr/bin/env bash

create_test_database() {
    local database_test="${POSTGRES_DB}_test"
    local database_user_test="${POSTGRES_USER}_test"

    echo "Creating test database: \"${database_test}\""
    echo "Creating test user: \"${database_user_test}\" with same password as user \"${POSTGRES_USER}\""

    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<EOF
        CREATE DATABASE ${database_test};

        CREATE USER ${database_user_test} WITH PASSWORD '${POSTGRES_PASSWORD}';

        GRANT ALL PRIVILEGES ON DATABASE ${database_test} TO ${database_user_test};
EOF

    echo "Granting schema privileges to \"${database_user_test}\""

    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "${database_test}" <<EOF
        GRANT ALL ON SCHEMA public TO ${database_user_test};
        GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO ${database_user_test};
        GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO ${database_user_test};
EOF
}

create_test_database
