#
# Table structure for table 'tx_mkcache_queue'
#
CREATE TABLE tx_mkcache_queue (
    hash varchar(255) DEFAULT '' NOT NULL,
    cache_identifier varchar(255) DEFAULT '' NOT NULL,
    clear_cache_method varchar(255) DEFAULT '' NOT NULL,
    entry_identifier text,
    tags text,

    KEY hash (hash)
);
