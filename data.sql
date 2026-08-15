
CREATE TABLE IF NOT EXISTS `users` (
    `uid` int(11) NULL AUTO_INCREMENT,
    `fullname` varchar(255) NOT NULL ,
    `username` varchar(255) NOT NULL ,
    `email` varchar(255) NOT NULL ,
    `password` varchar(255) NOT NULL ,
    `money` varchar(255) NOT NULL ,
    `level` varchar(255) NOT NULL ,
    `token` text NOT NULL ,
    `status` varchar(255) NOT NULL ,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`uid`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;


CREATE TABLE IF NOT EXISTS `setting` (
    `id` int(11) NULL AUTO_INCREMENT,
    `title` text NOT NULL ,
    `description` text NOT NULL ,
    `keywords` text NOT NULL ,
    `partner_id` text NOT NULL ,/* thesieure*/
    `partner_key` text NOT NULL , /* thesieure*/
    `signature` text NOT NULL , /* api.sieuthicode.net*/
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;


CREATE TABLE IF NOT EXISTS `product` (
    `id` int(11) NULL AUTO_INCREMENT,
    `thumbnail` text NOT NULL ,
    `name` text NULL ,
    `price` text NOT NULL,
    `type` varchar(255) NOT NULL ,
    `service` varchar(255) NOT NULL ,
    `typeacc` varchar(255) NOT NULL ,
    `account` text NOT NULL ,
    `password` text NOT NULL ,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `service` (
    `id` int(11) NULL AUTO_INCREMENT,
    `logo` text NOT NULL ,
    `name` varchar(255) NOT NULL ,
    `path` text NOT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;


CREATE TABLE IF NOT EXISTS `bank` (
    `id` int(11) NULL AUTO_INCREMENT,
    `qrcode` text NOT NULL ,
    `token` text NOT NULL ,
    `status` varchar(255) NOT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `account` (
    `id` int(11) NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;
