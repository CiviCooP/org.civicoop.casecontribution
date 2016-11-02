CREATE TABLE IF NOT EXISTS `civicrm_case_contribution` (
  `contribution_id` int(11) NOT NULL,
  `case_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`contribution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;