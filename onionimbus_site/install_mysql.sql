CREATE TABLE dns (
    dnsid           INT(11),
    site            INT(11),
    domain_name     VARCHAR(127),
    hidden_service  CHAR(40),
    node            INTEGER,
    ssl_private     TEXT,
    ssl_cert        TEXT,
    created         DATETIME,
    modified        DATETIME
);
CREATE TABLE nodes (
    nodeid          INT(11),
    nodename        VARCHAR(64),
    ipv4            VARCHAR(16),
    ipv6            VARCHAR(40),
    hosted          INTEGER,
    public_key      TEXT,
    status          VARCHAR(32),
    created         DATETIME,
    modified        DATETIME
);
CREATE TABLE sites (
    siteid          INT(11),
    owner           INTEGER,
    created         DATETIME,
    modified        DATETIME
);
CREATE TABLE users (
    userid          INT(11),
    username        VARCHAR(255),
    password        CHAR(79),
    email           VARCHAR(255),
    created         DATETIME,
    modified        DATETIME
);
