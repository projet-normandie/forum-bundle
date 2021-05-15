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

UPDATE forum_forum f
SET f.slug = get_slug(f.libForum)
WHERE f.slug is NULL;
