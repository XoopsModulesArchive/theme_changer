#
# `xoops_ctem_link`
#

CREATE TABLE ctem_link (
    mid      INT(5)       NOT NULL DEFAULT '0',
    theme    VARCHAR(100) NOT NULL DEFAULT '',
    title    VARCHAR(100) NOT NULL DEFAULT '',
    subtitle VARCHAR(100) NOT NULL DEFAULT '',
    metakey  TEXT         NOT NULL,
    metadesc TEXT         NOT NULL,
    UNIQUE KEY mid (mid)
)
    ENGINE = ISAM;

#
# `xoops_ctem_pagelink`
#

CREATE TABLE ctem_pagelink (
    pid       INT(5)       NOT NULL DEFAULT '0',
    pagename  VARCHAR(100) NOT NULL DEFAULT '',
    theme     VARCHAR(100) NOT NULL DEFAULT '',
    scripturl VARCHAR(200) NOT NULL DEFAULT '',
    title     VARCHAR(100) NOT NULL DEFAULT '',
    subtitle  VARCHAR(100) NOT NULL DEFAULT '',
    metakey   TEXT         NOT NULL,
    metadesc  TEXT         NOT NULL,
    UNIQUE KEY pid (pid)
)
    ENGINE = ISAM;

#
# `xoops_ctem_pagelink`
#

INSERT INTO ctem_pagelink
VALUES (0, 'Others', 'default', '', '', '', '', '');
INSERT INTO ctem_pagelink
VALUES (1, 'Top Page', 'default', '/index.php', '', '', '', '');


