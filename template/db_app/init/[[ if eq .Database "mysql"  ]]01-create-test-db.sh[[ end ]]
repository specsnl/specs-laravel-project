#!/usr/bin/env bash

create_test_database() {
    local database_test="${MYSQL_DATABASE}_test"
    local database_test_esc="${MYSQL_DATABASE//_/\\_}\_test"
    local database_user_test="${MYSQL_USER}_test"

    echo "Creating test database: \"${database_test}\""
    echo "Creating test user: \"${database_user_test}\" with same password as user \"${MYSQL_USER}\""

    mysql -uroot -p${MYSQL_ROOT_PASSWORD} --comments <<EOF
        CREATE DATABASE IF NOT EXISTS \`${database_test}\`;

        CREATE USER IF NOT EXISTS '${database_user_test}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';

        GRANT ALL PRIVILEGES ON \`${database_test_esc}\`.* TO '${database_user_test}'@'%';
EOF
}

create_test_database
