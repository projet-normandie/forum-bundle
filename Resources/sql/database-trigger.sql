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
                AND idForum = @idForum
                AND forum_topic_user.idUser = NEW.idUser
                AND boolRead = 0;

            IF (@nbTopicNotRead = 0) THEN
                UPDATE forum_forum_user
                SET boolRead = 1
                WHERE idUser = NEW.idUser
                AND idForum = @idForum;
            END IF;
        END IF;
    END IF;
END //
delimiter ;




