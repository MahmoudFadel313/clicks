<?php
//header("Content-Type: application/javascript");
//header("Access-Control-Allow-Origin: *");


class mag{
    public $mid;
    

    public function __construct($mid){
        $this->mid = $mid;
        $this->mysql_conn = mysqli_connect("localhost",'mamp','','node_database');

    }


    public function mag_index_explore(){ //For All Magazines In Database
        $mag_graph = array();
        $mag_sel = "select * from mag";
        $mag_sel_query = mysqli_query($this->mysql_conn,$mag_sel);
        while($mag_sel_fetch = mysqli_fetch_array($mag_sel_query)){
            $mag_graph[count($mag_graph)] = array(
                'mid' => $mag_sel_fetch[0],
                'name' => $mag_sel_fetch[1],
                'claps' => 0,
                'unclaps' => 0,
                'views' => 0,
                'follow' => 0,
                'prof' => 0,
                'image' => "data:image/png;base64,".base64_encode($mag_sel_fetch[2])." ",
                'cover' => "data:image/png;base64,".base64_encode($mag_sel_fetch[3])." ",
                'bio' => $mag_sel_fetch[4],
                'type' => array(
                    'news' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'tech' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'sports' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'politics' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'economics' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'art' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                    'life_style' => array(
                        'claps' => 0,
                        'unclaps' => 0,
                        'views' => 0,
                        'prof' => 0,
                    ),
                )
                
                
            );
            $mag_post_sel = "select * from mag_post";
            $mag_post_sel_query = mysqli_query($this->mysql_conn,$mag_post_sel);
            while($mag_post_sel_fetch = mysqli_fetch_array($mag_post_sel_query)){
                if($mag_post_sel_fetch[1] == $mag_sel_fetch[0]){
                    $post_claps = $mag_post_sel_fetch[8];
                    $post_unclaps = $mag_post_sel_fetch[9];
                    $post_views = $mag_post_sel_fetch[7];
                    $mag_graph[count($mag_graph) - 1]['claps'] += $post_claps;
                    $mag_graph[count($mag_graph) - 1]['unclaps'] += $post_unclaps;
                    $mag_graph[count($mag_graph) - 1]['views'] += $post_views;
                    $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['claps'] += $post_claps;
                    $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['unclaps'] += $post_unclaps;
                    $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['views'] += $post_views;
                    $type_claps = $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['claps'];
                    $type_unclaps = $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['unclaps'];
                    $type_views = $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['views'];
                    
                   // $p = $type_claps - $type_unclaps;
                   if($type_views == 0){
                    $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['prof'] = 0;
                   }else{
                    $mag_graph[count($mag_graph) - 1]['type'][$mag_post_sel_fetch[6]]['prof'] = ($type_claps - $type_unclaps) / $type_views;
                   }
                }
            }
            $post_follow_sel = 'select * from mag_follow';
            $post_follow_sel_query = mysqli_query($this->mysql_conn,$post_follow_sel);
            while($post_follow_sel_fetch = mysqli_fetch_array($post_follow_sel_query)){
                if($post_follow_sel_fetch[2] == $mag_sel_fetch[0]){
                    $mag_graph[count($mag_graph) - 1]['follow'] += 1;
                }
            }
            $mag_claps = $mag_graph[count($mag_graph) - 1]['claps'];
            $mag_unclaps = $mag_graph[count($mag_graph) - 1]['unclaps'];
            $mag_views = $mag_graph[count($mag_graph) - 1]['views'];
            $plus = $mag_claps - $mag_unclaps;
            if($plus > 0){
                $mag_prof = ($mag_claps - $mag_unclaps) / $mag_views;
                
            }else{
                $mag_prof = 0;
            }
            $mag_graph[count($mag_graph) - 1]['prof'] = $mag_prof;
            
        }
        return($mag_graph);
    }


    public function mag_index_prof(){ //Bubble Sort Algorithm
        $mag_graph = $this->mag_index_explore();
        $temp = 0;
        for($i = 0; $i < count($mag_graph); $i++){
            for($n = 0; $n < count($mag_graph) - 1; $n++){
                if($mag_graph[$n]['prof'] < $mag_graph[$n + 1]['prof']){
                    $temp = $mag_graph[$n];
                    $mag_graph[$n] = $mag_graph[$n + 1];
                    $mag_graph[$n + 1] = $temp;
                }
            }
        }
        return($mag_graph);
    }


    public function mag_index_type_prof($type){ //Bubble Sort Algorithm
        $mag_graph = $this->mag_index_explore();
        $temp = 0;
        for($i = 0; $i < count($mag_graph); $i++){
            for($n = 0; $n < count($mag_graph) - 1; $n++){
                if($mag_graph[$n]['type'][$type]['prof'] < $mag_graph[$n + 1]['type'][$type]['prof']){
                    $temp = $mag_graph[$n];
                    $mag_graph[$n] = $mag_graph[$n + 1];
                    $mag_graph[$n + 1] = $temp;
                }
            }
        }
        return($mag_graph);
    }


