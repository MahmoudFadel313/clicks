<?php 

class post{
    public $pid;

    public function __construct($pid){
        $this->pid = $pid;
        $this->mysql_conn = mysqli_connect("localhost","mamp","","node_database");
    }

    
    public function post_graph(){  //Props Of All Posts In Database
        $graph = array();
        $post_sel = "select * from mag_post";
        $post_sel_query = mysqli_query($this->mysql_conn, $post_sel);
        while($post_sel_fetch = mysqli_fetch_array($post_sel_query)){
            $graph[count($graph)] = array(
                "post_id" => $post_sel_fetch[0],
                'post_views' => $post_sel_fetch[7],
                "post_claps" => $post_sel_fetch[8],
                "post_unclaps" => $post_sel_fetch[9],
                "post_interact" => $post_sel_fetch[8] + $post_sel_fetch[9],
                "post_type" => $post_sel_fetch[6],
            );
        }
        return($graph);
    }


    public function post_prop(){  //Props Of Opereated Post (pid)
        $post_prop = array();
        $post_prop_sel = "select from mag_post where pid=$this->pid";
        $post_prop_sel_query = mysqli_query($this->mysql_conn, $post_prop_sel);
        $post_prop_sel_fetch = mysqli_fetch_array($post_prop_sel_query);
        $post_prop[$post_prop_sel_fetch[0]] = array(
            "post_views" => $post_prop_sel_fetch[7],
            "post_claps" => $post_prop_sel_fetch[8],
            "post_unclaps" => $post_prop_sel_fetch[9],
            "post_interact" => $post_prop_sel_fetch[8] + $post_prop_sel_fetch[9],
            "post_type" => $post_prop_sel_fetch[6],
        );
        return($post_prop);
    }


    public function post_graph_knn_views_interact(){ //Machine Learning Algorithm --Knn (Views, Claps + Unclaps)
        $posts_prop = $this->post_graph();
        $length_array = array();
        $post_prop = $this->post_prop();
        for($i = 0; $i < count($posts_prop); $i++){
            $x = $posts_prop[$i]['post_views'];
            $y = $posts_prop[$i]["post_interact"];
            $length = sqrt(pow($x - $post_prop[$this->pid]["post_views"], 2) + pow( $y - $post_prop[$this->pid]['post_interact'], 2));
            $length_array[$posts_prop[$i]["post_id"]] = $length;
        }
        arsort($length_array);
        return($length_array);
    }


    public function post_graph_knn_claps_unclaps(){ //Machine Learning Algorithm --Knn (Claps, Unclaps)
        $posts_prop = $this->post_graph();
        $post_prop = $this->post_prop();
        $length_array = array();
        for($i = 0; $i < count($posts_prop); $i++){
            $x = $posts_prop[$i]['post_claps'];
            $x = $posts_prop[$i]["post_unclaps"];
            $length = sqrt(pow($x - $post_prop[$this->pid]["post_claps"], 2) + pow($y - $post_prop[$this->pid]['post_unclaps']));
            $length_array[$posts_prop[$i]["post_id"]] = $length;
        }
        arsort($length_array);
        return($length_array);

    }

    public function post_graph_aprior(){ //Reccomendation Machine Learning Algorithm --Aprior
        $aprior_graph = array();
        $post_graph = $this->post_graph();
        $post_user_watch = array();
        $post_user_sel = "select * from mag_post_view";
        $post_user_sel_query = mysqli_query($this->mysql_conn, $post_user_sel);
        while($post_user_sel_fetch = mysqli_fetch_array($post_user_sel_query)){
            if($post_user_sel_fetch[0] == $this->pid){
                $post_user_watch[count($post_user_watch)] = $post_user_sel_fetch[2];
            }
        }
        
        $post_sel = "select * from mag_post_view";
        $post_sel_query = mysqli_query($this->mysql_conn, $post_sel);
        while($post_sel_fetch = mysqli_fetch_array($post_sel_query)){
            $count = 0;
            for($i = 0; $i < count($post_user_watch); $i++){
                if($post_sel_fetch[2] == $post_user_watch[$i]){
                    $count += 1;
                }
            }
            $aprior_graph[count($aprior_graph)] = array(
                'post_id' => $post_sel_fetch[0],
                'post_aprior_scale' => $count / count($post_user_watch),
            );
        }
        return($aprior_graph);

       
    }
    
}



?>