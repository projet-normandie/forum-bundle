-- Message
delimiter //
DROP TRIGGER IF EXISTS `forumMessageAfterInsert`//
CREATE TRIGGER forumMessageAfterInsert AFTER INSERT ON forum_message
FOR EACH ROW
BEGIN
	-- nbForumMessage
	UPDATE user SET nbForumMessage = nbForumMessage + 1 WHERE id = NEW.idUser;

END //
delimiter ;

-- Topic
delimiter //
DROP TRIGGER IF EXISTS `forumTopicAfterInsert`//
CREATE TRIGGER forumTopicAfterInsert AFTER INSERT ON forum_topic
FOR EACH ROW
BEGIN
	INSERT INTO forum_topic_user (idUser, idTopic, boolRead)
	SELECT DISTINCT idUser, NEW.id, 1
	FROM forum_forum_user;
END //
delimiter ;


-- Forum
delimiter //
DROP TRIGGER IF EXISTS `forumForumAfterInsert`//
CREATE TRIGGER forumForumAfterInsert AFTER INSERT ON forum_forum
    FOR EACH ROW
BEGIN
    INSERT INTO forum_forum_user (idUser, idForum, boolRead)
    SELECT DISTINCT idUser, NEW.id, 1
    FROM forum_forum_user;
END //
delimiter ;




