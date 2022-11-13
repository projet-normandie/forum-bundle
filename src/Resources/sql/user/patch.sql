DROP PROCEDURE IF EXISTS patch_forum_user;
DELIMITER &&
CREATE PROCEDURE patch_forum_user()
BEGIN
 	DECLARE user_id INT;

	DECLARE cur1 CURSOR FOR
	SELECT DISTINCt idUser
	FROM forum_forum_user;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO user_id;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- FORUM_USER
		INSERT INTO forum_forum_user (idForum, idUser)
        SELECT id, user_id
		FROM forum_forum
        WHERE id NOT IN (SELECT idForum FROM forum_forum_user WHERE idUser = user_id);


        -- ADD CHART
		INSERT INTO vgr_chart (idGroup, libChartEn, libChartFr, slug, created_at, updated_at)
		VALUES (group_id_dest, chart_lib_en, chart_lib_fr, chart_slug, NOW(), NOW());
		SET chart_id_dest = LAST_INSERT_ID();

        -- libRecord
        INSERT INTO vgr_chartlib (idChart, idType, name, created_at, updated_at) SELECT chart_id_dest, idType, name, NOW(), NOW() FROM vgr_chartlib WHERE idChart = chart_id_src;
    END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;

