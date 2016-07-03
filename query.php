<?php 
        $returned_set = $database->query("SELECT post_id, post_content, post_topic, post_date FROM posts WHERE post_by_who = :user_id';");
        
        //Lets say the query returned 3 results
        //Normally the following while loop would run 3 times then, as $result wouldn't be false until the fourth call to fetchArray()
        while($result = $returned_set->fetchArray()) {
                //HOWEVER HAVING AN ADDITIONAL CALL IN THE LOOP WILL CAUSE THE LOOP TO RUN AGAIN
                $returned_set->fetchArray();
        }
?>