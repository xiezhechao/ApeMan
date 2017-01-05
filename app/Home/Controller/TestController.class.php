<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 16/4/20
 * Time: 11:57
 */

namespace Home\Controller;


class TestController {

    public function index () {
        $data = "uid=565113&oid=15&cost=10";
        $data = array(
            'uid' => 565113,
            'oid' => 15,
            'cost'=> 10,
        );
        $key = "baf941697f3745a412024e80892c6360";
        echo $this->sign($data,$key);
    }


    function sign($data , $key){
        if (isset($data['sign'])) unset($data['sign']);
        ksort($data);
        $query = http_build_query($data);
        echo $query."<br/>";
        return md5($query . $key);
    }
}