-- Message
delimiter //
DROP TRIGGER IF EXISTS `forumMessageAfterInsert`//
CREATE TRIGGER forumMessageAfterInsert AFTER INSERT ON forum_message
FOR EACH ROW
BEGIN
	UPDATE forum_topic
	SET nbMessage = (SELECT COUNT(id) FROM forum_message WHERE idTopic = NEW.idTopic),
		idMessageMax = NEW.id
	WHERE id = NEW.idTopic;

	-- UPDATE t_membre SET nbForumMessage = nbForumMessage + 1 WHERE idMembre = NEW.idMembre;

	UPDATE forum_topic_user SET boolRead = 0 WHERE idTopic = NEW.idTopic AND idUser != NEW.idUser;

END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `forumMessageAfterDelete`//
CREATE TRIGGER forumMessageAfterDelete AFTER DELETE ON forum_message
FOR EACH ROW
BEGIN
	UPDATE forum_topic
	SET nbMessage = (SELECT COUNT(id) FROM forum_message WHERE idTopic = OLD.idTopic),
		idMessageMax = (SELECT MAX(id) FROM forum_message WHERE idTopic = OLD.idTopic)
	WHERE id = OLD.idTopic;
END //
delimiter ;


-- Topic
delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterInsert`//
CREATE TRIGGER forumTopicAfterInsert AFTER INSERT ON forum_topic
FOR EACH ROW
BEGIN
	UPDATE forum_forum
	SET nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = NEW.idForum)
	WHERE id = NEW.idForum;

	INSERT INTO forum_topic_user (idUser, idTopic, boolRead)
	SELECT DISTINCT idUser, NEW.id, 1
	FROM forum_forum_user;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterDelete`//
CREATE TRIGGER forumTopicAfterDelete AFTER DELETE ON forum_topic
FOR EACH ROW
BEGIN
	UPDATE forum_forum
	SET nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = OLD.idForum),
	    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = OLD.idForum),
	    idMessageMax = (SELECT MAX(a.id)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.id
		    				WHERE idForum = OLD.idForum)
		WHERE id = OLD.idForum;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterUpdate`//
CREATE TRIGGER forumTopicAfterUpdate AFTER UPDATE ON forum_topic
FOR EACH ROW
BEGIN
	IF OLD.nbMessage != NEW.nbMessage THEN
		UPDATE forum_forum
		SET nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = NEW.idForum)
		WHERE id = NEW.idForum;
	END IF;
	IF OLD.idMessageMax != NEW.idMessageMax	THEN
		UPDATE forum_forum
		SET idMessageMax = NEW.idMessageMax
		WHERE id = NEW.idForum;
	END IF;
	IF OLD.idForum != NEW.idForum THEN
		UPDATE forum_forum
		SET nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = NEW.idForum),
		    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = NEW.idForum),
		    idMessageMax = (SELECT MAX(a.id)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.id
		    				WHERE idForum = NEW.idForum)
		WHERE id = NEW.idForum;
		UPDATE forum_forum
		SET nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = OLD.idForum),
		    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = OLD.idForum),
			idMessageMax = (SELECT MAX(a.id)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.id
		    				WHERE idForum = OLD.idForum)
		WHERE id = OLD.idForum;
	END IF;
END //
delimiter ;



delimiter //
DROP TRIGGER IF EXISTS `forumTopicUserAfterUpdate`//
CREATE TRIGGER forumTopicUserAfterUpdate AFTER UPDATE ON forum_topic_user
    FOR EACH ROW
BEGIN
    IF OLD.boolRead != NEW.boolRead THEN
        SELECT idForum INTO @idForum FROM forum_topic WHERE id = NEW.idTopic;

        IF NEW.boolRead = 0 THEN
            UPDATE forum_forum_user
            SET boolRead = 0
            WHERE idUser = NEW.idUser
                    AND idForum = @idForum;
        ELSE
            SELECT COUNT(forum_topic.id) INTO @nbTopicNotRead
               FROM forum_topic_user, forum_topic
               WHERE forum_topic_user.idTopic = forum_topic.id
               AND idForum = @idForum;

            IF (@nbTopicNotRead = 0) THEN
                UPDATE forum_forum_user
                SET boolRead = 0
                WHERE idUser = NEW.idUser
                AND idForum = @idForum;
            END IF;
        END IF;
    END IF;
END //
delimiter ;



