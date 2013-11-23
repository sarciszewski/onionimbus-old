CREATE TABLE dns (
    dnsid           INTEGER PRIMARY KEY ASC,
    site            INTEGER,
    domain_name     TEXT,
    hidden_service  TEXT,
    node            INTEGER,
    ssl_private     TEXT,
    ssl_cert        TEXT,
    created         TEXT,
    modified        TEXT
);

CREATE TABLE nodes (
    nodeid          INTEGER PRIMARY KEY ASC,
    nodename        TEXT,
    ipv4            TEXT,
    ipv6            TEXT,
    hosted          INTEGER,
    public_key      TEXT,
    status          TEXT,
    created         TEXT,
    modified        TEXT
);

CREATE TABLE sites (
    siteid          INTEGER PRIMARY KEY ASC,
    owner           INTEGER,
    created         TEXT,
    modified        TEXT
);

CREATE TABLE users (
    userid          INTEGER PRIMARY KEY ASC,
    username        TEXT,
    password        TEXT,
    email           TEXT,
    created         TEXT,
    modified        TEXT
);