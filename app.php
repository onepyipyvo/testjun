<?php
if(isset($_POST) && isset($_POST['cityName'])) {
    // DB options
    $config = array('db_host' => 'localhost',
    'db_name' => 'test_jun',
    'db_user' => 'root',
    'db_password' => '',
    'db_charset' => 'utf8',
    'db_options' => [  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => true]);

    // DB connection
    $dsn = "mysql:host=".$config['db_host'].";dbname=".$config['db_name'].";charset=".$config['db_charset'];
    $pdo = new PDO($dsn, $config['db_user'], $config['db_password'], $config['db_options']);

    return getByCity($pdo, $_POST['cityName']);
}

function getByCity($db, $city){
    $table = 'forecast';
    $today_d = date("Y-m-d");
    $stm = $db->prepare('SELECT * FROM '.$table.' WHERE city_name = ? AND forecast_date = ?');
    $stm->bindParam(1, $city, PDO::PARAM_STR);
    $stm->bindValue(2, $today_d, PDO::PARAM_STR);

    $stm->execute();

    $row = $stm->fetch(PDO::FETCH_ASSOC);
    if(!$row) {
        $result = CallAPI($city);
        $res = handleCall($result);
        if($res['success']) {
            $format_data = saveToDB($db, $res['success'], $city);
            $r['success'] = $format_data[$today_d];
        } else {
            $r = $res;
        }
    } else {
        $r['success'] = $row;
    }
    echo json_encode($r);
}

