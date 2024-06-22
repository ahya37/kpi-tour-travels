DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgInsert_difference_on_detailed_marketing_targets` AFTER INSERT ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(difference) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_difference = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgInsert_realization_on_detailed_marketing_targets` AFTER INSERT ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(realization) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_realization  = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgInsert_target_on_detailed_marketing_targets` AFTER INSERT ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(target) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_target  = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgUpdate_difference_on_detailed_marketing_targets` AFTER UPDATE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(difference) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_difference  = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgUpdate_realization_on_detailed_marketing_targets` AFTER UPDATE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(realization) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_realization  = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgUpdate_target_on_detailed_marketing_targets` AFTER UPDATE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT SUM(target) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = NEW.marketing_target_id;
	UPDATE marketing_targets set total_target  = total  
	WHERE id = NEW.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgDelete_difference_on_detailed_marketing_targets` AFTER DELETE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT COALESCE(SUM(difference),0) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = OLD.marketing_target_id;
	UPDATE marketing_targets set total_difference = total  
	WHERE id = OLD.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgDelete_realization_on_detailed_marketing_targets` AFTER DELETE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT COALESCE(SUM(realization),0) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = OLD.marketing_target_id;
	UPDATE marketing_targets set total_realization  = total  
	WHERE id = OLD.marketing_target_id;
END$$

DELIMITER ;

DELIMITER $$ 

CREATE DEFINER=`root`@`localhost` TRIGGER `trgDelete_target_on_detailed_marketing_targets` AFTER DELETE ON `detailed_marketing_targets` FOR EACH ROW BEGIN 
	DECLARE total decimal(10,2);
	SELECT COALESCE(SUM(target),0) into total 
	from detailed_marketing_targets  
	WHERE marketing_target_id = OLD.marketing_target_id;
	UPDATE marketing_targets set total_target  = total  
	WHERE id = OLD.marketing_target_id;
END$$

DELIMITER ;