    public function mag_index_clap(){ //Bubble Sort Algorithm
        $mag_graph = $this->mag_index_explore();
        $temp = 0;
        for($i = 0; $i < count($mag_graph); $i++){
            for($n = 0; $n < count($mag_graph) - 1; $n++){
                if($mag_graph[$n]['claps'] > $mag_graph[$n + 1]['claps']){
                    $temp = $mag_graph[$n];
                    $mag_graph[$n] = $mag_graph[$n + 1];
                    $mag_graph[$n + 1] = $temp;
                }
            }
        }
        return($mag_graph);

    }
    

    public function mag_posts(){   //For Operated Magazine
        $mag_post_sel = "select * from mag_post";
        $mag_posts_props = array();
        $mag_post_sel_query = mysqli_query($this->mysql_conn,$mag_post_sel);
        while($mag_post_sel_fetch = mysqli_fetch_array($mag_post_sel_query)){
            if($mag_post_sel_fetch[1] == $this->mid){
                $post_id = $mag_post_sel_fetch[0];
                $post_text = $mag_post_sel_fetch[2];
                $post_image = "data:image/png;base64,".base64_encode($mag_post_sel_fetch[3])." ";
                $post_type = $mag_post_sel_fetch[6];
                $post_views = $mag_post_sel_fetch[7];
                $post_claps = $mag_post_sel_fetch[8];
                $post_unclaps = $mag_post_sel_fetch[9];
                $post_title = $mag_post_sel_fetch[10];
                $mag_posts_props[count($mag_posts_props)] = array(
                    'post_id' => $post_id,
                    'post_text' => $post_text,
                    'post_image' => $post_image,
                    'post_type' => $post_type,
                    'post_views' => $post_views,
                    'post_claps' => $post_claps,
                    'post_unclaps' => $post_unclaps,
                    'post_title' => $post_title,
                    'post_comments' => array(),
                );
                $post_comment_sel = "select * from mag_post_comment";
                $post_comment_sel_query = mysqli_query($this->mysql_conn,$post_comment_sel);
                while($post_comment_sel_fetch = mysqli_fetch_array($post_comment_sel_query)){
                    if($post_comment_sel_fetch[1] == $post_id){
                        $comment_user_id = $post_comment_sel_fetch[2];
                        $comment_text = $post_comment_sel_fetch[3];
                        $mag_posts_props[count($mag_posts_props) - 1]['post_comments'][count($mag_posts_props[count($mag_posts_props) - 1]['post_comments'])] = array(
                            'user_id' => $comment_user_id,
                            'comment_text' => $comment_text,
                        );
                    }
                }
            }
        }
        
        return($mag_posts_props);
    }


    public function mag_prof(){ //For Operated Magazine
        $mag_graph = $this->mag_index_prof();
        $index = 0;
        for($i = 0; $i < count($mag_graph) - 1; $i++){
            if($mag_graph[$i]['mid'] == $this->mid){
                $prof = $mag_graph[$i]['prof'];
                $index = $i + 1;
                break;
            }
            
        }
        return($index);
    }
    

    public function mag_profile(){ //For Operated Magazine
        $mag_posts = $this->mag_posts();
        $mag_props = array();
        $mid = $this->mid;
        $mag_sel = "select * from mag where mid=$mid";
        $mag_sel_query = mysqli_query($this->mysql_conn,$mag_sel);
        $mag_sel_fetch = mysqli_fetch_array($mag_sel_query);
        $mag_name = $mag_sel_fetch[1];
        $mag_id = $mag_sel_fetch[0];
        $mag_logo = "data:image/png;base64,".base64_encode($mag_sel_fetch[2])." ";
        $mag_cover = "data:image/png;base64,".base64_encode($mag_sel_fetch[3])." ";
        $mag_bio = $mag_sel_fetch[4];
        $mag_props[count($mag_props)] = array(
            'mag_id' => $mag_id,
            'mag_name' => $mag_name,
            'mag_image' => $mag_logo,
            'mag_cover' => $mag_cover,
            'mag_bio' => $mag_bio,
            'mag_bio' => $mag_bio,
        );
        $follow_count = 0;
        $mag_follow_sel = "select * from mag_follow";
        $mag_follow_sel_query = mysqli_query($this->mysql_conn,$mag_follow_sel);
        while($mag_follow_sel_fetch = mysqli_fetch_array($mag_follow_sel_query)){
            if($mag_follow_sel_fetch[2] == $this->mid){
                $follow_count += 1;
            }
        }
        $mag_props[count($mag_props) - 1]['mag_followers'] = $follow_count;
        $mag_props[count($mag_props) - 1]['mag_index'] = $this->mag_prof();
        $mag_props[count($mag_props) - 1]['mag_posts'] = $mag_posts;
        
        return $mag_props;
    }
}
//$mag = new mag(3);
//print_r(json_encode($mag->mag_index_type_prof("news")));
//print_r(json_encode($mag->mag_posts()));
//echo "well";
//echo $mag->mid;
//print_r(json_encode($mag->mag_profile()));

?>