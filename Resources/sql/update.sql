-- TOPIC --
UPDATE forum_topic t
SET t.idMessageMax = (SELECT MAX(id) FROM forum_message WHERE idTopic = t.id);

UPDATE forum_topic t
SET t.nbMessage = (SELECT COUNT(id) FROM forum_message WHERE idTopic = t.id);

-- FORUM --
UPDATE forum_forum f
SET f.idMessageMax = (SELECT MAX(idMessageMax) FROM forum_topic WHERE idForum = f.id);

UPDATE forum_forum f
SET f.nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = f.id);

UPDATE forum_forum f
SET f.nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = f.id);

-- FORUM PARENT --
UPDATE forum_forum f1,forum_forum f2
SET f1.idMessageMax = f2.idMessageMax
WHERE f1.id = f2.idParent;
-- => FAUX

UPDATE forum_forum f
SET f.nbTopic = (SELECT COUNT(id) FROM forum_topic WHERE idForum = f.id);

UPDATE forum_forum f
SET f.nbMessage = (SELECT SUM(nbMessage) FROM forum_topic WHERE idForum = f.id);


UPDATE forum_forum f
SET f.slug = get_slug(f.libForum)
WHERE f.slug is NULL;

-- FORUM_USER
UPDATE forum_forum_user fu
SET boolRead = (SELECT IFNULL(MIN(boolRead), 0)
                FROM forum_topic_user INNER JOIN forum_topic ON forum_topic_user.idTopic = forum_topic.id
                WHERE fu.idForum = forum_topic.idForum
                AND fu.idUser = forum_topic_user.idUser)
