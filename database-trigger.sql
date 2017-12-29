-- Message
delimiter //
DROP TRIGGER IF EXISTS `forumMessageAfterInsert`//
CREATE TRIGGER forumMessageAfterInsert AFTER INSERT ON forum_message
FOR EACH ROW
BEGIN
	UPDATE forum_topic
	SET nbMessage = (SELECT COUNT(idMessage) FROM forum_message WHERE idTopic = NEW.idTopic),
		idMessageMax = NEW.idMessage
	WHERE idTopic = NEW.idTopic;

	-- UPDATE t_membre SET nbForumMessage = nbForumMessage + 1 WHERE idMembre = NEW.idMembre;

	-- UPDATE t_forum_topic_membre SET estLu = 0 WHERE idTopic = NEW.idTopic AND idMembre != NEW.idMembre;

END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `forumMessageAfterDelete`//
CREATE TRIGGER forumMessageAfterDelete AFTER DELETE ON forum_message
FOR EACH ROW
BEGIN
	UPDATE forum_topic
	SET nbMessage = (SELECT COUNT(idMessage) FROM forum_message WHERE idTopic = OLD.idTopic),
		idMessageMax = (SELECT MAX(idMessage) FROM forum_message WHERE idTopic = OLD.idTopic)
	WHERE idTopic = OLD.idTopic;
END //
delimiter ;


-- Topic
delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterInsert`//
CREATE TRIGGER forumTopicAfterInsert AFTER INSERT ON forum_topic
FOR EACH ROW
BEGIN
	UPDATE forum_forum
	SET nbTopic = (SELECT COUNT(idTopic) FROM forum_topic WHERE idForum = NEW.idForum)
	WHERE idForum = NEW.idForum;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterDelete`//
CREATE TRIGGER forumTopicAfterDelete AFTER DELETE ON forum_topic
FOR EACH ROW
BEGIN
	UPDATE forum_forum
	SET nbTopic = (SELECT COUNT(idTopic) FROM forum_topic WHERE idForum = OLD.idForum),
	    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = OLD.idForum),
	    idMessageMax = (SELECT MAX(a.idMessage)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.idTopic
		    				WHERE idForum = OLD.idForum)
		WHERE idForum = OLD.idForum;
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
		WHERE idForum = NEW.idForum;
	END IF;
	IF OLD.idMessageMax != NEW.idMessageMax	THEN
		UPDATE forum_forum
		SET idMessageMax = NEW.idMessageMax
		WHERE idForum = NEW.idForum;
	END IF;
	IF OLD.idForum != NEW.idForum THEN
		UPDATE forum_forum
		SET nbTopic = (SELECT COUNT(idTopic) FROM forum_topic WHERE idForum = NEW.idForum),
		    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = NEW.idForum),
		    idMessageMax = (SELECT MAX(a.idMessage)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.idTopic
		    				WHERE idForum = NEW.idForum)
		WHERE idForum = NEW.idForum;
		UPDATE forum_forum
		SET nbTopic = (SELECT COUNT(idTopic) FROM forum_topic WHERE idForum = OLD.idForum),
		    nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = OLD.idForum),
			  idMessageMax = (SELECT MAX(a.idMessage)
		    				FROM forum_message a INNER JOIN forum_topic b ON a.idTopic = b.idTopic
		    				WHERE idForum = OLD.idForum)
		WHERE idForum = OLD.idForum;
	END IF;
END //
delimiter ;
