<?php

global $wpdb;
$prefix = $wpdb->prefix; //eg 'wp_'

$createZForm="
CREATE TABLE `{$prefix}zform` (
`id` INT NOT NULL AUTO_INCREMENT ,
`code` VARCHAR( 128 ) NOT NULL ,
`title` TEXT NOT NULL ,
`handler_id` INT NOT NULL ,
`success_email_id` INT NOT NULL ,
`error_email_id` INT NOT NULL ,
`email_disabled` TINYINT( 1 ) NOT NULL ,
UNIQUE (`code`),
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormRecaptcha="
CREATE TABLE `{$prefix}zform_recaptcha` (
`id` INT NOT NULL AUTO_INCREMENT,
`title` VARCHAR( 128 ) NOT NULL ,
`pub_key` VARCHAR( 255 ) NOT NULL ,
`priv_key` VARCHAR( 255 ) NOT NULL,
UNIQUE (`title`),
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormStep="
CREATE TABLE `{$prefix}zform_step` (
`id` INT NOT NULL AUTO_INCREMENT ,
`form_id` INT NOT NULL,
`decorator_id` INT NOT NULL ,
`step` TINYINT( 2 ) NOT NULL ,
`next_post_id` INT NOT NULL,
`custom_action` TEXT NOT NULL ,
`method` ENUM( 'post', 'get' ) NOT NULL ,
`requires_key` VARCHAR( 32 ) NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormInput = "
CREATE TABLE `{$prefix}zform_input` (
`id` INT NOT NULL AUTO_INCREMENT ,
`form_step_id` INT NOT NULL ,
`name` TEXT NOT NULL ,
`field_id` INT NULL ,
`label` TEXT NOT NULL ,
`type` VARCHAR( 32 ) NOT NULL ,
`required` TINYINT( 1 ) NOT NULL ,
`css_classes` TEXT NOT NULL ,
`css_id` TEXT NOT NULL ,
`validators` TEXT NOT NULL ,
`order` INT NOT NULL ,
`display_group` TINYINT( 2 ) NOT NULL ,
`default_value` TEXT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormInputValidator = "
CREATE TABLE `{$prefix}zform_input_validator` (
`id` INT NOT NULL AUTO_INCREMENT,
`input_id` INT NOT NULL ,
`type` VARCHAR( 32 ) NOT NULL ,
`message` TEXT NOT NULL,
`extra` VARCHAR( 32 ) NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormHandler = "
CREATE TABLE `{$prefix}zform_handler` (
`id` INT NOT NULL AUTO_INCREMENT,
`title` VARCHAR( 32 ) NOT NULL ,
`filename` VARCHAR( 255 ) NOT NULL,
UNIQUE (`title`),
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$insertExampleHandler = "
INSERT INTO `{$prefix}zform_handler` (`id`, `title`, `filename`) VALUES 
(1, 'Logger', 'logger.php'), 
(2, 'Save Post Type', 'save-post-type.php'),
(3, 'Authorize.net', 'authorize.php')";

$createZFormInputOption = "
CREATE TABLE `{$prefix}zform_input_option` (
`id` INT NOT NULL AUTO_INCREMENT ,
`input_id` INT NOT NULL ,
`label` TEXT NOT NULL ,
`value` TEXT NOT NULL ,
`order` INT NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$createZFormDecorator = "
CREATE TABLE `{$prefix}zform_decorator` (
`id` INT NOT NULL AUTO_INCREMENT,
`title` VARCHAR( 32 ) NOT NULL,
`container_tag` VARCHAR( 8 ) NOT NULL ,
`row_tag` VARCHAR( 8 ) NOT NULL ,
`label_tag` VARCHAR( 8 ) NOT NULL ,
`input_tag` VARCHAR( 8 ) NOT NULL,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";

$insertExampleDecorators = "
INSERT INTO `{$prefix}zform_decorator` (`id`, `title`, `container_tag`, `row_tag`, `label_tag`, `input_tag`) VALUES
(1, 'table', 'table', 'tr', 'td', 'td'),
(2, 'list', 'ul', 'none', 'li', 'li');
";

$createZFormEmail = "
CREATE TABLE `{$prefix}zform_email` (
`id` INT NOT NULL AUTO_INCREMENT ,
`title` VARCHAR( 128 ) NOT NULL , 
`to` TEXT NOT NULL ,
`to_name` TEXT NOT NULL ,
`from` TEXT NOT NULL ,
`from_name` TEXT NOT NULL ,
`force_from` TINYINT( 1 ) NOT NULL ,
`reply_to` TEXT NOT NULL ,
`subject` TEXT NOT NULL ,
`body` TEXT NULL ,
`is_html` TINYINT( 1 ) NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM";
/*

`is_zend` TINYINT( 1 ) NOT NULL ,
`is_smtp` TINYINT( 1 ) NOT NULL ,
`smtp_server` TEXT NOT NULL ,
`smtp_port` INT NOT NULL ,
`smtp_username` TEXT NOT NULL ,
`smtp_password` TEXT NOT NULL ,
`smtp_auth` VARCHAR( 16 ) NOT NULL ,
`smtp_ssl` VARCHAR( 8 ) NOT NULL ,

//*/

$dropZForm="drop table `{$prefix}zform`";
$dropZFormStep="drop table `{$prefix}zform_step`";
$dropZFormInput="drop table `{$prefix}zform_input`";
$dropZFormInputOption="drop table `{$prefix}zform_input_option`";
$dropZFormInputValidator="drop table `{$prefix}zform_input_validator`";
$dropZFormDecorator="drop table `{$prefix}zform_decorator`";
$dropZFormEmail="drop table `{$prefix}zform_email`";
$dropZFormHandler="drop table `{$prefix}zform_handler`";
$dropZFormRecaptcha="drop table `{$prefix}zform_recaptcha`";
