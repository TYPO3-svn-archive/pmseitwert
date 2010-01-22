#
# Table structure for table 'tx_pmseitwert_conf'
#
CREATE TABLE tx_pmseitwert_conf (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,

    url tinytext NOT NULL,
    apikey tinytext NOT NULL,
    days2keep int(4) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);



#
# Table structure for table 'tx_pmseitwert_data'
#
CREATE TABLE tx_pmseitwert_data (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,

    url_id int(11) DEFAULT '0' NOT NULL,
    seitwert tinytext,
    google tinytext,
    alexa tinytext,
    social tinytext,
    technical tinytext,
    yahoo tinytext,
    other tinytext,
    checktime tinytext,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);