function CallAPI($city)
{
    $app_id = '731fdb9f46272f54a8b68c894765410b';
    $url = 'https://api.openweathermap.org/data/2.5/forecast?q='.$city.'&mode=json&appid='.$app_id;
    $curl = curl_init();
    // test commit

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

function handleCall($result) {
//    $res_err = '{"cod":"404","message":"city not found"}';
//    $res_s = '{"cod":"200","message":0.0035,"cnt":40,"list":[{"dt":1550750400,"main":{"temp":274.87,"temp_min":273.842,"temp_max":274.87,"pressure":1014.59,"sea_level":1029.71,"grnd_level":1014.59,"humidity":78,"temp_kf":1.03},"weather":[{"id":802,"main":"Clouds","description":"scattered clouds","icon":"03d"}],"clouds":{"all":32},"wind":{"speed":9.41,"deg":310},"rain":{},"sys":{"pod":"d"},"dt_txt":"2019-02-21 12:00:00"},{"dt":1550761200,"main":{"temp":272.86,"temp_min":272.087,"temp_max":272.86,"pressure":1015.89,"sea_level":1031.04,"grnd_level":1015.89,"humidity":72,"temp_kf":0.77},"weather":[{"id":802,"main":"Clouds","description":"scattered clouds","icon":"03d"}],"clouds":{"all":44},"wind":{"speed":7.81,"deg":319},"rain":{},"sys":{"pod":"d"},"dt_txt":"2019-02-21 15:00:00"},{"dt":1550772000,"main":{"temp":269.61,"temp_min":269.096,"temp_max":269.61,"pressure":1017.6,"sea_level":1032.92,"grnd_level":1017.6,"humidity":76,"temp_kf":0.52},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02n"}],"clouds":{"all":12},"wind":{"speed":5.37,"deg":337.501},"rain":{},"sys":{"pod":"n"},"dt_txt":"2019-02-21 18:00:00"},{"dt":1550782800,"main":{"temp":267.31,"temp_min":267.047,"temp_max":267.31,"pressure":1018.16,"sea_level":1033.58,"grnd_level":1018.16,"humidity":76,"temp_kf":0.26},"weather":[{"id":802,"main":"Clouds","description":"scattered clouds","icon":"03n"}],"clouds":{"all":36},"wind":{"speed":3.67,"deg":348.002},"rain":{},"sys":{"pod":"n"},"dt_txt":"2019-02-21 21:00:00"},{"dt":1550793600,"main":{"temp":266.382,"temp_min":266.382,"temp_max":266.382,"pressure":1018.42,"sea_level":1033.99,"grnd_level":1018.42,"humidity":80,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"broken clouds","icon":"04n"}],"clouds":{"all":68},"wind":{"speed":2.81,"deg":340},"rain":{},"sys":{"pod":"n"},"dt_txt":"2019-02-22 00:00:00"},{"dt":1550804400,"main":{"temp":266.492,"temp_min":266.492,"temp_max":266.492,"pressure":1018.97,"sea_level":1034.58,"grnd_level":1018.97,"humidity":79,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"broken clouds","icon":"04n"}],"clouds":{"all":80},"wind":{"speed":3.41,"deg":353.002},"rain":{},"sys":{"pod":"n"},"dt_txt":"2019-02-22 03:00:00"},{"dt":1550815200,"main":{"temp":265.44,"temp_min":265.44,"temp_max":265.44,"pressure":1020.37,"sea_level":1035.96,"grnd_level":1020.37,"humidity":81,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"02d"}],"clouds":{"all":8},"wind":{"speed":5.62,"deg":8.5},"rain":{},"sys":{"pod":"d"},"dt_txt":"2019-02-22 06:00:00"},{"dt":1550826000,"main":{"temp":266.688,"temp_min":266.688,"temp_max":266.688,"pressure":1021.53,"sea_level":1037.02,"grnd_level":1021.53,"humidity":87,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"02d"}],"clouds":{"all":8},"wind":{"speed":6.61,"deg":5.50183},"rain":{},"sys":{"pod":"d"},"dt_txt":"2019-02-22 09:00:00"},{"dt":1550836800,"main":{"temp":267.775,"temp_min":267.775,"temp_max":267.775,"pressure":1022.02,"sea_level":1037.56,"grnd_level":1022.02,"humidity":80,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"scattered clouds","icon":"03d"}],"clouds":{"all":32},"wind":{"speed":7.72,"deg":357},"rain":{},"sys":{"pod":"d"},"dt_txt":"2019-02-22 12:00:00"},{"dt":1550847600,"main":{"temp":267.018,"temp_min":267.018,"temp_max":267.018,"pressure":1023.47,"sea_level":1039.12,"grnd_level":1023.47,"humidity":68,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":56},"wind":{"speed":7.67,"deg":357.503},"rain":{},"snow":{"3h":0.015},"sys":{"pod":"d"},"dt_txt":"2019-02-22 15:00:00"},{"dt":1550858400,"main":{"temp":266.735,"temp_min":266.735,"temp_max":266.735,"pressure":1025.21,"sea_level":1041.06,"grnd_level":1025.21,"humidity":74,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":64},"wind":{"speed":7.06,"deg":7.00037},"rain":{},"snow":{"3h":0.0575},"sys":{"pod":"n"},"dt_txt":"2019-02-22 18:00:00"},{"dt":1550869200,"main":{"temp":266.073,"temp_min":266.073,"temp_max":266.073,"pressure":1027.6,"sea_level":1043.52,"grnd_level":1027.6,"humidity":78,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":76},"wind":{"speed":6.81,"deg":15.5041},"rain":{},"snow":{"3h":0.0675},"sys":{"pod":"n"},"dt_txt":"2019-02-22 21:00:00"},{"dt":1550880000,"main":{"temp":265.716,"temp_min":265.716,"temp_max":265.716,"pressure":1029.76,"sea_level":1045.72,"grnd_level":1029.76,"humidity":80,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":68},"wind":{"speed":6.96,"deg":20.0012},"rain":{},"snow":{"3h":0.055},"sys":{"pod":"n"},"dt_txt":"2019-02-23 00:00:00"},{"dt":1550890800,"main":{"temp":265.396,"temp_min":265.396,"temp_max":265.396,"pressure":1031.71,"sea_level":1047.7,"grnd_level":1031.71,"humidity":77,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":88},"wind":{"speed":7.22,"deg":21.001},"rain":{},"snow":{"3h":0.0675},"sys":{"pod":"n"},"dt_txt":"2019-02-23 03:00:00"},{"dt":1550901600,"main":{"temp":265.628,"temp_min":265.628,"temp_max":265.628,"pressure":1034.29,"sea_level":1050.2,"grnd_level":1034.29,"humidity":76,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":64},"wind":{"speed":7.61,"deg":29.0018},"rain":{},"snow":{"3h":0.0075},"sys":{"pod":"d"},"dt_txt":"2019-02-23 06:00:00"},{"dt":1550912400,"main":{"temp":268.087,"temp_min":268.087,"temp_max":268.087,"pressure":1035.92,"sea_level":1051.75,"grnd_level":1035.92,"humidity":79,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":68},"wind":{"speed":7.61,"deg":28.5005},"rain":{},"snow":{"3h":0.0075},"sys":{"pod":"d"},"dt_txt":"2019-02-23 09:00:00"},{"dt":1550923200,"main":{"temp":271.017,"temp_min":271.017,"temp_max":271.017,"pressure":1035.71,"sea_level":1051.41,"grnd_level":1035.71,"humidity":79,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":0},"wind":{"speed":7.6,"deg":20.0034},"rain":{},"snow":{},"sys":{"pod":"d"},"dt_txt":"2019-02-23 12:00:00"},{"dt":1550934000,"main":{"temp":269.545,"temp_min":269.545,"temp_max":269.545,"pressure":1036.34,"sea_level":1052.12,"grnd_level":1036.34,"humidity":71,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":0},"wind":{"speed":5.96,"deg":11.5},"rain":{},"snow":{},"sys":{"pod":"d"},"dt_txt":"2019-02-23 15:00:00"},{"dt":1550944800,"main":{"temp":265.839,"temp_min":265.839,"temp_max":265.839,"pressure":1037.5,"sea_level":1053.49,"grnd_level":1037.5,"humidity":75,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01n"}],"clouds":{"all":0},"wind":{"speed":4.21,"deg":10.5003},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-23 18:00:00"},{"dt":1550955600,"main":{"temp":263.303,"temp_min":263.303,"temp_max":263.303,"pressure":1037.89,"sea_level":1054,"grnd_level":1037.89,"humidity":80,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02n"}],"clouds":{"all":20},"wind":{"speed":3.36,"deg":350.501},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-23 21:00:00"},{"dt":1550966400,"main":{"temp":263.104,"temp_min":263.104,"temp_max":263.104,"pressure":1037.38,"sea_level":1053.53,"grnd_level":1037.38,"humidity":82,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"broken clouds","icon":"04n"}],"clouds":{"all":56},"wind":{"speed":2.91,"deg":330.501},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-24 00:00:00"},{"dt":1550977200,"main":{"temp":263.837,"temp_min":263.837,"temp_max":263.837,"pressure":1036.31,"sea_level":1052.38,"grnd_level":1036.31,"humidity":77,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"broken clouds","icon":"04n"}],"clouds":{"all":68},"wind":{"speed":2.71,"deg":301.501},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-24 03:00:00"},{"dt":1550988000,"main":{"temp":265.672,"temp_min":265.672,"temp_max":265.672,"pressure":1035.26,"sea_level":1051.37,"grnd_level":1035.26,"humidity":81,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01d"}],"clouds":{"all":76},"wind":{"speed":3.56,"deg":296},"rain":{},"snow":{"3h":0.02},"sys":{"pod":"d"},"dt_txt":"2019-02-24 06:00:00"},{"dt":1550998800,"main":{"temp":269.985,"temp_min":269.985,"temp_max":269.985,"pressure":1034.67,"sea_level":1050.4,"grnd_level":1034.67,"humidity":89,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":88},"wind":{"speed":4.22,"deg":301.501},"rain":{},"snow":{"3h":0.1325},"sys":{"pod":"d"},"dt_txt":"2019-02-24 09:00:00"},{"dt":1551009600,"main":{"temp":270.168,"temp_min":270.168,"temp_max":270.168,"pressure":1033.04,"sea_level":1048.61,"grnd_level":1033.04,"humidity":88,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":88},"wind":{"speed":4.37,"deg":301.506},"rain":{},"snow":{"3h":0.4025},"sys":{"pod":"d"},"dt_txt":"2019-02-24 12:00:00"},{"dt":1551020400,"main":{"temp":269.97,"temp_min":269.97,"temp_max":269.97,"pressure":1032.15,"sea_level":1047.74,"grnd_level":1032.15,"humidity":82,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":76},"wind":{"speed":3.03,"deg":304.003},"rain":{},"snow":{"3h":0.065},"sys":{"pod":"d"},"dt_txt":"2019-02-24 15:00:00"},{"dt":1551031200,"main":{"temp":267.978,"temp_min":267.978,"temp_max":267.978,"pressure":1031.92,"sea_level":1047.68,"grnd_level":1031.92,"humidity":87,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"01n"}],"clouds":{"all":48},"wind":{"speed":1.61,"deg":271.001},"rain":{},"snow":{"3h":0.015},"sys":{"pod":"n"},"dt_txt":"2019-02-24 18:00:00"},{"dt":1551042000,"main":{"temp":265.754,"temp_min":265.754,"temp_max":265.754,"pressure":1031.27,"sea_level":1047.08,"grnd_level":1031.27,"humidity":85,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02n"}],"clouds":{"all":20},"wind":{"speed":1.32,"deg":227.503},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-24 21:00:00"},{"dt":1551052800,"main":{"temp":263.645,"temp_min":263.645,"temp_max":263.645,"pressure":1030.49,"sea_level":1046.38,"grnd_level":1030.49,"humidity":78,"temp_kf":0},"weather":[{"id":800,"main":"Clear","description":"clear sky","icon":"02n"}],"clouds":{"all":8},"wind":{"speed":1.31,"deg":194.002},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-25 00:00:00"},{"dt":1551063600,"main":{"temp":263.081,"temp_min":263.081,"temp_max":263.081,"pressure":1029.44,"sea_level":1045.34,"grnd_level":1029.44,"humidity":75,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02n"}],"clouds":{"all":20},"wind":{"speed":2.51,"deg":224.009},"rain":{},"snow":{},"sys":{"pod":"n"},"dt_txt":"2019-02-25 03:00:00"},{"dt":1551074400,"main":{"temp":263.858,"temp_min":263.858,"temp_max":263.858,"pressure":1028.54,"sea_level":1044.39,"grnd_level":1028.54,"humidity":82,"temp_kf":0},"weather":[{"id":801,"main":"Clouds","description":"few clouds","icon":"02d"}],"clouds":{"all":24},"wind":{"speed":1.51,"deg":212.502},"rain":{},"snow":{},"sys":{"pod":"d"},"dt_txt":"2019-02-25 06:00:00"},{"dt":1551085200,"main":{"temp":271.53,"temp_min":271.53,"temp_max":271.53,"pressure":1027.41,"sea_level":1042.79,"grnd_level":1027.41,"humidity":94,"temp_kf":0},"weather":[{"id":802,"main":"Clouds","description":"scattered clouds","icon":"03d"}],"clouds":{"all":44},"wind":{"speed":4.12,"deg":253.501},"rain":{},"snow":{},"sys":{"pod":"d"},"dt_txt":"2019-02-25 09:00:00"},{"dt":1551096000,"main":{"temp":274.024,"temp_min":274.024,"temp_max":274.024,"pressure":1024.59,"sea_level":1039.81,"grnd_level":1024.59,"humidity":88,"temp_kf":0},"weather":[{"id":803,"main":"Clouds","description":"broken clouds","icon":"04d"}],"clouds":{"all":80},"wind":{"speed":5.57,"deg":274.502},"rain":{},"snow":{},"sys":{"pod":"d"},"dt_txt":"2019-02-25 12:00:00"},{"dt":1551106800,"main":{"temp":273.998,"temp_min":273.998,"temp_max":273.998,"pressure":1022.23,"sea_level":1037.45,"grnd_level":1022.23,"humidity":88,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":80},"wind":{"speed":5.37,"deg":288.003},"rain":{},"snow":{"3h":0.0425},"sys":{"pod":"d"},"dt_txt":"2019-02-25 15:00:00"},{"dt":1551117600,"main":{"temp":273.827,"temp_min":273.827,"temp_max":273.827,"pressure":1021.07,"sea_level":1036.46,"grnd_level":1021.07,"humidity":89,"temp_kf":0},"weather":[{"id":500,"main":"Rain","description":"light rain","icon":"10n"}],"clouds":{"all":80},"wind":{"speed":4.72,"deg":317.501},"rain":{"3h":0.024},"snow":{"3h":0.015},"sys":{"pod":"n"},"dt_txt":"2019-02-25 18:00:00"},{"dt":1551128400,"main":{"temp":273.426,"temp_min":273.426,"temp_max":273.426,"pressure":1020.1,"sea_level":1035.47,"grnd_level":1020.1,"humidity":94,"temp_kf":0},"weather":[{"id":500,"main":"Rain","description":"light rain","icon":"10n"}],"clouds":{"all":88},"wind":{"speed":3.57,"deg":331.5},"rain":{"3h":0.06},"snow":{"3h":0.005},"sys":{"pod":"n"},"dt_txt":"2019-02-25 21:00:00"},{"dt":1551139200,"main":{"temp":272.57,"temp_min":272.57,"temp_max":272.57,"pressure":1019.86,"sea_level":1035.19,"grnd_level":1019.86,"humidity":95,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":80},"wind":{"speed":3.67,"deg":336},"rain":{},"snow":{"3h":0.06},"sys":{"pod":"n"},"dt_txt":"2019-02-26 00:00:00"},{"dt":1551150000,"main":{"temp":272.172,"temp_min":272.172,"temp_max":272.172,"pressure":1019.15,"sea_level":1034.48,"grnd_level":1019.15,"humidity":95,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13n"}],"clouds":{"all":88},"wind":{"speed":3.57,"deg":327.001},"rain":{},"snow":{"3h":0.11},"sys":{"pod":"n"},"dt_txt":"2019-02-26 03:00:00"},{"dt":1551160800,"main":{"temp":272.118,"temp_min":272.118,"temp_max":272.118,"pressure":1018.71,"sea_level":1034.01,"grnd_level":1018.71,"humidity":95,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":88},"wind":{"speed":3.33,"deg":319.002},"rain":{},"snow":{"3h":0.095},"sys":{"pod":"d"},"dt_txt":"2019-02-26 06:00:00"},{"dt":1551171600,"main":{"temp":274.282,"temp_min":274.282,"temp_max":274.282,"pressure":1018.36,"sea_level":1033.35,"grnd_level":1018.36,"humidity":93,"temp_kf":0},"weather":[{"id":600,"main":"Snow","description":"light snow","icon":"13d"}],"clouds":{"all":88},"wind":{"speed":3.31,"deg":318.503},"rain":{},"snow":{"3h":0.1},"sys":{"pod":"d"},"dt_txt":"2019-02-26 09:00:00"}],"city":{"id":703448,"name":"Kyiv","coord":{"lat":50.4501,"lon":30.5241},"country":"UA","population":2514227}}';

    $r = json_decode($result);


    if($r->cod != '200') {
        $res['error'] = "Error - ".$r->message;
    } else {
        $forecast = array();
        foreach($r->list as $p) {
            $d = gmdate("Y-m-d", $p->dt);
            $forecast[$d]['temp'] = $p->main->temp;
            $forecast[$d]['humidity'] = $p->main->humidity;
            $forecast[$d]['pressure'] = $p->main->pressure;
        }
        $res['success'] = $forecast;
    }
    return $res;
}

function saveToDB($db, $forecast, $city) {
    $data = array();
    if(count($forecast)) {
        $table = 'forecast';
        $columns = array('forecast_date',
            'city_name',
            'temperature',
            'humidity',
            'pressure');
        $columnsDefault = array('forecast_date',
            'city_name',
            'temp',
            'humidity',
            'pressure');

        foreach($forecast as $date => $cast) {
            $data_arr = $cast;
            $data_arr['city_name'] = $city;
            $data_arr['forecast_date'] = $date;
            $data[$date] = $data_arr;

            $columnSql = implode(',', $columns);
            $bindingSql = ':'.implode(',:', $columnsDefault);
            $sql = "INSERT INTO $table ($columnSql) VALUES ($bindingSql)";
            $stm = $db->prepare($sql);
            foreach ($data_arr as $key => $value) {
                $stm->bindValue(':'.$key, $value);
            }
            $status = $stm->execute();
        }
    }
    return $data;
}