-- FORUM
INSERT INTO forum_forum_user (idForum, idUser)
SELECT id, 9262 FROM forum_forum;

-- TOPIC
INSERT INTO forum_topic_user (idTopic, idUser)
SELECT id, 9262 FROM forum_topic;




