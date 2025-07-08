CREATE DATABASE usage_db CHARACTER SET utf8mb4;

USE usage_db;

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility VARCHAR(50),
    rating INT CHECK (rating BETWEEN 1 AND 5),
    vote_date DATE
    /*tomoka add*/
    /*start*/
    /*投票権を1日1票に制限*/
    user_id INT,
    UNIQUE(user_id, facility, vote_date)
    /*end*/
);